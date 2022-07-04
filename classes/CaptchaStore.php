<?php

namespace Sonder\Plugins\Captcha\Classes;

use PDO;
use Sonder\Plugins\Captcha\Exceptions\CaptchaException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException;
use Sonder\Plugins\Captcha\Interfaces\ICaptchaStore;
use Throwable;

final class CaptchaStore implements ICaptchaStore
{
    final public const TEMPORARY_DATABASE_FILE_NAME = 'dictionaries_tmp.db';

    private const DATABASE_FILE_NAME = 'dictionaries.db';

    /**
     * @var string|null
     */
    private ?string $_dataFilePath = null;

    /**
     * @var PDO|null
     */
    private ?PDO $_dbInstance = null;

    /**
     * @param string|null $dataDirPath
     * @param string|null $dataFileName
     * @throws CaptchaStoreException
     */
    final public function __construct(
        ?string $dataDirPath = null,
        ?string $dataFileName = null
    ) {
        if (empty($dataDirPath)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DATA_DIR_PATH_IS_NOT_SET,
                CaptchaException::CODE_STORE_DATA_DIR_PATH_IS_NOT_SET
            );
        }

        if (empty($dataFileName)) {
            $dataFileName = CaptchaStore::DATABASE_FILE_NAME;
        }

        $this->_dataFilePath = sprintf('%s/%s', $dataDirPath, $dataFileName);
    }

    final public function __destruct()
    {
        $this->_dbInstance = null;
    }

    /**
     * @param string|null $dictionary
     * @return string|null
     * @throws CaptchaStoreException
     */
    final public function getRandomWord(?string $dictionary = null): ?string
    {
        if (empty($dictionary)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DICTIONARY_IS_NOT_SET,
                CaptchaException::CODE_STORE_DICTIONARY_IS_NOT_SET
            );
        }

        $id = $this->_getRandomId($dictionary);

        if ($id < 0) {
            $errorMessage = sprintf(
                '%s. Table: %s',
                CaptchaStoreException::MESSAGE_STORE_TABLE_IS_EMPTY,
                $dictionary
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaException::CODE_STORE_TABLE_IS_EMPTY
            );
        }

        return $this->_getWord($dictionary, $id);
    }

    /**
     * @param string|null $dictionary
     * @throws CaptchaStoreException
     */
    final public function createDictionary(?string $dictionary = null): void
    {
        if (empty($dictionary)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DICTIONARY_IS_NOT_SET,
                CaptchaException::CODE_STORE_DICTIONARY_IS_NOT_SET
            );
        }

        $sql = /** @lang SQLite */
            '
            CREATE TABLE %s (
                word TEXT PRIMARY KEY
            );
        ';

        $sql = sprintf($sql, $dictionary);

        $this->_query($sql);
    }

    /**
     * @param string|null $word
     * @param string|null $dictionary
     * @throws CaptchaStoreException
     */
    final public function insertWord(
        ?string $word = null,
        ?string $dictionary = null
    ): void {
        if (empty($word)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_WORD_IS_NOT_SET,
                CaptchaException::CODE_STORE_WORD_IS_NOT_SET
            );
        }

        if (empty($dictionary)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DICTIONARY_IS_NOT_SET,
                CaptchaException::CODE_STORE_DICTIONARY_IS_NOT_SET
            );
        }

        $sql = /** @lang SQLite */
            '
            INSERT INTO %s (
                word
            ) VALUES (
                \'%s\'
            );
        ';

        $sql = sprintf($sql, $dictionary, $word);

        $this->_query($sql);
    }

    /**
     * @throws CaptchaStoreException
     */
    final public function createDatabase(): void
    {
        try {
            if (
                file_exists($this->_dataFilePath) ||
                is_file($this->_dataFilePath)
            ) {
                unlink($this->_dataFilePath);
            }

            touch($this->_dataFilePath);
            chmod($this->_dataFilePath, 0775);
        } catch (Throwable $thr) {
            $errorMessage = '%s. Error: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CaptchaStoreException::MESSAGE_STORE_CAN_NOT_CREATE_DATABASE,
                $thr->getMessage()
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaException::CODE_STORE_CAN_NOT_CREATE_DATABASE
            );
        }
    }

    /**
     * @param string|null $dataDirPath
     * @return bool
     * @throws CaptchaStoreException
     */
    final public function updateDatabase(?string $dataDirPath = null): bool
    {
        if (empty($dataDirPath)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DATA_DIR_PATH_IS_NOT_SET,
                CaptchaException::CODE_STORE_DATA_DIR_PATH_IS_NOT_SET
            );
        }

        $oldDatabaseFilePath = sprintf(
            '%s/%s',
            $dataDirPath,
            CaptchaStore::DATABASE_FILE_NAME
        );

        $newDatabaseFilePath = sprintf(
            '%s/%s',
            $dataDirPath,
            CaptchaStore::TEMPORARY_DATABASE_FILE_NAME
        );

        if (
            !file_exists($newDatabaseFilePath) ||
            !is_file($newDatabaseFilePath)
        ) {
            return false;
        }

        try {
            if (
                file_exists($oldDatabaseFilePath) &&
                is_file($oldDatabaseFilePath)
            ) {
                unlink($oldDatabaseFilePath);
            }

            copy($newDatabaseFilePath, $oldDatabaseFilePath);
            chmod($oldDatabaseFilePath, 0775);
            unlink($newDatabaseFilePath);
        } catch (Throwable $thr) {
            $errorMessage = '%s. Error: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CaptchaStoreException::MESSAGE_STORE_CAN_NOT_UPDATE_DATABASE,
                $thr->getMessage()
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaException::CODE_STORE_CAN_NOT_UPDATE_DATABASE
            );
        }

        return true;
    }

    /**
     * @throws CaptchaStoreException
     */
    private function _initStore(): void
    {
        try {
            if (
                !file_exists($this->_dataFilePath) ||
                !is_file($this->_dataFilePath)
            ) {
                touch($this->_dataFilePath);
                chmod($this->_dataFilePath, 0775);
            }
        } catch (Throwable $thr) {
            $errorMessage = '%s. Error: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CaptchaStoreException::MESSAGE_STORE_CAN_NOT_CREATE_DATABASE,
                $thr->getMessage()
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaException::CODE_STORE_CAN_NOT_CREATE_DATABASE
            );
        }

        $dsn = sprintf('sqlite:%s', $this->_dataFilePath);

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        $this->_dbInstance = new PDO($dsn, null, null, $options);
    }

    /**
     * @param string $dictionary
     * @return int
     * @throws CaptchaStoreException
     */
    private function _getRandomId(string $dictionary): int
    {
        $countRows = $this->_countDictionaryRows($dictionary);

        return rand(1, $countRows);
    }

    /**
     * @param string $dictionary
     * @return int
     * @throws CaptchaStoreException
     */
    private function _countDictionaryRows(string $dictionary): int
    {
        $sql = /** @lang SQLite */
            '
            SELECT
                COUNT(*) AS cnt
            FROM %s;
        ';

        $sql = sprintf($sql, $dictionary);

        $row = $this->_getRow($sql);

        if (empty($row)) {
            return 0;
        }

        if (!array_key_exists('cnt', $row)) {
            return 0;
        }

        $count = $row['cnt'];

        return (int)$count;
    }

    /**
     * @param string|null $dictionary
     * @param int|null $id
     * @return string|null
     * @throws CaptchaStoreException
     */
    private function _getWord(
        ?string $dictionary = null,
        ?int $id = null
    ): ?string {
        if (empty($dictionary)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DICTIONARY_IS_NOT_SET,
                CaptchaException::CODE_STORE_DICTIONARY_IS_NOT_SET
            );
        }

        if (empty($id)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_ID_IS_NOT_SET,
                CaptchaException::CODE_STORE_ID_IS_NOT_SET
            );
        }

        $sql = /** @lang SQLite */
            '
            SELECT
                word
            FROM %s
            WHERE rowid = %d;
        ';

        $sql = sprintf($sql, $dictionary, $id);
        $row = $this->_getRow($sql);

        if (empty($row)) {
            return null;
        }

        if (!array_key_exists('word', $row)) {
            return null;
        }

        return $row['word'];
    }

    /**
     * @param string|null $sql
     * @return array|null
     * @throws CaptchaStoreException
     */
    private function _getRow(?string $sql = null): ?array
    {
        if (empty($sql)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_SQL_IS_EMPTY,
                CaptchaException::CODE_STORE_SQL_IS_EMPTY
            );
        }

        try {
            if (empty($this->_dbInstance)) {
                $this->_initStore();
            }

            $rows = $this->_dbInstance->query($sql);
            $rows = (array)$rows->fetchALL();

            if (empty($rows)) {
                return null;
            }

            return array_shift($rows);
        } catch (Throwable $thr) {
            $errorMessage = '%s. Query: %s. Error: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CaptchaStoreException::MESSAGE_STORE_QUERY_ERROR,
                $sql,
                $thr->getMessage()
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaException::CODE_STORE_QUERY_ERROR
            );
        }
    }

    /**
     * @param string|null $sql
     * @throws CaptchaStoreException
     */
    private function _query(?string $sql = null): void
    {
        if (empty($sql)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_SQL_IS_EMPTY,
                CaptchaException::CODE_STORE_SQL_IS_EMPTY
            );
        }

        try {
            if (empty($this->_dbInstance)) {
                $this->_initStore();
            }

            $this->_dbInstance->query($sql);
        } catch (Throwable $thr) {
            $errorMessage = '%s. Query: %s. Error: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CaptchaStoreException::MESSAGE_STORE_QUERY_ERROR,
                $sql,
                $thr->getMessage()
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaException::CODE_STORE_QUERY_ERROR
            );
        }
    }
}
