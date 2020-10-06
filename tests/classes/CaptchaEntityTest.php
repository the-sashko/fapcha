<?php
use Core\Plugins\Captcha\Classes\CaptchaEntity;
use Core\Plugins\Captcha\Classes\CaptchaSettings;

/**
 * Class For Testing CaptchaPlugin Entity Class
 */
class CaptchaEntityTest extends CaptchaTest
{
    /**
     * @var string Sample Captcha Text For Unit Tests
     */
    const TEXT = 'foo bar';

    /**
     * @var string Sample Captcha Hash For Unit Tests
     */
    const HASH = '2918553c85b053da3f1bf4777fc64cca112e956eb1e8f3b627def9ab5dd'.
                 '31ac8';

    /**
     * @var string Sample Captcha Image File Name For Unit Tests
     */
    const IMAGE_FILE_NAME = 'test.png';

    /**
     * @var array Sample List Of Entity Data For Unit Tests
     */
    const ENTITY_DATA_LIST = [
        [
            'text'            => 'foo',
            'hash'            => 'foo',
            'image_file_name' => 'foo.png'
        ],
        [
            'text'            => 'bar',
            'hash'            => 'bar',
            'image_file_name' => 'bar.png'
        ],
        [
            'text'            => 'test',
            'hash'            => 'test',
            'image_file_name' => 'test.png'
        ]
    ];

    /**
     * @var string Sample Captcha Image Directory Path For Unit Tests
     */
    const IMAGE_DIRECTORY_PATH = 'test';

    /**
     * Unit Test Of CaptchaEntity getText Method
     */
    public function testGetText(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity   = $this->_getCaptchaEntityInstance();

        $this->assertEquals(
            $captchaEntity->getText(),
            static::TEXT
        );

        $captchaEntity = new CaptchaEntity(
            '',
            static::HASH,
            static::IMAGE_FILE_NAME,
            $captchaSettings
        );

        $exception = null;

        try {
            $captchaEntity->getText();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaEntityException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);
    }

    /**
     * Unit Test Of CaptchaEntity getHash Method
     */
    public function testGetHash(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity   = $this->_getCaptchaEntityInstance();

        $this->assertEquals(
            $captchaEntity->getHash(),
            static::HASH
        );

        $captchaEntity = new CaptchaEntity(
            static::TEXT,
            '',
            static::IMAGE_FILE_NAME,
            $captchaSettings
        );

        $exception = null;

        try {
            $captchaEntity->getHash();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaEntityException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);
    }

    /**
     * Unit Test Of CaptchaEntity getImageFilePath Method
     */
    public function testGetImageFilePath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity   = $this->_getCaptchaEntityInstance();
        $imageFilePath   = $captchaEntity->getImageFilePath();

        $assertRegexp = '/^%s\/img\/[\d]{4}\/[\d]{2}\/[\d]{2}\/[\d]{2}\/'.
                        '[\d]{2}\/[\d]{2}\/%s$/sui';
        $assertRegexp = sprintf(
            $assertRegexp,
            str_replace('/', '\/', $captchaSettings->getDataDirPath()),
            static::IMAGE_FILE_NAME
        );

        $this->assertTrue((bool) preg_match($assertRegexp, $imageFilePath));
    }

    /**
     * Unit Test Of CaptchaEntity getImageUrlPath Method
     */
    public function testGetImageUrlPath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity   = $this->_getCaptchaEntityInstance();
        $imageUrlPath   = $captchaEntity->getImageUrlPath();

        $assertRegexp = '/^%s[\d]{4}\/[\d]{2}\/[\d]{2}\/[\d]{2}\/'.
                        '[\d]{2}\/[\d]{2}\/%s$/sui';
        $assertRegexp = sprintf(
            $assertRegexp,
            str_replace('/', '\/', $captchaSettings->getImageUrlTemplate()),
            static::IMAGE_FILE_NAME
        );

        $this->assertTrue((bool) preg_match($assertRegexp, $imageUrlPath));
    }

    /**
     * Unit Test Of CaptchaEntity _setText Method
     */
    public function testSetText(): void
    {
        $captchaEntity = $this->_getCaptchaEntityInstance();

        $setTextMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaEntity',
            '_setText'
        );

        $setTextMethod->setAccessible(true);

        $entityDataList = static::ENTITY_DATA_LIST;

        foreach ($entityDataList as $entityData) {
            $text = $entityData['text'];

            $setTextMethod->invokeArgs($captchaEntity, [$text]);

            $this->assertEquals($captchaEntity->getText(), $text);
        }
    }

    /**
     * Unit Test Of CaptchaEntity _setHash Method
     */
    public function testSetHash(): void
    {
        $captchaEntity = $this->_getCaptchaEntityInstance();

        $setHashMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaEntity',
            '_setHash'
        );

        $setHashMethod->setAccessible(true);

        $entityDataList = static::ENTITY_DATA_LIST;

        foreach ($entityDataList as $entityData) {
            $hash = $entityData['hash'];

            $setHashMethod->invokeArgs($captchaEntity, [$hash]);

            $this->assertEquals($captchaEntity->getHash(), $hash);
        }
    }

    /**
     * Unit Test Of CaptchaEntity _setImagePath Method
     */
    public function testSetImagePath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity   = $this->_getCaptchaEntityInstance();

        $setImagePathMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaEntity',
            '_setImagePath'
        );

        $setImagePathMethod->setAccessible(true);

        $entityDataList = static::ENTITY_DATA_LIST;

        foreach ($entityDataList as $entityData) {
            $imageFileName = $entityData['image_file_name'];

            $setImagePathMethod->invokeArgs(
                $captchaEntity,
                [
                    $imageFileName,
                    $captchaSettings
                ]
            );

            $imageFilePath = $captchaEntity->getImageFilePath();
            $imageUrlPath  = $captchaEntity->getImageUrlPath();

            $dataDirPath      = $captchaSettings->getDataDirPath();
            $imageUrlTemplate = $captchaSettings->getImageUrlTemplate();

            $assertRegexp = '/^%s\/img\/[\d]{4}\/[\d]{2}\/[\d]{2}\/[\d]{2}\/'.
                            '[\d]{2}\/[\d]{2}\/%s$/sui';

            $assertRegexp = sprintf(
                $assertRegexp,
                str_replace('/', '\/', $dataDirPath),
                $imageFileName
            );

            $this->assertTrue((bool) preg_match(
                $assertRegexp,
                $imageFilePath
            ));

            $assertRegexp = '/^%s[\d]{4}\/[\d]{2}\/[\d]{2}\/'.
                            '[\d]{2}\/[\d]{2}\/[\d]{2}\/%s$/sui';

            $assertRegexp = sprintf(
                $assertRegexp,
                str_replace('/', '\/', $imageUrlTemplate),
                $imageFileName
            );

            $this->assertTrue((bool) preg_match(
                $assertRegexp,
                $imageUrlPath
            ));
        }
    }

    /**
     * Unit Test Of CaptchaEntity _createImageDerictory Method
     */
    public function testCreateImageDerictory(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity   = $this->_getCaptchaEntityInstance();

        $createImageDerictoryMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaEntity',
            '_createImageDerictory'
        );

        $createImageDerictoryMethod->setAccessible(true);

        $directoryPath = $createImageDerictoryMethod->invokeArgs(
            $captchaEntity,
            [$captchaSettings]
        );

        $this->assertTrue((bool) preg_match(
            '/^[\d]{4}\/[\d]{2}\/[\d]{2}\/[\d]{2}\/[\d]{2}\/[\d]{2}$/sui',
            $directoryPath
        ));
    }

    /**
     * Unit Test Of CaptchaEntity _setImageUrlPath Method
     */
    public function testImageUrlPath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity   = $this->_getCaptchaEntityInstance();

        $setImageUrlPathMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaEntity',
            '_setImageUrlPath'
        );

        $setImageUrlPathMethod->setAccessible(true);

        $entityDataList = static::ENTITY_DATA_LIST;

        foreach ($entityDataList as $entityData) {
            $imageFileName    = $entityData['image_file_name'];
            $dataDirPath      = $captchaSettings->getDataDirPath();
            $imageUrlTemplate = $captchaSettings->getImageUrlTemplate();

            $setImageUrlPathMethod->invokeArgs(
                $captchaEntity,
                [
                    $imageFileName,
                    static::IMAGE_DIRECTORY_PATH,
                    $imageUrlTemplate
                ]
            );

            $imageUrlPath = sprintf(
                '%s%s/%s',
                $imageUrlTemplate,
                static::IMAGE_DIRECTORY_PATH,
                $imageFileName
            );

            $this->assertEquals(
                $captchaEntity->getImageUrlPath(),
                $imageUrlPath
            );
        }
    }

    private function _getCaptchaSettingsInstance(): CaptchaSettings
    {
        return new CaptchaSettings($this->getSettingsData());
    }

    private function _getCaptchaEntityInstance(): CaptchaEntity
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();

        $captchaEntity = new CaptchaEntity(
            static::TEXT,
            static::HASH,
            static::IMAGE_FILE_NAME,
            $captchaSettings
        );

        return $captchaEntity;
    }
}
