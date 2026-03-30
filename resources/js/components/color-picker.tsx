import { useState } from 'react';
import { cn } from '@/lib/utils';

const COLORS = [
    '#EF4444', // Red
    '#F97316', // Orange
    '#F59E0B', // Amber
    '#EAB308', // Yellow
    '#84CC16', // Lime
    '#22C55E', // Green
    '#10B981', // Emerald
    '#14B8A6', // Teal
    '#06B6D4', // Cyan
    '#0EA5E9', // Sky
    '#3B82F6', // Blue
    '#6366F1', // Indigo
    '#8B5CF6', // Violet
    '#A855F7', // Purple
    '#D946EF', // Fuchsia
    '#EC4899', // Pink
    '#F43F5E', // Rose
    '#78716C', // Stone
    '#6B7280', // Gray
    '#64748B', // Slate
];

export default function ColorPicker({
    name,
    defaultValue = COLORS[11],
}: {
    name: string;
    defaultValue?: string;
}) {
    const [selected, setSelected] = useState(defaultValue);

    return (
        <div>
            <input type="hidden" name={name} value={selected} />
            <div className="flex flex-wrap gap-2">
                {COLORS.map((color) => (
                    <button
                        key={color}
                        type="button"
                        onClick={() => setSelected(color)}
                        className={cn(
                            'h-8 w-8 rounded-full border-2 transition-transform hover:scale-110',
                            selected === color
                                ? 'border-foreground ring-2 ring-foreground/20'
                                : 'border-transparent',
                        )}
                        style={{ backgroundColor: color }}
                        aria-label={`Select color ${color}`}
                    />
                ))}
            </div>
        </div>
    );
}
