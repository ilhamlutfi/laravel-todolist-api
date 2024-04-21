<?php

namespace App\Http\Controllers\Api;

use App\Models\Todo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use Exception;
use App\Models\Log as LogModel;
use Illuminate\Support\Facades\Log;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {
            $todolist = Todo::latest()->get();
            Log::channel('stack')->info("Accessed Todo List");
            Log::channel('slack')->info("Accessed Todo List");
            LogModel::record(auth()->user(), 'Accessed Todo List', 'GET');
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

    try {
        $todo = Todo::create($request->all());
        Log::channel('stack')->info("Todo List Created");
        Log::channel('slack')->info("Todo List Created");
        LogModel::record(auth()->user(), 'Todo List Created', 'POST');

        return response()->json([
            'message' => 'Todo Created Sucessfully',
            'data' => new TodoResource($todo)
        ], 201);
    } catch (Exception $error) {
        Log::channel('stack')->error("Failed : ", ['message' => $error->getMessage()]);
        Log::channel('slack')->error("Failed : ", ['message' => $error->getMessage()]);

        return response()->json([
            'message' => 'Failed To Create Todo',
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $todo = Todo::find($id);

        if ($todo == null) {
            Log::channel('stack')->warning("Todo List Not Found");
            Log::channel('slack')->warning("Todo List Not Found");
            LogModel::record(auth()->user(), 'Todo List Not Found', 'GET');

            return response()->json([
                'message' => 'Todo Not Found',
            ], 404);
        } else {
            Log::channel('stack')->info("Todo Retrieved Successfully");
            Log::channel('slack')->info("Todo Retrieved Successfully");

            LogModel::record(auth()->user(), 'Todo Retrieved Successfully', 'GET');
            return response()->json([
                'message' => 'Todo Retrieved Successfully',
                'data' => new TodoResource($todo)
            ]);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $todo = Todo::find($id);

        if ($todo == null) {
            Log::channel('stack')->warning("Todo List For Edit Not Found");
            Log::channel('slack')->warning("Todo List For Edit Not Found");
            LogModel::record(auth()->user(), 'Todo List For Edit Not Found', 'GET');

            return response()->json([
                'message' => 'Todo Not Found',
            ], 404);
        } else {
            $request->validate([
                'title' => 'required|min:3|max:255',
                'description' => 'required|min:3|max:255',
                'completed' => 'required|in:0,1',
            ]);

            try {
                $todo->update($request->all());

                Log::channel('stack')->info("Todo List Updated");
                Log::channel('slack')->info("Todo List Updated");
                LogModel::record(auth()->user(), 'Todo List Updated', 'PUT');

                return response()->json([
                    'message' => 'Todo Updated Sucessfully',
                    'data' => new TodoResource($todo)
                ]);
            } catch (Exception $error) {
                Log::channel('stack')->error("Failed : ", ['message' => $error->getMessage()]);
                Log::channel('slack')->error("Failed : ", ['message' => $error->getMessage()]);
                LogModel::record(auth()->user(), 'Todo List Failed Updated', 'PUT');

                return response()->json([
                    'message' => 'Todo List Failed Updated',
                ]);
            }


        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $todo = Todo::find($id);

        if ($todo == null) {
            Log::channel('stack')->warning("Todo List For Delete Not Found");
            Log::channel('slack')->warning("Todo List For Delete Not Found");
            LogModel::record(auth()->user(), 'Todo List For Delete Not Found', 'GET');

            return response()->json([
                'message' => 'Todo Not Found',
            ], 404);
        } else {


            try {
                $todo->delete();

                Log::channel('stack')->info("Todo List Deleted");
                Log::channel('slack')->info("Todo List Deleted");
                LogModel::record(auth()->user(), 'Todo List Deleted', 'DELETE');

                return response()->json([
                    'message' => 'Todo Deleted Sucessfully',
                ]);
            } catch (Exception $error) {
                Log::channel('stack')->error("Failed : ", ['message' => $error->getMessage()]);
                Log::channel('slack')->error("Failed : ", ['message' => $error->getMessage()]);
                LogModel::record(auth()->user(), 'Todo List Failed Deleted', 'DELETE');

                return response()->json([
                    'message' => 'Todo List Failed Deleted',
                ]);
            }
        }

    }
}
