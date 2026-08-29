<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Desa;
use App\Models\Kecamatan;
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

        $desas_without_account = Desa::whereDoesntHave('users', function($q) {
            $q->where('role', 'desa');
        })->orderBy('nama_desa')->get();
        
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.akun_desa.index', compact('akuns', 'desas_without_account', 'kecamatans'));
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

    public function store(Request $request)
    {
        $request->validate([
            'nama_desa' => ['required', 'string', 'max:255'],
            'kecamatan_id' => ['required', 'exists:kecamatans,id'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $desa = Desa::create([
            'nama_desa' => $request->nama_desa,
            'kecamatan_id' => $request->kecamatan_id
        ]);
        
        $email = $request->email;
        if (empty($email)) {
            // Generate a dummy email if not provided
            $email = 'desa_' . $desa->id . '_' . time() . '@sidmini.local';
        }

        User::create([
            'name' => 'Admin ' . $desa->nama_desa,
            'username' => $request->username,
            'email' => $email,
            'password' => Hash::make($request->password),
            'role' => 'desa',
            'desa_id' => $desa->id,
        ]);

        return back()->with('success', 'Akun untuk desa ' . $desa->nama_desa . ' berhasil dibuat.');
    }
}
