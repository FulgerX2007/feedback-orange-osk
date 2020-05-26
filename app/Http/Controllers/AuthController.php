<?php

namespace App\Http\Controllers;

use Adldap\Schemas\ActiveDirectory;
use Illuminate\Http\Request;
use Adldap\Adldap;

class AuthController extends Controller
{
    /** @var \Adldap\Laravel\Facades\Adldap */
    private static $activeDirectory;
    private static $selectedAttributes = [
        'cn',
        'displayName',
        'title',
        'description',
        'physicalDeliveryOfficeName',
        'mail',
        'objectGUID',
        'objectSid',
        'mobile',
        'thumbnailPhoto'
    ];

    /**
     * @throws \Adldap\Auth\BindException
     * @throws \Adldap\Connections\ConnectionException
     * @author Andrian Iliev <andrian.iliev@orange.com>
     */
    private static function init(): void
    {
        //         Create the configuration array.
        $config = [
            // Mandatory Configuration Options
            'hosts' => [env('LDAP_HOSTS')],
            'base_dn' => env('LDAP_BASE_DN'),
            'username' => env('LDAP_USERNAME'),
            'password' => env('LDAP_PASSWORD'),

            // Optional Configuration Options
            'schema' => ActiveDirectory::class,
            'account_prefix' => '',
            'account_suffix' => '',
            'port' => env('LDAP_PORT'),
            'follow_referrals' => false,
            'use_ssl' => env('LDAP_USE_SSL'),
            'use_tls' => env('LDAP_USE_TLS'),
            'version' => 3,
            'timeout' => 5,
        ];
        /** @var ActiveDirectory activeDirectory */
        self::$activeDirectory = (new Adldap())->addProvider($config)
            ->connect();
    }

    /**
     * @throws \Adldap\Auth\BindException
     * @throws \Adldap\Auth\PasswordRequiredException
     * @throws \Adldap\Auth\UsernameRequiredException
     * @throws \Adldap\Connections\ConnectionException
     * @author Andrian Iliev <andrian.iliev@orange.com>
     */
    public function ldapAuth()
    {
        self::init();

        try {
//            $user = self::$activeDirectory->search()->users()->find('ailiev')->filter(['cn']);
//            $user = self::$activeDirectory->search()->where('mail', '=', 'andrian.iliev@orange.com')->get();
//            $user = self::$activeDirectory->search()->select(self::$selectedAttributes)->where(
//                'mail',
//                '=',
//                'andrian.iliev@orange.com'
//            )->get();
            dd(self::$activeDirectory->auth()->attempt('andrian.iliev@orange.com','Figaro2032MaxiM!'));

//            dd(self::$activeDirectory->search()->findBy('mail', 'andrian.iliev@orange.com'));


            // Performing a raw search.
//            $user = self::$activeDirectory->search(
//                env('LDAP_BASE_DN'),
//                "ailiev",
//                self::$selectedAttributes
//            );
//            var_dump($user['cn']);
            dd($user);
            // Great, we're connected!
        } catch (Adldap\Auth\BindException $e) {
            // Failed to connect.
        }
    }
}
