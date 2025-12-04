<?php

namespace App\DataTransferObjects;

class CategoryData
{
   public function __construct(
      public readonly string $name,
      public readonly string $color = '#3B82F6',
   ) {}

   public static function fromRequest(array $data): self
   {
      return new self(
         name: $data['name'],
         color: $data['color'] ?? '#3B82F6',
      );
   }

   public function toArray(): array
   {
      return [
         'name' => $this->name,
         'color' => $this->color,
      ];
   }
}
