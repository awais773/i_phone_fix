<?php

namespace App\Http\Controllers\api;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Fault;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    public function index()
    {
        $data = Customer::with('color','modal','fault')->get();
        if (is_null($data)) {
            return response()->json('data not found',);
        }
        return response()->json([
            'success' => true,
            'message' => 'All Data susccessfull',
            'data' => $data,
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                // 'message' => $validator->errors()->toJson()
                'message' => 'Email already exist',

            ], 400);
        } {
       $driver = Customer::create($request->post());
      $driver->save();
            return response()->json([
                'success' => true,
                'message' => 'driver Create successfull',
                  'data'  =>$driver,
            ], 200);
        }
    }

    public function show($id)
    {
        $program = Customer::with('color','modal','fault')->where('id',$id)->first();
        if (is_null($program)) {
            return response()->json('Data not found', 404);
        }
        return response()->json([
            'success' => true,
            'data' => $program,
        ]);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // 'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $data = Customer::find($id);
        $data->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully.',
            'data' => $data,
        ], 200);
    }

    public function destroy($id)
    {
        $program = Customer::find($id);
        if (!empty($program)) {
            $program->delete();
            return response()->json([
                'success' => 'True',
                'message' => ' delete successfuly',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'something wrong try again ',
            ]);
        }
    }



    
    public function Dependecy()
    {
        $modal = Brand::get();
        $Color = Color::get();
        $Fault = Fault::get();
        return response()->json([
            'success' => true,
            'message' => 'All Data susccessfull',
            'modal' => $modal,
            'color' => $Color,
            'fault' => $Fault,
        ]);
    }

}
