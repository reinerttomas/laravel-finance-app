import { Head } from '@inertiajs/react';
import CreateCategoryDialog from '@/components/categories/create-category-dialog';
import DeleteCategoryDialog from '@/components/categories/delete-category-dialog';
import EditCategoryDialog from '@/components/categories/edit-category-dialog';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { index as categoriesIndex } from '@/routes/categories';
import type { CategoriesByType } from '@/types';

export default function Categories({
    categories,
}: {
    categories: CategoriesByType;
}) {
    return (
        <>
            <Head title="Categories" />

            <div className="px-4 py-6">
                <div className="mb-8 flex items-center justify-between">
                    <Heading
                        title="Categories"
                        description="Manage your income and expense categories"
                    />
                    <CreateCategoryDialog />
                </div>

                <div className="space-y-8">
                    <CategorySection
                        title="Expense categories"
                        categories={categories.Expense ?? []}
                    />
                    <CategorySection
                        title="Income categories"
                        categories={categories.Income ?? []}
                    />
                </div>
            </div>
        </>
    );
}

function CategorySection({
    title,
    categories,
}: {
    title: string;
    categories: CategoriesByType[keyof CategoriesByType];
}) {
    return (
        <div className="space-y-4">
            <Heading variant="small" title={title} />

            {categories.length === 0 ? (
                <p className="text-sm text-muted-foreground">
                    No categories yet.
                </p>
            ) : (
                <div className="divide-y rounded-lg border">
                    {categories.map((category) => (
                        <div
                            key={category.id}
                            className="flex items-center justify-between px-4 py-3"
                        >
                            <div className="flex items-center gap-3">
                                <span
                                    className="inline-block h-4 w-4 rounded-full"
                                    style={{
                                        backgroundColor: category.color,
                                    }}
                                />
                                <span className="text-sm font-medium">
                                    {category.name}
                                </span>
                                {category.is_default && (
                                    <Badge variant="secondary">Default</Badge>
                                )}
                            </div>

                            {!category.is_default && (
                                <div className="flex items-center gap-1">
                                    <EditCategoryDialog category={category} />
                                    <DeleteCategoryDialog
                                        category={category}
                                    />
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

Categories.layout = {
    breadcrumbs: [
        {
            title: 'Categories',
            href: categoriesIndex(),
        },
    ],
};
