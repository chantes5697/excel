<?php

namespace App\Imports;

use App\Models\Pedimentos;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PedimentosImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Pedimentos([
            'id_proveedor'     => $row['id_proveedor'],
            'id_planta'        => $row['id_planta'],
            'numero_pedimento' => $row['numero_pedimento'],
            'division'         => $row['division'],
            'periodo'          => $row['periodo'],
            'avance'           => $row['avance'],
            'estatus'          => $row['estatus'],
            'responsable'      => $row['responsable'],
            'procesos'         => $row['procesos'],
            'inicio_proceso'   => $row['inicio_proceso'],
            'tipo'             => $row['tipo'],
        ]);
    }
}
