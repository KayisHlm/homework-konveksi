<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcurementRequest;
use App\Models\Orders;
use App\Models\Procurement;
use App\Models\Suppliers;
use Illuminate\Http\RedirectResponse;
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
                        $orderQuery->where('order_number', 'like', "%{$q}%");
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

    public function store(ProcurementRequest $request): RedirectResponse
    {
        $procurement = Procurement::create([
            ...$request->safe()->except('received_at'),
            'status' => Procurement::STATUS_DIPESAN,
        ]);

        $this->markOrderWaitingForMaterial($procurement);

        return redirect()->route('procurement')->with('success', 'Pengadaan berhasil ditambahkan.');
    }

    public function edit(Procurement $procurement): View
    {
        return view('Page.Admin.Procurement.Edit', [
            'procurement' => $procurement,
            ...$this->formOptions(),
        ]);
    }

    public function update(ProcurementRequest $request, Procurement $procurement): RedirectResponse
    {
        $data = $request->validated();
        if ($procurement->status !== Procurement::STATUS_DITERIMA || empty($data['received_at'])) {
            unset($data['received_at']);
        }

        $procurement->update($data);
        $this->markOrderWaitingForMaterial($procurement);

        return redirect()->route('procurement')->with('success', 'Pengadaan berhasil diperbarui.');
    }

    public function destroy(Procurement $procurement): RedirectResponse
    {
        $procurement->delete();

        return redirect()->route('procurement')->with('success', 'Pengadaan berhasil dihapus.');
    }

    public function receive(Request $request, Procurement $procurement): RedirectResponse
    {
        if ($procurement->status !== Procurement::STATUS_DIPESAN) {
            return back()->with('error', 'Hanya pengadaan berstatus dipesan yang dapat dikonfirmasi.');
        }

        $validated = $request->validate([
            'received_at' => 'nullable|date|after_or_equal:'.$procurement->ordered_at->toDateString(),
        ]);

        $procurement->update([
            'status' => Procurement::STATUS_DITERIMA,
            'received_at' => $validated['received_at'] ?? now()->toDateString(),
        ]);

        return back()->with('success', 'Penerimaan bahan berhasil dikonfirmasi.');
    }

    public function cancel(Procurement $procurement): RedirectResponse
    {
        if ($procurement->status !== Procurement::STATUS_DIPESAN) {
            return back()->with('error', 'Hanya pengadaan berstatus dipesan yang dapat dibatalkan.');
        }

        $procurement->update(['status' => Procurement::STATUS_DIBATALKAN]);

        return back()->with('success', 'Pengadaan dibatalkan dan pengeluarannya dihapus.');
    }

    /** Pesanan baru menunggu bahan; status yang sudah lebih maju tidak dimundurkan. */
    protected function markOrderWaitingForMaterial(Procurement $procurement): void
    {
        $procurement->order()
            ->where('production_status', Orders::PRODUCTION_STATUS_PESANAN_DITERIMA)
            ->update(['production_status' => Orders::PRODUCTION_STATUS_MENUNGGU_BAHAN]);
    }

    protected function formOptions(): array
    {
        return [
            'orders' => Orders::query()->with(['customer', 'items'])->orderByDesc('created_at')->get(),
            'suppliers' => Suppliers::query()->orderBy('store_name')->get(),
        ];
    }
}
