export type Category = {
    id: number;
    user_id: number | null;
    name: string;
    type: 1 | 2;
    color: string;
    is_default: boolean;
    created_at: string;
    updated_at: string;
};

export type CategoriesByType = {
    Income: Category[];
    Expense: Category[];
};
