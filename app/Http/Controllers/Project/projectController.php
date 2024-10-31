<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Admin\Users\userController;
use App\Http\Controllers\Controller;
use App\Models\history;
use Illuminate\Http\Request;
use App\Models\project;
use App\Models\User;
use App\Models\usuariosHasProyectos;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class projectController extends Controller
{
    protected $disk = 'public';
    protected $messages = [
        'id.required' => 'El identificador es obligatorio.',
        'id.integer' => 'El identificador debe ser un entero.',

        'titulo.required' => 'El título es obligatorio.',
        'titulo.min' => 'El título debe tener al menos 3 caracteres.',
        'titulo.max' => 'El título no puede exceder los 200 caracteres.',
        'titulo.unique' => 'El título ya ha sido registrado.',

        'palabras_claves.required' => 'Palabras claves son obligatorias.',
        'palabras_claves.array' => 'Formato incorrecto para palabras claves.',

        'descripcion.required' => 'Descripción es obligatoria.',
        'descripcion.min' => 'Descripción debe tener al menos 20 caracteres.',
        'descripcion.max' => 'Descripción no puede exceder los 2600 caracteres.',

        'fechainicio.required' => 'Fecha de inicio es obligatoria.',
        'fechainicio.date' => 'El campo fecha inicial debe ser una fecha válida.',
        'fechainicio.date_format' => 'Formato incorrecto para fecha de inicio.',

        'fechafin.date' => 'El campo fecha final debe ser una fecha válida.',
        'fechafin.date_format' => 'Formato incorrecto para fecha final.',

        'id_categoria.required' => 'Categoría es obligatoria.',
        'id_categoria.integer' => 'Categoría debe ser un número entero.',
        'id_categoria.max' => 'Categoría no puede exceder los 2 caracteres.',

        'estado.required' => 'Estado es obligatorio.',
        'estado.integer' => 'Estado debe ser un número entero.',
        'estado.max' => 'Estado no puede exceder los 2 caracteres.',

        'archivo.file' => 'Información de archivo incorrecta.',
        'archivo.mimes' => 'El archivo debe ser de tipo PDF.',

        'id_proyecto'=>'Proyecto es obligatorio',
        'id_usuario'=>'Usuario es obligatorio.',
    ];


    /**
     * Muestra una lista de todos los proyectos con sus datos,
     * excluyendo aquellos que están inactivos o eliminados
     * 
     * @param int $rows Número de filas a obtener por defecto.
     * @return \Illuminate\Http\Response
     */
    public function index($rows = 10, $search = null)
    {
        $query = project::from('proyectos as p')
            ->where(function ($query) {
                $query->where('p.id_estado', 1)
                      ->orWhere('p.id_estado', 2);
            })
            ->join('categoria as c', 'p.id_categoria', '=', 'c.id')
            ->join('estados as e', 'p.id_estado', '=', 'e.id');
    
        if ($search) {
            $query->whereRaw("CONCAT_WS(' ', p.titulo, p.fechainicio, p.fechafin, p.ruta, p.palabras_claves, p.descripcion, c.descripcion, e.descripcion) LIKE ?", ["%$search%"]);
        }          
        return $query->paginate($rows, [
            'p.id',
            'p.titulo',
            'p.fechainicio',
            'p.fechafin',
            'p.ruta',
            'p.palabras_claves',
            'p.descripcion',
            'c.descripcion as categoria_descripcion',
            'e.descripcion as estado_descripcion'
        ]);
    }
    
    public function store(Request $request)
    {

        //TODO: Los proyectos solo los pueden crear el superadmin y el admin.
        //TODO:La vinculación de usuarios al proyecto solo la pueden realizar el superadmin y el admin.
        //TODO: La modificación del proyecto solo la pueden realizar el superadmin y el admin.
        //TODO: Los asistentes pueden subir y cambiar el archivo en formato PDF.
        $project = new project();
        $data = $request->all();
        $validator = Validator::make($data, [
            'titulo' => 'required|string|min:3|max:200|unique:proyectos',
            'palabras_claves' => 'required|array',
            'descripcion' => 'required|string|min:20|max:2600',
            'fechainicio' => 'required|date||date_format:Y-m-d',
            'fechafin' => 'nullable|date|date_format:Y-m-d',
            'id_categoria' => 'required|integer|max:2',
            'archivo' => 'nullable|file|mimes:pdf',
        ], $this->messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            //Guardar archivo pdf
            if ($request->hasFile('archivo')) {
                $path = $request->file('archivo')->store($this->disk);
                $project->ruta = basename($path); //nombre del archivo
            } else {
                $project->ruta = null; //nombre del archivo
            }
            //Guardar datos
            $project->titulo = $data['titulo'];
            $project->palabras_claves = json_encode($data['palabras_claves']);
            $project->descripcion = $data['descripcion'];
            $project->fechainicio = $data['fechainicio'];
            $project->fechafin = $data['fechafin'] ?? null;
            $project->id_categoria = $data['id_categoria'];
            $project->id_estado = 1;
            $project->save();

            //Guardar historico            
            $user = new userController();
            $idActualUser = $user->me()->getData()->id;
            $historico = new history();
            $historico->id_proyecto = $project->id;
            $historico->id_usuario = $idActualUser;
            $historico->fecha = now();
            $historico->descripcion = "Creación proyecto";
            $historico->save();

            return response()->json([
                'status' => 200,
                'success' => true,
                'data' => $project,
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

    public function update(Request $request)
    {
        $id = $request->id;
        //Valida si el proyecto está registrado en la base de datos.
        $project = project::find($id);
        if (!$project) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Proyecto no encontrado.',
            ]);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'id' => 'required|integer',
            'titulo' => 'required|string|min:3|max:200|unique:proyectos,titulo,'.$id.',id',
            'palabras_claves' => 'required|array',
            'descripcion' => 'required|string|min:20|max:2600',
            'fechainicio' => 'required|date||date_format:Y-m-d',
            'fechafin' => 'nullable|date|date_format:Y-m-d',
            'id_categoria' => 'required|integer|max:2',
            'id_estado'=>'required|integer|max:2',
            'archivo' => 'nullable|file|mimes:pdf',
        ], $this->messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            //Guardar archivo pdf
            if ($request->hasFile('archivo')) {
                $path = $request->file('archivo')->store($this->disk);
                $project->ruta = basename($path); //nombre del archivo
            } else {
                $project->ruta = null; //nombre del archivo
            }

            //Actualizar datos
            $project->titulo = $data['titulo'];
            $project->palabras_claves = json_encode($data['palabras_claves']);
            $project->descripcion = $data['descripcion'];
            $project->fechainicio = $data['fechainicio'];
            $project->fechafin = $data['fechafin'] ?? null;
            $project->id_categoria = $data['id_categoria'];
            $project->id_estado = $data['id_estado'];

            // Capturar el estado original antes de guardar
            $original = $project->getOriginal(); 
            $project->save();

            $user = new userController();
            $idActualUser = $user->me()->getData()->id;
            foreach ($project->getAttributes() as $key => $value) {
                if ($original[$key] != $value && $key != 'updated_at') {
                    $historico = new history();
                    $historico->id_proyecto = $project->id;
                    $historico->id_usuario = $idActualUser;
                    $historico->fecha = now();
                    $historico->descripcion = "Campo '{$key}' actualizado de '{$original[$key]}' a '{$value}'";
                    $historico->save();
                }
            }
            return response()->json([
                'status' => 200,
                'success' => true,
                'data' => $project,
                'message' => 'Registro editado exitosamente.',
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Error al editar el registro: ' . $ex->getMessage(),
            ]);
        }
    }
    public function destroy(string $id) {
             //Valida si el usuario está registrado en la base de datos.
             $project = project::find($id);
             if (!$project) {
                 return response()->json([
                     'status' => 400,
                     'success' => false,
                     'message' => 'Proyecto no encontrado.',
                 ]);
             }
     
             $user = new userController();
             $idActualUser = $user->me()->getData()->id;
            //  $roles= $this->validateRole($idActualUser, 1);//valida si es superadmin
     
            //  if (!$roles) {
            //      //Si no es usuario superadmin entonces no puede elminar
            //      return response()->json([
            //          'status' => 400,
            //          'success' => false,
            //          'message' => "Usuario no autorizado",
            //      ]);
            //  }
             

            $historico = new history();
            $historico->id_proyecto = $id;
            $historico->id_usuario = $idActualUser;
            $historico->fecha = now();
            $historico->descripcion = "Proyecto elminado";
            $historico->save();

             $project->id_estado = 3;
             $project->save();
             return response()->json([
                 'status' => 200,
                 'success' => true,
                 'data' => null,
                 'message' => 'Registro elminado exitosamente.',
             ]);
    }
    public function downloadFile($file)
    {
        $rutaArchivo = "public/{$file}";
        if (Storage::exists($rutaArchivo)) {
            return Storage::download($rutaArchivo);
        } else {
            return response()->json([
                'status' => 404,
                'success' => false,
                'data' => null,
                'message' => 'Archivo no encontrado',
            ]);
        }
    }
    public function findById(string $id)
    {
        try {
            $project = Project::where('id', $id)->where('id_estado', '!=', 3)->first();
            if (!$project) {
                return response()->json([
                    'status' => 400,
                    'success' => false,
                    'message' => 'Registro no encontrado.',
                ]);
            }
            $project->palabras_claves = json_decode($project->palabras_claves);
            $historico=history::where('id_proyecto', $project->id)->orderBy('id', 'asc') ->get();
            // $members= new usuariosHasProyectos();
            $members= usuariosHasProyectos::where('id_proyecto', $project->id)
            ->join('users', 'users.id', '=', 'usuarios_has_proyectos.id_usuario')
            ->join('tipos_identificacion','tipos_identificacion.id', '=', 'users.id_tipos_identificacion')
            ->select(
                'users.id',
                'tipos_identificacion.descripcion',
                'users.numero_identificacion',
                DB::raw("CONCAT_WS(' ', users.primer_nombre, users.otros_nombres, users.primer_apellido, users.segundo_apellido) as nombre"),
                'users.email',
                'users.telefono',
                
            )
            ->get();

            return response()->json([
                'status' => 200,
                'success' => true,
                'data'=>[
                    'proyecto' => $project, 
                    'historico' => $historico,
                    'integrantes' => $members
                ],
                'message' => 'Registro encontrado exitosamente.',
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status' => 400,
                'success' => false,                
                'message' => 'Error al consultar el registro: ' . $ex->getMessage(),
            ]);
        }
    }

    public function assignMember(Request $request){
        
        $data = $request->all();
        $validator = Validator::make($data, [
            'id_proyecto' => 'required|integer',
            'id_usuario' => 'required|integer',
        ], $this->messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $project = project::find($data['id_proyecto']);
        if (!$project) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Proyecto no encontrado.',
            ]);
        }

        $user = User::find($data['id_usuario']);
        if (!$user) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Usuario no encontrado.',
            ]);
        }

        $usuarioFinal= trim($user->primer_nombre . ' ' . $user->otros_nombres . ' ' . $user->primer_apellido . ' ' . $user->segundo_apellido);

        $members= new usuariosHasProyectos();
        $members->id_proyecto = $data['id_proyecto'];
        $members->id_usuario = $data['id_usuario'];
        $members->save();

        $user = new userController();
        $idActualUser = $user->me()->getData()->id;

        $historico = new history();
        $historico->id_proyecto = $data['id_proyecto'];
        $historico->id_usuario = $idActualUser;
        $historico->fecha = now();
        $historico->descripcion = 'Se agrega usuario ' . $usuarioFinal . ' al proyecto';
        $historico->save();
        

        return response()->json([
            'status' => 200,
            'success' => true,
            'data' => $members,
            'message' => 'Registro creado exitosamente.',
        ]);

    }

}
