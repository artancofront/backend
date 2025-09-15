<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Repositories\CustomerAddressRepository;

class CustomerAddressService
{
    protected CustomerAddressRepository $addressRepository;

    public function __construct(CustomerAddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * Get all addresses for a customer.
     */
    public function getAddresses(Customer $customer)
    {
        return $this->addressRepository->getAddresses($customer);
    }

    /**
     * Get a specific address for a customer.
     */
    public function getAddress(Customer $customer, int $addressId): ?CustomerAddress
    {
        return $this->addressRepository->findAddress($customer, $addressId);
    }

    /**
     * Create a new address for a customer.
     */
    public function createAddress(Customer $customer, array $data): CustomerAddress
    {
        return $this->addressRepository->create($customer, $data);
    }

    /**
     * Update an address for a customer.
     */
    public function updateAddress(Customer $customer, int $addressId, array $data): ?CustomerAddress
    {
        return $this->addressRepository->update($customer, $addressId, $data);
    }

    /**
     * Delete an address for a customer.
     */
    public function deleteAddress(Customer $customer, int $addressId): bool
    {
        return $this->addressRepository->delete($customer, $addressId);
    }

    /**
     * Get the default address for a customer.
     */
    public function getDefaultAddress(Customer $customer): ?CustomerAddress
    {
        return $this->addressRepository->getDefault($customer);
    }
}
