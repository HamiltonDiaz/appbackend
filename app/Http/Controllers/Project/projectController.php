<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\project;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

class projectController extends Controller
{
    protected $disk='public';
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

    public function storeFile(Request $request){

    }
    public function downloadFile($name){
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        ],$this->messages);

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
                $project->ruta=$path;//nombre del archivo
            } else {
                $project->ruta=null;//nombre del archivo
            }
            //Guardar datos
            $project->titulo=$data['titulo'];
            $project->palabras_claves=$data['palabras_claves'];
            $project->descripcion=$data['descripcion'];
            $project->fechainicio=$data['fecha_inicial'];
            $project->fechafin=$data['fecha_final']?? null;
            $project->id_categoria=$data['categoria'];    
            
            return response()->json([
                'status' => 200,
                'success' => true,
                'data'=>$project,
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
