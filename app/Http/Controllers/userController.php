<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class userController extends Controller
{
    public function userRegister(Request $request){
        //roles 'Superadmin\nAdmin\nTutor\nAsistente\n',

        $data= $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|string|min:5|max:20',//nombre usuario
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:3|max:100|confirmed',
            'primer_nombre'=> 'required|string|min:3|max:100',
            'otros_nombres'=> 'string|string|min:3|max:100',
            'primer_apellido'=> 'required|string|min:3|max:100',
            'segundo_apellido'=> 'string|string|min:3|max:100',
            // 'nombre_usuario'=> 'required',
            'telefono'=> 'required|string|min:7|max:20',
            'numero_identificacion'=> 'required|string|min:7|max:20',
            'id_tipos_identificacion'=> 'required|integer|max:2',
            // 'id_rol'=> 'required|string|min:8|max:255|confirmed',
            // 'id_estado'=> 'required|integer|max:2',
        ]);

        if ($validator) {
            $user= new User();
            $user->name= $data['name'];
            $user->email= $data['email'];
            $user->password= Hash::make($data['password']);
            $user->primer_nombre= $data['primer_nombre'];
            $user->otros_nombres= $data['otros_nombres'];
            $user->primer_apellido= $data['primer_apellido'];
            $user->segundo_apellido= $data['segundo_apellido'];
            $user->telefono= $data['telefono'];
            $user->numero_identificacion= $data['numero_identificacion'];
            $user->id_tipos_identificacion= $data['id_tipos_identificacion'];
            $user->id_estado= 1;            
            $user->save();
            if($request){
                return response()->json([
                    'status'=> 200,
                    'success'=> true,
                    'message'=> 'Regitro creado exitosamente.',
                ]);
            }else{
                return response()->json([
                    'status'=> 400,
                    'success'=> false,
                    'message'=> 'Error al crear el registro.',
                ]);
            }
        }else{
            return response()->json([
                'status'=> 400,
                'success'=> false,
                'message'=> $validator->errors(),
            ]);
        }

    }
}
