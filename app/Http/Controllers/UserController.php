<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'cpf' => 'required|cpf|unique:users,cpf',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:2000',
            'is_admin' => 'boolean',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'cpf' => $validated['cpf'],
            'phone' => $validated['phone'] ?? '',
            'bio' => $validated['bio'] ?? null,
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('admin.users.index')->with('message', 'Usuário criado com sucesso!');
    }

    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'cpf' => 'required|cpf|unique:users,cpf,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:2000',
            'public_slug' => 'nullable|string|max:100|unique:users,public_slug,'.$user->id,
            'is_admin' => 'boolean',
        ]);

        $data = collect($validated)->except(['password', 'password_confirmation'])->toArray();
        $data['is_admin'] = $request->boolean('is_admin');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('message', 'Usuário atualizado!');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Não é possível excluir seu próprio usuário.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('message', 'Usuário removido.');
    }
}
