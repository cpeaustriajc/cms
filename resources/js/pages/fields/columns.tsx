"use client";

import type { ColumnDef } from '@tanstack/react-table';
import { ArrowUpDown, MoreHorizontal } from 'lucide-react';
import { Link } from '@inertiajs/react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { edit as editField, show as showField } from '@/routes/fields';

import type { FieldItem } from './types';

export const columns: ColumnDef<FieldItem>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => (
            <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
                Name
                <ArrowUpDown className="ml-2 h-4 w-4" />
            </Button>
        ),
        cell: ({ row }) => (
            <Link href={showField.url(row.original.id)} className="hover:underline font-medium">
                {row.getValue('name') as string}
            </Link>
        ),
    },
    {
        accessorKey: 'handle',
        header: 'Handle',
    },
    {
        accessorKey: 'data_type',
        header: 'Type',
        cell: ({ row }) => <Badge variant="secondary">{row.getValue('data_type') as string}</Badge>,
    },
    {
        id: 'content_type',
        header: 'Content Type',
        cell: ({ row }) => {
            const ct = row.original.content_type;
            if (!ct) {
                return <span className="text-muted-foreground">—</span>;
            }
            return (
                <span>
                    {ct.name}
                    <Badge className="ml-2" variant="outline">
                        {ct.slug}
                    </Badge>
                </span>
            );
        },
    },
    {
        id: 'flags',
        header: 'Flags',
        cell: ({ row }) => {
            const f = row.original;
            return (
                <div className="flex flex-wrap gap-1">
                    {f.is_required && <Badge variant="outline">required</Badge>}
                    {f.is_unique && <Badge variant="outline">unique</Badge>}
                    {f.is_translatable && <Badge variant="outline">i18n</Badge>}
                    {f.is_repeatable && <Badge variant="outline">repeatable</Badge>}
                </div>
            );
        },
        enableSorting: false,
    },
    {
        id: 'actions',
        header: () => <div className="text-right">Actions</div>,
        cell: ({ row }) => {
            const field = row.original;
            return (
                <div className="flex justify-end">
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" className="h-8 w-8 p-0">
                                <span className="sr-only">Open menu</span>
                                <MoreHorizontal className="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuItem asChild>
                                <Link href={editField.url(field.id)}>Edit</Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem asChild>
                                <Link href={showField.url(field.id)}>View</Link>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem onClick={() => navigator.clipboard.writeText(String(field.id))}>
                                Copy ID
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            );
        },
        enableHiding: false,
        enableSorting: false,
    },
];
