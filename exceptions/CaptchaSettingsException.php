<?php
namespace Core\Plugins\Captcha\Exceptions;

class CaptchaSettingsException extends CaptchaException
{
    const MESSAGE_SETTINGS_DATA_IS_EMPTY = 'Settings Data Of Captcha Plugin '.
                                           'Is Empty';

    const MESSAGE_SETTINGS_SALT_IS_NOT_SET = 'Hash Salt Of Captcha Plugin Is '.
                                             'Not Set';

    const MESSAGE_SETTINGS_SALT_IS_EMPTY = 'Hash Salt Of Captcha Plugin Is '.
                                           'Empty';

    const MESSAGE_SETTINGS_DATA_DIR_NOT_FOUND = 'Data Directory Of Captcha '.
                                                'Plugin Not Found';
}
