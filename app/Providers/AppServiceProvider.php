<?php

namespace App\Providers;

use App\Adapters\Contracts\DoorLockGatewayInterface;
use App\Adapters\Contracts\OtaGatewayInterface;
use App\Adapters\Contracts\PaymentGatewayInterface;
use App\Adapters\Contracts\SmsGatewayInterface;
use App\Adapters\Mock\MockDoorLockAdapter;
use App\Adapters\Mock\MockMomoAdapter;
use App\Adapters\Mock\MockOtaAdapter;
use App\Adapters\Mock\MockSmsAdapter;
use App\Adapters\Mock\MockVnPayAdapter;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Policies\AmenityPolicy;
use App\Policies\BookingPolicy;
use App\Policies\BranchPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\FloorPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\RoomPolicy;
use App\Policies\RoomTypePolicy;
use App\Policies\ServiceCategoryPolicy;
use App\Policies\ServicePolicy;
use App\Policies\UserPolicy;
use App\Repositories\Contracts\AmenityRepositoryInterface;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\BranchRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\FloorRepositoryInterface;
use App\Repositories\Contracts\LoginLogRepositoryInterface;
use App\Repositories\Contracts\RoomRepositoryInterface;
use App\Repositories\Contracts\RoomTypeRepositoryInterface;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\AmenityRepository;
use App\Repositories\Eloquent\BookingRepository;
use App\Repositories\Eloquent\BranchRepository;
use App\Repositories\Eloquent\CompanyRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\FloorRepository;
use App\Repositories\Eloquent\LoginLogRepository;
use App\Repositories\Eloquent\RoomRepository;
use App\Repositories\Eloquent\RoomTypeRepository;
use App\Repositories\Eloquent\ServiceCategoryRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Services\MenuService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $bindings = [
            LoginLogRepositoryInterface::class => LoginLogRepository::class,
            BookingRepositoryInterface::class => BookingRepository::class,
            CompanyRepositoryInterface::class => CompanyRepository::class,
            BranchRepositoryInterface::class => BranchRepository::class,
            RoomTypeRepositoryInterface::class => RoomTypeRepository::class,
            FloorRepositoryInterface::class => FloorRepository::class,
            RoomRepositoryInterface::class => RoomRepository::class,
            AmenityRepositoryInterface::class => AmenityRepository::class,
            CustomerRepositoryInterface::class => CustomerRepository::class,
            ServiceCategoryRepositoryInterface::class => ServiceCategoryRepository::class,
            ServiceRepositoryInterface::class => ServiceRepository::class,
            UserRepositoryInterface::class => UserRepository::class,
        ];

        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }

        $this->app->bind(PaymentGatewayInterface::class, function () {
            return match (config('services.payment.driver')) {
                'momo' => app(MockMomoAdapter::class),
                'vnpay' => app(MockVnPayAdapter::class),
                default => app(MockVnPayAdapter::class),
            };
        });

        $this->app->bind(SmsGatewayInterface::class, MockSmsAdapter::class);
        $this->app->bind(OtaGatewayInterface::class, MockOtaAdapter::class);
        $this->app->bind(DoorLockGatewayInterface::class, MockDoorLockAdapter::class);
    }

    public function boot(): void
    {
        Blueprint::macro('auditColumns', function () {
            /** @var Blueprint $this */
            $this->unsignedBigInteger('created_by')->nullable();
            $this->unsignedBigInteger('updated_by')->nullable();

            $this->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $this->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $this->softDeletes();
        });

        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Branch::class, BranchPolicy::class);
        Gate::policy(RoomType::class, RoomTypePolicy::class);
        Gate::policy(Floor::class, FloorPolicy::class);
        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(Amenity::class, AmenityPolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(ServiceCategory::class, ServiceCategoryPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            $menuService = app(MenuService::class);

            $view->with([
                'sidebarMenu' => $menuService->getMenuForUser($user),
                'menuService' => $menuService,
                'userBranches' => $user ? $user->branches()->where('is_active', true)->get() : collect(),
                'currentBranchId' => session('current_branch_id'),
            ]);
        });
    }
}
