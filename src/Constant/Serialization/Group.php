<?php

namespace App\Constant\Serialization;

use App\Constant\BaseConstant;

class Group extends BaseConstant
{
    const LIST = 'LIST';
    const DETAIL = 'DETAIL';
    const EXTRA = 'EXTRA';

    const LIST_DETAIL = [self::LIST, self::DETAIL];
    const ALL = [self::LIST, self::DETAIL, self::EXTRA];
}