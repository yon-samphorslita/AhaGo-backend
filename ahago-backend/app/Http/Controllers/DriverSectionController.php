<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DriverSection;
use App\Models\DriverSectionButton;
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

        if (!empty($validated['buttons'])) {
    foreach ($validated['buttons'] as $btn) {
        // Create button for the section
        $button = $section->buttons()->create($btn);

        // Check if button has descriptions and create them
        if (!empty($btn['descriptions'])) {
            $button->descriptions()->createMany($btn['descriptions']);
        }
    }
}
        return response()->json($section->load('buttons.descriptions'), 201);
    }

        public function getButtons(){
        $buttons = DriverSectionButton::with('descriptions')->get();
        return response()->json($buttons);
    }

    public function createButton(Request $request){
        $validated = $request->validate([
            'img_src'=>'nullable|string',
            'name'=>'nullable|string',
            'text'=>'nullable|string',
            'link'=>'nullable|string',
            'descriptions'=>'nullable|array',
            'descriptions.*.title'=>'nullable|string',
            'descriptions.*.text'=>'nullable|string',
            'descriptions.*.svg'=>'nullable|string',
        ]);

        $button = DriverSectionButton::create([
            'img_src'=>$validated['img_src'] ?? null,
            'name'=>$validated['name'] ?? null,
            'text'=>$validated['text'] ?? null,
            'link'=>$validated['link'] ?? null,
            'section_id'=> null,
        ]);

        if (!empty($validated['descriptions'])) {
            $button->descriptions()->createMany($validated['descriptions']);
        }
        return response()->json($button->load('descriptions'), 201);
    }
}
