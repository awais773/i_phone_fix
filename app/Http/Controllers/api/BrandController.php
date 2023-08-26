<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{

    public function index()
    {
        $data = Brand::get();
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
       $Brand = Brand::create($request->post());
      $Brand->save();
            return response()->json([
                'success' => true,
                'message' => 'Brand Create successfull',
                  'data'  =>$Brand,
            ], 200);
        }
    }

    public function show($id)
    {
        $Brand = Brand::find($id);
        if (is_null($Brand)) {
            return response()->json('Data not found', 404);
        }
        return response()->json([
            'success' => true,
            'data' => $Brand,
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
        $data = Brand::find($id);
        $data->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully.',
            'data' => $data,
        ], 200);
    }

    public function destroy($id)
    {
        $Brand = Brand::find($id);
        if (!empty($Brand)) {
            $Brand->delete();
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

}
