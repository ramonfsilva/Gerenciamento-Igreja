<?php

namespace App\Livewire;

use App\Models\Church;
use App\Models\Member;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        if (isMaster()) {
            return view("livewire.dashboard", [
                "totalChurches" => Church::count(),
                "totalUsers" => User::count(),
                "activeChurches" => Church::where("active", true)->count(),
                "totalMembers" => Member::count(),
            ])->layout("layouts.app");
        }

        $church = currentChurch();
        $churchId = $church?->id;

        return view("livewire.dashboard", [
            "church" => $church,
            "totalUsers" => $churchId ? User::where("church_id", $churchId)->count() : 0,
            "totalMembers" => $churchId ? Member::where("church_id", $churchId)->count() : 0,
            "activeMembers" => $churchId ? Member::where("church_id", $churchId)->where("active", true)->count() : 0,
            "visitors" => $churchId ? Member::where("church_id", $churchId)->where("status", "Visitante")->count() : 0,
            "birthdayMembers" => $churchId
                ? Member::where("church_id", $churchId)
                    ->whereMonth("birth_date", now()->month)
                    ->whereNotNull("birth_date")
                    ->orderBy("birth_date")
                    ->get()
                : collect(),
        ])->layout("layouts.app");
    }
}
