<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    /**
     * Mostrar lista de usuarios
     */
    public function index()
    {
        $users = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('administracion.usuarios.index', compact('users'));
    }

    /**
     * Mostrar formulario para crear usuario
     */
    public function create()
    {
        $roles = Role::all();
        return view('administracion.usuarios.create', compact('roles'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : null,
        ]);

        // Asignar roles
        if (!empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()
            ->route('administracion.usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Mostrar un usuario específico
     */
    public function show(User $user)
    {
        $user->load('roles', 'permissions');
        return view('administracion.usuarios.show', compact('user'));
    }

    /**
     * Mostrar formulario para editar usuario
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');

        return view('administracion.usuarios.edit', compact('user', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Actualizar contraseña solo si se proporciona
        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Sincronizar roles
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        } else {
            $user->syncRoles([]);
        }

        return redirect()
            ->route('administracion.usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        // Prevenir que el usuario se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()
            ->route('administracion.usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Activar/Desactivar usuario
     */
    public function toggleStatus(User $user)
    {
        // Prevenir que el usuario se desactive a sí mismo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update([
            'is_active' => !($user->is_active ?? true),
        ]);

        $status = $user->is_active ? 'activado' : 'desactivado';

        return back()->with('success', "Usuario {$status} exitosamente.");
    }
}
