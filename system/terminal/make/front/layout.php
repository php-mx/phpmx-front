<?php

use PhpMx\File;
use PhpMx\Import;
use PhpMx\Path;
use PhpMx\Terminal;

return new class extends Terminal {

    function __invoke($layout)
    {
        $file = path('system/view/_front/layout', "$layout.html");

        if (File::check($file))
            return self::echo("[ignored] file [$file] already exists");

        $content = Path::seekForFile('library/template/terminal/front/layout.txt');
        $content = Import::content($content);
        File::create($file, $content);
        self::echo("[created] layout [$layout] created");
    }
};
