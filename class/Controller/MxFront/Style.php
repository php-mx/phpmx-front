<?php

namespace Controller\MxFront;

use PhpMx\Response;
use PhpMx\View;

class Style
{
    function __invoke()
    {
        Response::type('css');
        Response::content(View::render('front/base.css'));
        Response::send();
    }
}
