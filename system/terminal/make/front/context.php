<?php

use PhpMx\File;
use PhpMx\Import;
use PhpMx\Path;
use PhpMx\Terminal;

return new class extends Terminal {

    function __invoke($context)
    {
        $context = explode('/', $context);
        $context = array_map(fn($v) => str_replace(' ', '_', remove_accents(trim($v))), $context);

        $context = path('system/view/front/context', ...$context);
        $context = "$context.html";

        if (File::check($context))
            return self::echo("[ignored] file [$context] already exists");

        $content = Path::seekForFile('library/template/terminal/front/context.txt');
        $content = Import::content($content);
        File::create($context, $content);
        self::echo("[created] context [$context] created");
    }
};
