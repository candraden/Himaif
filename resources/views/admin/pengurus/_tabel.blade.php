{{-- Tampilan mobile: kartu --}}
<div class="divide-y divide-stone-100 md:hidden">
    @forelse ($daftar as $p)
        <div class="flex items-start gap-3 px-4 py-3">
            <span class="mt-1 w-6 shrink-0 text-center font-mono text-xs text-stone-400">{{ $p->urutan }}</span>

            @if ($p->anggota?->foto)
                <img src="{{ asset('storage/' . $p->anggota->foto) }}" alt="" class="h-10 w-10 shrink-0 rounded-full object-cover">
            @else
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-maroon-soft text-sm font-semibold text-maroon">{{ strtoupper(mb_substr($p->anggota?->nama ?? '?', 0, 1)) }}</div>
            @endif

            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-stone-800">{{ $p->anggota?->nama ?? '(anggota dihapus)' }}</p>
                <p class="font-mono text-xs text-stone-500">{{ $p->anggota?->nim }}</p>
                <span class="mt-1 inline-block whitespace-nowrap rounded-full bg-maroon-soft px-2.5 py-1 text-xs font-semibold text-maroon">{{ $p->jabatan }}</span>
            </div>

            <div class="flex shrink-0 flex-col items-end gap-1 text-xs font-semibold">
                <a href="{{ route('admin.pengurus.edit', $p) }}" class="text-maroon hover:underline">Edit</a>
                <form method="POST" action="{{ route('admin.pengurus.destroy', $p) }}"
                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus ' . ($p->anggota?->nama ?? 'pengurus ini') . ' dari struktur?') }})">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-700 hover:underline">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <p class="px-4 py-6 text-center text-sm text-stone-400">Belum ada pengurus.</p>
    @endforelse
</div>

{{-- Tampilan tablet/desktop: tabel --}}
<div class="hidden overflow-x-auto md:block">
    <table class="w-full text-left text-sm">
        <thead class="text-xs uppercase tracking-wide text-stone-400">
            <tr>
                <th class="px-5 py-2 text-center">Urutan</th>
                <th class="px-5 py-2">Nama</th>
                <th class="px-5 py-2">NIM</th>
                <th class="px-5 py-2">Jabatan</th>
                <th class="px-5 py-2 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @forelse ($daftar as $p)
                <tr class="hover:bg-stone-50">
                    <td class="px-5 py-3 text-center font-mono text-xs text-stone-500">{{ $p->urutan }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if ($p->anggota?->foto)
                                <img src="{{ asset('storage/' . $p->anggota->foto) }}" alt="" class="h-8 w-8 rounded-full object-cover">
                            @else
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-maroon-soft text-xs font-semibold text-maroon">{{ strtoupper(mb_substr($p->anggota?->nama ?? '?', 0, 1)) }}</div>
                            @endif
                            <span class="font-medium">{{ $p->anggota?->nama ?? '(anggota dihapus)' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 font-mono text-xs text-stone-500">{{ $p->anggota?->nim }}</td>
                    <td class="px-5 py-3">
                        <span class="whitespace-nowrap rounded-full bg-maroon-soft px-2.5 py-1 text-xs font-semibold text-maroon">{{ $p->jabatan }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3 text-xs font-semibold">
                            <a href="{{ route('admin.pengurus.edit', $p) }}" class="text-maroon hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.pengurus.destroy', $p) }}"
                                  onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus ' . ($p->anggota?->nama ?? 'pengurus ini') . ' dari struktur?') }})">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-700 hover:underline">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-6 text-center text-sm text-stone-400">Belum ada pengurus.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>