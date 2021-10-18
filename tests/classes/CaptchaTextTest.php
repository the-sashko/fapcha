<?php

use Sonder\Plugins\Captcha\Classes\CaptchaStore;
use Sonder\Plugins\Captcha\Classes\CaptchaText;
use Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaTextException;

final class CaptchaTextTest extends CaptchaTest
{
    const DATA_DIR_PATH = __DIR__ . '/../tmp';

    const DATABASE_FILE_PATH = __DIR__ . '/../tmp/dictionaries.db';

    const DATABASE_FILE_NAME = 'dictionaries.db';

    const DICTIONARY_ADJECTIVE = 'test_adjective_male';

    const DICTIONARY_NOUN = 'test_noun_male';

    const WORD_ADJECTIVE = 'foo';

    const SETTINGS_DATA_LIST = [
        [
            'hash_salt' => 'foo',
            'image_url_template' => 'foo',
            'language' => 'foo'
        ],
        [
            'hash_salt' => 'bar',
            'image_url_template' => 'bar',
            'language' => 'bar'
        ],
        [
            'hash_salt' => 'test',
            'image_url_template' => 'test',
            'language' => 'test'
        ]
    ];

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

    const INVALID_SETTINGS_META_LIST = [
        'foo' => 'bar'
    ];

    const TEST_FILE_PATH = __DIR__ . '/../tmp/test.txt';

    const LANGUAGE = 'test';

    const INVALID_LANGUAGE = 'invalid';

    const DICTIONARIES_WITH_GENDERS = [
        'test_adjective_male' => '{dir}/test/adjective_male.txt',
        'test_adjective_female' => '{dir}/test/adjective_female.txt',
        'test_adjective_neutral' => '{dir}/test/adjective_neutral.txt',
        'test_adjective_plural' => '{dir}/test/adjective_plural.txt',
        'test_noun_male' => '{dir}/test/noun_male.txt',
        'test_noun_female' => '{dir}/test/noun_female.txt',
        'test_noun_neutral' => '{dir}/test/noun_neutral.txt',
        'test_noun_plural' => '{dir}/test/noun_plural.txt'
    ];

    const DICTIONARIES_WITHOUT_GENDERS = [
        'test_adjective_male' => '{dir}/test/adjective.txt',
        'test_adjective_female' => '{dir}/test/adjective.txt',
        'test_adjective_neutral' => '{dir}/test/adjective.txt',
        'test_adjective_plural' => '{dir}/test/adjective.txt',
        'test_noun_male' => '{dir}/test/noun.txt',
        'test_noun_female' => '{dir}/test/noun.txt',
        'test_noun_neutral' => '{dir}/test/noun.txt',
        'test_noun_plural' => '{dir}/test/noun.txt'
    ];

    const TEXT = 'foo bar';

    /**
     * @throws CaptchaStoreException
     * @throws CaptchaTextException
     */
    final public function testGet(): void
    {
        $captchaText = new CaptchaText();

        $exception = null;

        try {
            $captchaText->get();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $language = CaptchaTextTest::INVALID_LANGUAGE;

        $exception = null;

        try {
            $captchaText->get($language);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $language = CaptchaTextTest::LANGUAGE;

        $exception = null;

        try {
            $captchaText->get($language);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $text = $captchaText->get($language, CaptchaTextTest::DATA_DIR_PATH);

        $this->assertEquals(CaptchaTextTest::TEXT, $text);
    }

    /**
     * @throws CaptchaStoreException
     * @throws CaptchaTextException
     */
    final public function testUpdate(): void
    {
        $captchaText = new CaptchaText();

        if (
            file_exists(CaptchaTextTest::DATABASE_FILE_PATH) &&
            is_file(CaptchaTextTest::DATABASE_FILE_PATH)
        ) {
            unlink(CaptchaTextTest::DATABASE_FILE_PATH);
        }

        $this->assertFalse(
            file_exists(CaptchaTextTest::DATABASE_FILE_PATH) &&
            is_file(CaptchaTextTest::DATABASE_FILE_PATH)
        );

        $captchaText->update(CaptchaTextTest::DATA_DIR_PATH);

        $this->assertTrue(
            file_exists(CaptchaTextTest::DATABASE_FILE_PATH) &&
            is_file(CaptchaTextTest::DATABASE_FILE_PATH)
        );

        $this->prepareStore();
    }

    /**
     * @throws ReflectionException
     */
    final public function testIsValidLanguage(): void
    {
        $captchaText = new CaptchaText();

        $isValidLanguageMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaText',
            '_isValidLanguage'
        );

        $isValidLanguageMethod->setAccessible(true);

        $this->assertFalse($isValidLanguageMethod->invoke($captchaText));

        $this->assertFalse($isValidLanguageMethod->invokeArgs(
            $captchaText,
            [CaptchaTextTest::INVALID_LANGUAGE]
        ));

        $this->assertTrue($isValidLanguageMethod->invokeArgs(
            $captchaText,
            [CaptchaTextTest::LANGUAGE]
        ));
    }

    /**
     * @throws CaptchaStoreException
     * @throws ReflectionException
     */
    final public function testInsertDictionaryToStore(): void
    {
        $captchaText = new CaptchaText();

        $captchaStore = new CaptchaStore(
            CaptchaTextTest::DATA_DIR_PATH,
            CaptchaTextTest::DATABASE_FILE_NAME
        );

        $insertDictionaryToStoreMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaText',
            '_insertDictionaryToStore'
        );

        $queryMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaStore',
            '_query'
        );

        $insertDictionaryToStoreMethod->setAccessible(true);
        $queryMethod->setAccessible(true);

        $exception = null;

        try {
            $insertDictionaryToStoreMethod->invoke($captchaText);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $insertDictionaryToStoreMethod->invokeArgs(
                $captchaText,
                [CaptchaTextTest::DICTIONARY_ADJECTIVE]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $insertDictionaryToStoreMethod->invokeArgs(
                $captchaText,
                [
                    CaptchaTextTest::DICTIONARY_ADJECTIVE,
                    [CaptchaTextTest::WORD_ADJECTIVE]
                ]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $sql = 'DROP TABLE %s;';
        $sql = sprintf($sql, CaptchaTextTest::DICTIONARY_ADJECTIVE);
        $queryMethod->invokeArgs($captchaStore, [$sql]);

        $insertDictionaryToStoreMethod->invokeArgs(
            $captchaText,
            [
                CaptchaTextTest::DICTIONARY_ADJECTIVE,
                [CaptchaTextTest::WORD_ADJECTIVE],
                $captchaStore
            ]
        );

        $word = $captchaStore->getRandomWord(
            CaptchaTextTest::DICTIONARY_ADJECTIVE
        );

        $this->assertEquals(CaptchaTextTest::WORD_ADJECTIVE, $word);

        $this->prepareStore();
    }

    /**
     * @throws ReflectionException
     */
    final public function testGetGender(): void
    {
        $captchaText = new CaptchaText();

        $getGenderMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaText',
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
     * @throws ReflectionException
     */
    final public function testGetDictionaries(): void
    {
        $captchaText = new CaptchaText();

        $getDictionariesMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaText',
            '_getDictionaries'
        );

        $getDictionariesMethod->setAccessible(true);

        $dictionaries = $getDictionariesMethod->invoke($captchaText);

        $this->assertNotEmpty($dictionaries);

        $this->assertArrayHasKey(CaptchaTextTest::DICTIONARY_ADJECTIVE,
            $dictionaries);

        $dictionary = $dictionaries[CaptchaTextTest::DICTIONARY_ADJECTIVE];

        $this->assertNotEmpty($dictionary);

        $this->assertArrayHasKey(CaptchaTextTest::DICTIONARY_NOUN,
            $dictionaries);

        $dictionary = $dictionaries[CaptchaTextTest::DICTIONARY_NOUN];

        $this->assertNotEmpty($dictionary);
    }

    /**
     * @throws ReflectionException
     */
    final public function testIsDictionariesMetaHasCorrectFormat(): void
    {
        $captchaText = new CaptchaText();

        $isDictionariesMetaHasCorrectFormatMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaText',
            '_isDictionariesMetaHasCorrectFormat'
        );

        $isDictionariesMetaHasCorrectFormatMethod->setAccessible(true);

        $exception = null;

        try {
            $isDictionariesMetaHasCorrectFormatMethod->invokeArgs(
                $captchaText,
                [CaptchaTextTest::INVALID_SETTINGS_META_LIST]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaTextException',
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
                [CaptchaTextTest::SETTINGS_META_LIST]
            )
        );
    }

    /**
     * @throws ReflectionException
     */
    final public function testSetDictionariesByLanguage(): void
    {
        $captchaText = new CaptchaText();

        $setDictionariesByLanguageMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaText',
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
                'Sonder\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $setDictionariesByLanguageMethod->invokeArgs(
            $captchaText,
            [
                &$dictionaries,
                CaptchaTextTest::LANGUAGE,
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
                CaptchaTextTest::LANGUAGE,
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
     * @throws ReflectionException
     */
    final public function testGetTextFromFile(): void
    {
        $captchaText = new CaptchaText();

        $getTextFromFileMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaText',
            '_getTextFromFile'
        );

        $getTextFromFileMethod->setAccessible(true);

        if (
            file_exists(CaptchaTextTest::TEST_FILE_PATH) &&
            is_file(CaptchaTextTest::TEST_FILE_PATH)
        ) {
            unlink(CaptchaTextTest::TEST_FILE_PATH);
        }

        file_put_contents(CaptchaTextTest::TEST_FILE_PATH, CaptchaTextTest::TEXT);
        chmod(CaptchaTextTest::TEST_FILE_PATH, 0775);

        $exception = null;

        try {
            $getTextFromFileMethod->invoke($captchaText);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $text = $getTextFromFileMethod->invokeArgs(
            $captchaText,
            [CaptchaTextTest::TEST_FILE_PATH]
        );

        $this->assertEquals(CaptchaTextTest::TEXT, $text);

        unlink(CaptchaTextTest::TEST_FILE_PATH);

        $exception = null;

        try {
            $getTextFromFileMethod->invokeArgs(
                $captchaText,
                [CaptchaTextTest::TEST_FILE_PATH]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);
    }

    /**
     * @param bool $isGenderDerivatives
     *
     * @return string[]
     */
    private function _getSampleDictionaries(bool $isGenderDerivatives): array
    {
        $dictionaries = CaptchaTextTest::DICTIONARIES_WITHOUT_GENDERS;

        if ($isGenderDerivatives) {
            $dictionaries = CaptchaTextTest::DICTIONARIES_WITH_GENDERS;
        }

        $dirPath = realpath(__DIR__ . '/../../classes');
        $dirPath = $dirPath . '/../res/dictionaries';

        foreach ($dictionaries as $dictionaryKey => $dictionary) {
            $dictionary = str_replace('{dir}', $dirPath, $dictionary);
            $dictionaries[$dictionaryKey] = $dictionary;
        }

        return $dictionaries;
    }
}
