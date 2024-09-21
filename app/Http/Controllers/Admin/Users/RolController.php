<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
        $user->roles()->sync($request->roles);
        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Rol asignado exitosamente.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
