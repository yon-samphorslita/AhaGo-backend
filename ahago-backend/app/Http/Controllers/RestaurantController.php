<?php

namespace App\Http\Controllers;

use App\Models\RestaurantProfile;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    // GET /api/rests
    public function getAllRests() {
        return RestaurantProfile::all();
    }
    // Post a new restaurant
    // protected $fillable = [
    //     'name',
    //     'user_id',
    //     'working_hours',
    //     'description'
    // ];

    // POST /api/rests
    public function createRest(Request $request) {
        // validate request
        $validated = $request->validate([
            'name' => 'required|string',
            'user_id' => 'required',
        ]);
        // add to db
        $rest = RestaurantProfile::create($validated);
        // return success
        return response()->json([
            'message' => 'Restaurant created successfully',
            'data' => $rest
        ], 200);
    }

    // GET /api/rests/{restId}
    public function getRest($restId)
    {
        $rest = RestaurantProfile::with('user')->find($restId);

        if (!$rest) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }

        return response()->json([
            'message' => "Restaurant #$restId fetched successfully",
            'data' => $rest
        ]);
    }
    // PATCH /api/rests/{restId}
    public function updateRest(Request $request, $restId)
    {
        // validate request
        $validated = $request->validate([
            'name' => 'required|string',
            'working_hours' => 'string',
            'description' => ['nullable', 'string'],
        ]);
        // find rest by id
        $rest = RestaurantProfile::find($restId);
        if (!$rest) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }
        // update data
        $rest->name = $validated['name'];
        $rest->working_hours = $validated['working_hours'];
        $rest->description = $validated['description'];
        // save to db
        $rest->save();
        // return success
        return response()->json([
            'message' => "Restaurant #$restId updated successfully",
            'data' => $rest
        ]);
    }
    // DELETE /api/rests/{restId}
    public function deleteRest($restId)
    {
        $rest = RestaurantProfile::find($restId);

        if (!$rest) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }

        $rest->delete();

        return response()->json([
            'message' => "Restaurant #$restId deleted successfully"
        ]);
    }
    
}
