<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DriverSection;

class DriverSectionController extends Controller
{
    public function getSections(){
        $sections = DriverSection::with('buttons.descriptions')->get();
        return response()->json($sections);
    }

    public function createSection(Request $request){
        $validated = $request->validate([
            'title'=>'nullable|string',
            'text'=>'nullable|string',
            'link_to'=>'nullable|string',
            'buttons'=>'nullable|array',
            'buttons.*.img_src'=>'nullable|string',
            'buttons.*.name'=>'nullable|string',
            'buttons.*.text'=>'nullable|string',
            'buttons.*.link'=>'nullable|string',
            'buttons.*.descriptions'=>'nullable|array',
            'buttons.*.descriptions.*.title'=>'nullable|string',
            'buttons.*.descriptions.*.text'=>'nullable|string',
            'buttons.*.descriptions.*.svg'=>'nullable|string',
        ]);

        $section = DriverSection::create($validated);

        if(!empty($validated['buttons'])) {
            foreach ($validated['buttons'] as $btn){
                $button = $section->buttons()->create($btn);

                if(!empty($validated['descriptions'])) {
                    $button->descriptions()->createMany($btn['description']);
                }
            }
        }

        return response()->json($section->load('buttons.descriptions'), 201);
    }
}
