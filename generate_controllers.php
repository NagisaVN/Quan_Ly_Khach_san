<?php

$controllers = [
    ['Enterprise', 'Company', 'companies', 'enterprise.companies', 'Công ty', 'company'],
    ['Enterprise', 'Branch', 'branches', 'enterprise.branches', 'Chi nhánh', 'branch'],
    ['Rooms', 'RoomType', 'room-types', 'rooms.room-types', 'Loại phòng', 'roomType'],
    ['Rooms', 'Floor', 'floors', 'rooms.floors', 'Tầng', 'floor'],
    ['Rooms', 'Room', 'rooms', 'rooms.rooms', 'Phòng', 'room'],
    ['Rooms', 'Amenity', 'amenities', 'rooms.amenities', 'Tiện ích', 'amenity'],
    ['Customers', 'Customer', 'customers', 'customers', 'Khách hàng', 'customer'],
    ['Services', 'ServiceCategory', 'categories', 'services.categories', 'Danh mục dịch vụ', 'serviceCategory'],
    ['Services', 'Service', 'items', 'services.items', 'Dịch vụ', 'service'],
    ['System', 'User', 'users', 'system.users', 'Người dùng', 'user'],
];

foreach ($controllers as [$namespace, $name, $folder, $routePrefix, $label, $var]) {
    $controller = <<<PHP
<?php

namespace App\Http\Controllers\\{$namespace};

use App\Http\Controllers\Controller;
use App\Http\Requests\\{$namespace}\\Store{$name}Request;
use App\Http\Requests\\{$namespace}\\Update{$name}Request;
use App\Models\\{$name};
use App\Services\\{$name}Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class {$name}Controller extends Controller
{
    public function __construct(private {$name}Service \$service) {}

    public function index(Request \$request): View
    {
        \$this->authorize('viewAny', {$name}::class);

        \${$var}s = \$this->service->paginate(\$request->only('search'));

        return view('{$folder}.index', compact('{$var}s'));
    }

    public function create(): View
    {
        \$this->authorize('create', {$name}::class);

        return view('{$folder}.create');
    }

    public function store(Store{$name}Request \$request): RedirectResponse
    {
        \$this->authorize('create', {$name}::class);

        \$record = \$this->service->create(\$request->validated());

        return redirect()->route('{$routePrefix}.show', \$record)
            ->with('success', 'Tạo {$label} thành công.');
    }

    public function show({$name} \${$var}): View
    {
        \$this->authorize('view', \${$var});

        return view('{$folder}.show', compact('{$var}'));
    }

    public function edit({$name} \${$var}): View
    {
        \$this->authorize('update', \${$var});

        return view('{$folder}.edit', compact('{$var}'));
    }

    public function update(Update{$name}Request \$request, {$name} \${$var}): RedirectResponse
    {
        \$this->authorize('update', \${$var});

        \$this->service->update(\${$var}, \$request->validated());

        return redirect()->route('{$routePrefix}.show', \${$var})
            ->with('success', 'Cập nhật {$label} thành công.');
    }

    public function destroy({$name} \${$var}): RedirectResponse
    {
        \$this->authorize('delete', \${$var});

        \$this->service->delete(\${$var});

        return redirect()->route('{$routePrefix}.index')
            ->with('success', 'Xóa {$label} thành công.');
    }
}

PHP;

    $dir = "app/Http/Controllers/{$namespace}";
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents("{$dir}/{$name}Controller.php", $controller);

    $reqDir = "app/Http/Requests/{$namespace}";
    if (! is_dir($reqDir)) {
        mkdir($reqDir, 0777, true);
    }

    $store = <<<PHP
<?php

namespace App\Http\Requests\\{$namespace};

use Illuminate\Foundation\Http\FormRequest;

class Store{$name}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}

PHP;
    file_put_contents("{$reqDir}/Store{$name}Request.php", $store);

    $update = <<<PHP
<?php

namespace App\Http\Requests\\{$namespace};

use Illuminate\Foundation\Http\FormRequest;

class Update{$name}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}

PHP;
    file_put_contents("{$reqDir}/Update{$name}Request.php", $update);
}

echo "Controllers and requests generated\n";
