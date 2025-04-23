<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepository;

class CustomerService
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function findByPhone(string $phone): ?Customer
    {
        return $this->customerRepository->findByPhone($phone);
    }

    public function paginate(int $perPage = 10)
    {
        return $this->customerRepository->paginate($perPage);
    }

    public function findById(int $id): ?Customer
    {
        return $this->customerRepository->findById($id);
    }

    public function create(array $data): Customer
    {
        return $this->customerRepository->create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        return $this->customerRepository->update($customer, $data);
    }

    public function delete(Customer $customer): bool
    {
        return $this->customerRepository->delete($customer);
    }

    public function getDefaultAddress(Customer $customer)
    {
        return $this->customerRepository->getDefaultAddress($customer);
    }
}
