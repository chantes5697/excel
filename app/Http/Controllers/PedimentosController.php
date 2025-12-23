<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Imports\PedimentosImport;
use App\Models\Pedimentos;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class PedimentosController extends Controller
{
    public function index() {
        return view('upload');
    }

    // 1. Handle Dropzone Upload
    public function upload(Request $request) {
        $file = $request->file('file');
        $path = $file->store('temp'); // Store in storage/app/temp

        return response()->json(['path' => $path]);
    }

    // 2. Preview data from temporary file
    public function preview(Request $request) {
        $path = $request->input('path');
        $path = $request->input('path');

        // Check if file exists using the Storage facade
        if (!Storage::exists($path)) {
            return response()->json(['error' => 'File not found at: ' . $path], 404);
        }

        // Get the FULL absolute path for the Excel library
        $fullPath = Storage::path($path);

        // Import to array
        // We specify \Maatwebsite\Excel\Excel::CSV if it's a CSV, 
        // but the package usually auto-detects.
        $data = Excel::toArray(new PedimentosImport, $fullPath);
        
        return response()->json($data[0]);
    }

    // 3. Final Save
   public function store(Request $request) 
{
    $items = $request->input('items'); // This is the array of selected rows

    if (empty($items)) {
        return response()->json(['error' => 'No data selected'], 400);
    }

    try {
        foreach ($items as $item) {
            // Note: The keys here must match the keys in your JSON (returned from Excel::toArray)
            // Usually, Maatwebsite Excel converts headers to snake_case.
            Pedimentos::create([
                'id_proveedor'     => $item['id_proveedor'],
                'id_planta'        => $item['id_planta'],
                'numero_pedimento' => $item['numero_pedimento'] ?? $item['Numero_Pedimento'],
                'division'         => $item['division'] ?? $item['Division'],
                'periodo'          => $item['periodo'] ?? $item['Periodo'],
                'avance'           => $item['avance'] ?? $item['Avance'],
                'estatus'          => $item['estatus'] ?? $item['Estatus'],
                'responsable'      => $item['responsable'] ?? $item['Responsable'],
                'procesos'         => $item['procesos'] ?? $item['procesos'],
                'inicio_proceso'   => $item['inicio_proceso'] ?? $item['Inicio_Proceso'],
                'tipo'             => $item['tipo'] ?? $item['tipo'],
            ]);
        }

        return response()->json(['message' => count($items) . ' rows saved successfully!']);
        
    } catch (\Exception $e) {
        return response()->json(['error' => 'Database Error: ' . $e->getMessage()], 500);
    }
}
    /**
     * Display the specified resource.
     */
    public function show(Pedimentos $pedimentos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedimentos $pedimentos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedimentos $pedimentos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedimentos $pedimentos)
    {
        //
    }
}
