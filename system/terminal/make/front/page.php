<?php

use PhpMx\Terminal;

return new class extends Terminal {

    function __invoke($page)
    {
        $page = path('page', $page);

        self::run('make.view', $page);
    }
};
