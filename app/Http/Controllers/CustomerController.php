<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use Error;
use Illuminate\Http\Request;

use function Laravel\Prompts\error;

class CustomerController extends Controller
{
    // Get Customer
    public function getCustomer()
    {   
        // $customer = Customers::all();
        //     return response()->json($customer);
        // } catch (\Exception $error) {
        //     return response()->json(["error" => $error->getMessage()], 500);
        // }

        return response()->json(Customers::all(),200);
    }


    // Add Customer
    public function addCustomer(Request $request)
    {
        try {
            $email = Customers::where('email', $request->input('email'))->first();
            if (is_null($email)) {
                $customer = new Customers([
                    'fullname' => $request->input('fullname'),
                    'email' => $request->input('email'),
                    'contact_no' => $request->input('contact_no'),
                    'gender'=>$request->input('gender'),
                    'address'=>$request->input('address'),
                ]);
                
                $customer->save();
                return response()->json(['message' => 'Successfully Added']);
            } else {
                return response()->json(['error' => 'email is already exist']);
            }
        } catch (\Exception $error) {
            return response()->json(["error" => $error->getMessage()], 500);
        }
    }

    //update Customer

    public function updateCustomer(Request $request , $id){
      try{
        $customer = Customers::find($id);
        if(is_null($customer)){
           return response()->json(['message'=>'customer was not found'],404); 
        }else{
            $customer->update([
              'fullname'=>$request->input('fullname'),
              'email'=>$request->input('email'),
              'contact_no'=>$request->input('contact_no'),
              'gender'=>$request->input('gender'),
              'address'=>$request->input('address'),
            ]);
          return response()->json(['message'=>'customer was updated']);
        }
      }
      catch(\Exception $error){
         return response()->json(['error'=>$error->getMessage()],500);
      }  
    }

    //delete customer

    public function deleteCustomer($id){
        try{
        $customer = Customers::find($id);
        if(is_null($customer)){
            return response()->json(['message'=>'customer was not found'],404);
        }else{
           $customer->delete();
           return response()->json(['message'=>'customer was deleted']); 
        }
    }
    catch(\Exception $error){
        return response()->json(['error'=>$error->getMessage()],500);
    }
}
 // get customer by id

    public function getCustomerById($id){
        try{
            $customer = Customers::find($id);
            if(is_null($customer)){
                return response()->json(['message'=>'customer is not found'],404);
            }
            return response()->json(['customer'=>$customer]);
        }catch(\Exception $error){
            return response()->json(['error'=>$error->getMessage()],500);
        }
    }
}
