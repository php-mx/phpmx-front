<?php

namespace Controller\MxFront;

use PhpMx\Response;
use PhpMx\View;

class Script
{
    function __invoke()
    {
        Response::type('js');
        Response::content(View::render('_front/script'));
        Response::send();
    }
}
