<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::select('id', 'name', 'description', 'price', 'image', 'category', 'meal_time', 'rating')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'message' => 'Menus fetched successfully',
            'data' => $menus,
        ]);
    }
}
