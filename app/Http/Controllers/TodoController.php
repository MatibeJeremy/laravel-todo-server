<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Http\Controllers\API\v1\BaseController as BaseController;
use Carbon\Carbon;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Todo as TodoResource;

class TodoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $todos = Todo::all();
        return $this->sendResponse(TodoResource::collection($todos), 'Todos fetched.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $id = auth('sanctum')->user()->id;
        $validator = Validator::make($input, [
            'todo' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $todo = Todo::create(array_merge($input,['user_id' => $id]));
        return $this->sendResponse(new TodoResource($todo), 'Todo created.');
    }

    /**
     * Display the specified resource.
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function show($todo)
    {
        $todo = Todo::where('id', $todo)->first();
        if (is_null($todo)) {
            return $this->sendError('Todo does not exist.');
        }
        return $this->sendResponse(new TodoResource($todo), 'Todo fetched.');
    }

    /**
     * Display the specified resource.
     *
     * @param
     * @return \Illuminate\Http\JsonResponse
     */
    public function show_date($date_)
    {
        $string = str_replace(' ', '-', $date_);
        $date =Carbon::parse($string)->format('Y-m-d');
        $todos = Todo::whereDate('created_at', $date)->get();

        return response()->json([
            'date' => $date,
            'data' => $todos
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Todo $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todo $todo)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'todo' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());
        }

        $todo->todo = $input['todo'];
        $todo->memo = $input['memo'];
        $todo->save();

        return $this->sendResponse(new TodoResource($todo), 'Todo updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Todo $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();
        return $this->sendResponse([], 'Todo deleted.');
    }
}
