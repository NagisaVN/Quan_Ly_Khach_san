<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CustomerService
{
    public function __construct(private CustomerRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Customer
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $cccdImage = null): Customer
    {
        $data['company_id'] = auth()->user()->company_id;
        $data['branch_id'] = session('current_branch_id');

        if (empty($data['code'])) {
            $data['code'] = $this->generateCustomerCode($data['branch_id']);
        }

        $customer = $this->repository->create($data);

        if ($cccdImage) {
            $this->storeCccdDocument($customer, $cccdImage, $data['id_number'] ?? null);
        }

        return $customer;
    }

    public function update(Customer $model, array $data, ?UploadedFile $cccdImage = null): Customer
    {
        $customer = $this->repository->update($model, $data);

        if ($cccdImage) {
            $this->storeCccdDocument($customer, $cccdImage, $data['id_number'] ?? $customer->id_number);
        }

        return $customer;
    }

    public function delete(Customer $model): bool
    {
        return $this->repository->delete($model);
    }

    private function generateCustomerCode(?int $branchId): string
    {
        $prefix = 'CUS-'.($branchId ?? '0').'-';
        $last = Customer::where('code', 'like', $prefix.'%')->orderByDesc('id')->first();
        $seq = $last ? ((int) substr($last->code, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    private function storeCccdDocument(Customer $customer, UploadedFile $file, ?string $idNumber): void
    {
        $path = $file->store('customers/cccd', 'public');

        CustomerDocument::create([
            'customer_id' => $customer->id,
            'type' => 'cccd',
            'document_number' => $idNumber,
            'path' => $path,
        ]);
    }
}
