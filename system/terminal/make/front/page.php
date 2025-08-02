<?php

use PhpMx\File;
use PhpMx\Import;
use PhpMx\Path;
use PhpMx\Terminal;

return new class extends Terminal {

    function __invoke($page)
    {
        $page = explode('/', $page);
        $page = array_map(fn($v) => str_replace(' ', '_', remove_accents(trim($v))), $page);

        $page = path('system/view/page', ...$page);
        $page = "$page.html";

        if (File::check($page))
            return self::echo("[ignored] file [$page] already exists");

        $content = Path::seekForFile('library/template/terminal/front/page.txt');
        $content = Import::content($content);
        File::create($page, $content);
        self::echo("[created] page [$page] created");
    }
};
