<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function findByPhone($phone)
    {
        return Customer::where('phone', $phone)->first();
    }

    public function paginate($perPage=10)
    {
        return Customer::all()->paginate($perPage);
    }

    public function findById(int $id)
    {
        return Customer::find($id);
    }

    public function create(array $data)
    {
        return Customer::create($data);
    }

    public function update(Customer $Customer, array $data)
    {
        $Customer->update($data);
        return $Customer;
    }

    public function delete(Customer $Customer)
    {
        return $Customer->delete();
    }
}

