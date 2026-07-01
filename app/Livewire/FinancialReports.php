<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use Livewire\Component;

class FinancialReports extends Component
{
    public $filterMonth = "";
    public $filterYear = "";
    public $filterChurchId = "";

    public function mount()
    {
        $this->filterMonth = now()->month;
        $this->filterYear = now()->year;
    }

    public function render()
    {
        $churchId = null;
        if (!isMaster()) {
            $churchId = auth()->user()->church_id;
        } elseif ($this->filterChurchId) {
            $churchId = $this->filterChurchId;
        }

        $query = fn($q) => $q
            ->when($churchId, fn($q) => $q->where("church_id", $churchId))
            ->when($this->filterMonth, fn($q) => $q->whereMonth("date", $this->filterMonth))
            ->when($this->filterYear, fn($q) => $q->whereYear("date", $this->filterYear));

        $categories = Category::where("active", true)->get();

        $incomesByCategory = (clone $query)(Income::query())
            ->selectRaw("category_id, SUM(amount) as total")
            ->groupBy("category_id")
            ->pluck("total", "category_id");

        $expensesByCategory = (clone $query)(Expense::query())
            ->selectRaw("category_id, SUM(amount) as total")
            ->groupBy("category_id")
            ->pluck("total", "category_id");

        $reportRows = $categories->map(fn($cat) => [
            "category" => $cat->name,
            "type" => $cat->type,
            "income" => $incomesByCategory->get($cat->id, 0),
            "expense" => $expensesByCategory->get($cat->id, 0),
            "balance" => $incomesByCategory->get($cat->id, 0) - $expensesByCategory->get($cat->id, 0),
        ]);

        $totals = [
            "income" => $reportRows->sum("income"),
            "expense" => $reportRows->sum("expense"),
            "balance" => $reportRows->sum("balance"),
        ];

        $churches = isMaster() ? \App\Models\Church::where("active", true)->get() : collect();

        return view("livewire.financial.reports", [
            "reportRows" => $reportRows,
            "totals" => $totals,
            "churches" => $churches,
        ])->layout("layouts.app");
    }
}
