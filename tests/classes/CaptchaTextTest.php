<?php
use Core\Plugins\Captcha\Classes\CaptchaText;
use Core\Plugins\Captcha\Classes\CaptchaStore;

/**
 * Class For Testing CaptchaPlugin Text Class
 */
class CaptchaTextTest extends CaptchaTest
{
    /**
     * @var string Sample Data Directory Path For Unit Tests
     */
    const DATA_DIR_PATH = __DIR__.'/../tmp';

    /**
     * @var string Sample Database Path For Unit Tests
     */
    const DATABASE_FILE_PATH = __DIR__.'/../tmp/dictionaries.db';

    /**
     * @var string Sample Database File Name For Unit Tests
     */
    const DATABASE_FILE_NAME = 'dictionaries.db';

    /**
     * @var string Sample Captcha Dictionary Of Adjectives For Unit Tests
     */
    const DICTIONARY_ADJECTIVE = 'test_adjective_male';

    /**
     * @var string Sample Captcha Dictionary Of Nouns For Unit Tests
     */
    const DICTIONARY_NOUN = 'test_noun_male';

    /**
     * @var string Sample Captcha Adjective Word For Unit Tests
     */
    const WORD_ADJECTIVE = 'foo';

    /**
     * @var array Sample List Of Settings Data For Unit Tests
     */
    const SETTINGS_DATA_LIST = [
        [
            'hash_salt'          => 'foo',
            'image_url_template' => 'foo',
            'language'           => 'foo'
        ],
        [
            'hash_salt'          => 'bar',
            'image_url_template' => 'bar',
            'language'           => 'bar'
        ],
        [
            'hash_salt'          => 'test',
            'image_url_template' => 'test',
            'language'           => 'test'
        ]
    ];

    /**
     * @var array Sample List Of Settings Meta Data For Unit Tests
     */
    const SETTINGS_META_LIST = [
        'foo' => [
            'gender_derivatives' => true
        ],
        'bar' => [
            'gender_derivatives' => false
        ],
        'test' => [
            'gender_derivatives' => false
        ]
    ];

    /**
     * @var array Sample Invalid List Of Settings Meta Data For Unit Tests
     */
    const INVALID_SETTINGS_META_LIST = [
        'foo' => 'bar'
    ];

    /**
     * @var string Sample Test File Path For Unit Tests
     */
    const TEST_FILE_PATH = __DIR__.'/../tmp/test.txt';

    /**
     * @var string Sample Language Value For Unit Tests
     */
    const LANGUAGE = 'test';

    /**
     * @var string Sample Invalid Language Value For Unit Tests
     */
    const INVALID_LANGUAGE = 'invalid';

    /**
     * @var array Sample List Of Dictionaries With Genders For Unit Tests
     */
    const DICTIONARIES_WITH_GENDERS = [
        'test_adjective_male'    => '{dir}/test/adjective_male.txt',
        'test_adjective_female'  => '{dir}/test/adjective_female.txt',
        'test_adjective_neutral' => '{dir}/test/adjective_neutral.txt',
        'test_adjective_plural'  => '{dir}/test/adjective_plural.txt',
        'test_noun_male'         => '{dir}/test/noun_male.txt',
        'test_noun_female'       => '{dir}/test/noun_female.txt',
        'test_noun_neutral'      => '{dir}/test/noun_neutral.txt',
        'test_noun_plural'       => '{dir}/test/noun_plural.txt'
    ];

    /**
     * @var array Sample List Of Dictionaries Without Genders For Unit Tests
     */
    const DICTIONARIES_WITHOUT_GENDERS = [
        'test_adjective_male'    => '{dir}/test/adjective.txt',
        'test_adjective_female'  => '{dir}/test/adjective.txt',
        'test_adjective_neutral' => '{dir}/test/adjective.txt',
        'test_adjective_plural'  => '{dir}/test/adjective.txt',
        'test_noun_male'         => '{dir}/test/noun.txt',
        'test_noun_female'       => '{dir}/test/noun.txt',
        'test_noun_neutral'      => '{dir}/test/noun.txt',
        'test_noun_plural'       => '{dir}/test/noun.txt'
    ];

    /**
     * @var string Sample Captcha Text For Unit Tests
     */
    const TEXT = 'foo bar';

    /**
     * Unit Test Of CaptchaText get Method
     */
    public function testGet(): void
    {
        $captchaText = new CaptchaText();

        $exception = null;

        try {
            $captchaText->get();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $language = static::INVALID_LANGUAGE;

        $exception = null;

        try {
            $captchaText->get($language);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $language = static::LANGUAGE;

        $exception = null;

        try {
            $captchaText->get($language);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $text = $captchaText->get($language, static::DATA_DIR_PATH);

        $this->assertEquals($text, static::TEXT);
    }

    /**
     * Unit Test Of CaptchaText update Method
     */
    public function testUpdate(): void
    {
        $captchaText = new CaptchaText();

        if (
            file_exists(static::DATABASE_FILE_PATH) &&
            is_file(static::DATABASE_FILE_PATH)
        ) {
            unlink(static::DATABASE_FILE_PATH);
        }

        $this->assertFalse(
            file_exists(static::DATABASE_FILE_PATH) &&
            is_file(static::DATABASE_FILE_PATH)
        );

        $captchaText->update(static::DATA_DIR_PATH);

        $this->assertTrue(
            file_exists(static::DATABASE_FILE_PATH) &&
            is_file(static::DATABASE_FILE_PATH)
        );

        $this->prepareStore();
    }

    /**
     * Unit Test Of CaptchaText _isValidLanguage Method
     */
    public function testIsValidLanguage(): void
    {
        $captchaText = new CaptchaText();

        $isValidLanguageMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaText',
            '_isValidLanguage'
        );

        $isValidLanguageMethod->setAccessible(true);

        $this->assertFalse($isValidLanguageMethod->invoke($captchaText));

        $this->assertFalse($isValidLanguageMethod->invokeArgs(
            $captchaText,
            [static::INVALID_LANGUAGE]
        ));

        $this->assertTrue($isValidLanguageMethod->invokeArgs(
            $captchaText,
            [static::LANGUAGE]
        ));
    }

    /**
     * Unit Test Of CaptchaText _insertDictionaryToStore Method
     */
    public function testInsertDictionaryToStore(): void
    {
        $captchaText = new CaptchaText();

        $captchaStore = new CaptchaStore(
            static::DATA_DIR_PATH,
            static::DATABASE_FILE_NAME
        );

        $insertDictionaryToStoreMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaText',
            '_insertDictionaryToStore'
        );

        $queryMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaStore',
            '_query'
        );

        $insertDictionaryToStoreMethod->setAccessible(true);
        $queryMethod->setAccessible(true);

        $exception = null;

        try {
            $insertDictionaryToStoreMethod->invoke($captchaText);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $insertDictionaryToStoreMethod->invokeArgs(
                $captchaText,
                [static::DICTIONARY_ADJECTIVE]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $insertDictionaryToStoreMethod->invokeArgs(
                $captchaText,
                [
                    static::DICTIONARY_ADJECTIVE,
                    [static::WORD_ADJECTIVE]
                ]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $sql = 'DROP TABLE %s;';
        $sql = sprintf($sql, static::DICTIONARY_ADJECTIVE);
        $queryMethod->invokeArgs($captchaStore, [$sql]);

        $insertDictionaryToStoreMethod->invokeArgs(
            $captchaText,
            [
                static::DICTIONARY_ADJECTIVE,
                [static::WORD_ADJECTIVE],
                $captchaStore
            ]
        );

        $word = $captchaStore->getRandomWord(
            static::DICTIONARY_ADJECTIVE
        );

        $this->assertEquals($word, static::WORD_ADJECTIVE);

        $this->prepareStore();
    }

    /**
     * Unit Test Of CaptchaText _getGender Method
     */
    public function testGetGender(): void
    {
        $captchaText = new CaptchaText();

        $getGenderMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaText',
            '_getGender'
        );

        $getGenderMethod->setAccessible(true);

        for ($i = 1; $i <= 10; $i++) {
            $gender = $getGenderMethod->invoke($captchaText);

            $this->assertNotEmpty($gender);
            $this->assertTrue(in_array($gender, CaptchaText::GENDERS));
        }
    }

    /**
     * Unit Test Of CaptchaText _getDictionaries Method
     */
    public function testGetDictionaries(): void
    {
        $captchaText = new CaptchaText();

        $getDictionariesMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaText',
            '_getDictionaries'
        );

        $getDictionariesMethod->setAccessible(true);

        $dictionaries = $getDictionariesMethod->invoke($captchaText);

        $this->assertNotEmpty($dictionaries);

        $this->assertTrue(array_key_exists(
            static::DICTIONARY_ADJECTIVE,
            $dictionaries
        ));

        $dictionary = $dictionaries[static::DICTIONARY_ADJECTIVE];

        $this->assertNotEmpty($dictionary);

        $this->assertTrue(array_key_exists(
            static::DICTIONARY_NOUN,
            $dictionaries
        ));

        $dictionary = $dictionaries[static::DICTIONARY_NOUN];

        $this->assertNotEmpty($dictionary);
    }

    /**
     * Unit Test Of CaptchaText _isDictionariesMetaHasCorrectFormat Method
     */
    public function testIsDictionariesMetaHasCorrectFormat(): void
    {
        $captchaText = new CaptchaText();

        $isDictionariesMetaHasCorrectFormatMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaText',
            '_isDictionariesMetaHasCorrectFormat'
        );

        $isDictionariesMetaHasCorrectFormatMethod->setAccessible(true);

        $exception = null;

        try {
            $isDictionariesMetaHasCorrectFormatMethod->invokeArgs(
                $captchaText,
                [static::INVALID_SETTINGS_META_LIST]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $this->assertFalse($isDictionariesMetaHasCorrectFormatMethod->invoke(
            $captchaText
        ));

        $this->assertTrue(
            $isDictionariesMetaHasCorrectFormatMethod->invokeArgs(
                $captchaText,
                [static::SETTINGS_META_LIST]
            )
        );
    }

    /**
     * Unit Test Of CaptchaText _setDictionariesByLanguage Method
     */
    public function testSetDictionariesByLanguage(): void
    {
        $captchaText = new CaptchaText();

        $setDictionariesByLanguageMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaText',
            '_setDictionariesByLanguage'
        );

        $setDictionariesByLanguageMethod->setAccessible(true);

        $dictionaries = [];

        $exception = null;

        try {
            $setDictionariesByLanguageMethod->invokeArgs(
                $captchaText,
                [&$dictionaries]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
               'Core\Plugins\Captcha\Exceptions\CaptchaTextException',
               $exception
            );
        }

        $this->assertNotEmpty($exception);

        $setDictionariesByLanguageMethod->invokeArgs(
            $captchaText,
            [
                &$dictionaries,
                static::LANGUAGE,
                false
            ]
        );

        $this->assertEquals(
            $dictionaries,
            $this->_getSampleDictionaries(false)
        );

        $dictionaries = [];

        $setDictionariesByLanguageMethod->invokeArgs(
            $captchaText,
            [
                &$dictionaries,
                static::LANGUAGE,
                true
            ]
        );

        $this->assertEquals(
            $dictionaries,
            $this->_getSampleDictionaries(true)
        );

        $this->prepareStore();
    }

    /**
     * Unit Test Of CaptchaText _getTextFromFile Method
     */
    public function testGetTextFromFile(): void
    {
        $captchaText = new CaptchaText();

        $getTextFromFileMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaText',
            '_getTextFromFile'
        );

        $getTextFromFileMethod->setAccessible(true);

        if (
            file_exists(static::TEST_FILE_PATH) &&
            is_file(static::TEST_FILE_PATH)
        ) {
            unlink(static::TEST_FILE_PATH);
        }

        file_put_contents(static::TEST_FILE_PATH, static::TEXT);
        chmod(static::TEST_FILE_PATH, 0775);

        $exception = null;

        try {
            $getTextFromFileMethod->invoke($captchaText);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $text = $getTextFromFileMethod->invokeArgs(
            $captchaText,
            [static::TEST_FILE_PATH]
        );

        $this->assertEquals($text, static::TEXT);

        unlink(static::TEST_FILE_PATH);

        $exception = null;

        try {
            $getTextFromFileMethod->invokeArgs(
                $captchaText,
                [static::TEST_FILE_PATH]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);
    }

    private function _getSampleDictionaries(bool $isGenderDerivatives): array
    {
        $dictionaries = static::DICTIONARIES_WITHOUT_GENDERS;

        if ($isGenderDerivatives) {
            $dictionaries = static::DICTIONARIES_WITH_GENDERS;
        }

        $dirPath = realpath(__DIR__.'/../../classes');
        $dirPath = $dirPath.'/../res/dictionaries';

        foreach ($dictionaries as $dictionaryKey => $dictionary) {
            $dictionary = str_replace('{dir}', $dirPath, $dictionary);
            $dictionaries[$dictionaryKey] = $dictionary;
        }

        return $dictionaries;
    }
}
