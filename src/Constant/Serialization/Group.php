<?php

namespace App\Constant\Serialization;

use App\Constant\BaseConstant;

class Group extends BaseConstant
{
    const DEFAULT = 'DEFAULT';

    const LIST = 'LIST';
    const DETAIL = 'DETAIL';
    const EXTRA = 'EXTRA';

    const LIST_DETAIL = [self::DEFAULT, self::LIST, self::DETAIL];
    const ALL = [self::DEFAULT, self::LIST, self::DETAIL, self::EXTRA];
}