<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,critical'],
            'owner_id' => ['nullable', 'uuid', 'exists:users,id'],
            'parent_id' => ['nullable', 'uuid', 'exists:tasks,id'],
            'due_date' => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'integer', 'min:0'],
            'tags' => ['nullable', 'array'],
        ];
    }
}

