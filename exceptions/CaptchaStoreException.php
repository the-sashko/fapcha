<?php

namespace Sonder\Plugins\Captcha\Exceptions;

final class CaptchaStoreException extends CaptchaException
{
    final public const MESSAGE_STORE_DATA_DIR_PATH_IS_NOT_SET = 'Data Directory Path Of Captcha Plugin Is Not Set';
    final public const MESSAGE_STORE_DICTIONARY_IS_NOT_SET = 'Dictionary Of Captcha Plugin Is Not Set';
    final public const MESSAGE_STORE_ID_IS_NOT_SET = 'ID Of Captcha Plugin Is Not Set';
    final public const MESSAGE_STORE_WORD_IS_NOT_SET = 'Word Of Captcha Plugin Is Not Set';
    final public const MESSAGE_STORE_SQL_IS_EMPTY = 'SQL Of Captcha Plugin In Is Empty';
    final public const MESSAGE_STORE_QUERY_ERROR = 'Query Error In Database Of Captcha Plugin';
    final public const MESSAGE_STORE_TABLE_IS_EMPTY = 'Table In Database Of Captcha Plugin Is Empty';
    final public const MESSAGE_STORE_CAN_NOT_CREATE_DATABASE = 'Can Not Create Database Of Captcha Plugin';
    final public const MESSAGE_STORE_CAN_NOT_UPDATE_DATABASE = 'Can Not Update Database Of Captcha Plugin';
}
