<?php
namespace Core\Plugins\Captcha\Exceptions;

class CaptchaImageException extends CaptchaException
{
    const MESSAGE_IMAGE_TEXT_IS_EMPTY = 'Image Text Of Captcha Plugin Is '.
                                        'Empty';

    const MESSAGE_IMAGE_FILE_PATH_IS_NOT_SET = 'Image File Path Of Captcha '.
                                               'Plugin Is Not Set';

    const MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY = 'Imagick Instance Of '.
                                                    'Captcha Plugin Is Empty';

    const MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY = 'Imagick Draw Instance Of '.
                                                 'Captcha Plugin Is Empty';

    const MESSAGE_IMAGE_TEXT_IS_NOT_SET = 'Image Text Of Captcha Plugin Is '.
                                          'Not Set';

    const MESSAGE_IMAGE_TEXT_LENGTH_IS_NOT_SET = 'Image Text Length Of '.
                                                 'Captcha Plugin Is Not Set';

    const MESSAGE_IMAGE_WIDTH_IS_NOT_SET = 'With Of Captcha Plugin Image Is '.
                                           'Not Set';

    const MESSAGE_IMAGE_HEIGHT_IS_NOT_SET = 'Height Of Captcha Plugin Image '.
                                            'Is Not Set';

    const MESSAGE_IMAGE_DOT_SIZE_HAS_BAD_FORMAT = 'Dot Size Of Captcha '.
                                                  'Plugin Image Has Bad '.
                                                  'Format';

    const MESSAGE_IMAGE_CREATE_ERROR = 'Can Not Create Image Of Captcha '.
                                       'Plugin';
}
