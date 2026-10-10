<?php

namespace App\Http\Controllers;

use App\Models\Ganador;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GanadoresExport;
use App\Imports\GanadoresImport;

class GanadorController extends Controller
{
    /**
     * Muestra la lista de ganadores con buscador y paginación.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search'));

        $ganadores = Ganador::when($search, function ($query, $search) {
            // Limpiar el término de búsqueda para WhatsApp (dejar solo dígitos)
            $searchDigits = preg_replace('/\D/', '', $search);

            return $query->where(function ($q) use ($search, $searchDigits) {
                // 1. Búsqueda por Nombre (coincidencia parcial)
                $q->where('nombre', 'LIKE', "%{$search}%")
                  // 2. Búsqueda por Programa
                  ->orWhere('programa', 'LIKE', "%{$search}%");

                // 3. Búsqueda por WhatsApp ignorando espacios/guiones si el usuario ingresó números
                if (!empty($searchDigits)) {
                    $q->orWhereRaw("REPLACE(REPLACE(REPLACE(whatsapp, ' ', ''), '-', ''), '+', '') LIKE ?", ["%{$searchDigits}%"]);
                } else {
                    $q->orWhere('whatsapp', 'LIKE', "%{$search}%");
                }
            });
        })
        ->orderBy('id', 'desc')
        ->paginate(15);

        return view('ganadores.index', compact('ganadores'));
    }

    /**
     * Muestra el formulario para crear un nuevo registro (opcional si usas modales).
     */
    public function create()
    {
        return view('ganadores.create');
    }

    /**
     * Guarda un nuevo registro de ganador en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'          => 'required|string|max:255',
            'edad'            => 'nullable|integer',
            'whatsapp'        => 'nullable|string|max:20',
            'facebook_id'     => 'nullable|string|max:255',
            'fecha_dinamica'  => 'nullable|date',
            'fecha_entrega'   => 'nullable|date',
            'programa'        => 'nullable|string|max:255',
            'premio'          => 'nullable|string|max:255',
            'patrocinador'    => 'nullable|string|max:255',
            'caza_premios'    => 'boolean',
            'alerta'          => 'boolean',
            'premio_especial' => 'boolean', // <-- Agregado aquí
        ]);

        // Asegurar exclusión mutua: si se marca uno, se desactivan los demás
        if (!empty($validated['alerta'])) {
            $validated['caza_premios'] = false;
            $validated['premio_especial'] = false;
        } elseif (!empty($validated['caza_premios'])) {
            $validated['alerta'] = false;
            $validated['premio_especial'] = false;
        } elseif (!empty($validated['premio_especial'])) {
            $validated['alerta'] = false;
            $validated['caza_premios'] = false;
        }

        Ganador::create($validated);

        return redirect()->route('ganadores.index')
            ->with('success', 'Ganador registrado correctamente.');
    }

    /**
     * Muestra el formulario para editar un registro (opcional si usas modales).
     */
    public function edit($id)
    {
        $ganador = Ganador::findOrFail($id);
        return view('ganadores.edit', compact('ganador'));
    }

    /**
     * Actualiza la información de un ganador existente.
     */
    public function update(Request $request, $id)
    {
        $ganador = Ganador::findOrFail($id);

        $validated = $request->validate([
            'nombre'          => 'required|string|max:255',
            'edad'            => 'nullable|integer',
            'whatsapp'        => 'nullable|string|max:20',
            'facebook_id'     => 'nullable|string|max:255',
            'fecha_dinamica'  => 'nullable|date',
            'fecha_entrega'   => 'nullable|date',
            'programa'        => 'nullable|string|max:255',
            'premio'          => 'nullable|string|max:255',
            'patrocinador'    => 'nullable|string|max:255',
            'caza_premios'    => 'boolean',
            'alerta'          => 'boolean',
            'premio_especial' => 'boolean', // <-- Agregado aquí
        ]);

        // Asegurar exclusión mutua en actualizaciones
        if (!empty($validated['alerta'])) {
            $validated['caza_premios'] = false;
            $validated['premio_especial'] = false;
        } elseif (!empty($validated['caza_premios'])) {
            $validated['alerta'] = false;
            $validated['premio_especial'] = false;
        } elseif (!empty($validated['premio_especial'])) {
            $validated['alerta'] = false;
            $validated['caza_premios'] = false;
        }

        $ganador->update($validated);

        return redirect()->route('ganadores.index')
            ->with('success', 'Registro actualizado correctamente.');
    }

    /**
     * Elimina un registro individual.
     */
    public function destroy($id)
    {
        $ganador = Ganador::findOrFail($id);
        $ganador->delete();

        return redirect()->route('ganadores.index')
            ->with('success', 'Registro eliminado correctamente.');
    }

    /**
     * Elimina masivamente los registros pertenecientes a un año específico.
     */
    public function destroyPorAno(Request $request)
    {
        $request->validate([
            'ano' => 'required|numeric|digits:4',
        ]);

        $ano = $request->input('ano');

        $eliminados = Ganador::whereYear('fecha_dinamica', $ano)
            ->orWhereYear('fecha_entrega', $ano)
            ->delete();

        if ($eliminados > 0) {
            return redirect()->route('ganadores.index')
                ->with('success', "Se eliminaron {$eliminados} registros del año {$ano} correctamente.");
        }

        return redirect()->route('ganadores.index')
            ->with('error', "No se encontraron registros del año {$ano}.");
    }

    /**
     * Exporta los datos a Excel.
     */
    public function export()
    {
        return Excel::download(new GanadoresExport, 'ganadores.xlsx');
    }

    /**
     * Importa datos desde un archivo Excel/CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        Excel::import(new GanadoresImport, $request->file('archivo_excel'));

        return redirect()->route('ganadores.index')
            ->with('success', 'Registros importados correctamente desde el Excel.');
    }
}