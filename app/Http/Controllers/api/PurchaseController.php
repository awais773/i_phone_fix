<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{

    public function index()
    {
        $data = Purchase::get();
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
       $Purchase = Purchase::create($request->post());
      $Purchase->save();
            return response()->json([
                'success' => true,
                'message' => 'Purchase Create successfull',
                  'data'  =>$Purchase,
            ], 200);
        }
    }

    public function show($id)
    {
        $Purchase = Purchase::find($id);
        if (is_null($Purchase)) {
            return response()->json('Data not found', 404);
        }
        return response()->json([
            'success' => true,
            'data' => $Purchase,
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
        $data = Purchase::find($id);
        $data->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Purchase updated successfully.',
            'data' => $data,
        ], 200);
    }

    public function destroy($id)
    {
        $Purchase = Purchase::find($id);
        if (!empty($Purchase)) {
            $Purchase->delete();
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
