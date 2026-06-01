<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeamController extends Controller
{
    //
    public function index(Request $request)
    {
        $teamMembers = collect([
            ['name' => 'Willie T. Anderson', 'role' => 'HR Manager', 'whatsapp' => '628111111111'],
            ['name' => 'Anna M. Hines', 'role' => 'Sales Lead', 'whatsapp' => '628122222222'],
            ['name' => 'Judith H. Fritsche', 'role' => 'Operations', 'whatsapp' => '628133333333'],
            ['name' => 'Peter T. Smith', 'role' => 'Designer', 'whatsapp' => '628144444444'],
            ['name' => 'Emmanuel J. Delcid', 'role' => 'Production', 'whatsapp' => '628155555555'],
            ['name' => 'William J. Cook', 'role' => 'Finance', 'whatsapp' => '628166666666'],
            ['name' => 'Vanessa R. Davis', 'role' => 'Marketing', 'whatsapp' => '628177777777'],
            ['name' => 'Candice F. Gilmore', 'role' => 'Customer Support', 'whatsapp' => '628188888888'],
        ]);

        $q = $request->queryTrimmed('q');
        if ($q !== '') {
            $qLower = mb_strtolower($q);
            $teamMembers = $teamMembers->filter(function (array $member) use ($qLower) {
                return str_contains(mb_strtolower($member['name']), $qLower) || str_contains(mb_strtolower($member['role']), $qLower);
            })->values();
        }

        return view('Page.Admin.Team.Index', compact('teamMembers'));
    }

    public function create()
    {
        return view('Page.Admin.Team.Create');
    }
}
