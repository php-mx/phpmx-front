<?php

use PhpMx\Front\Icon;
use PhpMx\View;

View::$RENDER_CLASS['vue'] = [\PhpMx\View\RenderVue::class, false];

View::globalPrepare('URL', fn(...$params) => url(...$params));

View::globalPrepare('SVG', fn($iconName) => Icon::svg($iconName));
View::globalPrepare('ICON', fn($iconName, ...$styleClass) => Icon::get($iconName, ...$styleClass));

View::globalPrepare('FORM', fn($name) => prepare("data-form-key='[#]' method='post' action='[#]'", [
    mx5(["form-$name", url('.')]),
    url('.')
]));

View::globalPrepare('VUE', fn($app, $name = null) => View::render("$app.vue", [], ['name' => $name]));
