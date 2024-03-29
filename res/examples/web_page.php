<?php
/**
 * Attention!
 * If You Did Not Run example/cron.php After Installation
 * You Must Run example/cron.php Before Running This Script
 */

/**
 * Display All Errors For Develop/Testing
 * Remove This Code In Production
 */

use Sonder\Plugins\CaptchaPlugin;

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * Path To init.php File
 */
require_once __DIR__ . '/../../init.php';

/**
 * Path To Directory With Captcha Database And Captcha Images
 */
$dataDirPath = __DIR__ . '/captcha';

/**
 * Secret String For Creating Unique Hashes
 */
$hashSalt = '<SECURITY_HASH_STRING>';

/**
 * URL Part For Web Access To img Directory With Captcha Images
 */
$imageUrlTemplate = '/captcha/img/';

/**
 * Captcha Language Code (Need To be Same As Dictionary Name)
 */
$language = 'uk';

$settings = [
    'data_dir_path' => $dataDirPath,
    'hash_salt' => $hashSalt,
    'image_url_template' => $imageUrlTemplate,
    'language' => $language
];

$result = null;
$text = null;
$hash = null;

/**
 * Remember! You Must Always Sanitize Input And Check Is Params Exists!
 * Plugin Do Not Contain This Logic!
 */
if (!empty($_POST)) {
    $text = $_POST['text'];
    $hash = $_POST['hash'];
}

try {
    $captchaPlugin = new CaptchaPlugin($settings);

    if (!empty($_POST)) { // If Captcha Failed
        $result = '<span style="color:red"><strong>Fail!</strong></span>';
    }

    if (
        !empty($text) &&
        !empty($hash) &&
        $captchaPlugin->check($text, $hash)
    ) { // If Captcha Successfully Passed
        $result = '<span style="color:green"><strong>Success!</strong></span>';
    }

    $captchaEntity = $captchaPlugin->getEntity();
    $captchaImageUrl = $captchaEntity->getImageUrlPath();
    $captchaHash = $captchaEntity->getHash();
} catch (Throwable $thr) {
    /**
     * If You Want To Catch Captcha Plugin Exception
     * You Can Find List Of Exception Classes, Errors And Codes
     * In exceptions Directory
     */
    echo sprintf('<h1>%s</h1>', get_class($thr));
    echo sprintf('<h2>%s</h2>', $thr->getMessage());
    echo sprintf('<h3>%s</h3>', $thr->getCode());

    exit(0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Example Integration Of Captcha Plugin</title>
    <style>
        body {
            text-align: center;
        }

        img {
            width: 200px;
            height: 50px;
            border: 1px solid #975;
            border-radius: .125rem;
            margin-top: 50px;
        }

        p {
            padding: 0;
            margin: .5rem;
        }
    </style>
</head>
<body>
<!-- Form Action Must Be This File -->
<form action="" method="post">
    <p>
        <!-- Display Is Captcha Passed By User -->
        <?= $result; ?>
    </p>
    <p>
        <!-- Display Captcha Image -->
        <img src="<?= $captchaImageUrl; ?>" alt="captcha">
    </p>
    <p>
        <!-- Captcha Text Param -->
        <label for="text"></label>
        <input id="text" type="text" name="text" placeholder="text">
    </p>
    <!-- Captcha Hash Param -->
    <input type="hidden" name="hash" value="<?= $captchaHash; ?>">
    <p>
        <input type="submit" value="SEND">
    </p>
</form>
</body>
</html>
