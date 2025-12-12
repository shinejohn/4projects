<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        return response()->json([
            'data' => $project->members()->with('user')->get(),
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'role' => ['required', 'string', 'in:owner,admin,member,viewer'],
            'allocation_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $project->members()->syncWithoutDetaching([
            $validated['user_id'] => [
                'role' => $validated['role'],
                'allocation_percentage' => $validated['allocation_percentage'] ?? 100,
            ],
        ]);

        return response()->json(['message' => 'Member added'], 201);
    }

    public function update(Request $request, Project $project, string $member)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'role' => ['sometimes', 'required', 'string', 'in:owner,admin,member,viewer'],
            'allocation_percentage' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $project->members()->updateExistingPivot($member, $validated);

        return response()->json(['message' => 'Member updated']);
    }

    public function destroy(Project $project, string $member)
    {
        $this->authorize('update', $project);

        $project->members()->detach($member);

        return response()->json(null, 204);
    }
}

