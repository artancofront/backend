<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Cart;

class CustomerRepository
{
    public function findByPhone($phone)
    {
        return Customer::where('phone', $phone)->first();
    }

    public function paginate($perPage = 10)
    {
        return Customer::paginate($perPage);
    }

    public function findById(int $id)
    {
        return Customer::find($id);
    }

    public function create(array $data)
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data)
    {
        $customer->update($data);
        return $customer;
    }

    public function delete(Customer $customer)
    {
        return $customer->delete();
    }


    public function getDefaultAddress(Customer $customer)
    {
        return $customer->addresses()->where('is_default', true)->first();
    }


}
