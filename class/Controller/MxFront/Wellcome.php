<?php

namespace Controller\MxFront;

use PhpMx\View;

class Wellcome
{
    function __invoke()
    {
        return View::render('page/wellcome');
    }
}
