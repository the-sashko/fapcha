<?php
use PHPUnit\Framework\TestCase;

/**
 * Main Class For Testing
 */
class CaptchaTest extends TestCase
{
    /**
     * @var string Sample Captcha Hash Salt For Unit Tests
     */
    const HASH_SALT = 'test';

    /**
     * @var string Sample Data Directory Path For Unit Tests
     */
    const DATA_DIR_PATH = __DIR__.'/tmp';

    /**
     * @var string Sample Image Url Template Value For Unit Tests
     */
    const IMAGE_URL_TEMPLATE = '/test/';

    /**
     * @var string Sample Language Value For Unit Tests
     */
    const LANGUAGE = 'test';

    /**
     * @var string Sample Copy Of Database Path For Unit Tests
     */
    const COPY_DATABASE_FILE_PATH = __DIR__.'/samples/dictionaries.db';

    /**
     * @var string Sample Database Path For Unit Tests
     */
    const DATABASE_FILE_PATH = __DIR__.'/tmp/dictionaries.db';

    public function __construct()
    {
        parent::__construct();

        $this->removeTestFiles();

        mkdir(static::DATA_DIR_PATH.'/img/', 0775, true);

        $this->prepareStore();
    }

    public function __destruct()
    {
        $this->removeTestFiles();
    }

    private function removeTestFiles(?string $path = null): bool
    {
        if (empty($path)) {
            $path = static::DATA_DIR_PATH;
        }

        if (!file_exists(static::DATA_DIR_PATH)) {
            return false;
        }

        if (is_file($path)) {
            unlink($path);

            return true;
        }

        array_map([$this, 'removeTestFiles'], glob(sprintf('%s/*', $path)));
        rmdir($path);

        return true;
    }

    public function getSettingsData(): array
    {
        return [
            'hash_salt'          => static::HASH_SALT,
            'data_dir_path'      => static::DATA_DIR_PATH,
            'image_url_template' => static::IMAGE_URL_TEMPLATE,
            'language'           => static::LANGUAGE
        ];
    }

    public function prepareStore(): void
    {
        $this->removeStore();

        copy(
            static::COPY_DATABASE_FILE_PATH,
            static::DATABASE_FILE_PATH
        );
    }

    public function removeStore(): void
    {
        if (
            file_exists(static::DATABASE_FILE_PATH) ||
            is_file(static::DATABASE_FILE_PATH)
        ) {
            unlink(static::DATABASE_FILE_PATH);
        }
    }
}
