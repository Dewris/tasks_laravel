<?php

namespace App\Services;

use App\Models\Task;

class ValidateTaskServices
{

    public function checkTasksStatus($newStatus, $task)
    {
        if ($newStatus === Task::STATUS_DONE
            && $task->status === Task::STATUS_TODO
            && !empty($task->subtask)) {
            $this->checkStatus($task->subtask);
            $task->update(['closed_at' => now()]);
        }
    }

    public function checkStatus($tasks)
    {
        foreach ($tasks as $subtask) {
            if ($subtask->status !== Task::STATUS_DONE) {
                die('not complete subtask');
            } else {
                if (!empty($subtask->subtask)) {
                    $this->checkStatus($subtask->subtask);
                }
            }
        }
    }
}
