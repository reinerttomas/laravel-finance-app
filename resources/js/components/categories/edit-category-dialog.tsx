import { Form } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import { useState } from 'react';
import CategoryController from '@/actions/App/Http/Controllers/CategoryController';
import ColorPicker from '@/components/color-picker';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Category } from '@/types';

export default function EditCategoryDialog({
    category,
}: {
    category: Category;
}) {
    const [open, setOpen] = useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="ghost" size="icon">
                    <Pencil className="h-4 w-4" />
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Edit category</DialogTitle>
                <DialogDescription>
                    Update the name or color of your category.
                </DialogDescription>

                <Form
                    {...CategoryController.update.form(category.id)}
                    options={{
                        preserveScroll: true,
                        onSuccess: () => setOpen(false),
                    }}
                    className="space-y-4"
                >
                    {({ processing, errors, resetAndClearErrors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor={`name-${category.id}`}>
                                    Name
                                </Label>
                                <Input
                                    id={`name-${category.id}`}
                                    name="name"
                                    required
                                    defaultValue={category.name}
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label>Type</Label>
                                <Input
                                    value={
                                        category.type === 2
                                            ? 'Expense'
                                            : 'Income'
                                    }
                                    disabled
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label>Color</Label>
                                <ColorPicker
                                    name="color"
                                    defaultValue={category.color}
                                />
                                <InputError message={errors.color} />
                            </div>

                            <DialogFooter className="gap-2">
                                <DialogClose asChild>
                                    <Button
                                        variant="secondary"
                                        onClick={() => resetAndClearErrors()}
                                    >
                                        Cancel
                                    </Button>
                                </DialogClose>
                                <Button disabled={processing} asChild>
                                    <button type="submit">Save</button>
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
