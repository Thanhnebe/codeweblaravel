<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeightRequest;
use App\Models\Weight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WeightController extends Controller
{
    public function index()
    {
        $data['weights'] = Weight::orderBy('id', 'DESC')->paginate(5);

        return view('admin.pages.weights.index', compact('data'));
    }

    public function weights()
    {
        $weights = Weight::orderBy('id', 'ASC')->get();

        return response()->json($weights);
    }

    public function create()
    {
        return view('admin.pages.weights.create');
    }

    public function store(WeightRequest $request)
    {
        try {
            DB::beginTransaction();

            $weightModel = new Weight();
            $weightModel->weight = $request->weight;
            $weightModel->unit = $request->unit;
            $weightModel->save();

            DB::commit();

            return redirect()->route('weights.index')->with('status_succeed', 'Thêm trọng lượng thành công');
        } catch (\Exception $e) {
            DB::rollback();

            Log::error($e->getMessage());

            return back()->with('status_failed', 'Đã xảy ra lỗi!');
        }
    }

    public function edit($id)
    {
        $data['weight'] = Weight::find($id);
        return view('admin.pages.weights.edit', compact('data'));
    }

    public function update(WeightRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $weightModel = Weight::find($id);
            $weightModel->weight = $request->weight;
            $weightModel->unit = $request->unit;
            $weightModel->save();

            DB::commit();

            return redirect()->route('weights.index')->with('status_succeed', 'Cập nhật trọng lượng thành công');
        } catch (\Exception $e) {
            DB::rollback();

            Log::error($e->getMessage());

            return back()->with('status_failed', 'Đã xảy ra lỗi!');
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $weight = Weight::find($id);

            $weight->delete();

            DB::commit();

            return redirect()->route('weights.index')->with('status_succeed', 'Xóa trọng lượng thành công');
        } catch (\Exception $e) {
            DB::rollback();

            Log::error($e->getMessage());

            return back()->with('status_failed', 'Đã xảy ra lỗi!');
        }
    }
}
