<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AkunDesaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('desa.kecamatan')->where('role', 'desa');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhereHas('desa', function ($qDesa) use ($search) {
                      $qDesa->where('nama_desa', 'like', "%{$search}%");
                  });
            });
        }

        // Sort by kecamatan then desa
        $akuns = $query->join('desas', 'users.desa_id', '=', 'desas.id')
            ->leftJoin('kecamatans', 'desas.kecamatan_id', '=', 'kecamatans.id')
            ->orderBy('kecamatans.nama_kecamatan')
            ->orderBy('desas.nama_desa')
            ->select('users.*') // to avoid id collision
            ->paginate(15);

        return view('admin.akun_desa.index', compact('akuns'));
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password untuk akun desa ' . ($user->desa->nama_desa ?? '') . ' berhasil diperbarui.');
    }
}
