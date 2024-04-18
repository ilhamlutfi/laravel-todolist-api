<?php

namespace App\Http\Controllers\Api;

use App\Models\Todo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use Exception;
// use App\Models\Log;
use Illuminate\Support\Facades\Log;
use Throwable;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Log::record(auth()->user(), 'Accessed Todo List', 'GET');

        try {
            $todolist = Todo::latest()->get();
            Log::channel('stack')->info("Accessed Todo List");
            Log::channel('slack')->info("Accessed Todo List");
        } catch (Exception $error) {
            Log::channel('stack')->error("Failed : ", ['message' => $error->getMessage()]);
            Log::channel('slack')->error("Failed : ", ['message' => $error->getMessage()]);
        }

        return TodoResource::collection($todolist);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'completed' => 'required|in:0,1',
        ]);

        $todo = Todo::create($request->all());

        return response()->json([
            'message' => 'Todo Created Sucessfully',
            'data' => new TodoResource($todo)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $todo = Todo::find($id);

        if ($todo == null) {
            return response()->json([
                'message' => 'Todo Not Found',
            ], 404);
        }

        return response()->json([
            'message' => 'Todo Retrieved Successfully',
            'data' => new TodoResource($todo)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $todo = Todo::find($id);

        if ($todo == null) {
            return response()->json([
                'message' => 'Todo Not Found',
            ], 404);
        } else {
            $request->validate([
                'title' => 'required|min:3|max:255',
                'description' => 'required|min:3|max:255',
                'completed' => 'required|in:0,1',
            ]);

            $todo->update($request->all());

            return response()->json([
                'message' => 'Todo Updated Sucessfully',
                'data' => new TodoResource($todo)
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $todo = Todo::find($id);

        if ($todo == null) {
            return response()->json([
                'message' => 'Todo Not Found',
            ], 404);
        }

        $todo->delete();

        return response()->json([
            'message' => 'Todo Deleted Sucessfully',
        ]);
    }
}
