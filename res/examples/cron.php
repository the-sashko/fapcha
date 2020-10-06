<?php
/**
 * Attention!
 * You Must Run This Script After Installation
 * You Must Run This Script After Changing Dictionaries
 */

/**
 * Display All Errors For Develop/Testing
 * Remove This Code In Production
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * Path To init.php File
 */
require_once __DIR__.'/captcha_plugin/init.php';

/**
 * Path To Directory With Captcha Database And Captcha Images
 */
$dataDirPath = __DIR__.'/captcha';

/**
 * Secret String For Creating Unique Hashes
 */
$hashSalt = <SECURITY_HASH_STRING>;

$settings = [
    'data_dir_path' => $dataDirPath,
    'hash_salt'     => $hashSalt
];

try {
    $captchaPlugin = new CaptchaPlugin();
    $captchaPlugin->setSettings($settings);
    $captchaPlugin->cron();
} catch (\Exception $exp) {
    /**
     * If You Want To Catch Captcha Plugin Exception
     * You Can Find List Of Exception Classes, Errors And Codes
     * In exeptions Direcrory
    */
    echo sprintf('<h1>%s</h1>', get_class($exp));
    echo sprintf('<h2>%s</h2>', $exp->getMessage());
    echo sprintf('<h3>%s</h3>', $exp->getCode());

    exit(0);
}
