<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcurementRequest;
use App\Models\Orders;
use App\Models\Procurement;
use App\Models\Suppliers;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcurementController extends Controller
{
    public function index(Request $request): View
    {
        $procurementsQuery = Procurement::query()->with(['order.customer', 'supplier']);

        $status = $request->queryEnum('status', Procurement::statuses());
        if ($status !== null) {
            $procurementsQuery->where('status', $status);
        }

        $supplierId = $request->query('supplier_id');
        if (is_numeric($supplierId)) {
            $procurementsQuery->where('supplier_id', (int) $supplierId);
        }

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $procurementsQuery->where(function ($query) use ($q) {
                $query
                    ->where('material_name', 'like', "%{$q}%")
                    ->orWhereHas('order', function ($orderQuery) use ($q) {
                        $orderQuery
                            ->where('order_number', 'like', "%{$q}%")
                            ->orWhere('product_name', 'like', "%{$q}%");
                    })
                    ->orWhereHas('supplier', function ($supplierQuery) use ($q) {
                        $supplierQuery
                            ->where('store_name', 'like', "%{$q}%")
                            ->orWhere('pic_name', 'like', "%{$q}%");
                    });
            });
        }

        $summarySource = Procurement::query()->with('supplier')->whereNotNull('received_at')->get();
        $avgWaitBySupplier = $summarySource
            ->groupBy('supplier_id')
            ->map(function ($items) {
                $first = $items->first();
                $averageDays = round($items->avg(fn (Procurement $procurement) => $procurement->ordered_at?->diffInDays($procurement->received_at) ?? 0), 1);

                return [
                    'supplier_name' => $first?->supplier?->store_name ?? 'Tanpa pemasok',
                    'average_days' => $averageDays,
                ];
            })
            ->sortBy('supplier_name')
            ->values();

        return view('Page.Admin.Procurement.Index', [
            'procurements' => $procurementsQuery->orderByDesc('ordered_at')->paginate(10)->withQueryString(),
            'statuses' => Procurement::statuses(),
            'suppliers' => Suppliers::query()->orderBy('store_name')->get(),
            'avgWaitBySupplier' => $avgWaitBySupplier,
        ]);
    }

    public function create(Request $request): View
    {
        return view('Page.Admin.Procurement.Create', [
            'procurement' => new Procurement([
                'order_id' => $request->query('order_id'),
                'ordered_at' => now()->toDateString(),
                'status' => Procurement::STATUS_DIPESAN,
            ]),
            ...$this->formOptions(),
        ]);
    }

    public function store(ProcurementRequest $request)
    {
        $procurement = Procurement::create($request->validated());

        $procurement->order()->update([
            'production_status' => Orders::PRODUCTION_STATUS_MENUNGGU_BAHAN,
        ]);

        return redirect()->route('procurement')->with('success', 'Pengadaan berhasil ditambahkan.');
    }

    public function edit(Procurement $procurement): View
    {
        return view('Page.Admin.Procurement.Edit', [
            'procurement' => $procurement,
            ...$this->formOptions(),
        ]);
    }

    public function update(ProcurementRequest $request, Procurement $procurement)
    {
        $procurement->update($request->validated());

        return redirect()->route('procurement')->with('success', 'Pengadaan berhasil diperbarui.');
    }

    public function destroy(Procurement $procurement)
    {
        $procurement->delete();

        return redirect()->route('procurement')->with('success', 'Pengadaan berhasil dihapus.');
    }

    public function receive(Procurement $procurement)
    {
        $procurement->update([
            'status' => Procurement::STATUS_DITERIMA,
            'received_at' => $procurement->received_at ?: now()->toDateString(),
        ]);

        return back()->with('success', 'Status pengadaan berhasil diubah menjadi diterima.');
    }

    protected function formOptions(): array
    {
        return [
            'orders' => Orders::query()->with('customer')->orderByDesc('created_at')->get(),
            'suppliers' => Suppliers::query()->orderBy('store_name')->get(),
            'statuses' => Procurement::statuses(),
        ];
    }
}
