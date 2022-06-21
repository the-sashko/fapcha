<?php

namespace Sonder\Plugins\Captcha\Exceptions;

use Exception;
use Throwable;

class CaptchaException extends Exception implements Throwable
{
    final public const CODE_PLUGIN_SETTINGS_ARE_NOT_SET = 1001;
    final public const CODE_PLUGIN_TEXT_IS_NOT_SET = 1002;
    final public const CODE_PLUGIN_HASH_IS_NOT_SET = 1003;

    final public const CODE_SETTINGS_DATA_IS_EMPTY = 2001;
    final public const CODE_SETTINGS_SALT_IS_NOT_SET = 2002;
    final public const CODE_SETTINGS_SALT_IS_EMPTY = 2003;
    final public const CODE_SETTINGS_DATA_DIR_NOT_FOUND = 2004;

    final public const CODE_IMAGE_FILE_PATH_IS_NOT_SET = 3001;
    final public const CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY = 3002;
    final public const CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY = 3003;
    final public const CODE_IMAGE_TEXT_IS_NOT_SET = 3004;
    final public const CODE_IMAGE_TEXT_LENGTH_IS_NOT_SET = 3005;
    final public const CODE_IMAGE_WIDTH_IS_NOT_SET = 3006;
    final public const CODE_IMAGE_HEIGHT_IS_NOT_SET = 3007;
    final public const CODE_IMAGE_DOT_SIZE_HAS_BAD_FORMAT = 3008;
    final public const CODE_IMAGE_CREATE_ERROR = 3009;

    final public const CODE_ENTITY_TEXT_IS_EMPTY = 4001;
    final public const CODE_ENTITY_HASH_IS_EMPTY = 4002;
    final public const CODE_ENTITY_FILE_PATH_IS_EMPTY = 4003;
    final public const CODE_ENTITY_URL_PATH_IS_EMPTY = 4004;
    final public const CODE_ENTITY_CAN_NOT_CREATE_DIR = 4005;

    final public const CODE_STORE_DATA_DIR_PATH_IS_NOT_SET = 5001;
    final public const CODE_STORE_DICTIONARY_IS_NOT_SET = 5002;
    final public const CODE_STORE_ID_IS_NOT_SET = 5003;
    final public const CODE_STORE_WORD_IS_NOT_SET = 5004;
    final public const CODE_STORE_SQL_IS_EMPTY = 5005;
    final public const CODE_STORE_QUERY_ERROR = 5006;
    final public const CODE_STORE_TABLE_IS_EMPTY = 5007;
    final public const CODE_STORE_CAN_NOT_CREATE_DATABASE = 5008;
    final public const CODE_STORE_CAN_NOT_UPDATE_DATABASE = 5009;

    final public const CODE_TEXT_DATA_DIR_PATH_IS_NOT_SET = 6001;
    final public const CODE_TEXT_FILE_PATH_IS_EMPTY = 6002;
    final public const CODE_TEXT_CAN_NOT_OPEN_FILE = 6003;
    final public const CODE_TEXT_METADATA_HAS_BAD_FORMAT = 6004;
    final public const CODE_TEXT_LANGUAGE_IS_NOT_SET = 6005;
    final public const CODE_TEXT_INVALID_LANGUAGE = 6006;
    final public const CODE_TEXT_DICTIONARY_IS_NOT_SET = 6007;
    final public const CODE_TEXT_DICTIONARIES_NOT_FOUND = 6008;
    final public const CODE_TEXT_STORE_IS_NOT_SET = 6009;
    final public const CODE_TEXT_WORDS_ARE_NOT_SET = 6010;
    final public const CODE_TEXT_WORD_IS_EMPTY = 6011;
}
