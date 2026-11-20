<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Modules\JenisSampah\Entities\JenisSampah;
use Modules\Setoran\Entities\Setoran;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $jumlahKatalog = JenisSampah::count();
            $transaksiMenunggu = Setoran::where('status', 'Menunggu')->count();
            $totalBeratDiverifikasi = Setoran::where('status', 'Diverifikasi')->sum('berat_kg');

            return view('dashboard', compact(
                'user',
                'jumlahKatalog',
                'transaksiMenunggu',
                'totalBeratDiverifikasi'
            ));
        }

        $transaksiSaya = Setoran::with('jenisSampah')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $jumlahTransaksiSaya = $transaksiSaya->count();

        return view('dashboard', compact(
            'user',
            'transaksiSaya',
            'jumlahTransaksiSaya'
        ));
    }

    public function profil()
    {
        return view('profil', ['user' => Auth::user()]);
    }
}