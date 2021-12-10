<?php

return [
    /* FRONTEND (Site) */
    '' => 'site/index',
    'page/<view:[a-zA-Z0-9-]+>' => 'site/page',

    '<controller:\w+>/page-<page:\d+>' => '<controller>/index',
    '<controller:\w+>' => '<controller>/index',
    '<controller:\w+>/<action>/<id:\d+>' => '<controller>/<action>',
];