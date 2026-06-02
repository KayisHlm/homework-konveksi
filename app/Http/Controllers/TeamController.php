<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    //
    public function index(Request $request)
    {
        $teamMembers = User::query()
            ->whereNotNull('role')
            ->whereNotNull('whatsapp');

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $teamMembers->where(function ($query) use ($q) {
                $query
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('role', 'like', "%{$q}%");
            });
        }

        $teamMembers = $teamMembers
            ->orderBy('name')
            ->get(['name', 'role', 'whatsapp'])
            ->map(fn (User $member) => [
                'name' => (string) $member->getAttribute('name'),
                'role' => (string) $member->getAttribute('role'),
                'whatsapp' => (string) $member->getAttribute('whatsapp'),
            ]);

        return view('Page.Admin.Team.Index', compact('teamMembers'));
    }

    public function create()
    {
        return view('Page.Admin.Team.Create');
    }
}
