<?php

namespace App\Http\Controllers;

use App\Models\patient_medicine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class PatientMedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = patient_medicine::all();
        return $data;
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
        $time = now();
        $date = new Carbon( $time ); 

        $latestorder = patient_medicine::where('patient_id', $request['patient_id'])->where('medicine_id', $request['medicine_id'])->count();
       
        $currentId =  $request['patient_id'] . $request['medicine_id'] .'-'. $latestorder;


        if( !empty( patient_medicine::select('patientMedicine_id')->where('patientMedicine_id', $currentId)->first()->patientMedicine_id )){
        do{
            $latestorder++;
            $depId = $request['patient_id'] . $request['medicine_id'] .'-'. $latestorder;
            $id = patient_medicine::select('patientMedicine_id')->where('patientMedicine_id', $depId)->first();
         
        }while(!empty($id));
    }

        $newId =  $request['patient_id'] . $request['medicine_id'] .'-'. $latestorder;

        patient_medicine::insert([
            'patientMedicine_id' =>  $newId,
            'patientMedicineDate' => $request['patientMedicineDate'],
            'medicine_frequency' => $request['medicine_frequency'],
            'patient_id' => $request['patient_id'],
            'medicine_id' => $request['medicine_id'],

            'created_at' => now(),
            'updated_at' => now(),
        ]);
        

        $medicineController = new medicineController;
        $medName = $medicineController->getMedNamebyId($request['medicine_id']);

        $patientController = new patientController;
        $patient = $patientController->getPatientbyId($request['patient_id']);
        $patientName = $patient['patient_lName'] .', '. $patient['patient_fName'] .' '. $patient['patient_mName'];

        $action ='added a new medicine-'. $medName .' for patient-'. $patientName;
        $user = Auth::user();
        if($user['role'] != 'admin'){
        $log = new ResActionLogController;
        $log->store(Auth::user(), $action);
        }
        return response('stored');
    }

    /**
     * Display the specified resource.
     */
    public function show(patient_medicine $patient_medicine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(patient_medicine $patient_medicine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        patient_medicine::where('patientMedicine_id', $id)->update(
            [
                'patientMedicine_id' => $request['patientMedicine_id'],
                'patientMedicineDate' => $request['patientMedicineDate'],
                'patientMedicine_frequency' => $request['patientMedicine_frequency'],
                'patient_id' => $request['patient_id'],
                'medicine_id' => $request['medicine_id'],

                'updated_at' => now(),
            ]);

        $action ='updated a medicine-'. $request['medicine_id'] .' for patient-'. $request['patient_id'];
        $user = Auth::user();
        if($user['role'] != 'admin'){
        $log = new ResActionLogController;
        $log->store(Auth::user(), $action);
        }
        return response('updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $meds = patient_medicine::where('patientMedicine_id', $id)->first();

        $medicineController = new medicineController;
        $medicineName =  $medicineController->getMedNamebyId($meds['medicine_id']);

        $patientController = new patientController;
        $patient = $patientController->getPatientbyId($meds['patient_id']);


        $patientName = $patient['patient_lName'] .', '. $patient['patient_fName'] .' '. $patient['patient_mName'];
        $action ='deleted a medicine-'. $medicineName . 'for patient-'.  $patientName;


        $user = Auth::user();
        if($user['role'] != 'admin'){
        $log = new ResActionLogController;
        $log->store(Auth::user(), $action);
        }


        patient_medicine::destroy($id);
        return response('deleted');
    }
}
 