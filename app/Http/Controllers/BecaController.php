<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Beca;
use App\Models\BecasView;
use App\Models\ObjResponse;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\ModelsView;

class BecaController extends Controller
{
    /**
     * Actualizar beca por pagina o guardado temporal.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function saveBeca(Request $request, Response $response, Int $folio, Int $page)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $beca = Beca::where('folio', $folio)->first();
            if (!$beca) {
                // $response->data["message"] = 'peticion satisfactoria | Avance guardado.';
                $response->data["alert_text"] = 'Beca no encontrada';
                return response()->json($response, $response->data["status_code"]);
            }

            // if ($request->folio) $beca->folio = $request->folio;
            if ((int)$page === 1) {
                // error_log("PAGINA - 1 === $page");
                if ($request->tutor_id) $beca->user_id = $request->tutor_id;
                if ($request->tutor_data_id) $beca->tutor_data_id = $request->tutor_data_id;
            }
            if ((int)$page === 2) {
                // error_log("PAGINA - 2 === $page");
                if ($request->student_data_id) $beca->student_data_id = $request->student_data_id;
            }
            if ((int)$page === 3) {
                // error_log("PAGINA - 3 === $page");
                if ($request->school_id) $beca->school_id = $request->school_id;
                if ($request->grade) $beca->grade = $request->grade;
                if ($request->average) $beca->average = $request->average;
                if ($request->comments) $beca->comments = $request->comments;
                $beca->current_page = 4;
            }
            if ((int)$page === 4) {
                // error_log("PAGINA - 4 === $page");
                if ($request->extra_income) $beca->extra_income = $request->extra_income;
                if ($request->monthly_income) $beca->monthly_income = $request->monthly_income;
                if ($request->finish) $beca->current_page = 4;
            }
            if ((int)$page === 5) {
                // error_log("PAGINA - 5 === $page");
                if ($request->total_expenses) $beca->total_expenses = $request->total_expenses;
            }
            if ((int)$page === 8) {
                // error_log("PAGINA - 8 === $page");
                if ($request->under_protest) $beca->under_protest = $request->under_protest;
            }
            // if ($request->socioeconomic_study) $beca->socioeconomic_study = $request->socioeconomic_study;

            $beca->save();

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | Avance guardado.';
            $response->data["alert_text"] = 'Avance guardado';
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }

    //#region CRUD
    /**
     * Mostrar lista de becas activas.
     *
     * @return \Illuminate\Http\Response $response
     */
    public function index(Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $list = BecasView::all();
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Peticion satisfactoria | Lista de becas.';
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
            $list = Beca::where('active', true)
                ->select('becas.id as id', 'becas.folio as label')
                ->orderBy('becas.folio', 'asc')->get();
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'Peticion satisfactoria | Lista de becas';
            $response->data["result"] = $list;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }

    /**
     * Crear beca.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function create(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $tutorDataController = new Beca1TutorDataController();
            $studentDataController = new Beca1StudentDataController();
            $tutor_data = $tutorDataController->createOrUpdateByBeca($request);
            $student_data = $studentDataController->createOrUpdateByBeca($request);

            $folio = $this->getLastFolio($response);

            $new_beca = Beca::create([
                'folio' => (int)$folio + 1,
                'user_id' => $request->user_id,
                // 'single_mother' => $request->single_mother,
                'tutor_data_id' => $tutor_data->id,
                'student_data_id' => $student_data->id,
                'school_id' => $request->school_id,
                'grade' => $request->grade,
                'average' => $request->average,
                'extra_income' => $request->extra_income,
                'monthly_income' => $request->monthly_income,
                'total_expenses' => $request->total_expenses,
                'under_protest' => $request->under_protest,
                'comments' => $request->comments,
                // 'socioeconomic_study' => $request->socioeconomic_study,
            ]);
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | beca registrada.';
            $response->data["alert_text"] = 'Beca registrada';
            $response->data["result"] = $new_beca;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }

    /**
     * Mostrar beca.
     *
     * @param   int $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function show(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $beca = Beca::where('id', $request->id)->first();

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | beca encontrada.';
            $response->data["result"] = $beca;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }

    /**
     * Actualizar beca.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function update(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $beca = Beca::find($request->id)
                ->update([
                    'folio' => $request->folio,
                    'user_id' => $request->tutor_id,
                    // 'single_mother' => $request->single_mother,
                    'tutor_data_id' => $request->tutor_data_id,
                    'student_data_id' => $request->student_data_id,
                    'school_id' => $request->school_id,
                    'grade' => $request->grade,
                    'average' => $request->average,
                    'extra_income' => $request->extra_income,
                    'monthly_income' => $request->monthly_income,
                    'total_expenses' => $request->total_expenses,
                    'under_protest' => $request->under_protest,
                    'comments' => $request->comments,
                    'comments' => $request->comments,
                    'socioeconomic_study' => $request->socioeconomic_study,
                ]);

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | beca actualizada.';
            $response->data["alert_text"] = 'Beca actualizada';
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }

    /**
     * Eliminar (cambiar estado activo=false) beca.
     *
     * @param  int $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function destroy(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            Beca::find($request->id)
                ->update([
                    'active' => false,
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);
            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | beca eliminada.';
            $response->data["alert_text"] = 'Beca eliminada';
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    //#endregion CRUD


    /**
     * Mostrar beca por folio.
     *
     * @param   Int $folio
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function getBecaByFolio(Response $response, Int $folio)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $beca = BecasView::where('folio', $folio)->first();

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | beca encontrada.';
            $response->data["result"] = $beca;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }
    /**
     * Mostrar beca por folio.
     *
     * @param   Int $folio
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function _getBecaByFolio(Int $folio)
    {
        try {
            $beca = Beca::where('folio', $folio)->first();
            return $beca;
        } catch (\Exception $ex) {
            $msg =  "Error al obtener beca por folio: " . $ex->getMessage();
            error_log("$msg");
            return null;
        }
    }


    /**
     * Mostrar beca por folio.
     * Mostrar solo si el usuario logeado es el correspondiente a la beca o eres admin
     *
     * @param   int $id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function getBecasByUser(Request $request, Response $response)
    {
        $response->data = ObjResponse::DefaultResponse();
        try {
            $role_id = Auth::user()->role_id;
            // return "que tengo aqui? -> $role_id";
            $beca = BecasView::where('folio', $request->folio)->first();
            // $beca = Becas::where('folio', $request->folio)->first();

            $response->data = ObjResponse::CorrectResponse();
            $response->data["message"] = 'peticion satisfactoria | beca encontrada.';
            $response->data["result"] = $beca;
        } catch (\Exception $ex) {
            $response->data = ObjResponse::CatchResponse($ex->getMessage());
        }
        return response()->json($response, $response->data["status_code"]);
    }


    /**
     * Obtener el ultimo folio.
     *
     * @return \Illuminate\Http\Int $folio
     */
    private function getLastFolio()
    {
        try {
            $folio = Beca::max('folio');
            if ($folio == null) return 0;
            return $folio;
        } catch (\Exception $ex) {
            $msg =  "Error al crear o actualizar estudiante por medio de la beca: " . $ex->getMessage();
            echo "$msg";
            return $msg;
        }
    }
}
