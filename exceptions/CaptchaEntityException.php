<?php

namespace Sonder\Plugins\Captcha\Exceptions;

final class CaptchaEntityException extends CaptchaException
{
    final public const MESSAGE_ENTITY_TEXT_IS_EMPTY = 'Text Of Captcha Plugin Is Empty';
    final public const MESSAGE_ENTITY_HASH_IS_EMPTY = 'Hash Of Captcha Plugin Is Empty';
    final public const MESSAGE_ENTITY_FILE_PATH_IS_EMPTY = 'File Path Of Captcha Plugin Is Empty';
    final public const MESSAGE_ENTITY_URL_PATH_IS_EMPTY = 'URL Path Of Captcha Plugin Is Empty';
    final public const MESSAGE_ENTITY_CAN_NOT_CREATE_DIR = 'Can Not Create Image Directory Of Captcha Plugin';
}
