<?php
use Core\Plugins\Captcha\Classes\CaptchaSettings;

/**
 * Class For Testing CaptchaPlugin Settings Class
 */
class CaptchaSettingsTest extends CaptchaTest
{
    /**
     * @var string Sample Data Directory Path For Unit Tests
     */
    const DATA_DIR_PATH = __DIR__.'/../tmp';

    /**
     * @var string Sample Invalid Data Directory Path For Unit Tests
     */
    const INVALID_DATA_DIR_PATH = __DIR__.'/../tmp/test/';

    /**
     * @var string Sample Image Url Template Value For Unit Tests
     */
    const IMAGE_URL_TEMPLATE = '/test/';

    /**
     * @var string Sample Language Value For Unit Tests
     */
    const LANGUAGE = 'test';

    /**
     * @var string Sample Captcha Hash Salt For Unit Tests
     */
    const HASH_SALT = 'test';

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
     * @var array Sample Invalid Settings Data For Unit Tests
     */
    const INVALID_SETTINGS_DATA = [
        'foo' => 'bar'
    ];

    /**
     * Unit Test Of CaptchaSettings getHashSalt Method
     */
    public function testGetHashSalt(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData    = $this->getSettingsData();

        $this->assertEquals(
            $captchaSettings->getHashSalt(),
            $settingsData['hash_salt']
        );
    }

    /**
     * Unit Test Of CaptchaSettings getDataDirPath Method
     */
    public function testGetDataDirPath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData    = $this->getSettingsData();

        $this->assertEquals(
            $captchaSettings->getDataDirPath(),
            $settingsData['data_dir_path']
        );
    }

    /**
     * Unit Test Of CaptchaSettings getImageUrlTemplate Method
     */
    public function testGetImageUrlTemplate(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData    = $this->getSettingsData();

        $this->assertEquals(
            $captchaSettings->getImageUrlTemplate(),
            $settingsData['image_url_template']
        );
    }

    /**
     * Unit Test Of CaptchaSettings getLanguage Method
     */
    public function testGetLanguage(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData    = $this->getSettingsData();

        $this->assertEquals(
            $captchaSettings->getLanguage(),
            $settingsData['language']
        );
    }

    /**
     * Unit Test Of CaptchaSettings _mapSettingsData Method
     */
    public function testMapSettingsData(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData    = $this->getSettingsData();

        $mapSettingsDataMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaSettings',
            '_mapSettingsData'
        );

        $mapSettingsDataMethod->setAccessible(true);

        $exception = null;

        try {
            $mapSettingsDataMethod->invoke($captchaSettings);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $mapSettingsDataMethod->invokeArgs(
                $captchaSettings,
                [static::INVALID_SETTINGS_DATA]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $mapSettingsDataMethod->invokeArgs($captchaSettings, [$settingsData]);

        $this->assertEquals(
            $captchaSettings->getHashSalt(),
            static::HASH_SALT
        );

        $this->assertEquals(
            $captchaSettings->getDataDirPath(),
            static::DATA_DIR_PATH
        );

        $this->assertEquals(
            $captchaSettings->getImageUrlTemplate(),
            static::IMAGE_URL_TEMPLATE
        );

        $this->assertEquals(
            $captchaSettings->getLanguage(),
            static::LANGUAGE
        );
    }

    /**
     * Unit Test Of CaptchaSettings _checkSettingsData Method
     */
    public function testCheckSettingsData(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData    = $this->getSettingsData();

        $checkSettingsDataMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaSettings',
            '_checkSettingsData'
        );

        $checkSettingsDataMethod->setAccessible(true);

        $checkSettingsDataMethod->invokeArgs(
            $captchaSettings,
            [$settingsData]
        );

        $settingsData['hash_salt'] = null;
        $exception                 = null;

        try {
            $checkSettingsDataMethod->invokeArgs(
                $captchaSettings,
                [$settingsData]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        unset($settingsData['hash_salt']);

        $exception = null;

        try {
            $checkSettingsDataMethod->invokeArgs(
                $captchaSettings,
                [$settingsData]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $settingsData = null;
        $exception    = null;

        try {
            $checkSettingsDataMethod->invokeArgs(
                $captchaSettings,
                [$settingsData]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $settingsData = static::INVALID_SETTINGS_DATA;
        $exception    = null;

        try {
            $checkSettingsDataMethod->invokeArgs(
                $captchaSettings,
                [$settingsData]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);
    }

    /**
     * Unit Test Of CaptchaSettings _setHashSalt Method
     */
    public function testSetHashSalt(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();

        $setHashSaltMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaSettings',
            '_setHashSalt'
        );

        $setHashSaltMethod->setAccessible(true);

        $exception = null;

        try {
            $setHashSaltMethod->invoke($captchaSettings);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $settingsDataList = static::SETTINGS_DATA_LIST;

        foreach ($settingsDataList as $settingsData) {
            $hashSalt = $settingsData['hash_salt'];

            $setHashSaltMethod->invokeArgs($captchaSettings, [$hashSalt]);

            $this->assertEquals($captchaSettings->getHashSalt(), $hashSalt);
        }
    }

    /**
     * Unit Test Of CaptchaSettings _setDataDirPath Method
     */
    public function testSetDataDirPath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData    = $this->getSettingsData();

        $setDataDirPathMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaSettings',
            '_setDataDirPath'
        );

        $setDataDirPathMethod->setAccessible(true);

        $exception = null;

        try {
            $setDataDirPathMethod->invoke($captchaSettings);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $setDataDirPathMethod->invokeArgs(
                $captchaSettings,
                [static::INVALID_DATA_DIR_PATH]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $dataDirPath = $settingsData['data_dir_path'];

        $setDataDirPathMethod->invokeArgs($captchaSettings, [$dataDirPath]);

        $this->assertEquals($captchaSettings->getDataDirPath(), $dataDirPath);
    }

    /**
     * Unit Test Of CaptchaSettings _setImageUrlTemplate Method
     */
    public function testSetImageUrlTemplate(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();

        $setImageUrlTemplateMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaSettings',
            '_setImageUrlTemplate'
        );

        $setImageUrlTemplateMethod->setAccessible(true);

        $setImageUrlTemplateMethod->invoke($captchaSettings);

        $this->assertEquals(
            $captchaSettings->getImageUrlTemplate(),
            CaptchaSettings::DEFAULT_IMAGE_URL_TEMPLATE
        );

        $exception = null;

        $settingsDataList = static::SETTINGS_DATA_LIST;

        foreach ($settingsDataList as $settingsData) {
            $imageUrlTemplate = $settingsData['image_url_template'];

            $setImageUrlTemplateMethod->invokeArgs(
                $captchaSettings,
                [$imageUrlTemplate]
            );

            $this->assertEquals(
                $captchaSettings->getImageUrlTemplate(),
                $imageUrlTemplate
            );
        }
    }

    /**
     * Unit Test Of CaptchaSettings _setLanguage Method
     */
    public function testSetLanguage(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();

        $setLanguageMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaSettings',
            '_setLanguage'
        );

        $setLanguageMethod->setAccessible(true);

        $setLanguageMethod->invoke($captchaSettings);

        $this->assertEquals(
            $captchaSettings->getLanguage(),
            CaptchaSettings::DEFAULT_LANGUAGE
        );

        $settingsDataList = static::SETTINGS_DATA_LIST;

        foreach ($settingsDataList as $settingsData) {
            $language = $settingsData['language'];

            $setLanguageMethod->invokeArgs($captchaSettings, [$language]);

            $this->assertEquals(
                $captchaSettings->getLanguage(),
                $language
            );
        }
    }

    private function _getCaptchaSettingsInstance(): CaptchaSettings
    {
        return new CaptchaSettings($this->getSettingsData());
    }
}
