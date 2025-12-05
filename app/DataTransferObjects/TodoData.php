<?php

namespace App\DataTransferObjects;

use App\Enums\Priority;
use Carbon\Carbon;

class TodoData
{
   public function __construct(
      public readonly string $title,
      public readonly ?string $description,
      public readonly bool $completed = false,
      public readonly ?Priority $priority = null,
      public readonly ?Carbon $due_date = null,
   ) {}

   public static function fromRequest(array $data): self
   {
      return new self(
         title: $data['title'],
         description: $data['description'] ?? null,
         completed: isset($data['completed']) ? (bool)$data['completed'] : false,
         priority: isset($data['priority']) && $data['priority'] !== null
            ? Priority::from($data['priority'])
            : null,
         due_date: isset($data['due_date']) && $data['due_date'] !== null
            ? Carbon::parse($data['due_date'])
            : null,
      );
   }

   public function toArray(): array
   {
      return [
         'title' => $this->title,
         'description' => $this->description,
         'completed' => $this->completed,
         'priority' => $this->priority?->value,
         'due_date' => $this->due_date?->format('Y-m-d'),
      ];
   }
}
