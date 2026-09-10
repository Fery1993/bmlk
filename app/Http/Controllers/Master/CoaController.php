<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CoaController extends Controller
{
    public function index(Request $request)
    {
        $query = ChartOfAccount::with('parent')->orderBy('kode_akun');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_akun', 'like', "%{$search}%")
                  ->orWhere('nama_akun', 'like', "%{$search}%");
            });
        }

        if ($kelompok = $request->get('kelompok')) {
            $query->where('kelompok', $kelompok);
        }

        $coa = $query->paginate(20)->withQueryString();

        return view('master.coa.index', compact('coa'));
    }

    public function create()
    {
        $parents = ChartOfAccount::orderBy('kode_akun')->get();

        return view('master.coa.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        ChartOfAccount::create($data);

        return redirect()->route('master.coa.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    public function show(ChartOfAccount $coa)
    {
        $coa->load('parent', 'children');

        return view('master.coa.show', compact('coa'));
    }

    public function edit(ChartOfAccount $coa)
    {
        $parents = ChartOfAccount::where('id', '!=', $coa->id)->orderBy('kode_akun')->get();

        return view('master.coa.edit', compact('coa', 'parents'));
    }

    public function update(Request $request, ChartOfAccount $coa)
    {
        $data = $this->validated($request, $coa->id);

        $coa->update($data);

        return redirect()->route('master.coa.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(ChartOfAccount $coa)
    {
        if ($coa->children()->exists()) {
            return back()->with('error', 'Akun ini punya sub-akun, hapus atau pindahkan sub-akun terlebih dahulu.');
        }

        if ($coa->journalItems()->exists()) {
            return back()->with('error', 'Akun ini sudah dipakai di jurnal dan tidak bisa dihapus.');
        }

        $coa->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'kode_akun' => ['required', 'string', 'max:20', Rule::unique('chart_of_accounts', 'kode_akun')->ignore($ignoreId)],
            'nama_akun' => ['required', 'string', 'max:150'],
            'kelompok' => ['required', 'in:aset,kewajiban,ekuitas,pendapatan,beban'],
            'posisi_normal' => ['required', 'in:debit,kredit'],
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
