<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SlideRequest; // Ensure this request exists
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function index()
    {
        $data['sliders'] = Slider::orderBy('id', 'DESC')->paginate(5);
        return view('admin.pages.sliders.index', compact('data'));
    }

    public function create()
    {
        return view('admin.pages.sliders.create');
    }

    public function store(SlideRequest $request)
    {
        try {
            DB::beginTransaction();

            $slider = new Slider();
            $slider->title = $request->title;

            if ($request->hasFile('image')) {
                $slider->image = $request->file('image')->store('sliders', 'public');
            }

            $slider->link = $request->link;
            $slider->save();

            DB::commit();
            return redirect()->route('sliders.index')->with('status_succeed', 'Slider added successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->with('status_failed', 'An error occurred!');
        }
    }

    public function edit($id)
    {
        $data['slider'] = Slider::findOrFail($id);
        return view('admin.pages.sliders.edit', compact('data'));
    }

    public function update(SlideRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $slider = Slider::findOrFail($id);
            $slider->title = $request->title;

            if ($request->hasFile('image')) {
                Storage::disk('public')->delete($slider->image);
                $slider->image = $request->file('image')->store('sliders', 'public');
            }

            $slider->link = $request->link;
            $slider->save();

            DB::commit();
            return redirect()->route('sliders.index')->with('status_succeed', 'Slider updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->with('status_failed', 'An error occurred!');
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $slider = Slider::findOrFail($id);
            Storage::disk('public')->delete($slider->image);
            $slider->delete();

            DB::commit();
            return redirect()->route('sliders.index')->with('status_succeed', 'Slider deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->with('status_failed', 'An error occurred!');
        }
    }
}
