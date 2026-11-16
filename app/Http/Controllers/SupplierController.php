<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Suppliers;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    //
    public function index(Request $request)
    {
        $suppliersQuery = Suppliers::query()->withCount('procurements');

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $suppliersQuery->where(function ($query) use ($q) {
                $query
                    ->where('pic_name', 'like', "%{$q}%")
                    ->orWhere('store_name', 'like', "%{$q}%")
                    ->orWhere('product_category', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            });
        }

        $category = $request->queryTrimmed('category');
        if ($category !== '') {
            $suppliersQuery->where('product_category', $category);
        }

        $sort = $request->queryEnum('sort', ['id_desc', 'id_asc', 'pic_asc', 'pic_desc', 'store_asc', 'store_desc', 'category_asc', 'category_desc']);
        if ($sort === 'id_asc') {
            $suppliersQuery->orderBy('id');
        } elseif ($sort === 'pic_asc') {
            $suppliersQuery->orderBy('pic_name');
        } elseif ($sort === 'pic_desc') {
            $suppliersQuery->orderByDesc('pic_name');
        } elseif ($sort === 'store_asc') {
            $suppliersQuery->orderBy('store_name');
        } elseif ($sort === 'store_desc') {
            $suppliersQuery->orderByDesc('store_name');
        } elseif ($sort === 'category_asc') {
            $suppliersQuery->orderBy('product_category');
        } elseif ($sort === 'category_desc') {
            $suppliersQuery->orderByDesc('product_category');
        } else {
            $suppliersQuery->orderByDesc('id');
        }

        $suppliers = $suppliersQuery->paginate(10)->withQueryString();
        $categories = Suppliers::query()
            ->select('product_category')
            ->whereNotNull('product_category')
            ->where('product_category', '!=', '')
            ->distinct()
            ->orderBy('product_category')
            ->pluck('product_category')
            ->values();

        return view('Page.Admin.Supplier.Index', compact('suppliers', 'categories'));
    }

    public function create()
    {
        return view('Page.Admin.Supplier.Create');
    }

    public function store(SupplierRequest $request)
    {
        Suppliers::create($request->validated());

        return redirect()
            ->route('supplier')
            ->with('success', 'Supplier created successfully');
    }

    public function edit(Suppliers $supplier)
    {
        return view('Page.Admin.Supplier.Edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Suppliers $supplier)
    {
        $supplier->update($request->validated());

        return redirect()
            ->route('supplier')
            ->with('success', 'Supplier updated successfully');
    }

    /** Pemasok yang masih memiliki pengadaan tidak dapat dihapus agar riwayat pengadaan tetap utuh. */
    public function destroy(Suppliers $supplier)
    {
        if ($supplier->procurements()->exists()) {
            return back()->with('error', 'Pemasok masih memiliki pengadaan bahan sehingga tidak dapat dihapus.');
        }

        $supplier->delete();

        return redirect()
            ->route('supplier')
            ->with('success', 'Pemasok berhasil dihapus.');
    }
}
