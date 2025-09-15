<?php
namespace App\Repositories;

use App\Models\Customer;
use App\Models\CustomerAddress;

class CustomerAddressRepository
{
    public function getAddresses(Customer $customer)
    {
        return $customer->addresses;
    }

    public function findAddress(Customer $customer, int $addressId): ?CustomerAddress
    {
        return $customer->addresses()->find($addressId);
    }

    public function create(Customer $customer, array $data): CustomerAddress
    {
        if (!empty($data['is_default'])) {
            $customer->addresses()->update(['is_default' => false]);
        }

        return $customer->addresses()->create($data);
    }

    public function update(Customer $customer, int $addressId, array $data): ?CustomerAddress
    {
        $address = $this->findAddress($customer, $addressId);

        if (!$address) {
            return null;
        }

        if (!empty($data['is_default'])) {
            $customer->addresses()->where('id', '!=', $addressId)->update(['is_default' => false]);
        }

        $address->update($data);

        return $address;
    }

    public function delete(Customer $customer, int $addressId): bool
    {
        $address = $this->findAddress($customer, $addressId);
        return $address ? $address->delete() : false;
    }

    public function getDefault(Customer $customer): ?CustomerAddress
    {
        return $customer->addresses()->where('is_default', true)->first();
    }
}
