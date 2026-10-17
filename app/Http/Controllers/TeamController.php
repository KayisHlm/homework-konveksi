<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $teamMembers = User::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $teamMembers->where(function ($query) use ($q) {
                $query
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('role', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('whatsapp', 'like', "%{$q}%");
            });
        }

        $teamMembers = $teamMembers
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'whatsapp'])
            ->map(fn (User $member) => [
                'id' => (int) $member->getAttribute('id'),
                'name' => (string) $member->getAttribute('name'),
                'email' => (string) $member->getAttribute('email'),
                'role' => (string) ($member->getAttribute('role') ?: 'admin'),
                'whatsapp' => (string) ($member->getAttribute('whatsapp') ?: '-'),
            ]);

        return view('Page.Admin.Team.Index', compact('teamMembers'));
    }

    public function create(): View
    {
        return view('Page.Admin.Team.Create', [
            'member' => new User(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMember($request);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'whatsapp' => $validated['whatsapp'] ?? null,
            'role' => $validated['role'] ?? 'admin',
        ]);

        return redirect()->route('team')->with('success', 'Anggota tim berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('Page.Admin.Team.Edit', [
            'member' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validateMember($request, $user);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'role' => $validated['role'] ?? 'admin',
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        return redirect()->route('team')->with('success', 'Data anggota tim berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('team')->with('success', 'Anggota tim berhasil dihapus.');
    }

    protected function validateMember(Request $request, ?User $user = null): array
    {
        $passwordRules = $user
            ? ['nullable', 'string', 'min:8']
            : ['required', 'string', 'min:8'];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => $passwordRules,
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'role' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
