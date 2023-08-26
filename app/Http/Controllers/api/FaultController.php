<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Fault;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class FaultController extends Controller
{

    public function index()
    {
        $data = Fault::with('color','modal')->get();
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
       $Fault = Fault::create($request->post());
      $Fault->save();
            return response()->json([
                'success' => true,
                'message' => 'Fault Create successfull',
                  'data'  =>$Fault,
            ], 200);
        }
    }

    public function show($id)
    {
        $Fault = Fault::with('color','modal')->where('id',$id)->first();
        if (is_null($Fault)) {
            return response()->json('Data not found', 404);
        }
        return response()->json([
            'success' => true,
            'data' => $Fault,
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
        $data = Fault::find($id);
        $data->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Fault updated successfully.',
            'data' => $data,
        ], 200);
    }

    public function destroy($id)
    {
        $Fault = Fault::find($id);
        if (!empty($Fault)) {
            $Fault->delete();
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


    public function faultDependecy()
    {
        $modal = Brand::get();
        $Color = Color::get();
        return response()->json([
            'success' => true,
            'message' => 'All Data susccessfull',
            'modal' => $modal,
            'color' => $Color,
        ]);
    }

}
