<?php
namespace Core\Plugins\Captcha\Classes;

use Core\Plugins\Captcha\Interfaces\ICaptchaStore;

use Core\Plugins\Captcha\Exceptions\CaptchaStoreException;

class CaptchaStore implements ICaptchaStore
{
    const DATABASE_FILE_NAME = 'dictionaries.db';

    const TEMPORARY_DATABASE_FILE_NAME = 'dictionaries_tmp.db';

    private $_dataFilePath = null;

    private $_dbInstance = null;

    public function __construct(
        ?string $dataDirPath  = null,
        ?string $dataFileName = null
    )
    {
        if (empty($dataDirPath)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DATA_DIR_PATH_IS_NOT_SET,
                CaptchaStoreException::CODE_STORE_DATA_DIR_PATH_IS_NOT_SET
            );
        }

        if (empty($dataFileName)) {
            $dataFileName = static::DATABASE_FILE_NAME;
        }

        $this->_dataFilePath = sprintf('%s/%s', $dataDirPath, $dataFileName);
    }

    public function __destruct()
    {
        $this->_dbInstance = null;
    }

    public function getRandomWord(?string $dictionary = null): ?string
    {
        if (empty($dictionary)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DICTIONARY_IS_NOT_SET,
                CaptchaStoreException::CODE_STORE_DICTIONARY_IS_NOT_SET
            );
        }

        $id = $this->_getRandomId($dictionary);

        if ($id < 0) {
            $errorMessage = '%s. Table: %s';

            $errorMessage = sprintf(
                CaptchaStoreException::MESSAGE_STORE_TABLE_IS_EMPTY,
                $dictionary
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaStoreException::CODE_STORE_TABLE_IS_EMPTY
            );
        }

        return $this->_getWord($dictionary, $id);
    }

    public function createDictionary(?string $dictionary = null): void
    {
        if (empty($dictionary)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DICTIONARY_IS_NOT_SET,
                CaptchaStoreException::CODE_STORE_DICTIONARY_IS_NOT_SET
            );
        }

        $sql = '
            CREATE TABLE %s (
                word TEXT PRIMARY KEY
            );
        ';

        $sql = sprintf($sql, $dictionary);

        $this->_query($sql);
    }

    public function insertWord(
        ?string $word       = null,
        ?string $dictionary = null
    ): void
    {
        if (empty($word)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_WORD_IS_NOT_SET,
                CaptchaStoreException::CODE_STORE_WORD_IS_NOT_SET
            );
        }

        if (empty($dictionary)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DICTIONARY_IS_NOT_SET,
                CaptchaStoreException::CODE_STORE_DICTIONARY_IS_NOT_SET
            );
        }

        $sql = '
            INSERT INTO %s (
                word
            ) VALUES (
                \'%s\'
            );
        ';

        $sql = sprintf($sql, $dictionary, $word);

        $this->_query($sql);
    }

    public function createDatabase(): void
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
        } catch (\Exception $exp) {
            $errorMessage = '%s. Error: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CaptchaStoreException::MESSAGE_STORE_CAN_NOT_CREATE_DATABASE,
                $exp->getMessage()
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaStoreException::CODE_STORE_CAN_NOT_CREATE_DATABASE
            );
        }
    }

    public function updateDatabase(?string $dataDirPath = null): bool
    {
        if (empty($dataDirPath)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DATA_DIR_PATH_IS_NOT_SET,
                CaptchaStoreException::CODE_STORE_DATA_DIR_PATH_IS_NOT_SET
            );
        }

        $oldDatabaseFilePath = sprintf(
            '%s/%s',
            $dataDirPath,
            static::DATABASE_FILE_NAME
        );

        $newDatabaseFilePath = sprintf(
            '%s/%s',
            $dataDirPath,
            static::TEMPORARY_DATABASE_FILE_NAME
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
        } catch (\Exception $exp) {
            $errorMessage = '%s. Error: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CaptchaStoreException::MESSAGE_STORE_CAN_NOT_UPDATE_DATABASE,
                $exp->getMessage()
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaStoreException::CODE_STORE_CAN_NOT_UPDATE_DATABASE
            );
        }

        return true;
    }

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
        } catch (\Exception $exp) {
            $errorMessage = '%s. Error: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CaptchaStoreException::MESSAGE_STORE_CAN_NOT_CREATE_DATABASE,
                $exp->getMessage()
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaStoreException::CODE_STORE_CAN_NOT_CREATE_DATABASE
            );
        }

        $dsn = sprintf('sqlite:%s', $this->_dataFilePath);

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];

        $this->_dbInstance = new \PDO($dsn, null, null, $options);
    }

    private function _getRandomId(string $dictionary): int
    {
        $countRows = $this->_countDictionaryRows($dictionary);
        $randomId  = rand(1, $countRows);

        return $randomId;
    }

    private function _countDictionaryRows(string $dictionary): int
    {
        $sql = '
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

        return (int) $count;
    }

    private function _getWord(
        ?string $dictionary = null,
        ?int    $id         = null
    ): ?string
    {
        if (empty($dictionary)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_DICTIONARY_IS_NOT_SET,
                CaptchaStoreException::CODE_STORE_DICTIONARY_IS_NOT_SET
            );
        }

        if (empty($id)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_ID_IS_NOT_SET,
                CaptchaStoreException::CODE_STORE_ID_IS_NOT_SET
            );
        }

        $sql = '
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

    private function _getRow(?string $sql = null): ?array
    {
        if (empty($sql)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_SQL_IS_EMPTY,
                CaptchaStoreException::CODE_STORE_SQL_IS_EMPTY
            );
        }

        try {
            if (empty($this->_dbInstance)) {
                $this->_initStore();
            }

            $rows = $this->_dbInstance->query($sql);
            $rows = (array) $rows->fetchALL();

            if (empty($rows)) {
                return null;
            }

            return array_shift($rows);
        } catch (\Exception $exp) {
            $errorMessage = '%s. Query: %s. Error: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CaptchaStoreException::MESSAGE_STORE_QUERY_ERROR,
                $sql,
                $exp->getMessage()
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaStoreException::CODE_STORE_QUERY_ERROR
            );
        }
    }

    private function _query(?string $sql = null): bool
    {
        if (empty($sql)) {
            throw new CaptchaStoreException(
                CaptchaStoreException::MESSAGE_STORE_SQL_IS_EMPTY,
                CaptchaStoreException::CODE_STORE_SQL_IS_EMPTY
            );
        }

        try {
            if (empty($this->_dbInstance)) {
                $this->_initStore();
            }

            return (bool) $this->_dbInstance->query($sql);
        } catch (\Exception $exp) {
            $errorMessage = '%s. Query: %s. Error: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CaptchaStoreException::MESSAGE_STORE_QUERY_ERROR,
                $sql,
                $exp->getMessage()
            );

            throw new CaptchaStoreException(
                $errorMessage,
                CaptchaStoreException::CODE_STORE_QUERY_ERROR
            );
        }
    }
}
