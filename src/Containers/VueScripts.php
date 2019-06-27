<?php

namespace CeresCoconut\Containers;

use Plenty\Plugin\Templates\Twig;

class VueScripts
{
    public function call(Twig $twig):string
    {
        return $twig->render('CeresCoconut::VueScripts');
    }
}
