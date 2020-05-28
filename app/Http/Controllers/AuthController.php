<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;

class AuthController extends Controller
{
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
     * @throws \Adldap\Auth\PasswordRequiredException
     * @throws \Adldap\Auth\UsernameRequiredException
     * @throws \Adldap\Connections\ConnectionException
     * @author Andrian Iliev <andrian.iliev@orange.com>
     */
    public function ldapAuth()
    {
        $user = Adldap::getDefaultProvider()->search()->users()->select(self::$selectedAttributes)->find('ailiev');

        dd($user);
     }

    /**
     * Import all users from LDAP
     * and insert it in the database
     *
     * @author Andrian Iliev <andrian.iliev@orange.com>
     */
    public function importAllUsers(): int
    {
        $ldapUsers = Adldap::getDefaultProvider()
            ->search()
            ->users()
            ->in(env('LDAP_USERS_CATALOG'))
            ->select(self::$selectedAttributes)
            ->get();

        $countOfAddedUsers = 0;
        $totalCountOfLdapUsers = count($ldapUsers);

        if ($totalCountOfLdapUsers) {
            foreach ($ldapUsers as $key => $ldapUser) {
                if (
                    !empty($ldapUser->cn[0])
                    && !empty($ldapUser->mail[0])
                    && !empty($ldapUser->displayName[0])
                    && !empty($ldapUser->title[0])
                    && !empty($ldapUser->mobile[0])
                    && !empty($ldapUser->thumbnailPhoto[0])
                ) {

                    User::insert([
                                     'guid' => $ldapUser->getConvertedGuid(),
                                     'name' => $ldapUser->cn[0],
                                     'email' => $ldapUser->mail[0],
                                     'username' => $ldapUser->displayName[0],
                                     'title' => $ldapUser->title[0],
                                     'mobile' => $ldapUser->mobile[0],
                                     'thumbnailPhoto' => base64_encode($ldapUser->thumbnailPhoto[0])
                                 ]);
                    $countOfAddedUsers++;
                }
            }
        }
    }
}
