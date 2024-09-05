<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;


class userController extends Controller
{
    public function userRegister(Request $request)
    {
        //roles 'Superadmin\nAdmin\nTutor\nAsistente\n',

        $data = $request->all();

        $messages = [
            'name.required' => 'El nombre de usuario es obligatorio.',
            'name.min' => 'El nombre de usuario debe tener al menos 5 caracteres.',
            'name.max' => 'El nombre de usuario no puede exceder los 20 caracteres.',
            
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder los 100 caracteres.',
            'email.unique' => 'El correo electrónico ya ha sido registrado.',
            
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 3 caracteres.',
            'password.max' => 'La contraseña no puede exceder los 100 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.min' => 'El primer nombre debe tener al menos 3 caracteres.',
            'primer_nombre.max' => 'El primer nombre no puede exceder los 100 caracteres.',
            
            'otros_nombres.string' => 'Los otros nombres deben ser una cadena de texto.',
            'otros_nombres.min' => 'Los otros nombres deben tener al menos 3 caracteres.',
            'otros_nombres.max' => 'Los otros nombres no pueden exceder los 100 caracteres.',
            
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.min' => 'El primer apellido debe tener al menos 3 caracteres.',
            'primer_apellido.max' => 'El primer apellido no puede exceder los 100 caracteres.',
            
            'segundo_apellido.string' => 'El segundo apellido debe ser una cadena de texto.',
            'segundo_apellido.min' => 'El segundo apellido debe tener al menos 3 caracteres.',
            'segundo_apellido.max' => 'El segundo apellido no puede exceder los 100 caracteres.',
            
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.min' => 'El teléfono debe tener al menos 7 caracteres.',
            'telefono.max' => 'El teléfono no puede exceder los 20 caracteres.',
            
            'numero_identificacion.required' => 'El número de identificación es obligatorio.',
            'numero_identificacion.min' => 'El número de identificación debe tener al menos 7 caracteres.',
            'numero_identificacion.max' => 'El número de identificación no puede exceder los 20 caracteres.',
            
            'id_tipos_identificacion.required' => 'El tipo de identificación es obligatorio.',
            'id_tipos_identificacion.integer' => 'El tipo de identificación debe ser un número entero.',
            'id_tipos_identificacion.max' => 'El tipo de identificación no puede exceder los 2 caracteres.',
        ];

        $validator = Validator::make($data, [
            'name' => 'required|string|min:5|max:20', // nombre usuario
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:3|max:100|confirmed',
            'primer_nombre' => 'required|string|min:3|max:100',
            'otros_nombres' => 'string|min:3|max:100|nullable',
            'primer_apellido' => 'required|string|min:3|max:100',
            'segundo_apellido' => 'string|min:3|max:100|nullable',
            'telefono' => 'required|string|min:7|max:20',
            'numero_identificacion' => 'required|string|min:7|max:20',
            'id_tipos_identificacion' => 'required|integer|max:2',
        ],$messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);
            $user->primer_nombre = $data['primer_nombre'];
            $user->otros_nombres = $data['otros_nombres'] ?? null; // nullable field
            $user->primer_apellido = $data['primer_apellido'];
            $user->segundo_apellido = $data['segundo_apellido'] ?? null; // nullable field
            $user->telefono = $data['telefono'];
            $user->numero_identificacion = $data['numero_identificacion'];
            $user->id_tipos_identificacion = $data['id_tipos_identificacion'];
            $user->id_estado = 1;
            $user->save();

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Registro creado exitosamente.',
            ]);

        } catch (QueryException $ex) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Error al crear el registro: ' . $ex->getMessage(),
            ]);
        }
    }
}
