<?php
// index.php

date_default_timezone_set('UTC');


error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/core/init.php';
require_once __DIR__ . '/routes/web.php';
