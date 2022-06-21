<?php

namespace Sonder\Plugins\Captcha\Exceptions;

final class CaptchaPluginException extends CaptchaException
{
    final public const MESSAGE_PLUGIN_SETTINGS_ARE_NOT_SET = 'Settings Instance Of Captcha Plugin Is Not Set';
    final public const MESSAGE_PLUGIN_TEXT_IS_NOT_SET = 'Captcha Plugin Text Is Not Set';
    final public const MESSAGE_PLUGIN_HASH_IS_NOT_SET = 'Captcha Plugin Hash Is Not Set';
}
