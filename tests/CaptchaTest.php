<?php

use PHPUnit\Framework\TestCase;

class CaptchaTest extends TestCase
{
    private const HASH_SALT = 'test';

    private const DATA_DIR_PATH = __DIR__ . '/tmp';

    private const IMAGE_URL_TEMPLATE = '/test/';

    private const LANGUAGE = 'test';

    private const COPY_DATABASE_FILE_PATH = __DIR__ . '/samples/dictionaries.db';

    private const DATABASE_FILE_PATH = __DIR__ . '/tmp/dictionaries.db';

    public function __construct()
    {
        parent::__construct();

        $this->_removeTestFiles(null);

        mkdir(static::DATA_DIR_PATH . '/img/', 0775, true);

        $this->prepareStore();
    }

    final public function __destruct()
    {
        $this->_removeTestFiles(null);
    }

    /**
     * @return string[]
     */
    final public function getSettingsData(): array
    {
        return [
            'hash_salt' => static::HASH_SALT,
            'data_dir_path' => static::DATA_DIR_PATH,
            'image_url_template' => static::IMAGE_URL_TEMPLATE,
            'language' => static::LANGUAGE
        ];
    }

    final public function prepareStore(): void
    {
        $this->removeStore();

        copy(
            static::COPY_DATABASE_FILE_PATH,
            static::DATABASE_FILE_PATH
        );
    }

    final public function removeStore(): void
    {
        if (
            file_exists(static::DATABASE_FILE_PATH) ||
            is_file(static::DATABASE_FILE_PATH)
        ) {
            unlink(static::DATABASE_FILE_PATH);
        }
    }

    /**
     * @param string|null $path
     */
    private function _removeTestFiles(?string $path = ''): void
    {
        if (empty($path)) {
            $path = static::DATA_DIR_PATH;
        }

        if (file_exists(static::DATA_DIR_PATH) && is_file($path)) {
            unlink($path);
        }

        if (file_exists(static::DATA_DIR_PATH) && is_dir($path)) {
            array_map([$this, '_removeTestFiles'],
                glob(sprintf('%s/*', $path)));
            rmdir($path);
        }
    }
}
