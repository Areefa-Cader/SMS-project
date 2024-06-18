<?php

namespace App\Http\Controllers;

use App\Models\Services;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function storeService(){
        $service = Services::all();
        return response()->json($service);
    }

    public function addService(Request $request){
        try{
        $serviceName = Services::where('service_name', $request->input('service_name'))->first();
        if(!is_null($serviceName)){
           return response()->json(['message'=>'Service is already exist']);
        }else{
            $service = new Services([
               'service_name'=>$request->input('service_name'),
               'service_category'=>$request->input('service_category'),
               'duration'=>$request->input('duration'),
               'price'=>$request->input('price') 
            ]);
            $service->save();
            return response()->json(['message'=>'Service was added successfully']);
        }
    
    }
    catch(\Exception $error){
        return response()->json(['error'=>$error->getMessage()],500);
    }
    }

    //update service

    public function updateService(Request $request, $id){
        try{
        $service = Services::find($id);
        if(is_null($service)){
            return response()->json(['message'=>'Service is not found']);
        }
        else{
            $service->update([
                'service_name'=>$request->input('service_name'),
                'service_category'=> $request->input('service_category'),
                'duration'=>$request->input('duration'),
                'price'=> $request->input('price')
            ]);
            return response()->json(['message'=>'successfully updated']);
        }

        }
        catch(\exception $error){
            return response()->json(['error'=>$error->getMessage()]);
        }
    }

    //delete service

    public function deleteService($id){
        try{
        $service = Services::find($id);
        if(is_null($service)){
            return response()->json(['message'=>'service is not found'],404);
        }else{
            $service->delete();
            return response()->json(['message'=> 'service was deleted']);
        }
    }catch(\Exception $error){
        return response()->json(['error'=>$error->getMessage()],500);
    }
}
}
