<?php

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__));
    $dotenv->load();
    // Privilégier l'utilisation de $_ENV pour $dotenv
} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}

require __DIR__.'/../app/cartoEssence.php';