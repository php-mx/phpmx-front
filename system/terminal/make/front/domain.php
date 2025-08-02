<?php

use PhpMx\File;
use PhpMx\Import;
use PhpMx\Path;
use PhpMx\Terminal;

return new class extends Terminal {

    function __invoke($domain)
    {
        $domain = explode('/', $domain);
        $domain = array_map(fn($v) => str_replace(' ', '_', remove_accents(trim($v))), $domain);

        $domain = path('system/view/front/domain', ...$domain);
        $domain = "$domain.html";

        if (File::check($domain))
            return self::echo("[ignored] file [$domain] already exists");

        $content = Path::seekForFile('library/template/terminal/front/domain.txt');
        $content = Import::content($content);
        File::create($domain, $content);
        self::echo("[created] domain [$domain] created");
    }
};
