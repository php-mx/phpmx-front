<?php

namespace Controller\Mx;

use PhpMx\Response;
use PhpMx\View;

class Script
{
    function __invoke()
    {
        Response::type('js');
        Response::content(View::render('front/base.js'));
        Response::send();
    }
}
