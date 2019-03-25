<?php

namespace App\Constant\User;

use App\Constant\BaseConstant;
use App\Entity\User\User;

class Role extends BaseConstant
{
    const ROLE_USER = User::ROLE_DEFAULT;
    const ROLE_ADMIN = 'ROLE_ADMIN';
}