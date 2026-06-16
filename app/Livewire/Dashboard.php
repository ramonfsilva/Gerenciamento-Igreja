<?php

namespace App\Livewire;

use App\Models\Church;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        if (isMaster()) {
            $totalChurches = Church::count();
            $totalUsers = User::count();
            $activeChurches = Church::where("active", true)->count();

            return view("livewire.dashboard", [
                "totalChurches" => $totalChurches,
                "totalUsers" => $totalUsers,
                "activeChurches" => $activeChurches,
            ])->layout("layouts.app");
        }

        $church = currentChurch();
        $totalUsers = $church
            ? User::where("church_id", $church->id)->count()
            : 0;

        return view("livewire.dashboard", [
            "church" => $church,
            "totalUsers" => $totalUsers,
        ])->layout("layouts.app");
    }
}
