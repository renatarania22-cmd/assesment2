<?php

namespace Modules\Setoran\Http\Controllers; // Sesuaikan jika folder aslinya memakai \App\Http\Controllers

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\JenisSampah\Entities\JenisSampah;
use Modules\Setoran\Entities\Setoran;
use Modules\Setoran\Http\Requests\KeputusanSetoranRequest;
use Modules\Setoran\Http\Requests\StoreSetoranRequest;
use Modules\Setoran\Http\Requests\UpdateSetoranRequest;

class SetoranController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Setoran::with(['user', 'jenisSampah'])->latest();

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $setorans = $query->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $setorans->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'user_id' => $item->user_id,
                        'nama_nasabah' => $item->user->name ?? '-',
                        'jenis_sampah_id' => $item->jenis_sampah_id,
                        'kode_jenis' => $item->jenisSampah->kode_jenis ?? '-',
                        'nama_jenis' => $item->jenisSampah->nama_jenis ?? '-',
                        'berat_kg' => number_format((float)$item->berat_kg, 2, '.', ''),
                        'tanggal_setor' => $item->tanggal_setor,
                        'catatan' => $item->catatan,
                        'status' => $item->status,
                        'foto_url' => $item->foto_bukti ? Storage::url($item->foto_bukti) : null,
                        'harga_saat_setor' => (float)$item->harga_saat_setor,
                        'total_nilai' => (float)$item->total_nilai,
                    ];
                })
            ]);
        }

        $katalog = JenisSampah::orderBy('kode_jenis')->get();
        return view('setoran::index', compact('setorans', 'katalog'));
    }

    public function show($id)
    {
        $setoran = Setoran::with(['user', 'jenisSampah'])->findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'admin' && $setoran->user_id !== $user->id) {
            abort(403, 'Akses ditolak. Anda tidak berhak melihat transaksi akun lain.');
        }

        return response()->json([
            'id' => $setoran->id,
            'nama_nasabah' => $setoran->user->name ?? '-',
            'jenis_sampah_id' => $setoran->jenis_sampah_id,
            'kode_jenis' => $setoran->jenisSampah->kode_jenis ?? '-',
            'nama_jenis' => $setoran->jenisSampah->nama_jenis ?? '-',
            'berat_kg' => number_format((float)$setoran->berat_kg, 2, '.', ''),
            'tanggal_setor' => $setoran->tanggal_setor,
            'catatan' => $setoran->catatan,
            'status' => $setoran->status,
            'foto_url' => $setoran->foto_bukti ? Storage::url($setoran->foto_bukti) : null,
            'harga_saat_setor' => (float)$setoran->harga_saat_setor,
            'total_nilai' => (float)$setoran->total_nilai,
        ]);
    }

    public function store(StoreSetoranRequest $request)
    {
        $jenis = JenisSampah::findOrFail($request->jenis_sampah_id);
        $hargaSaatSetor = (float) $jenis->harga_per_kg;
        $totalNilai = round((float) $request->berat_kg * $hargaSaatSetor, 2);

        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoPath = $request->file('foto_bukti')->store('bukti-setoran', 'public');
        }

        $setoran = Setoran::create([
            'user_id' => Auth::id(),
            'jenis_sampah_id' => $jenis->id,
            'berat_kg' => $request->berat_kg,
            'tanggal_setor' => $request->tanggal_setor,
            'catatan' => $request->catatan,
            'status' => 'Menunggu',
            'foto_bukti' => $fotoPath,
            'harga_saat_setor' => $hargaSaatSetor,
            'total_nilai' => $totalNilai,
        ]);

        return response()->json([
            'message' => 'Setoran sampah berhasil dibuat dengan status Menunggu.',
            'data' => $setoran
        ], 201);
    }

    public function edit($id)
    {
        $setoran = Setoran::findOrFail($id);
        $user = Auth::user();

        if ($setoran->user_id !== $user->id) {
            abort(403, 'Akses ditolak. Transaksi ini bukan milik Anda.');
        }

        if ($setoran->status !== 'Menunggu') {
            abort(403, 'Akses ditolak. Hanya transaksi berstatus Menunggu yang dapat diedit.');
        }

        return response()->json([
            'id' => $setoran->id,
            'jenis_sampah_id' => $setoran->jenis_sampah_id,
            'berat_kg' => number_format((float)$setoran->berat_kg, 2, '.', ''),
            'tanggal_setor' => $setoran->tanggal_setor,
            'catatan' => $setoran->catatan,
            'foto_url' => $setoran->foto_bukti ? Storage::url($setoran->foto_bukti) : null,
        ]);
    }

    public function update(UpdateSetoranRequest $request, $id)
    {
        $setoran = Setoran::findOrFail($id);
        $user = Auth::user();

        if ($setoran->user_id !== $user->id) {
            abort(403, 'Akses ditolak. Transaksi ini bukan milik Anda.');
        }

        if ($setoran->status !== 'Menunggu') {
            abort(403, 'Akses ditolak. Transaksi yang sudah diproses tidak dapat diubah.');
        }

        $jenis = JenisSampah::findOrFail($request->jenis_sampah_id);
        $hargaSaatSetor = (float) $jenis->harga_per_kg;
        $totalNilai = round((float) $request->berat_kg * $hargaSaatSetor, 2);

        $fotoPath = $setoran->foto_bukti;
        if ($request->hasFile('foto_bukti')) {
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto_bukti')->store('bukti-setoran', 'public');
        }

        $setoran->update([
            'jenis_sampah_id' => $jenis->id,
            'berat_kg' => $request->berat_kg,
            'tanggal_setor' => $request->tanggal_setor,
            'catatan' => $request->catatan,
            'foto_bukti' => $fotoPath,
            'harga_saat_setor' => $hargaSaatSetor,
            'total_nilai' => $totalNilai,
        ]);

        return response()->json([
            'message' => 'Setoran sampah berhasil diperbarui.',
            'data' => $setoran
        ]);
    }

    public function keputusan(KeputusanSetoranRequest $request, $id)
    {
        $setoran = Setoran::findOrFail($id);

        if ($setoran->status !== 'Menunggu') {
            return response()->json([
                'message' => 'Keputusan final sudah ditetapkan (' . $setoran->status . ') dan tidak dapat diubah kembali.'
            ], 422);
        }

        $setoran->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Status setoran berhasil diubah menjadi ' . $request->status . '.',
            'data' => $setoran
        ]);
    }

    public function destroy($id)
    {
        $setoran = Setoran::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'admin') {
            if ($setoran->user_id !== $user->id) {
                abort(403, 'Akses ditolak. Anda tidak dapat menghapus transaksi milik akun lain.');
            }
            if ($setoran->status !== 'Menunggu') {
                abort(403, 'Akses ditolak. Hanya transaksi berstatus Menunggu yang dapat dihapus.');
            }
        }

        if ($setoran->foto_bukti && Storage::disk('public')->exists($setoran->foto_bukti)) {
            Storage::disk('public')->delete($setoran->foto_bukti);
        }

        $setoran->delete();

        return response()->json([
            'message' => 'Transaksi setoran berhasil dihapus.'
        ]);
    }
}