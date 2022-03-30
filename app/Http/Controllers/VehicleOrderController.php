<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\VehicleOrder;
use App\Models\Order;
use App\Models\Vehicle;

class VehicleOrderController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|numeric|exists:vehicles,id',
            'order_id' => 'required|numeric|exists:orders,id'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        // check if vehicle available
        $vehicle = Vehicle::find($request->vehicle_id);

        if($vehicle->status != 'available') {
            return response()->json([
                'status' => false,
                'message' => 'Please pick another car. This one is not available'
            ], 400);
        }

        $vehicle_order = VehicleOrder::create([
            'order_id' => $request->order_id,
            'vehicle_id' => $request->vehicle_id
        ]);

        if( !$vehicle_order) {
            return response()->json([
                'status' => false,
                'message' => 'Could not assign vehicle to order . Server error.'
            ], 500);
        }

        $order = Order::find($request->order_id);

        $order->status = 'loading';

        $order->save();


        $vehicle = Vehicle::find($request->vehicle_id);

        $vehicle->status = 'loading';

        $vehicle->save();

        return response()->json([
            'status' => true,
            'message' => 'Assigned vehicle to order!'
        ], 200);
    }
}
