<?php
$captchaPluginAutoload = function(string $dir, Closure $autoload): void
{
    foreach (glob($dir.'/*') as $fileItem) {
        if ($fileItem == __FILE__) {
            continue;
        }

        if (is_dir($fileItem)) {
            $autoload($fileItem, $autoload);

            continue;
        }

        if (preg_match('/^(.*?)\.php$/', $fileItem)) {
            include_once $fileItem;
        }
    }
};

require_once __DIR__.'/exceptions/CaptchaException.php';

$captchaPluginAutoload(__DIR__.'/exceptions', $captchaPluginAutoload);
$captchaPluginAutoload(__DIR__.'/interfaces', $captchaPluginAutoload);
$captchaPluginAutoload(__DIR__.'/classes', $captchaPluginAutoload);

require_once __DIR__.'/captcha.plugin.php';
