<?php

namespace Alive2212\LaravelMobilePassport\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class Init extends Command
{
    protected $secret = 'CLH2lpFcOSNUhpFg5pRO43MO2bUvF4UggTbUL3Tm';
    protected $oauthName = 'LaravelPassport';
    protected $redirect = 'http://localhost';
    protected $fileName = 'alive_mobile_passport_client.bin';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mobile_passport:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To initialize laravel mobile passport';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        // TODO create dynamic secret key

        $user = new User();
        $user = $user->first();
        if (is_null($user)) {
            echo PHP_EOL;
            echo 'you must create at least one user';
            echo PHP_EOL;
            return;
        }

        $oauthClient = DB::table('oauth_clients')->where([
            ['name', '=', $this->oauthName],
            ['secret', '=', $this->secret],
            ['redirect', '=', $this->redirect],
        ])->first();

        if (!is_null($oauthClient)) {
            echo PHP_EOL;
            echo 'it seems created before';
            echo PHP_EOL;
            echo 'if you want to create it again please remove ID = ' . $oauthClient->id . ' form oauth_clients';
            echo PHP_EOL;
            return;
        }

        $oauthClient = DB::table('oauth_clients')->insert([
            'user_id' => $user->toArray()['id'],
            'name' => $this->oauthName,
            'secret' => $this->secret,
            'redirect' => $this->redirect,
            'personal_access_client' => 1,
            'password_client' => 1,
            'revoked' => 0,
        ]);
        echo PHP_EOL;
        echo 'client created successfully';
        echo PHP_EOL;

        try {
            file_put_contents(storage_path($this->fileName), serialize([
                'id' => $oauthClient['id'],
                'secret' => $this->secret,
            ]));
        } catch (FileException $exception) {
            echo PHP_EOL;
            echo $exception->getMessage();
            echo PHP_EOL;
        }

//        $user = new User();
//        $expireTime = Carbon::now()->addYears(100);
//        $token = $user->createToken('Init Token',[],$expireTime);
//        echo PHP_EOL;
//        echo $token->accessToken;
//        echo PHP_EOL;
    }
}

