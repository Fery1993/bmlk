<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query()->orderBy('nama');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('npwp', 'like', "%{$search}%");
            });
        }

        $vendors = $query->paginate(20)->withQueryString();

        return view('master.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('master.vendors.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Vendor::create($data);

        return redirect()->route('master.vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['bills' => fn ($q) => $q->latest()->take(10)]);

        return view('master.vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        return view('master.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $data = $this->validated($request, $vendor->id);

        $vendor->update($data);

        return redirect()->route('master.vendors.index')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        if ($vendor->bills()->exists()) {
            return back()->with('error', 'Vendor ini sudah punya tagihan dan tidak bisa dihapus.');
        }

        $vendor->delete();

        return back()->with('success', 'Vendor berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'kode' => ['required', 'string', 'max:30', Rule::unique('vendors', 'kode')->ignore($ignoreId)],
            'nama' => ['required', 'string', 'max:150'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'alamat' => ['nullable', 'string'],
            'npwp' => ['nullable', 'string', 'max:30'],
        ]);
    }
}
