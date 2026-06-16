<?php

use App\Models\Church;
use App\Models\User;

if (!function_exists("isMaster")) {
    function isMaster(): bool
    {
        return auth()->check() && auth()->user()->hasRole("Master");
    }
}

if (!function_exists("currentChurch")) {
    function currentChurch(): ?Church
    {
        if (!auth()->check() || !auth()->user()->church_id) {
            return null;
        }
        return auth()->user()->church;
    }
}
