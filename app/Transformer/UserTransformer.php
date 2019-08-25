<?php

namespace App\Transformer;

use App\RainbowAccount;
use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id'            => (int) $user->id,
            'name'          => $user->name,
            'email'          => $user->email,
            'contact_id'     => $user->rainbowAccount->contact_id,
        ];
    }
}