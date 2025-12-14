<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'state' => $this->state->value,
            'priority' => $this->priority->value,
            'source_channel' => $this->source_channel->value,
            'due_date' => $this->due_date?->toIso8601String(),
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'estimated_hours' => $this->estimated_hours,
            'actual_hours' => $this->actual_hours,
            'is_overdue' => $this->is_overdue,
            'overdue_risk_score' => $this->overdue_risk_score,
            'tags' => $this->tags,
            'requestor' => $this->whenLoaded('requestor', fn() => [
                'id' => $this->requestor->id,
                'name' => $this->requestor->name,
            ]),
            'owner' => $this->whenLoaded('owner', fn() => [
                'id' => $this->owner->id,
                'name' => $this->owner->name,
            ]),
            'project' => $this->whenLoaded('project', fn() => [
                'id' => $this->project->id,
                'name' => $this->project->name,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


