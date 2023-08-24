<?php

namespace App\Http\Controllers;
use File;
use App\Models\fileUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class FileUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $file = $request->file('file');
        $path = Storage::putFileAs(
            'file',  $file,  $file->getClientOriginalName()
        );



        $time = now();
        $date = new Carbon( $time ); 
        $latestorder = fileUpload::all()->count();
        $currentId = $date->year . $date->month  . 'FU' . $latestorder;


        if( !empty(fileUpload::select('file_id')->where('file_id', $currentId)->first()->file_id)){
        do{
            $latestorder++;
            $depId =  $date->year . $date->month  . 'FU' . $latestorder;
            $id = fileUpload::select('file_id')->where('file_id', $depId)->first();
         
        }while(!empty($id));
    }

        $newId = $date->year . $date->month  . 'FU' . $latestorder;

        fileUpload::insert([
            'file_id' => $newId,
            'file_path' => $path,
            'file_name'=> $file->getClientOriginalName(),
            'file_size'=> $file->getSize(),
            'file_ext'=> $file->extension(),
            'patient_id' => $request['patient_id'],
            'resident_id'=> $request['resident_id'],

            'created_at' => now(),
        ]);


        return response()->json($file->getClientOriginalName().' file uploaded');

    }

    /**
     * Display the specified resource.
     */
    public function show(fileUpload $fileUpload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, fileUpload $fileUpload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
      

        $file = fileUpload::select('file_name')->where('file_id', $id)->first()->file_name;
        Storage::delete('file/'.  $file);
        fileUpload::destroy($id);

        return response()->json($file . ' has been Deleted');
    }
}