<?php

namespace CeresCoconut\Contexts;

use IO\Helper\ContextInterface;
use Ceres\Contexts\GlobalContext;

class CoconutGlobalContext extends GlobalContext implements ContextInterface
{
    public $cookieState;

    public function init($params)
    {
        $this->$cookieState = $sessionStorageService->getCookieState();
    }
}
