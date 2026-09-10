<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::query()->orderBy('nama');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        $karyawan = $query->paginate(20)->withQueryString();

        return view('master.karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        return view('master.karyawan.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Karyawan::create($data);

        return redirect()->route('master.karyawan.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Karyawan $karyawan)
    {
        $karyawan->load(['operationalFees' => fn ($q) => $q->latest()->take(10)]);

        return view('master.karyawan.show', compact('karyawan'));
    }

    public function edit(Karyawan $karyawan)
    {
        return view('master.karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $data = $this->validated($request, $karyawan->id);

        $karyawan->update($data);

        return redirect()->route('master.karyawan.index')
            ->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        if ($karyawan->operationalFees()->exists()) {
            return back()->with('error', 'Karyawan ini sudah punya riwayat fee dan tidak bisa dihapus.');
        }

        $karyawan->delete();

        return back()->with('success', 'Karyawan berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'nik' => ['required', 'string', 'max:30', Rule::unique('karyawan', 'nik')->ignore($ignoreId)],
            'nama' => ['required', 'string', 'max:150'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'rekening_bank' => ['nullable', 'string', 'max:100'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
