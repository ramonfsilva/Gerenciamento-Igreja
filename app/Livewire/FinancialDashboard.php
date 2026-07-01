<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Income;
use Livewire\Component;

class FinancialDashboard extends Component
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
        $incomeQuery = Income::query();
        $expenseQuery = Expense::query();

        if (!isMaster()) {
            $incomeQuery->where("church_id", auth()->user()->church_id);
            $expenseQuery->where("church_id", auth()->user()->church_id);
        } elseif ($this->filterChurchId) {
            $incomeQuery->where("church_id", $this->filterChurchId);
            $expenseQuery->where("church_id", $this->filterChurchId);
        }

        $incomeTotal = (clone $incomeQuery)->sum("amount");
        $expenseTotal = (clone $expenseQuery)->sum("amount");

        if ($this->filterMonth) {
            $incomeQuery->whereMonth("date", $this->filterMonth);
            $expenseQuery->whereMonth("date", $this->filterMonth);
        }
        if ($this->filterYear) {
            $incomeQuery->whereYear("date", $this->filterYear);
            $expenseQuery->whereYear("date", $this->filterYear);
        }

        $monthIncome = (clone $incomeQuery)->sum("amount");
        $monthExpense = (clone $expenseQuery)->sum("amount");
        $monthBalance = $monthIncome - $monthExpense;

        $lastIncomes = (clone $incomeQuery)->with("category")->orderByDesc("date")->take(5)->get();
        $lastExpenses = (clone $expenseQuery)->with("category")->orderByDesc("date")->take(5)->get();

        $churches = isMaster() ? \App\Models\Church::where("active", true)->get() : collect();

        return view("livewire.financial.dashboard", [
            "monthIncome" => $monthIncome,
            "monthExpense" => $monthExpense,
            "monthBalance" => $monthBalance,
            "incomeTotal" => $incomeTotal,
            "expenseTotal" => $expenseTotal,
            "lastIncomes" => $lastIncomes,
            "lastExpenses" => $lastExpenses,
            "churches" => $churches,
        ])->layout("layouts.app");
    }
}
