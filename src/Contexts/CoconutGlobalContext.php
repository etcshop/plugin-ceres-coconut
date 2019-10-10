<?php

namespace CeresCoconut\Contexts;

use IO\Helper\ContextInterface;
use Ceres\Contexts\GlobalContext;

class CoconutGlobalContext extends GlobalContext implements ContextInterface
{
    public $cookieState;

    public function init($params)
    {
        parent::init($params);

        $this->cookieState = $_COOKIE['mcoo'];
    }
}
