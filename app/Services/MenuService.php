<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class MenuService
{
    public function getMenuForUser(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $items = $this->filterItems(config('menu', []), $user);

        return [
            ['header' => null, 'items' => $items],
        ];
    }

    public function isActive(?string $routeName): bool
    {
        if (! $routeName) {
            return false;
        }

        return request()->routeIs($routeName) || request()->routeIs($routeName.'.*');
    }

    public function isTreeOpen(array $item): bool
    {
        if (empty($item['children'])) {
            return false;
        }

        foreach ($item['children'] as $child) {
            if (! empty($child['route']) && $this->isActive($child['route'])) {
                return true;
            }
        }

        return false;
    }

    private function filterItems(array $items, User $user): array
    {
        $result = [];

        foreach ($items as $item) {
            if (! empty($item['children'])) {
                $children = $this->filterItems($item['children'], $user);
                if (empty($children)) {
                    continue;
                }
                $item['children'] = $children;
                if (! empty($item['permission']) && ! $user->can($item['permission'])) {
                    continue;
                }
                $result[] = $item;
                continue;
            }

            if (! empty($item['permission']) && ! $user->can($item['permission'])) {
                continue;
            }

            if (! empty($item['route']) && ! Route::has($item['route'])) {
                continue;
            }

            $result[] = $item;
        }

        return $result;
    }
}
