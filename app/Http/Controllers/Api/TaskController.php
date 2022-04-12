<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Services\ValidateTaskServices;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort');
        $order = $request->input('order') ?? 'asc';
        $search = $request->input('search');
        $filterStatus = $request->input('filter_status');
        if ($request->input('filter_priority_from') || $request->input('filter_priority_to')) {
            $filterPriority = [$request->input('filter_priority_from') ?? 1, $request->input('filter_priority_to') ?? 5];
        } else {
            $filterPriority = [];
        }
        $user_id = 2;//Auth::id();
        $user = User::find($user_id);
        $task = $user->tasks()->where('parent_id', null)
            ->when($search, function ($query, $search) {
                return $query->whereFullText(['title', 'description'], $search);
            })
            ->when($filterStatus, function ($query, $filterStatus) {
                return $query->where('status', $filterStatus);
            })
            ->when($filterPriority, function ($query, $filterPriority) {
                return $query->whereBetween('priority', $filterPriority);
            })
            ->when($sort, function ($query, $sort) use ($order) {
                return $query->orderBy($sort, $order);
            })->get();

        return TaskResource::collection($task);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return TaskResource
     */
    public function store(TaskStoreRequest $request)
    {
        $user_id = 2;//Auth::id();
        $user = User::find($user_id);
        $task = $user->tasks()->create($request->validated())->fresh();
        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return TaskResource
     */
    public function show($id)
    {
        $user_id = 2;//Auth::id();
        $user = User::find($user_id);
        $task = $user->tasks()->findOrFail($id);

        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return TaskResource
     */
    public function update(ValidateTaskServices $validateTaskServices, TaskStoreRequest $request, int $id)
    {
        $user_id = 2;//Auth::id();
        $user = User::find($user_id);
        $task = $user->tasks()->findOrFail($id);
        $validateTaskServices->checkTasksStatus($request->status, $task);

        $task->update($request->validated());
        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $user_id = 2;//Auth::id();
        $user = User::find($user_id);
        $task = $user->tasks()->findOrFail($id);
        if ($task->status === Task::STATUS_TODO) {
            $task->delete();
            return response()->json([
                'message' => 'Task deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Completed task cannot be deleted.'
            ], 200);
        }
    }
}
