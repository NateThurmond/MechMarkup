<?php

// Load environment variables from the .env file (from the root directory)
require_once __DIR__ . '/../vendor/autoload.php';  // Adjust path if necessary
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); // Path to root directory
$dotenv->load();

/*
    This is not how I would normally store creds these days but I just need to get this project
    up and running for some screenshots to see how it use to look. It was one of my first college
    projects, however it stored separate database stuff which is lost to time and I'll need to reverse
    engineer
*/

define('MONGO_URI', $_ENV['MONGO_URI']);
define('MONGO_DB', $_ENV['MONGO_DB']);
define('MONGO_COLL_MECHS', $_ENV['MONGO_COLL_MECHS']);
define('MONGO_COLL_PDFS', $_ENV['MONGO_COLL_PDFS']);
define('MONGO_COLL_MARKUPS', $_ENV['MONGO_COLL_MARKUPS']);
define('MONGO_USER', $_ENV['MONGO_USER']);
define('MONGO_PASS', $_ENV['MONGO_PASS']);
define('VERSION', $_ENV['VERSION']);
define('MECH_SAVE_PASS', $_ENV['MECH_SAVE_PASS']);
define('WORKING_DIR', $_ENV['WORKING_DIR']);
define('MYSQL_HOST_LOCAL', $_ENV['MYSQL_HOST_LOCAL']);

?>