<?php

namespace App\Http\Controllers;

use App\Models\phr_attributeValues;
use App\Models\phr_categoryAttributes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class PhrAttributeValuesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = phr_attributeValues::all();

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $attributes = phr_categoryAttributes::all();


        
       



        foreach($attributes as $attribute){
      
        
            $id = $request['patient_id'] . '-' . $attribute['categoryAtt_id'];

            if(!empty($request[$attribute['categoryAtt_name']])){
                phr_attributeValues::insert([
           
                    'attributeVal_id' => $id,
                    'attributeVal_values' => $request[$attribute['categoryAtt_name']],
                    'patient_id' => $request['patient_id'],
                    'categoryAtt_id' => $attribute['categoryAtt_id'],

            
                     'created_at' => now(),
                     'updated_at' => now(),
            ]);
        }else{
            phr_attributeValues::insert([
           
                'attributeVal_id' => $id,
                'attributeVal_values' => 0,
                'patient_id' => $request['patient_id'],
                'categoryAtt_id' => $attribute['categoryAtt_id'],

        
                 'created_at' => now(),
                 'updated_at' => now(),
        ]);


            
        }
    }
        $action ='added a new categoryAttribute';
        app('App\Http\Controllers\resActionLogController')->store(Auth::user(), $action);
       return response()->json('stored');
    
        
    }

    /**
     * Display the specified resource.
     */
    public function show($patient_id)
    {
        $patient = phr_attributeValues::where('patient_id', $patient_id)->get();


        return response()->json($patient);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, phr_attributeValues $phr_attributeValues)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $action ='deleted an attribute value-'. $id;
        app('App\Http\Controllers\resActionLogController')->store(Auth::user(), $action);

        phr_attributeValues::destroy($id);

       
        return response('deleted');
    }




}