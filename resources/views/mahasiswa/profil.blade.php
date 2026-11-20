@extends('layouts.mahasiswa')

@section('title', 'Profil Saya')

@section('content')
<div class="mx-auto max-w-lg space-y-5">

    {{-- Info akun --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex items-center gap-4">
            {{-- Foto --}}
            <div class="relative">
                <div class="h-20 w-20 overflow-hidden rounded-full border-2 border-stone-200 bg-stone-100">
                    @if($anggota?->foto)
                        <img src="{{ $anggota->foto_url }}" class="h-full w-full object-cover" alt="">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-[#FBF4F3]">
                            <span class="font-serif text-3xl font-bold text-[#6B1124]">
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <h2 class="font-serif text-xl font-bold text-stone-800">{{ $user->name }}</h2>
                <p class="font-mono text-sm text-stone-500">{{ $user->nim }}</p>
                @if($anggota)
                    <p class="text-sm text-stone-500">{{ $anggota->prodi }} · Angkatan {{ $anggota->angkatan }}</p>
                    <span class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $anggota->status === 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                        {{ ucfirst($anggota->status) }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Ubah foto --}}
    @if($anggota)
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <h3 class="mb-4 font-serif text-base font-bold text-stone-800">Foto Profil</h3>

        <form method="POST" action="{{ route('mahasiswa.profil.foto') }}" enctype="multipart/form-data"
              class="space-y-4">
            @csrf

            {{-- Preview foto --}}
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 overflow-hidden rounded-full border-2 border-stone-200 bg-stone-100" id="preview-wrap">
                    @if($anggota->foto)
                        <img id="preview-img" src="{{ $anggota->foto_url }}" class="h-full w-full object-cover" alt="">
                    @else
                        <img id="preview-img" src="" class="hidden h-full w-full object-cover" alt="">
                        <div id="preview-placeholder"
                             class="flex h-full w-full items-center justify-center bg-[#FBF4F3] text-xl font-bold text-[#6B1124]">
                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <label for="foto"
                           class="inline-block cursor-pointer rounded-lg border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50 transition">
                        Pilih Foto
                    </label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
                           class="sr-only">
                    <p class="mt-1 text-xs text-stone-400">JPG, PNG, WebP · maks. 2 MB</p>
                </div>
            </div>

            @error('foto')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror

            <button type="submit"
                    class="w-full rounded-lg bg-[#6B1124] py-2.5 text-sm font-semibold text-white hover:bg-[#4A0012] transition">
                Simpan Foto
            </button>
        </form>
    </div>
    @endif

    {{-- Ganti password --}}
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <h3 class="mb-4 font-serif text-base font-bold text-stone-800">Ganti Password</h3>

        <form method="POST" action="{{ route('mahasiswa.profil.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-stone-600">
                    Password Lama
                </label>
                <input type="password" name="password_lama" required
                       placeholder="Masukkan password lama"
                       class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none transition
                              {{ $errors->has('password_lama') ? 'border-red-400 bg-red-50' : 'border-stone-300 focus:border-[#6B1124] focus:ring-1 focus:ring-[#6B1124]/30' }}">
                @error('password_lama')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-stone-600">
                    Password Baru
                </label>
                <input type="password" name="password" required
                       placeholder="Min. 8 karakter"
                       class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none transition
                              {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-stone-300 focus:border-[#6B1124] focus:ring-1 focus:ring-[#6B1124]/30' }}">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-stone-600">
                    Konfirmasi Password Baru
                </label>
                <input type="password" name="password_confirmation" required
                       placeholder="Ulangi password baru"
                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-[#6B1124] focus:outline-none focus:ring-1 focus:ring-[#6B1124]/30 transition">
            </div>

            <button type="submit"
                    class="w-full rounded-lg bg-[#6B1124] py-2.5 text-sm font-semibold text-white hover:bg-[#4A0012] transition">
                Ganti Password
            </button>
        </form>
    </div>

</div>

<script>
(function () {
    var input = document.getElementById('foto');
    if (!input) return;

    input.addEventListener('change', function () {
        var file = input.files && input.files[0];
        if (!file) return;

        var img = document.getElementById('preview-img');
        var placeholder = document.getElementById('preview-placeholder');
        if (!img) return;

        img.src = URL.createObjectURL(file);
        img.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');
    });
})();
</script>
@endsection