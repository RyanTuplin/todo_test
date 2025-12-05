export interface Category {
   id: number;
   name: string;
   color: string;
   todos_count?: number;
   created_at: string;
   updated_at: string;
}

export type Priority = 'high' | 'medium' | 'low';

export interface Todo {
   id: number;
   title: string;
   description: string | null;
   completed: boolean;
   priority: Priority | null;
   priority_label: string | null;
   priority_color: string | null;
   due_date: string | null;
   due_date_formatted: string | null;
   is_overdue: boolean;
   is_due_today: boolean;
   categories?: Category[];
   created_at: string;
   updated_at: string;
}

export interface TodoFormData {
   title: string;
   description: string;
   completed: boolean;
   priority: Priority | null;
   due_date: string | null;
}

export interface CategoryFormData {
   name: string;
   color: string;
}