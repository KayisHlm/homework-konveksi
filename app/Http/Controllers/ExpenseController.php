<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $expensesQuery = Expense::query()->with(['order.customer', 'procurement.supplier']);

        $month = $request->queryTrimmed('month');
        if ($month !== '' && preg_match('/^\d{4}-\d{2}$/', $month) === 1) {
            [$year, $monthNumber] = explode('-', $month);
            $expensesQuery
                ->whereYear('spent_at', (int) $year)
                ->whereMonth('spent_at', (int) $monthNumber);
        }

        $category = $request->queryEnum('category', Expense::categories());
        if ($category !== null) {
            $expensesQuery->where('category', $category);
        }

        $orderId = $request->query('order_id');
        if (is_numeric($orderId)) {
            $expensesQuery->where('order_id', (int) $orderId);
        }

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $expensesQuery->where(function ($query) use ($q) {
                $query
                    ->where('description', 'like', "%{$q}%")
                    ->orWhereHas('order', function ($orderQuery) use ($q) {
                        $orderQuery
                            ->where('order_number', 'like', "%{$q}%")
                            ->orWhere('product_name', 'like', "%{$q}%");
                    });
            });
        }

        $summarySource = (clone $expensesQuery)->get();

        return view('Page.Admin.Expense.Index', [
            'expenses' => $expensesQuery->orderByDesc('spent_at')->paginate(10)->withQueryString(),
            'categories' => Expense::categories(),
            'orders' => Orders::query()->with('customer')->orderByDesc('created_at')->get(),
            'linkedTotals' => $summarySource
                ->whereNotNull('order_id')
                ->groupBy('category')
                ->map(fn ($items) => (float) $items->sum('amount')),
            'generalTotals' => $summarySource
                ->whereNull('order_id')
                ->groupBy('category')
                ->map(fn ($items) => (float) $items->sum('amount')),
        ]);
    }

    public function create(Request $request): View
    {
        return view('Page.Admin.Expense.Create', [
            'expense' => new Expense([
                'order_id' => $request->query('order_id'),
                'spent_at' => now()->toDateString(),
            ]),
            ...$this->formOptions(),
        ]);
    }

    public function store(ExpenseRequest $request)
    {
        Expense::create($request->validated());

        return redirect()->route('expense')->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function edit(Expense $expense): View|\Illuminate\Http\RedirectResponse
    {
        if ($expense->procurement_id) {
            return redirect()->route('expense')->with('error', 'Pengeluaran dari pengadaan hanya bisa diubah dari modul pengadaan.');
        }

        return view('Page.Admin.Expense.Edit', [
            'expense' => $expense,
            ...$this->formOptions(),
        ]);
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        if ($expense->procurement_id) {
            return redirect()->route('expense')->with('error', 'Pengeluaran dari pengadaan hanya bisa diubah dari modul pengadaan.');
        }

        $expense->update($request->validated());

        return redirect()->route('expense')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->procurement_id) {
            return redirect()->route('expense')->with('error', 'Pengeluaran dari pengadaan hanya bisa dihapus dari modul pengadaan.');
        }

        $expense->delete();

        return redirect()->route('expense')->with('success', 'Pengeluaran berhasil dihapus.');
    }

    protected function formOptions(): array
    {
        return [
            'categories' => Expense::categories(),
            'orders' => Orders::query()->with('customer')->orderByDesc('created_at')->get(),
        ];
    }
}
