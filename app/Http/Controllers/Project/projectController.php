<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Admin\Users\userController;
use App\Http\Controllers\Controller;
use App\Models\history;
use Illuminate\Http\Request;
use App\Models\project;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
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

        'categoria.required' => 'Categoría es obligatoria.',
        'categoria.integer' => 'Categoría debe ser un número entero.',
        'categoria.max' => 'Categoría no puede exceder los 2 caracteres.',

        'estado.required' => 'Estado es obligatorio.',
        'estado.integer' => 'Estado debe ser un número entero.',
        'estado.max' => 'Estado no puede exceder los 2 caracteres.',

        'archivo.file' => 'Información de archivo incorrecta.',
        'archivo.mimes' => 'El archivo debe ser de tipo PDF.',
    ];


    /**
     * Muestra una lista de todos los proyectos con sus datos,
     * excluyendo aquellos que están inactivos o eliminados
     * 
     * @param int $rows Número de filas a obtener por defecto.
     * @return \Illuminate\Http\Response
     */
    public function index($rows = 10)
    {
        return project::from('proyectos as p')
            ->where('p.id_estado', 1)
            ->orWhere('p.id_estado', 2)
            ->join('categoria as c', 'p.id_categoria', '=', 'c.id')
            ->join('estados as e', 'p.id_estado', '=', 'e.id')
            ->paginate($rows, [
                'p.id',
                'p.titulo',
                'p.fechainicio',
                'p.fechafin',
                'p.ruta',
                'p.palabras_claves',
                'p.descripcion',
                'c.descripcion',
                'e.descripcion'
            ]);
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
                $project->ruta = $path; //nombre del archivo
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
            $user = new userController(null);
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
                $project->ruta = $path; //nombre del archivo
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

            $user = new userController(null);
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


    public function destroy(string $id) {}
    public function downloadFile($name) {}


    public function show(string $id)
    {
        //
    }

    public function edit(string $id) {}
}
