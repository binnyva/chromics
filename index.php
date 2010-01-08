<?php
include('common.php');
include('get_comics.php');

$template->addResource('http://localhost/iframe/js/library/plugins/jsl_debug.js','js',true);
render();
