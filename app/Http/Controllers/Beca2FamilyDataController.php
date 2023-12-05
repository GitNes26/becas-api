<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Beca2FamilyData;
use App\Models\Beca1TutorData;
use App\Models\ObjResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class Beca2FamilyDataController extends Controller
{
    /**
     * Crear o Actualizar familiar desde formulario beca.
     *
     * @return \Illuminate\Http\Response $response
     */
    public function createOrUpdateByBeca($request)
    {
        try {
            $student_data = Beca2FamilyData::where('id', $request->curp)->first();
            if (!$student_data) $student_data = new Beca2FamilyData();

            $student_data->beca_id = $request->beca_id;
            $student_data->relationship = $request->relationship;
            $student_data->age = $request->age;
            $student_data->occupation = $request->occupation;
            $student_data->monthly_income = $request->monthly_income;

            $student_data->save();
            return $student_data;
        } catch (\Exception $ex) {
            $msg =  "Error al crear o actualizar familiar por medio de la beca: " . $ex->getMessage();

            echo "$msg";
            return $msg;
        }
    }

    /**
     * Mostrar lista de familiares activos.
     *
     * @return \Illuminate\Http\Response $response
     */
    public function index(Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $list = Beca1TutorData::where('beca_2_family_data.active', true)
                ->join('disabilities', 'beca_2_family_data.disability_id', '=', 'disabilities.id')
                ->select('beca_2_family_data.*', 'disabilities.disability', 'disabilities.description')
                ->orderBy('beca_2_family_data.id', 'desc')->get();
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Peticion satisfactoria | Lista de familiares.';
            $response->data["result"] = $list;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }

    /**
     * Mostrar listado para un selector.
     *
     * @return \Illuminate\Http\Response $response
     */
    public function selectIndex(Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $list = Beca1TutorData::where('beca_2_family_data.active', true)
                ->select('beca_2_family_data.id as id', 'beca_2_family_data.name as label')
                ->orderBy('beca_2_family_data.name', 'asc')->get();
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Peticion satisfactoria | Lista de familiares';
            $response->data["result"] = $list;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }


    /**
     * Mostrar familiar.
     *
     * @param   int $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function show(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            // $field = 'beca_2_family_data.id';
            // $value = $request->id;
            // if ($request->curp) {
            //     $field = 'beca_2_family_data.curp';
            //     $value = $request->curp;
            // }

            $student_data = Beca2FamilyData::find($request->id);
            // $student_data = Beca2FamilyData::where("$field", "$value")
            //     ->join('disabilities', 'beca_2_family_data.disability_id', '=', 'disabilities.id')
            //     ->select('beca_2_family_data.*', 'disabilities.disability', 'disabilities.description')
            //     ->first();

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | familiar encontrada.';
            $response->data["result"] = $student_data;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }



    /**
     * Eliminar (cambiar estado activo=false) familiar.
     *
     * @param  int $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function destroy(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            Beca2FamilyData::find($request->id)
                ->update([
                    'active' => false,
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | familiar eliminado.';
            $response->data["alert_text"] = 'Familiar eliminado';
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
}
