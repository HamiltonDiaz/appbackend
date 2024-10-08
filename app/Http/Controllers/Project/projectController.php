<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Admin\Users\userController;
use App\Http\Controllers\Controller;
use App\Models\history;
use Illuminate\Http\Request;
use App\Models\project;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

class projectController extends Controller
{
    protected $disk = 'public';
    protected $messages = [
        'titulo.required' => 'El título es obligatorio.',
        'titulo.min' => 'El título debe tener al menos 3 caracteres.',
        'titulo.max' => 'El título no puede exceder los 200 caracteres.',
        'titulo.unique' => 'El título ya ha sido registrado.',

        'palabras_claves.required' => 'Palabras claves son obligatorias.',
        'palabras_claves.array' => 'Formato incorrecto para palabras claves.',

        'descripcion.required' => 'Descripción es obligatoria.',
        'descripcion.min' => 'Descripción debe tener al menos 20 caracteres.',
        'descripcion.max' => 'Descripción no puede exceder los 2600 caracteres.',

        'fecha_inicial.required' => 'Fecha de inicio es obligatoria.',
        'fecha_inicial.date' => 'El campo fecha inicial debe ser una fecha válida.',
        'fecha_inicial.date_format' => 'Formato incorrecto para fecha de inicio.',

        'fecha_final.date' => 'El campo fecha final debe ser una fecha válida.',
        'fecha_final.date_format' => 'Formato incorrecto para fecha final.',

        'categoria.required' => 'Categoría es obligatoria.',
        'categoria.integer' => 'Categoría debe ser un número entero.',
        'categoria.max' => 'Categoría no puede exceder los 2 caracteres.',

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
            'fecha_inicial' => 'required|date||date_format:Y-m-d',
            'fecha_final' => 'nullable|date|date_format:Y-m-d',
            'categoria' => 'required|integer|max:2',
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
            $project->fechainicio = $data['fecha_inicial'];
            $project->fechafin = $data['fecha_final'] ?? null;
            $project->id_categoria = $data['categoria'];
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
    public function downloadFile($name) {}


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
