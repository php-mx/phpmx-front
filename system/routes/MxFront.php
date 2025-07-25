<?php

use PhpMx\Router;

Router::get('style.css', \Controller\MxFront\Style::class);
Router::get('script.js', \Controller\MxFront\Script::class);

Router::middleware(['front'], function () {
    Router::add('', \Controller\MxFront\Wellcome::class);
});
