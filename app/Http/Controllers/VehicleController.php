<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required',
            'registration' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $vehicle = Vehicle::create([
            'vehicle_type' => $request->vehicle_type,
            'registration' => $request->registration,
            'status' => 'available'
        ]);

        if( !$vehicle) {
            return response()->json([
                'status' => false,
                'message' => 'Could not log vehicle. Server error.'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Vehicle created!'
        ], 200);
    }
    
    public function index() {
        $data = Vehicle::select('vehicle_type', 'registration', 'status', 'id')
            ->orderBy('id', 'DESC')
            ->get();
        
        return response()->json([
            'status' => true,
            'data' => $data,
            'headers' => ['vehicle_type', 'registration', 'status'],
            'totals' => count($data)
        ], 200);
    }

    public function show($id) {
        $data = Vehicle::select('vehicle_type', 'registration', 'id')->find($id);
        
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function update($id, Request $request) {
        $vehicle = Vehicle::find($id);

        if( !$vehicle) {
            return response()->json([
                'status' => false,
                'message' => 'No vehicle found under this ID.'
            ], 400);
        }

        if($request->status && $request->status != $vehicle->status) {
            $vehicle->status = $request->status;
        }

        if($request->vehicle_type && $request->vehicle_type != $vehicle->vehicle_type) {
            $vehicle->vehicle_type = $request->vehicle_type;
        }
        
        if($request->registration && $request->registration != $vehicle->registration) {
            $vehicle->registration = $request->registration;
        }        

        $saved = $vehicle->save();

        if( !$saved) {
            return response()->json([
                'status' => false,
                'message' => 'Could not update vehicle details. Server Error.'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Updated!'
        ], 200);
    }

    public function reports() {
        $total = Vehicle::count();

        $available = Vehicle::where('status', 'available')->count();

        $loading = Vehicle::where('status', 'loading')->count();

        $on_transit = Vehicle::where('status', 'on transit')->count();

        return response()->json([
            'status' => true,
            'data' => [
                'total' => $total,
                'available' => $available,
                'loading' => $loading,
                'on_transit' => $on_transit
            ]
        ], 200);
    }

    public function delete($id) {
        $vehicle = Vehicle::find($id);

        if( !$vehicle) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle not found'
            ], 404);
        }

        $deleted = $vehicle->delete();

        if( !$deleted) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle not deleted.'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Vehicle deleted.'
        ], 200);
    }
}
