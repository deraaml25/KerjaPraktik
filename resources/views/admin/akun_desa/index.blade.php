<x-app-layout>
    @section('title', 'Manajemen Akun Desa')

    <div class="mb-6">
        <form action="{{ route('admin.akun_desa.index') }}" method="GET" class="w-full">
            <div class="relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama desa atau username..."
                    class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6">
            </div>
        </form>
    </div>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session("success") }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top'
                });
            });
        </script>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-200 text-sm font-medium">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-card shadow-sm border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm whitespace-nowrap text-left">
                <thead class="bg-gray-50 border-b border-border">
                    <tr>
                        <th class="px-6 py-4 font-bold text-ink text-left" style="width: 35%; padding-left: 5rem;">Nama Desa</th>
                        <th class="px-6 py-4 font-bold text-ink text-center" style="width: 25%;">Kecamatan</th>
                        <th class="px-6 py-4 font-bold text-ink text-center" style="width: 25%;">Username</th>
                        <th class="px-6 py-4 font-bold text-ink text-center" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($akuns as $akun)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 text-left" style="width: 35%; padding-left: 5rem;">
                                <div class="font-bold text-ink">{{ $akun->desa->nama_desa ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm text-muted">{{ $akun->desa->kecamatan->nama_kecamatan ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="font-mono text-sm text-primary font-bold">{{ $akun->username }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" 
                                    x-data=""
                                    x-on:click="$dispatch('open-modal', 'reset-password-{{ $akun->id }}')"
                                    class="inline-flex items-center px-2 py-1.5 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 hover:text-yellow-700 text-xs font-medium rounded border border-yellow-200 transition-all hover:scale-105" title="Reset Password">
                                    <span class="material-symbols-outlined text-[16px]">key</span>
                                </button>

                                <!-- Modal Reset Password -->
                                <x-modal name="reset-password-{{ $akun->id }}" :show="false" focusable>
                                    <form method="post" action="{{ route('admin.akun_desa.update_password', $akun->id) }}" class="p-6 text-left">
                                        @csrf
                                        @method('patch')

                                        <h2 class="text-lg font-bold text-gray-900 mb-4">
                                            Reset Password Akun {{ $akun->desa->nama_desa ?? '' }}
                                        </h2>
                                        
                                        <p class="text-sm text-gray-600 mb-6">
                                            Username: <span class="font-mono font-bold text-primary">{{ $akun->username }}</span>
                                        </p>

                                        <div class="mb-4">
                                            <label for="password_{{ $akun->id }}" class="block text-sm font-medium text-gray-700">Password Baru</label>
                                            <input type="password" name="password" id="password_{{ $akun->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                            <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter.</p>
                                        </div>

                                        <div class="mb-6">
                                            <label for="password_confirmation_{{ $akun->id }}" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation_{{ $akun->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                        </div>

                                        <div class="mt-6 flex justify-end">
                                            <button type="button" x-on:click="$dispatch('close')" class="mr-3 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                                Batal
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-primary border border-transparent rounded-md text-sm font-medium text-white hover:bg-primary-dark">
                                                Simpan Password
                                            </button>
                                        </div>
                                    </form>
                                </x-modal>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-muted">
                                Tidak ada data akun desa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($akuns->hasPages())
            <div class="p-4 border-t border-border bg-gray-50">
                {{ $akuns->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

