<?php
namespace Core\Plugins\Captcha\Exceptions;

class CaptchaTextException extends CaptchaException
{
    const MESSAGE_TEXT_DATA_DIR_PATH_IS_NOT_SET = 'Data Dir Path Of Captcha '.
                                                  'Plugin Is No Set';

    const MESSAGE_TEXT_FILE_PATH_IS_EMPTY = 'File Path Of Captcha Plugin Is '.
                                            'Empty';

    const MESSAGE_TEXT_CAN_NOT_OPEN_FILE = 'Can Not Open Dictionaries '.
                                           'Metadata File Of Captcha Plugin';

    const MESSAGE_TEXT_METADATA_HAS_BAD_FORMAT = 'Dictionaries Metadata '.
                                                 'Has Bad Format';

    const MESSAGE_TEXT_LANGUAGE_IS_NOT_SET = 'Language Of Captcha Plugin '.
                                             'Is Not Set';

    const MESSAGE_TEXT_INVALID_LANGUAGE = 'Invalid Language Value Of Captcha '.
                                          'Plugin';

    const MESSAGE_TEXT_DICTIONARY_IS_NOT_SET = 'Dictionary Of Captcha Plugin '.
                                               'Is Not Set';

    const MESSAGE_TEXT_DICTIONARIES_NOT_FOUND = 'Dictionaries Of Captcha '.
                                                'Plugin Not Found';

    const MESSAGE_TEXT_STORE_IS_NOT_SET = 'CaptchaStore Instance Of Captcha '.
                                          'Plugin Is Not Set';

    const MESSAGE_TEXT_WORDS_ARE_NOT_SET = 'Words Of Captcha Plugin Are '.
                                           'Not Set';

    const MESSAGE_TEXT_WORD_IS_EMPTY = 'Word Of Captcha Plugin Is Empty';
}
