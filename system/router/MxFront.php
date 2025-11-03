<?php

use PhpMx\Router;

Router::get('style.css', \Controller\Mx\Style::class);
Router::get('script.js', \Controller\Mx\Script::class);
