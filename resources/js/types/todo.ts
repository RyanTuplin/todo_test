export interface Category {
   id: number;
   name: string;
   color: string;
   todos_count?: number;
   created_at: string;
   updated_at: string;
}

export interface Todo {
   id: number;
   title: string;
   description: string | null;
   completed: boolean;
   categories?: Category[];
   created_at: string;
   updated_at: string;
}

export interface TodoFormData {
   title: string;
   description: string;
   completed: boolean;
}

export interface CategoryFormData {
   name: string;
   color: string;
}