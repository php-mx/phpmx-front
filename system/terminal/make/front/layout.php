<?php

use PhpMx\File;
use PhpMx\Import;
use PhpMx\Path;
use PhpMx\Terminal;

return new class extends Terminal {

    function __invoke($layout)
    {
        $layout = explode('/', $layout);
        $layout = array_map(fn($v) => str_replace(' ', '_', remove_accents(trim($v))), $layout);
        $layout[] = '_content.html';
        $layout = path('system/view/front/layout', ...$layout);

        if (File::check($layout))
            return self::echo("[ignored] file [$layout] already exists");

        $content = Path::seekForFile('library/template/terminal/front/layout.txt');
        $content = Import::content($content);
        File::create($layout, $content);
        self::echo("[created] layout [$layout] created");
    }
};
