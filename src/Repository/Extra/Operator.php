<?php

namespace App\Repository\Extra;

use App\Constant\BaseConstant;

class Operator extends BaseConstant
{
    const EQUAL = 'EQUAL';
    const NOT_EQUAL = 'NOT_EQUAL';
    const IN = 'IN';
    const NOT_IN = 'NOT_IN';
    const LESS_THAN = 'LESS_THAN';
    const LESS_THAN_OR_EQUAL = 'LESS_THAN_OR_EQUAL';
    const GREAT_THAN = 'GREAT_THAN';
    const GREAT_THAN_OR_EQUAL = 'GREAT_THAN_OR_EQUAL';
    const IS_NULL = 'IS_NULL';
}