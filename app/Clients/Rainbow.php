<?php

namespace Clients;

use Mockery\Exception;
use GuzzleHttp\Client;

class  Rainbow
{
    protected $url;

    protected $port;

    protected $email;

    protected $password;

    protected $client;

    protected $applicationId;

    protected $applicationSecret;

    protected $token;

    public function __construct()
    {
        $this->client = new Client();

        $this->url = config('rainbow.base_url');

        $this->port = config('rainbow.port');

        $this->email = config('rainbow.email');

        $this->password = config('rainbow.password');

        $this->applicationId = config('rainbow.application_id');

        $this->applicationSecret = config('rainbow.application_secret');

        $this->setToken();
    }

    private function setToken()
    {
        $auth = base64_encode($this->email . ':' . $this->password);
        $appAuth = base64_encode($this->applicationId . ':' . (hash('sha256', $this->applicationSecret . $this->password)));
        $response = $this->client->get($this->url.'/api/rainbow/authentication/v1.0/login', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $auth,
                "x-rainbow-app-auth" => 'Basic ' . $appAuth,
            ]
        ]);
        $this->token = json_decode($response->getBody())->token;

        return $this;
    }

    public function hasRainbowAccount($user)
    {
        $rainbow = $this->client->get($this->url.'/api/rainbow/admin/v1.0/users', [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
            ],
        ])->getBody();

        $rainbowUsers = json_decode($rainbow);

        if (!isset($user->rainbowAccount->email)) {
            return false;
        }

        $users = collect($rainbowUsers->data)->map(function ($item) use ($user) {
            return $item->loginEmail == $user->rainbowAccount->email ? $user : null;
        })->filter();

        if (count($users) == 0) {
            return (bool) false;
        } else {
            return (bool) true;
        }
    }

    public function createAccount($userId)
    {
        $user = User::find($userId);

        $data = [
            'user_id' => $userId,
            'password' => $this->getToken(10),
        ];
        if (env('APP_ENV') == 'staging') {
            $data['email'] = 'mpd_customer_staging_'. $user->id . '@mypocketdoctor.com';
        } else {
            $data['email'] = 'mpd_customer_'. $user->id . '@mypocketdoctor.com';
        }

        RainbowAccount::create($data);

        try {
            $rainbow = $this->client->post( $this->url.'/api/rainbow/admin/v1.0/users', [
                'headers' => [
                    'Authorization' => "Bearer {$this->token}",
                ],
                'json' => [
                    "loginEmail" => $data['email'],
                    "password" => $data['password'],
                    "firstName" => $data['email'],
                    "lastName" => "Last name",
                ],
            ])->getBody();

        } catch (Exception $exception) {
            return false;
        }
        $user->has_rainbow_account = 1;
        $user->rainbowAccount->update([
            'contact_id' => json_decode($rainbow)->data->id
        ]);

        return $user;
    }

    function getToken($length){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $codeAlphabet.= "!@#$%&*";
        $max = strlen($codeAlphabet);

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max-1)];
        }

        return bcrypt($token);
    }

}