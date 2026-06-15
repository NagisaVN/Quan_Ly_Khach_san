<?php

$entities = [
    ['Company', 'Company', 'enterprise', 'companies', false, false],
    ['Branch', 'Branch', 'enterprise', 'branches', false, false],
    ['RoomType', 'RoomType', 'rooms', 'room-types', false, true],
    ['Floor', 'Floor', 'rooms', 'floors', true, false],
    ['Room', 'Room', 'rooms', 'rooms', true, false],
    ['Amenity', 'Amenity', 'rooms', 'amenities', false, true],
    ['Customer', 'Customer', 'customers', 'customers', false, false],
    ['ServiceCategory', 'ServiceCategory', 'services', 'categories', true, false],
    ['Service', 'Service', 'services', 'items', true, false],
    ['User', 'User', 'system', 'users', false, false],
];

foreach ($entities as [$name, $model, $module, $route, $branchScoped, $companyScoped]) {
    $iface = <<<PHP
<?php

namespace App\Repositories\Contracts;

use App\Models\\{$model};

interface {$name}RepositoryInterface extends BaseRepositoryInterface
{
}

PHP;
    file_put_contents("app/Repositories/Contracts/{$name}RepositoryInterface.php", $iface);

    $scope = '';
    if ($branchScoped) {
        $scope .= "
        if (\$branchId = \$this->currentBranchId()) {
            \$query->where('branch_id', \$branchId);
        }";
    }
    if ($companyScoped) {
        $scope .= "
        if (\$companyId = \$this->currentCompanyId()) {
            \$query->where('company_id', \$companyId);
        }";
    }
    $scope .= "
        if (! empty(\$filters['search'])) {
            \$search = '%'.\$filters['search'].'%';
            \$query->where(function (\$q) use (\$search) {
                \$q->where('name', 'like', \$search);
            });
        }";

    $repo = <<<PHP
<?php

namespace App\Repositories\Eloquent;

use App\Models\\{$model};
use App\Repositories\Contracts\\{$name}RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class {$name}Repository extends BaseRepository implements {$name}RepositoryInterface
{
    public function __construct({$model} \$model)
    {
        parent::__construct(\$model);
    }

    protected function applyFilters(Builder \$query, array \$filters): Builder
    {{$scope}

        return \$query;
    }
}

PHP;
    file_put_contents("app/Repositories/Eloquent/{$name}Repository.php", $repo);

    $svc = <<<PHP
<?php

namespace App\Services;

use App\Models\\{$model};
use App\Repositories\Contracts\\{$name}RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class {$name}Service
{
    public function __construct(private {$name}RepositoryInterface \$repository) {}

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

    public function all(array \$filters = [])
    {
        return \$this->repository->all(\$filters);
    }
}

PHP;
    file_put_contents("app/Services/{$name}Service.php", $svc);

    $policy = <<<PHP
<?php

namespace App\Policies;

use App\Models\\{$model};
use App\Models\User;

class {$name}Policy
{
    public function viewAny(User \$user): bool
    {
        return \$user->can('{$module}.view');
    }

    public function view(User \$user, {$model} \$model): bool
    {
        return \$user->can('{$module}.view');
    }

    public function create(User \$user): bool
    {
        return \$user->can('{$module}.create');
    }

    public function update(User \$user, {$model} \$model): bool
    {
        return \$user->can('{$module}.update');
    }

    public function delete(User \$user, {$model} \$model): bool
    {
        return \$user->can('{$module}.delete');
    }
}

PHP;
    file_put_contents("app/Policies/{$name}Policy.php", $policy);
}

echo "Done\n";
