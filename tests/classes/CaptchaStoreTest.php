<?php
use Core\Plugins\Captcha\Interfaces\ICaptchaStore;

use Core\Plugins\Captcha\Classes\CaptchaStore;

/**
 * Class For Testing CaptchaPlugin Store Class
 */
class CaptchaStoreTest extends CaptchaTest
{
    /**
     * @var string Sample Data Directory Path For Unit Tests
     */
    const DATA_DIR_PATH = __DIR__.'/../tmp';

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
     * @var string Sample Captcha Noun Word For Unit Tests
     */
    const WORD_NOUN = 'bar';

    /**
     * @var int Sample Count Of Words For Unit Tests
     */
    const COUNT_WORDS = 1;

    /**
     * @var string Sample Captcha Text For Unit Tests
     */
    const TEXT = 'foo bar';

    /**
     * Unit Test Of CaptchaStore getRandomWord Method
     */
    public function testGetRandomWord(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $exception = null;

        try {
            $captchaStore->getRandomWord();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $adjective = $captchaStore->getRandomWord(
            static::DICTIONARY_ADJECTIVE
        );

        $this->assertEquals($adjective, static::WORD_ADJECTIVE);

        $noun = $captchaStore->getRandomWord(static::DICTIONARY_NOUN);

        $this->assertEquals($noun, static::WORD_NOUN);
    }

    /**
     * Unit Test Of CaptchaStore createDictionary Method
     */
    public function testCreateDictionary(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $queryMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaStore',
            '_query'
        );

        $queryMethod->setAccessible(true);

        $sql = 'DROP TABLE %s;';
        $sql = sprintf($sql, static::DICTIONARY_ADJECTIVE);
        $queryMethod->invokeArgs($captchaStore, [$sql]);

        $captchaStore->createDictionary(static::DICTIONARY_ADJECTIVE);

        $exception = null;

        try {
            $captchaStore->createDictionary();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $this->prepareStore();
    }

    /**
     * Unit Test Of CaptchaText insertWord Method
     */
    public function testInsertWord(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $queryMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaStore',
            '_query'
        );

        $queryMethod->setAccessible(true);

        $exception = null;

        try {
            $captchaStore->insertWord();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $captchaStore->insertWord(static::WORD_ADJECTIVE);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $sql = 'DELETE FROM %s;';
        $sql = sprintf($sql, static::DICTIONARY_ADJECTIVE);
        $queryMethod->invokeArgs($captchaStore, [$sql]);

        $captchaStore->insertWord(
            static::WORD_ADJECTIVE,
            static::DICTIONARY_ADJECTIVE
        );

        $word = $captchaStore->getRandomWord(static::DICTIONARY_ADJECTIVE);

        $this->assertEquals($word, static::WORD_ADJECTIVE);
    }

    /**
     * Unit Test Of CaptchaStore updateDatabase Method
     */
    public function testUpdateDatabase(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $exception = null;

        try {
            $captchaStore->updateDatabase();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $captchaStore->updateDatabase(static::DATA_DIR_PATH);
    }

    /**
     * Unit Test Of CaptchaStore _getRandomId Method
     */
    public function testGetRandomId(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $getRandomIdMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaStore',
            '_getRandomId'
        );

        $getRandomIdMethod->setAccessible(true);

        $randomId = $getRandomIdMethod->invokeArgs(
            $captchaStore,
            [static::DICTIONARY_ADJECTIVE]
        );

        $this->assertEquals($randomId, static::COUNT_WORDS);

        $randomId = $getRandomIdMethod->invokeArgs(
            $captchaStore,
            [static::DICTIONARY_NOUN]
        );

        $this->assertEquals($randomId, static::COUNT_WORDS);
    }

    /**
     * Unit Test Of CaptchaStore _countDictionaryRows Method
     */
    public function testCountDictionaryRows(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $countDictionaryRowsMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaStore',
            '_countDictionaryRows'
        );

        $countDictionaryRowsMethod->setAccessible(true);

        $countDictionaryRows = $countDictionaryRowsMethod->invokeArgs(
            $captchaStore,
            [static::DICTIONARY_ADJECTIVE]
        );

        $this->assertEquals($countDictionaryRows, static::COUNT_WORDS);

        $countDictionaryRows = $countDictionaryRowsMethod->invokeArgs(
            $captchaStore,
            [static::DICTIONARY_NOUN]
        );

        $this->assertEquals($countDictionaryRows, static::COUNT_WORDS);
    }

    /**
     * Unit Test Of CaptchaStore _getWord Method
     */
    public function testGetWord(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $getWordMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaStore',
            '_getWord'
        );

        $getWordMethod->setAccessible(true);

        $exception = null;

        try {
            $getWordMethod->invoke($captchaStore);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $getWordMethod->invokeArgs(
                $captchaStore,
                [static::DICTIONARY_ADJECTIVE]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $this->assertEmpty($getWordMethod->invokeArgs(
            $captchaStore,
            [
                static::DICTIONARY_ADJECTIVE,
                static::COUNT_WORDS + 1
            ]
        ));

        $word = $getWordMethod->invokeArgs(
            $captchaStore,
            [
                static::DICTIONARY_ADJECTIVE,
                static::COUNT_WORDS
            ]
        );

        $this->assertNotEmpty($word);

        $this->assertEquals($word, static::WORD_ADJECTIVE);
    }

    /**
     * Unit Test Of CaptchaStore _getRow Method
     */
    public function testGetRow(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $getRowMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaStore',
            '_getRow'
        );

        $getRowMethod->setAccessible(true);

        $sql = '
            SELECT
                word
            FROM %s
            WHERE rowid = %d;
        ';

        $sql = sprintf(
            $sql,
            static::DICTIONARY_ADJECTIVE,
            static::COUNT_WORDS
        );

        $row = $getRowMethod->invokeArgs($captchaStore, [$sql]);

        $this->assertNotEmpty($row);

        $this->assertTrue(array_key_exists('word', $row));

        $this->assertEquals($row['word'], static::WORD_ADJECTIVE);
    }

    /**
     * Unit Test Of CaptchaStore _query Method
     */
    public function testQuery(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $queryMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaStore',
            '_query'
        );

        $getRowMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaStore',
            '_getRow'
        );

        $queryMethod->setAccessible(true);
        $getRowMethod->setAccessible(true);

        $selectQuery = 'SELECT foo FROM bar;';

        $createQuery = '
            CREATE TABLE bar (
                foo TEXT PRIMARY KEY
            );
        ';

        $insertQuery = '
            INSERT INTO bar (
                foo
            ) VALUES (
                \'%s\'
            );
        ';

        $insertQuery = sprintf($insertQuery, static::TEXT);

        $exception = null;

        try {
            $queryMethod->invoke($captchaStore);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $this->assertTrue($queryMethod->invokeArgs(
            $captchaStore,
            [$createQuery]
        ));

        $this->assertTrue($queryMethod->invokeArgs(
            $captchaStore,
            [$insertQuery]
        ));

        $row = $getRowMethod->invokeArgs($captchaStore, [$selectQuery]);

        $this->assertNotEmpty($row);

        $this->assertTrue(array_key_exists('foo', $row));

        $this->assertEquals($row['foo'], static::TEXT);
    }

    private function _getCaptchaStoreInstance(): ICaptchaStore
    {
        return new CaptchaStore(
            static::DATA_DIR_PATH,
            static::DATABASE_FILE_NAME
        );
    }
}
