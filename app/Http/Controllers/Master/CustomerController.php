<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()->orderBy('nama');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('npwp', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(20)->withQueryString();

        return view('master.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('master.customers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Customer::create($data);

        return redirect()->route('master.customers.index')
            ->with('success', 'Customer berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['invoices' => fn ($q) => $q->latest()->take(10)]);

        return view('master.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('master.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $this->validated($request, $customer->id);

        $customer->update($data);

        return redirect()->route('master.customers.index')
            ->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->invoices()->exists()) {
            return back()->with('error', 'Customer ini sudah punya invoice dan tidak bisa dihapus.');
        }

        $customer->delete();

        return back()->with('success', 'Customer berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'kode' => ['required', 'string', 'max:30', Rule::unique('customers', 'kode')->ignore($ignoreId)],
            'nama' => ['required', 'string', 'max:150'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'alamat' => ['nullable', 'string'],
            'npwp' => ['nullable', 'string', 'max:30'],
        ]);
    }
}
