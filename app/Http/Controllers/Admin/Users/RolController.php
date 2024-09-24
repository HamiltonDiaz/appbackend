<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;


class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($rows=10)
    {
        $users = Role::paginate($rows, ['*']);
        return $users;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $messages = [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.min' => 'El nombre del rol debe tener al menos 5 caracteres.',
            'name.max' => 'El nombre  del rol no puede exceder los 20 caracteres.',       
            'name.unique' => 'El nombre del rol ya ha sido registrado.',

            'description.required' => 'Descripci[on  es obligatoria.',
            'permissions.required' => 'Permisos es obligatorio.',
            'permissions.array' => 'Formato de permisos incorrecto',
            'permissions.*.integer' => 'El permiso debe ser un número entero.',            
            'permissions.*.distinct' => 'No se pueden seleccionar permisos duplicados.',

        ];

        $validator = Validator::make($data, [
            'name' => 'required|string|min:5|max:20|unique:roles', // nombre usuario
            'description' => 'required|string|min:5|max:255', // nombre usuario
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id|distinct', // Valida que cada elemento del array sea un entero y exista en la tabla de permisos        
        ],$messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);
        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Rol registrado exitosamente.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function asingRole(Request $request)
    {
        $user= new User();
        $user=$user::find ($request->id_user);
        if (!$user) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Registro no encontrado.',
            ]);
        }

        $data = $request->all();
        $messages = [
            'id_user.required' => 'Usuario es obligatorio.',

            'roles.required' => 'Permisos es obligatorio.',
            'roles.array' => 'Formato de roles incorrecto',        
            'roles.*.integer' => 'Cada rol debe ser un número entero.',
            'roles.*.exists' => 'El rol seleccionado no es válido.',
            'roles.*.distinct' => 'No se pueden seleccionar roles duplicados.',
        ];

        $validator = Validator::make($data, [
            'id_user' => 'required|integer',
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,id|distinct',
        ],$messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }



        $user->roles()->sync($request->roles);
        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Rol asignado exitosamente.',
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
