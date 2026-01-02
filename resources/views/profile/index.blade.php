@extends('layouts.mobile_modern')

@section('content')
    @php
        /** @var \App\Models\User $user */
        $user = Auth::user();
    @endphp
    <!-- Header -->
    <div class="flex items-center justify-between mb-5 mt-2">
        <div class="flex items-center gap-3">
            <a href="/dashboard"
                class="flex items-center justify-center h-10 w-10 bg-white rounded-full shadow-sm text-slate-500 border border-slate-100 hover:bg-slate-50 transition-colors">
                <ion-icon name="chevron-back-outline" class="text-xl"></ion-icon>
            </a>
            <h1 class="text-xl font-bold text-slate-800">Profile & Settings</h1>
        </div>
    </div>

    <!-- Profile Photo Section -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mb-6 text-center">
        <div class="relative w-24 h-24 mx-auto mb-4">
            @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                @php $fotoPath = getfotoKaryawan($karyawan->foto); @endphp
                <img src="{{ $fotoPath }}" alt="Profile"
                    class="w-full h-full object-cover rounded-full border-4 border-slate-50 shadow-sm cursor-pointer hover:opacity-90 transition-opacity"
                    onclick="showProfileImage('{{ $fotoPath }}')">
            @else
                @php $fotoPath = asset('assets/img/avatars/No_Image_Available.jpg'); @endphp
                <img src="{{ $fotoPath }}" alt="Default Profile"
                    class="w-full h-full object-cover rounded-full border-4 border-slate-50 shadow-sm cursor-pointer hover:opacity-90 transition-opacity"
                    onclick="showProfileImage('{{ $fotoPath }}')">
            @endif
            <label for="foto"
                class="absolute bottom-0 right-0 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center shadow-lg cursor-pointer hover:bg-blue-700 transition-colors">
                <ion-icon name="camera-outline"></ion-icon>
            </label>
        </div>
        <h2 class="text-lg font-bold text-slate-800">{{ $karyawan->nama_karyawan }}</h2>
        <p class="text-slate-500 text-sm">{{ $karyawan->jabatan }}</p>
    </div>

    <!-- Personal Info Form -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mb-6">
        <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
            <ion-icon name="person-circle-outline" class="text-primary text-xl"></ion-icon>
            Informasi Pribadi
        </h3>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="formProfile"
            class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Hidden File Input triggered by label above -->
            <input type="file" name="foto" id="foto" class="hidden" accept=".jpg, .jpeg, .png">

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 ml-1">Nama Lengkap</label>
                <input type="text" name="nama_karyawan" value="{{ $karyawan->nama_karyawan }}"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 ml-1">No. KTP</label>
                <input type="text" name="no_ktp" value="{{ $karyawan->no_ktp }}"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 ml-1">No. HP</label>
                <input type="text" name="no_hp" value="{{ $karyawan->no_hp }}"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 ml-1">Alamat</label>
                <textarea name="alamat"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none h-24">{{ $karyawan->alamat }}</textarea>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 ml-1">Username</label>
                <input type="text" name="username" value="{{ $user->username }}"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 ml-1">Email</label>
                <input type="email" name="email" value="{{ $user->email }}"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>

            <button type="submit"
                class="w-full bg-primary text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-500/40 hover:bg-blue-700 active:scale-95 transition-all text-sm">
                Simpan Perubahan
            </button>
        </form>
    </div>

    <!-- Security Settings (Password) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mb-6">
        <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
            <ion-icon name="shield-checkmark-outline" class="text-primary text-xl"></ion-icon>
            Keamanan
        </h3>

        <form action="{{ route('users.updatepassword', Crypt::encrypt($user->id)) }}" method="POST" id="formPassword"
            class="space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 ml-1">Password Baru</label>
                <div class="relative">
                    <input type="password" id="passwordbaru" name="passwordbaru" placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    <button type="button" onclick="togglePassword('passwordbaru')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                        <ion-icon name="eye-outline"></ion-icon>
                    </button>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-500 ml-1">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" id="konfirmasipassword" name="konfirmasipassword"
                        placeholder="Ulangi password baru"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    <button type="button" onclick="togglePassword('konfirmasipassword')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                        <ion-icon name="eye-outline"></ion-icon>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-slate-800 text-white font-bold py-3 rounded-xl shadow-lg hover:bg-slate-900 active:scale-95 transition-all text-sm flex items-center justify-center gap-2">
                <ion-icon name="lock-closed-outline"></ion-icon>
                Update Password
            </button>
        </form>
    </div>

    <!-- Spacer for Bottom Nav -->
    <div class="h-20"></div>
@endsection

@push('scripts')
    <script>
        function showProfileImage(src) {
            Swal.fire({
                imageUrl: src,
                imageAlt: 'Profile Picture',
                showConfirmButton: true,
                showCloseButton: false,
                confirmButtonText: 'Tutup',
                buttonsStyling: false,
                background: 'transparent',
                backdrop: `rgba(0,0,0,0.8)`,
                heightAuto: false,
                scrollbarPadding: false,
                customClass: {
                    image: 'rounded-xl shadow-2xl max-h-[80vh] w-auto',
                    confirmButton: 'mt-4 px-6 py-2 bg-white/20 text-white font-bold rounded-full border border-white/30 hover:bg-white/30 transition-all backdrop-blur-sm'
                }
            });
        }

        function togglePassword(id) {
            var x = document.getElementById(id);
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        // Preview Photo
        $('#foto').change(function () {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.relative img').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
    </script>
@endpush