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
        $user_id = auth('sanctum')->user()->id;
        $todos = Todo::where('user_id',$user_id);
        $todos = Todo::select('*')->where([['pinned',0],['user_id' ,$user_id]])->get();
        return $this->sendResponse(TodoResource::collection($todos), 'Todos fetched.');
    }

    /**
     * Display pinned todos
     *
     * @return \Illuminate\Http\Response
     */
    public function pinned($date_)
    {
        $string = str_replace(' ', '-',$date_);
        $date =Carbon::parse($string)->format('Y-m-d');
        $user_id = auth('sanctum')->user()->id;
        $todos = Todo::select('*')->where([['user_id',$user_id],['pinned' ,1],['created_at',$date]])->get();
        return $this->sendResponse(TodoResource::collection($todos), 'Pinned Todos fetched.');
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
        $date = Carbon::parse($input['date']);
        $todo = new Todo;
        $todo->memo = $input['memo'];
        $todo->todo = $input['todo'];
        $todo->created_at = $date;
        $todo->user_id = $id;
        $todo->save();
        return $this->sendResponse(new TodoResource($todo), 'Todo created.');
    }

    /**
     * Pin to do on top of div
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function pin_todo($id){

        $todo=Todo::find($id);
        $todo->pinned = 1;
        $todo->save();
        return $this->sendResponse($todo, 'Todo pinned.');
    }

    /**
     * Unpin from the top of div
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function unpin_todo($id){

        $todo=Todo::find($id);
        $todo->pinned = 0;
        $todo->save();
        return $this->sendResponse($todo, 'Todo unpinned.');
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
        $string = str_replace(' ', '-',$date_);
        $date =Carbon::parse($string)->format('Y-m-d');
        $id = auth('sanctum')->user()->id;
        $todos = Todo::select('*')->where([['created_at',$date],['user_id' ,$id],['pinned',0]])->get();

        return response()->json([
            'date' => $date,
            'data' => $todos
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function weeks_data($date_)
    {
        $string = str_replace(' ', '-',$date_);
        $date =Carbon::parse($string)->format('Y-m-d');
        $sub_date = Carbon::today()->subDays(7);
        $id = auth('sanctum')->user()->id;
        $todos = Todo::whereBetween('created_at',[$sub_date,$date])->where('user_id',$id)->orderBy('created_at')->get();

        return $this->sendResponse(TodoResource::collection($todos), 'Todos fetched.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function postMemo(Request $request)
    {
        $todo=Todo::find($request->id);
        $todo->memo = $request->memo;
        $todo->save();


        return $request->all();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $todo=Todo::find($id);
        $todo->delete();
        return $this->sendResponse($todo, 'Todo deleted.');
    }
}
