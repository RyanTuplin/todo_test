<?php

namespace App\DataTransferObjects;

class TodoData
{

   public function __construct(
      public readonly string $title,
      public readonly ?string $description,
      public readonly bool $completed = false,
   ) {}

   public static function fromRequest(array $data): self
   {
      return new self(
         title: $data['title'],
         description: $data['description'] ?? null,
         completed: $data['completed'] ?? false,
      );
   }

   public function toArray(): array
   {
      return [
         'title' => $this->title,
         'description' => $this->description,
         'completed' => $this->completed,
      ];
   }
}
