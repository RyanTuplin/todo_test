<?php

namespace App\Enums;

enum Priority: string
{
   case HIGH = 'high';
   case MEDIUM = 'medium';
   case LOW = 'low';

   public function label(): string
   {
      return match ($this) {
         self::HIGH => 'High',
         self::MEDIUM => 'Medium',
         self::LOW => 'Low',
      };
   }

   public function color(): string
   {
      return match ($this) {
         self::HIGH => '#EF4444',    // Red
         self::MEDIUM => '#F59E0B',  // Orange
         self::LOW => '#10B981',     // Green
      };
   }
}
