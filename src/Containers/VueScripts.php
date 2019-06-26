<?php

namespace CeresCoconut\Containers;

use Plenty\Plugin\Templates\Twig;

class CeresCoconutVueScripts
{
    public function call(Twig $twig):string
    {
        return $twig->render('CeresCoconut::VueScripts');
    }
}
