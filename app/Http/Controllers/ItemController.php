<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Item;


class ItemController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $item = Item::create([
            'name' => $request->name,
            'price' => $request->price
        ]);

        if( !$item) {
            return response()->json([
                'status' => false,
                'message' => 'Could not create item. Server error.'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Item created!'
        ], 200);
    }
    
    public function index() {
        $data = Item::select('name', 'price', 'id')
            ->orderBy('id', 'DESC')
            ->get();
        
        return response()->json([
            'status' => true,
            'data' => $data,
            'headers' => ['name', 'price'],
            'totals' => count($data)
        ], 200);
    }

    public function show($id) {
        $data = Item::select('name', 'price')->find($id);
        
        return response()->json([
            'status' => true,
            'data' => $data,
            'total' => count($data)
        ], 200);
    }
}
