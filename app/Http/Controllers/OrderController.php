<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\VehicleOrder;
use App\Models\Vehicle;
use DB;


class OrderController extends Controller
{
    public function store(Request $request) {    
        DB::beginTransaction();

        try {     
            $order = Order::create([
                'user_id' => 2,
            ]);
            
            foreach($request->all() as $value) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $value['item_id']
                ]);
            }

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => 'Order not created.'.$e
            ], 500);
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Order created!'
        ], 200);
    }

    public function index() {
        $data = Order::select('status', 'id')
            ->with(['order_items' => function($query) {
                $query->select('order_id', 'name', 'price');
            }])
            ->orderBy('id', 'DESC')
            ->get();
        
        return response()->json([
            'status' => true,
            'data' => $data,
            'headers' => ['id', 'status'],
            'totals' => count($data)
        ], 200);
    }

    public function show($id) {
        $data = Order::select('status', 'id')
        ->with(['order_items' => function($query) {
            $query->select('order_id', 'name', 'price');
        }])
        ->orderBy('id', 'DESC')
        ->where('id', $id)
        ->get();
        
        return response()->json([
            'status' => true,
            'data' => $data,
            'total' => count($data)
        ], 200);
    }

    public function update($id, Request $request) {
        $order = Order::find($id);

        if( !$order) {
            return response()->json([
                'status' => false,
                'message' => 'No order found under this ID.'
            ], 400);
        }

        if($request->status && $request->status != $order->status) {
            $order->status = $request->status;

            if($request->status == 'dispatched') {
                // if dispatch, update vehicle status too
                $vehicle = VehicleOrder::select('vehicle_id')->where('order_id', $order->id)->first();

                // update vehicle status
                $vehicle = Vehicle::find($vehicle->vehicle_id);
                $vehicle->status = 'on transit';
                $vehicle->save();
            } else if($request->status == 'delivered') {
                // if dispatch, update vehicle status too
                $vehicle = VehicleOrder::select('vehicle_id')->where('order_id', $order->id)->first();

                // update vehicle status
                $vehicle = Vehicle::find($vehicle->vehicle_id);
                $vehicle->status = 'available';
                $vehicle->save();
            }
        }

        $saved = $order->save();

        if( !$saved) {
            return response()->json([
                'status' => false,
                'message' => 'Could not update order details. Server Error.'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Updated!'
        ], 200);
    }

    public function reports() {
        $total = Order::count();

        $pending = Order::where('status', 'pending')->count();

        $loading = Order::where('status', 'loading')->count();

        $dispatched = Order::where('status', 'dispatched')->count();
        
        $delivered = Order::where('status', 'delivered')->count(); 

        return response()->json([
            'status' => true,
            'data' => [
                'total' => $total,
                'pending' => $pending,
                'loading' => $loading,
                'dispatched' => $dispatched,
                'delivered' => $delivered,
            ]
        ], 200);
    }
}
