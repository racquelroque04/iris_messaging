<?php

namespace App\Http\Controllers\User;

use App\Clients\Rainbow;
use App\Transformer\UserTransformer;
use App\User;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class UserController extends Controller
{
    private  $fractal;

    private  $userTransformer;

    function __construct(Manager $fractal, UserTransformer $userTransformer)
    {
        $this->fractal = $fractal;
        $this->userTransformer = $userTransformer;
    }

    public function index()
    {
        $user = auth()->user();

        $rainbowClient = new Rainbow();

        $hasRainbowAccount = $rainbowClient->hasRainbowAccount($user);

        if (!$hasRainbowAccount) {
            $rainbowClient->createAccount($user->id);
        }

        return view('web.home.index');
    }

    public function getAllRainbowUser()
    {
        $users = User::query()->whereHas('rainbowAccount')->get();

        $users = new Collection($users, $this->userTransformer);

        $users = $this->fractal->createData($users);

        return $users->toArray();
    }

    public function getUser($id)
    {
        $user = User::find($id);

        $user = new Item($user, $this->userTransformer);

        $user = $this->fractal->createData($user);

        return $user->toArray();
    }
}
