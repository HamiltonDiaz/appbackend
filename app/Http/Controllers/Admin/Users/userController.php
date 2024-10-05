<?php

namespace App\Http\Controllers\Admin\Users;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Services\MailConfigService;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;


class userController extends Controller
{
    protected $mailConfig;
    protected   $messages = [
        'name.required' => 'El nombre de usuario es obligatorio.',
        'name.min' => 'El nombre de usuario debe tener al menos 5 caracteres.',
        'name.max' => 'El nombre de usuario no puede exceder los 20 caracteres.',
        'name.unique' => 'El nombre de usuario ya ha sido registrado.',

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

        'token.required' => 'Token incorrecto.',

        'id_estado.required' => 'Estado es obligatorio.',
        'id_estado.integer' => 'El id del estado debe ser un número entero.',
        'id_estado.max' => 'El id del estado no puede exceder los 2 caracteres.',

    ];

    public function __construct(MailConfigService $mailConfig = null)
    {
        if ($mailConfig === null) {
            // Manejar el caso en que el servicio no fue pasado
            $this->mailConfig = app(MailConfigService::class); // Obtener el servicio desde el contenedor si es necesario
        } else {
            $this->mailConfig = $mailConfig;
        }
    }


    /**
     * Muestra una lista de todos los usuarios con sus datos,
     * excluyendo aquellos que están inactivos o elminados
     * 
     * @param int $rows Número de filas a obtener por defecto.
     * @return \Illuminate\Http\Response
     */
    public function index($rows=10){
        $users = User::where('id_estado', 1)->orwhere('id_estado', 2)     
        ->paginate($rows, [
            // '*' //Esto significa que se devuelven todos los campos de la tabla
            'id',
            'numero_identificacion',
            'primer_nombre',
            'otros_nombres',
            'primer_apellido',
            'segundo_apellido',
            'name',
            'email',
            'telefono',
        ]);
        return $users;
    }


    /**
     * Registra un nuevo usuario en la base de datos.
     *
     * La función recibe los datos del formulario de registro por medio de la petición HTTP.
     * Verifica que los datos estén correctos y si es así, los registra en la base de datos.
     * Si hay algún error, devuelve una respuesta HTTP con un mensaje de error.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userRegister(Request $request)
    {
        //roles 'Superadmin\nAdmin\nTutor\nAsistente\n',

        $exists = User::documentExists($request->id_tipos_identificacion,$request->numero_identificacion);
        if ($exists) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Tipo y número de documento ya registrado.',
            ]);
        }

        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|string|min:5|max:20|unique:users', // nombre usuario
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:3|max:100|confirmed',
            'primer_nombre' => 'required|string|min:3|max:100',
            'otros_nombres' => 'string|min:3|max:100|nullable',
            'primer_apellido' => 'required|string|min:3|max:100',
            'segundo_apellido' => 'string|min:3|max:100|nullable',
            'telefono' => 'required|string|min:7|max:20',
            'numero_identificacion' => 'required|string|min:7|max:20',
            'id_tipos_identificacion' => 'required|integer|max:3',
        ],$this->messages);

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
                'data'=>$user,
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

    public function findById($id){        
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 400,
                    'success' => false,
                    'message' => 'Usuario no encontrado.',
                ]);
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'data'=>$user,
                'message' => 'Usuario encontrado exitosamente.',
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status' => 400,
                'success' => false,                
                'message' => 'Error al consultar el registro: ' . $ex->getMessage(),
            ]);
        }
        
    }

    /**
     * Maneja la solicitud de inicio de sesión del usuario.
     *
     * Este método recibe una solicitud con las credenciales del usuario (nombre y contraseña).
     * Utiliza el método `Auth::attempt()` para intentar autenticar al usuario con las credenciales proporcionadas.
     * Si la autenticación falla, devuelve una respuesta JSON con un mensaje de error y un código de estado 401.
     * Si la autenticación es exitosa, devuelve un token de acceso mediante el método `respondWithToken()`.
     *
     * @param \Illuminate\Http\Request $request La solicitud que contiene las credenciales del usuario.
     * 
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene el token de acceso o un mensaje de error.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('name', 'password'); 
        $token =Auth::attempt($credentials);
        if (!$token) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }
        return $this->respondWithToken($token);
    }

    /**
     * Cierra la sesión actual y elimina el token de autenticación.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $token = JWTAuth::getToken();// Obtén el token actual
        JWTAuth::invalidate($token);//Elimina el token
        Auth::logout();    
        return response()->json([
            'status' => 200,
            'success' => true, 
            'message' => '!Hasta pronto!'
        ]);
    }

    /**
     * Devuelve el usuario autenticado actual en formato JSON.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(Auth::user());
    }


    /**
     * Actualiza el token de autenticación actual.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(){
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Retorna a success JSON response with the given token.
     *
     * @param  string  $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60 //Devuelve el tiempo de vida del token
        ]);
    }


    /**
     * Envía un enlace de restablecimiento de contraseña al correo electrónico proporcionado.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'email' => 'required|string|email|max:100',         
        ],$this->messages);

        $this->mailConfig->configure();

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([                
            'status' => 404,
            'success' => false, 
            'message' => 'No se encontró el usuario'
            ], 404);
        }
    
        $token = Password::createToken($user);
    
        // Enviar correo con el token
        Mail::to($request->email)->send(new ResetPasswordMail($token));    
        return response()->json([                            
            'status' => 200,
            'success' => true,
            'data' => ["token" => $token],
            'message' => 'Enlace de restablecimiento enviado correctamente.',
        ]);
    }

    public function reset(Request $request)
    {
        // Validar los datos de la solicitud     
        $data = $request->all();
        $validator = Validator::make($data, [
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|min:3|max:100|confirmed',
            'token' => 'required|string',
        ],$this->messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        $this->mailConfig->configure();

        // Intentar restablecer la contraseña
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Guardar la nueva contraseña encriptada
                $user->password = Hash::make($password);
                $user->save();

                // Lanzar evento de restablecimiento
                event(new PasswordReset($user));
            }
        );

        // Responder según el estado del restablecimiento
        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => "restablecimiento de contrasenia exitoso",
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => "Error al restablecer contraseña",
            ], 400);
        }
    }

    public function validateRole($idUser, $idRole){
        $actualUser = User::find($idUser);
        //"superadmin"=>id=1,"admin"=>id=2,"tutor"=>id=3,"asistente"=>id=4
        $roles = $actualUser->roles()->pluck('id')->toArray();
        return in_array($idRole, $roles);
    }

    public function updateUser(Request $request)
    {
        $id = $request->id;
        //Valida si el usuario está registrado en la base de datos.
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Usuario no encontrado.',
            ]);
        }

        $data = $request->all();
        $idActualUser = $this->me()->getData()->id;      
        $roles= $this->validateRole($idActualUser, 1);//valida si es superadmin

        if (!$roles && $user->id != $idActualUser) {
            //Si no es usuario superadmin y tampoco es el mismo usuario entonces no puede modificar
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => "Usuario no autorizado",
            ]);
        }

        $exists = User::documentExists($request->id_tipos_identificacion,$request->numero_identificacion,$id);
        if ($exists) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Tipo y número de documento ya registrado.',
            ]);
        }

        if ($roles) {            
            //Superadmin puede modificar todos los datos
            $validator = Validator::make($data, [
                'name' => 'required|string|min:5|max:20|unique:users,name,' . $id,
                'email' => 'required|string|email|max:100|unique:users,email,' . $id,
                'password' => 'required|string|min:3|max:100|confirmed',
                'primer_nombre' => 'required|string|min:3|max:100',
                'otros_nombres' => 'string|min:3|max:100|nullable',
                'primer_apellido' => 'required|string|min:3|max:100',
                'segundo_apellido' => 'string|min:3|max:100|nullable',
                'telefono' => 'required|string|min:7|max:20',
                'numero_identificacion' => 'required|string|min:7|max:20',
                'id_tipos_identificacion' => 'required|integer|max:3',
                'id_estado' => 'required|integer|max:2',
            ], $this->messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ]);
            }
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
            $user->id_estado = $data['id_estado'];
        }
        
        
        if ($user->id == $idActualUser && !$roles) {            
            // El usuario solo puede modificar correo electrónico, número de celular y la contraseña
            $validator = Validator::make($data, [
                'email' => 'email|unique:users,email,' . $id,            
                'telefono' => 'string|min:7|max:20',
                'password' => 'string|min:3|max:100|confirmed',
            ], $this->messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $user->email = $data['email'] ?? $user->email;
            $user->password = Hash::make($data['password']??$user->password);
            $user->telefono = $data['telefono']??$user->telefono;
        }
        try {
            $user->save();
            return response()->json([
                'status' => 200,
                'success' => true,
                'data' => $user,
                'message' => 'Registro actualizado exitosamente.',
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Error al actualizar el registro: ' . $ex->getMessage(),
            ]);
        }
    }
    public function destroy($id)
    {
        //Valida si el usuario está registrado en la base de datos.
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Usuario no encontrado.',
            ]);
        }

        $idActualUser = $this->me()->getData()->id;      
        $roles= $this->validateRole($idActualUser, 1);//valida si es superadmin

        if (!$roles) {
            //Si no es usuario superadmin entonces no puede elminar
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => "Usuario no autorizado",
            ]);
        }
        
        $user->id_estado = 3;
        $user->save();
        return response()->json([
            'status' => 200,
            'success' => true,
            'data' => null,
            'message' => 'Registro elminado exitosamente.',
        ]);
    }
    
}
