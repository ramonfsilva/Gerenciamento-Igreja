<?php

namespace App\Livewire;

use App\Models\Member;
use Livewire\Component;

class MemberReports extends Component
{
    public $filterChurchId = "";

    public function render()
    {
        $query = Member::query();

        if (!isMaster()) {
            $query->where("church_id", auth()->user()->church_id);
        } elseif ($this->filterChurchId) {
            $query->where("church_id", $this->filterChurchId);
        }

        $total = (clone $query)->count();
        $active = (clone $query)->where("active", true)->count();

        $byStatus = [
            "Ativo" => (clone $query)->where("status", "Ativo")->count(),
            "Congregado" => (clone $query)->where("status", "Congregado")->count(),
            "Visitante" => (clone $query)->where("status", "Visitante")->count(),
            "Afastado" => (clone $query)->where("status", "Afastado")->count(),
            "Transferido" => (clone $query)->where("status", "Transferido")->count(),
        ];

        $birthdayQuery = clone $query;
        $birthdays = $birthdayQuery->whereMonth("birth_date", now()->month)
            ->whereNotNull("birth_date")
            ->orderByRaw("CAST(strftime('%d', birth_date) AS INTEGER)")
            ->get();

        $churches = isMaster() ? \App\Models\Church::where("active", true)->get() : collect();

        return view("livewire.reports.members", [
            "total" => $total,
            "active" => $active,
            "byStatus" => $byStatus,
            "birthdays" => $birthdays,
            "churches" => $churches,
        ])->layout("layouts.app");
    }
}
