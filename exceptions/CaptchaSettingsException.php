<?php

namespace Sonder\Plugins\Captcha\Exceptions;

final class CaptchaSettingsException extends CaptchaException
{
    final public const MESSAGE_SETTINGS_DATA_IS_EMPTY = 'Settings Data Of Captcha Plugin Is Empty';
    final public const MESSAGE_SETTINGS_SALT_IS_NOT_SET = 'Hash Salt Of Captcha Plugin Is Not Set';
    final public const MESSAGE_SETTINGS_SALT_IS_EMPTY = 'Hash Salt Of Captcha Plugin Is Empty';
    final public const MESSAGE_SETTINGS_DATA_DIR_NOT_FOUND = 'Data Directory Of Captcha Plugin Not Found';
}
