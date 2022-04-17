<?php

use Sonder\Plugins\Captcha\Classes\CaptchaStore;
use Sonder\Plugins\Captcha\Interfaces\ICaptchaStore;

final class CaptchaStoreTest extends CaptchaTest
{
    const DATA_DIR_PATH = __DIR__ . '/../tmp';

    const DATABASE_FILE_NAME = 'dictionaries.db';

    const DICTIONARY_ADJECTIVE = 'test_adjective_male';

    const DICTIONARY_NOUN = 'test_noun_male';

    const WORD_ADJECTIVE = 'foo';

    const WORD_NOUN = 'bar';

    const COUNT_WORDS = 1;

    const TEXT = 'foo bar';

    final public function testGetRandomWord(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $exception = null;

        try {
            $captchaStore->getRandomWord();
        } catch (Throwable $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $adjective = $captchaStore->getRandomWord(
            CaptchaStoreTest::DICTIONARY_ADJECTIVE
        );

        $this->assertEquals(CaptchaStoreTest::WORD_ADJECTIVE, $adjective);

        $noun = $captchaStore->getRandomWord(CaptchaStoreTest::DICTIONARY_NOUN);

        $this->assertEquals(CaptchaStoreTest::WORD_NOUN, $noun);
    }

    /**
     * @throws ReflectionException
     */
    final public function testCreateDictionary(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $queryMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaStore',
            '_query'
        );

        $queryMethod->setAccessible(true);

        $sql = 'DROP TABLE %s;';
        $sql = sprintf($sql, CaptchaStoreTest::DICTIONARY_ADJECTIVE);
        $queryMethod->invokeArgs($captchaStore, [$sql]);

        $captchaStore->createDictionary(CaptchaStoreTest::DICTIONARY_ADJECTIVE);

        $exception = null;

        try {
            $captchaStore->createDictionary();
        } catch (Throwable $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $this->prepareStore();
    }

    /**
     * @throws ReflectionException
     */
    final public function testInsertWord(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $queryMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaStore',
            '_query'
        );

        $queryMethod->setAccessible(true);

        $exception = null;

        try {
            $captchaStore->insertWord();
        } catch (Throwable $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $captchaStore->insertWord(CaptchaStoreTest::WORD_ADJECTIVE);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $sql = 'DELETE FROM %s;';
        $sql = sprintf($sql, CaptchaStoreTest::DICTIONARY_ADJECTIVE);
        $queryMethod->invokeArgs($captchaStore, [$sql]);

        $captchaStore->insertWord(
            CaptchaStoreTest::WORD_ADJECTIVE,
            CaptchaStoreTest::DICTIONARY_ADJECTIVE
        );

        $word = $captchaStore->getRandomWord(CaptchaStoreTest::DICTIONARY_ADJECTIVE);

        $this->assertEquals(CaptchaStoreTest::WORD_ADJECTIVE, $word);
    }

    final public function testUpdateDatabase(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $exception = null;

        try {
            $captchaStore->updateDatabase();
        } catch (Throwable $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $captchaStore->updateDatabase(CaptchaStoreTest::DATA_DIR_PATH);
    }

    /**
     * @throws ReflectionException
     */
    final public function testGetRandomId(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $getRandomIdMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaStore',
            '_getRandomId'
        );

        $getRandomIdMethod->setAccessible(true);

        $randomId = $getRandomIdMethod->invokeArgs(
            $captchaStore,
            [CaptchaStoreTest::DICTIONARY_ADJECTIVE]
        );

        $this->assertEquals(CaptchaStoreTest::COUNT_WORDS, $randomId);

        $randomId = $getRandomIdMethod->invokeArgs(
            $captchaStore,
            [CaptchaStoreTest::DICTIONARY_NOUN]
        );

        $this->assertEquals(CaptchaStoreTest::COUNT_WORDS, $randomId);
    }

    /**
     * @throws ReflectionException
     */
    final public function testCountDictionaryRows(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $countDictionaryRowsMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaStore',
            '_countDictionaryRows'
        );

        $countDictionaryRowsMethod->setAccessible(true);

        $countDictionaryRows = $countDictionaryRowsMethod->invokeArgs(
            $captchaStore,
            [CaptchaStoreTest::DICTIONARY_ADJECTIVE]
        );

        $this->assertEquals(CaptchaStoreTest::COUNT_WORDS, $countDictionaryRows);

        $countDictionaryRows = $countDictionaryRowsMethod->invokeArgs(
            $captchaStore,
            [CaptchaStoreTest::DICTIONARY_NOUN]
        );

        $this->assertEquals(CaptchaStoreTest::COUNT_WORDS, $countDictionaryRows);
    }

    /**
     * @throws ReflectionException
     */
    final public function testGetWord(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $getWordMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaStore',
            '_getWord'
        );

        $getWordMethod->setAccessible(true);

        $exception = null;

        try {
            $getWordMethod->invoke($captchaStore);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $getWordMethod->invokeArgs(
                $captchaStore,
                [CaptchaStoreTest::DICTIONARY_ADJECTIVE]
            );
        } catch (Throwable $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $this->assertEmpty($getWordMethod->invokeArgs(
            $captchaStore,
            [
                CaptchaStoreTest::DICTIONARY_ADJECTIVE,
                CaptchaStoreTest::COUNT_WORDS + 1
            ]
        ));

        $word = $getWordMethod->invokeArgs(
            $captchaStore,
            [
                CaptchaStoreTest::DICTIONARY_ADJECTIVE,
                CaptchaStoreTest::COUNT_WORDS
            ]
        );

        $this->assertNotEmpty($word);

        $this->assertEquals(CaptchaStoreTest::WORD_ADJECTIVE, $word);
    }

    /**
     * @throws ReflectionException
     */
    final public function testGetRow(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $getRowMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaStore',
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
            CaptchaStoreTest::DICTIONARY_ADJECTIVE,
            CaptchaStoreTest::COUNT_WORDS
        );

        $row = $getRowMethod->invokeArgs($captchaStore, [$sql]);

        $this->assertNotEmpty($row);

        $this->assertArrayHasKey('word', $row);

        $this->assertEquals(CaptchaStoreTest::WORD_ADJECTIVE, $row['word']);
    }

    /**
     * @throws ReflectionException
     */
    final public function testQuery(): void
    {
        $captchaStore = $this->_getCaptchaStoreInstance();

        $queryMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaStore',
            '_query'
        );

        $getRowMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaStore',
            '_getRow'
        );

        $queryMethod->setAccessible(true);
        $getRowMethod->setAccessible(true);

        $selectQuery = /** @lang SQLite */
            'SELECT foo FROM bar;';

        $createQuery = /** @lang SQLite */
            '
            CREATE TABLE bar (
                foo TEXT PRIMARY KEY
            );
        ';

        $insertQuery = /** @lang SQLite */
            '
            INSERT INTO bar (
                foo
            ) VALUES (
                \'%s\'
            );
        ';

        $insertQuery = sprintf($insertQuery, CaptchaStoreTest::TEXT);

        $exception = null;

        try {
            $queryMethod->invoke($captchaStore);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException',
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

        $this->assertArrayHasKey('foo', $row);

        $this->assertEquals(CaptchaStoreTest::TEXT, $row['foo']);
    }

    /**
     * @return ICaptchaStore
     */
    private function _getCaptchaStoreInstance(): ICaptchaStore
    {
        return new CaptchaStore(
            CaptchaStoreTest::DATA_DIR_PATH,
            CaptchaStoreTest::DATABASE_FILE_NAME
        );
    }
}
