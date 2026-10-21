<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    private function writeAuditLog(
        Request $request,
        string $action,
        Expense $expense,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'module' => 'Expenses',
            'description' => $action . ' expense: '
                . $expense->title
                . ' (Amount: ' . $expense->amount . ')',
            'auditable_type' => Expense::class,
            'auditable_id' => $expense->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
            ],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $expensesQuery = Expense::with('user');

        if (!empty($validated['start_date'])) {
            $expensesQuery->whereDate(
                'expense_date',
                '>=',
                $validated['start_date']
            );
        }

        if (!empty($validated['end_date'])) {
            $expensesQuery->whereDate(
                'expense_date',
                '<=',
                $validated['end_date']
            );
        }

        if (!empty($validated['category'])) {
            $expensesQuery->where(
                'category',
                $validated['category']
            );
        }

        $totalExpenses = (clone $expensesQuery)->sum('amount');

        $expenses = $expensesQuery
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $categories = Expense::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('expenses.index', compact(
            'expenses',
            'totalExpenses',
            'categories'
        ));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'expense_date' => ['required', 'date_format:Y-m-d'],
            'payment_method' => [
                'required',
                Rule::in([
                    'Cash',
                    'M-Pesa',
                    'Bank Transfer',
                    'Card',
                    'Other',
                ]),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['user_id'] = $request->user()->id;

        $expense = Expense::create($validated);

        $this->writeAuditLog(
            $request,
            'Created',
            $expense,
            null,
            $expense->only([
                'title',
                'category',
                'amount',
                'expense_date',
                'payment_method',
                'description',
            ])
        );

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'expense_date' => ['required', 'date_format:Y-m-d'],
            'payment_method' => [
                'required',
                Rule::in([
                    'Cash',
                    'M-Pesa',
                    'Bank Transfer',
                    'Card',
                    'Other',
                ]),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $oldValues = $expense->only([
            'title',
            'category',
            'amount',
            'expense_date',
            'payment_method',
            'description',
        ]);

        $expense->update($validated);

        $this->writeAuditLog(
            $request,
            'Updated',
            $expense,
            $oldValues,
            $expense->only([
                'title',
                'category',
                'amount',
                'expense_date',
                'payment_method',
                'description',
            ])
        );

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Request $request, Expense $expense)
    {
        $oldValues = $expense->only([
            'title',
            'category',
            'amount',
            'expense_date',
            'payment_method',
            'description',
        ]);

        $this->writeAuditLog(
            $request,
            'Deleted',
            $expense,
            $oldValues,
            null
        );

        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
