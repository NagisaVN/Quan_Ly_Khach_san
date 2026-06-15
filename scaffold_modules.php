<?php

/**
 * Run: php scaffold_modules.php
 * Generates Sprint 2 CRUD modules following Company pattern.
 */

$base = __DIR__;

$modules = [
    ['Luggage', 'luggage', 'luggage', 'branch', 'Hành lý', 'fas fa-suitcase', 'tag_code', ['tag_code', 'customer_id', 'status', 'storage_location']],
    ['Product', 'products', 'inventory', 'branch', 'Kho hàng', 'fas fa-boxes', 'sku', ['name', 'sku', 'stock_quantity', 'selling_price', 'is_active'], 'inventory'],
    ['MaintenanceRequest', 'maintenance_requests', 'maintenance', 'branch', 'Bảo trì', 'fas fa-tools', 'title', ['title', 'room_id', 'priority', 'status'], 'maintenance/requests'],
    ['Contract', 'contracts', 'contracts', 'company', 'Hợp đồng', 'fas fa-file-contract', 'contract_number', ['contract_number', 'title', 'start_date', 'end_date', 'status']],
    ['Department', 'departments', 'enterprise', 'branch', 'Phòng ban', 'fas fa-sitemap', 'name', ['name', 'code', 'is_active'], 'enterprise/departments'],
    ['Supplier', 'suppliers', 'enterprise', 'company', 'Nhà cung cấp', 'fas fa-truck', 'name', ['name', 'code', 'phone', 'is_active'], 'enterprise/suppliers'],
    ['BankAccount', 'bank_accounts', 'enterprise', 'company', 'Tài khoản NH', 'fas fa-university', 'account_number', ['bank_name', 'account_number', 'account_holder', 'is_active'], 'enterprise/bank-accounts'],
    ['Tax', 'taxes', 'enterprise', 'company', 'Thuế', 'fas fa-percent', 'code', ['name', 'code', 'rate', 'is_active'], 'enterprise/taxes'],
    ['ServiceFee', 'service_fees', 'enterprise', 'company', 'Phí dịch vụ', 'fas fa-coins', 'code', ['name', 'code', 'type', 'value', 'is_active'], 'enterprise/service-fees'],
    ['SystemConfig', 'system_configs', 'system', 'global', 'Cấu hình', 'fas fa-sliders-h', 'key', ['key', 'value', 'group', 'is_active'], 'system/configs'],
];

function writeFile(string $path, string $content): void
{
    $dir = dirname($path);
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($path, $content);
    echo "Created: $path\n";
}

function studly(string $name): string
{
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
}

function snake(string $name): string
{
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
}

function permissionModule(string $module): string
{
    return match ($module) {
        'inventory' => 'inventory',
        'maintenance/requests' => 'maintenance',
        default => explode('/', $module)[0],
    };
}

foreach ($modules as [$model, $table, $permModule, $scope, $label, $icon, $routeKey, $fields, $routePrefix]) {
    $routePrefix = $routePrefix ?? $permModule;
    $perm = permissionModule($routePrefix);
    $var = lcfirst($model);
    $vars = str($model)->plural()->camel()->toString();
    if ($model === 'MaintenanceRequest') {
        $vars = 'maintenanceRequests';
    }
    if ($model === 'BankAccount') {
        $vars = 'bankAccounts';
    }
    if ($model === 'ServiceFee') {
        $vars = 'serviceFees';
    }
    if ($model === 'SystemConfig') {
        $vars = 'systemConfigs';
    }

    $policyContent = <<<PHP
<?php

namespace App\Policies;

use App\Models\\{$model};
use App\Models\User;

class {$model}Policy
{
    public function viewAny(User \$user): bool
    {
        return \$user->can('{$perm}.view');
    }

    public function view(User \$user, {$model} \$model): bool
    {
        return \$user->can('{$perm}.view');
    }

    public function create(User \$user): bool
    {
        return \$user->can('{$perm}.create');
    }

    public function update(User \$user, {$model} \$model): bool
    {
        return \$user->can('{$perm}.update');
    }

    public function delete(User \$user, {$model} \$model): bool
    {
        return \$user->can('{$perm}.delete');
    }
}
PHP;

    writeFile("$base/app/Policies/{$model}Policy.php", $policyContent);

    $interfaceContent = <<<PHP
<?php

namespace App\Repositories\Contracts;

interface {$model}RepositoryInterface extends BaseRepositoryInterface {}
PHP;
    writeFile("$base/app/Repositories/Contracts/{$model}RepositoryInterface.php", $interfaceContent);

    $repoContent = <<<PHP
<?php

namespace App\Repositories\Eloquent;

use App\Models\\{$model};
use App\Repositories\Contracts\\{$model}RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class {$model}Repository extends BaseRepository implements {$model}RepositoryInterface
{
    public function __construct({$model} \$model)
    {
        parent::__construct(\$model);
    }

    protected function applyFilters(Builder \$query, array \$filters): Builder
    {
        if (! empty(\$filters['search'])) {
            \$search = '%'.\$filters['search'].'%';
            \$query->where(function (\$q) use (\$search) {
                \$q->where('name', 'like', \$search)
                    ->orWhere('code', 'like', \$search);
            });
        }
        \$branchId = \$this->currentBranchId();
        if (\$branchId && \$this->model->isFillable('branch_id')) {
            \$query->where('branch_id', \$branchId);
        }
        \$companyId = \$this->currentCompanyId();
        if (\$companyId && \$this->model->isFillable('company_id') && ! \$this->model->isFillable('branch_id')) {
            \$query->where('company_id', \$companyId);
        }

        return \$query;
    }
}
PHP;
    writeFile("$base/app/Repositories/Eloquent/{$model}Repository.php", $repoContent);

    $serviceContent = <<<PHP
<?php

namespace App\Services;

use App\Models\\{$model};
use App\Repositories\Contracts\\{$model}RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class {$model}Service
{
    public function __construct(private {$model}RepositoryInterface \$repository) {}

    public function paginate(array \$filters = [], int \$perPage = 15): LengthAwarePaginator
    {
        return \$this->repository->paginate(\$filters, \$perPage);
    }

    public function findOrFail(int \$id): {$model}
    {
        return \$this->repository->findOrFail(\$id);
    }

    public function create(array \$data): {$model}
    {
        return \$this->repository->create(\$data);
    }

    public function update({$model} \$model, array \$data): {$model}
    {
        return \$this->repository->update(\$model, \$data);
    }

    public function delete({$model} \$model): bool
    {
        return \$this->repository->delete(\$model);
    }
}
PHP;
    writeFile("$base/app/Services/{$model}Service.php", $serviceContent);

    $ns = match ($perm) {
        'luggage' => 'Luggage',
        'inventory' => 'Inventory',
        'maintenance' => 'Maintenance',
        'contracts' => 'Contracts',
        'enterprise' => 'Enterprise',
        'system' => 'System',
        default => 'Modules',
    };

    $controllerContent = <<<PHP
<?php

namespace App\Http\Controllers\\{$ns};

use App\Http\Controllers\Controller;
use App\Models\\{$model};
use App\Services\\{$model}Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class {$model}Controller extends Controller
{
    public function __construct(private {$model}Service \$service) {}

    public function index(Request \$request): View
    {
        \$this->authorize('viewAny', {$model}::class);
        \${$vars} = \$this->service->paginate(\$request->only('search'));

        return view('".str_replace('/', '.', $routePrefix).".index', compact('{$vars}'));
    }

    public function create(): View
    {
        \$this->authorize('create', {$model}::class);

        return view('".str_replace('/', '.', $routePrefix).".create');
    }

    public function store(Request \$request): RedirectResponse
    {
        \$this->authorize('create', {$model}::class);
        \$data = \$request->validate([
            'name' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        if (session('current_branch_id') && in_array('branch_id', (new {$model})->getFillable())) {
            \$data['branch_id'] = session('current_branch_id');
        }
        if (auth()->user()?->company_id && in_array('company_id', (new {$model})->getFillable())) {
            \$data['company_id'] = auth()->user()->company_id;
        }
        \$model = \$this->service->create(\$data);

        return redirect()->route('".str_replace('/', '.', $routePrefix).".show', \$model)->with('success', 'Tạo thành công.');
    }

    public function show({$model} \${$var}): View
    {
        \$this->authorize('view', \${$var});

        return view('".str_replace('/', '.', $routePrefix).".show', compact('{$var}'));
    }

    public function edit({$model} \${$var}): View
    {
        \$this->authorize('update', \${$var});

        return view('".str_replace('/', '.', $routePrefix).".edit', compact('{$var}'));
    }

    public function update(Request \$request, {$model} \${$var}): RedirectResponse
    {
        \$this->authorize('update', \${$var});
        \$data = \$request->validate([
            'name' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        \$this->service->update(\${$var}, \$data);

        return redirect()->route('".str_replace('/', '.', $routePrefix).".show', \${$var})->with('success', 'Cập nhật thành công.');
    }

    public function destroy({$model} \${$var}): RedirectResponse
    {
        \$this->authorize('delete', \${$var});
        \$this->service->delete(\${$var});

        return redirect()->route('".str_replace('/', '.', $routePrefix).".index')->with('success', 'Đã xóa.');
    }
}
PHP;
    $ctrlPath = "$base/app/Http/Controllers/{$ns}/{$model}Controller.php";
    writeFile($ctrlPath, $controllerContent);

    $viewFolder = str_replace('/', DIRECTORY_SEPARATOR, $routePrefix);
    foreach (['index', 'create', 'edit', 'show'] as $view) {
        $content = match ($view) {
            'index' => "@extends('layouts.app')\n@section('title', '{$label}')\n@section('page-title', '{$label}')\n@section('content')\n<x-adminlte-card>\n<div class=\"d-flex justify-content-between mb-3\">\n<a href=\"{{ route('".str_replace('/', '.', $routePrefix).".create') }}\" class=\"btn btn-primary\"><i class=\"fas fa-plus\"></i> Thêm</a>\n</div>\n<table class=\"table table-striped\"><thead><tr><th>ID</th><th>Tên</th><th>Thao tác</th></tr></thead>\n<tbody>@forelse(\${$vars} as \${$var})<tr><td>{{ \${$var}->id }}</td><td>{{ \${$var}->name ?? \${$var}->title ?? \${$var}->tag_code ?? \${$var}->contract_number ?? '—' }}</td><td><a href=\"{{ route('".str_replace('/', '.', $routePrefix).".show', \${$var}) }}\" class=\"btn btn-sm btn-info\"><i class=\"fas fa-eye\"></i></a></td></tr>@empty<tr><td colspan=\"3\" class=\"text-center\">Chưa có dữ liệu</td></tr>@endforelse</tbody></table>\n{{ \${$vars}->links() }}\n</x-adminlte-card>\n@endsection",
            default => "@extends('layouts.app')\n@section('title', '{$label}')\n@section('page-title', '{$label}')\n@section('content')\n<x-adminlte-card><p>Module {$label} — view {$view}</p></x-adminlte-card>\n@endsection",
        };
        writeFile("$base/resources/views/{$viewFolder}/{$view}.blade.php", $content);
    }
}

echo "\nDone. Update routes/web.php, AppServiceProvider, config/menu.php manually or run scaffold_routes.php\n";
