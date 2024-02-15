<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Task::query()->orderBy('id', 'desc');
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '<a href="'. route('user.task.show', ['task' => $row->id]) .'" class="edit btn btn-primary btn-sm mx-2 view-btn" data-title="View Task">View</a>';
                        $btn .= '<a href="'. route('user.task.edit', ['task' => $row->id]) .'" class="edit btn btn-success btn-sm mx-2 add-data" data-title="Edit Task">Edit</a>';
                        $btn .= '<a href="'. route('user.task.destroy', ['task' => $row->id]) .'" class="edit btn btn-danger btn-sm delete-btn">Delete</a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('task.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('task.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all(), $request->title, $request->file('file'));
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'priority' => 'required',
        ];

        $messages = [
            'required' => 'Required.',
        ];

        if (!isset($request->id)) {
            $rules['file'] = 'required';
        }
        
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'errors' => $validator->getMessageBag(),
                'success' => false,
                'alert' => false,
            ]);
        }
        else {
            try {
                DB::transaction(function() use ($request) {

                    if (isset($request->id) && !isset($request->file)) {
                        $task = Task::where(['id' => $request->id])->first();
                        $filename = $task->image;
                    }
                    else {
                        if($request->hasFile('file')) {
                            $file = $request->file('file');
                            $extension = $file->getClientOriginalName();
                            $filename = time().".".$extension;
                        }

                        //store in DROPBOX
                        Storage::disk('dropbox')->put('WebFiles', $file);
                    }
                    // $path = $file->storeAs('uploads', $file->getClientOriginalName(), 'dropbox');
        
                    Task::updateorcreate([
                        'id' => $request->id
                    ],[
                        'title' => $request->title,
                        'description' => $request->description,
                        'image' => isset($filename) ? $filename : "",
                        'priority' => $request->priority,
                        'completed' => isset($request->completed) ? $request->completed : '0',
                    ]);
                });

                return response()->json([
                    'error' => false,
                    'errors' => [],
                    'success' => true,
                    'msg' => isset($request->id) ? 'Task Updated Successfully.' : 'Task Created Successfully.'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => true,
                    'errors' => json_encode($e->getMessage()),
                    'success' => false,
                    'msg' => 'Sometihing went wrong.',
                    'alert' => true,
                ]);
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::where(['id' => $id])->first();
        return view('task.view', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::where(['id' => $id])->first();
        return view('task.create', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Task::where(['id' => $id])->delete();

        return response()->json([
            'error' => false,
            'errors' => [],
            'success' => true,
        ]);
    }

    public function status(Request $request)
    {
        Task::where(['id' => $request->id])->update([
            'completed' => $request->value
        ]);

        return response()->json([
            'error' => false,
            'errors' => [],
            'success' => true,
            'msg' => 'Status Changes Successfully.'
        ]);
    }

    public function feature_function()
    {
        $low_task = Task::where(['priority' => '0'])->get();
        $med_task = Task::where(['priority' => '1'])->get();
        $high_task = Task::where(['priority' => '2'])->get();
        return view('task.feature', compact('low_task', 'med_task', 'high_task'));
    }

    public function priority_change(Request $request)
    {
        Task::where(['id' => $request->id])->update([
            'priority' => $request->priority
        ]);
    }
}
