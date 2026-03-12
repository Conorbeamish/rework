<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    //TOOD add proper auth
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Order $order): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(?User $user, Order $order): bool
    {
        return true;
    }

    public function delete(?User $user, Order $order): bool
    {
        return true;
    }
}
