<?php

use Sonder\Plugins\Captcha\Classes\CaptchaSettings;
use Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException;

final class CaptchaSettingsTest extends CaptchaTest
{
    const DATA_DIR_PATH = __DIR__ . '/../tmp';

    const INVALID_DATA_DIR_PATH = __DIR__ . '/../tmp/test/';

    const IMAGE_URL_TEMPLATE = '/test/';

    const LANGUAGE = 'test';

    const HASH_SALT = 'test';

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

    const INVALID_SETTINGS_DATA = [
        'foo' => 'bar'
    ];

    /**
     * @throws CaptchaSettingsException
     */
    final public function testGetHashSalt(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData = $this->getSettingsData();

        $this->assertEquals(
            $captchaSettings->getHashSalt(),
            $settingsData['hash_salt']
        );
    }

    /**
     * @throws CaptchaSettingsException
     */
    final public function testGetDataDirPath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData = $this->getSettingsData();

        $this->assertEquals(
            $captchaSettings->getDataDirPath(),
            $settingsData['data_dir_path']
        );
    }

    /**
     * @throws CaptchaSettingsException
     */
    final public function testGetImageUrlTemplate(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData = $this->getSettingsData();

        $this->assertEquals(
            $captchaSettings->getImageUrlTemplate(),
            $settingsData['image_url_template']
        );
    }

    /**
     * @throws CaptchaSettingsException
     */
    final public function testGetLanguage(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData = $this->getSettingsData();

        $this->assertEquals(
            $captchaSettings->getLanguage(),
            $settingsData['language']
        );
    }

    /**
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testMapSettingsData(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData = $this->getSettingsData();

        $mapSettingsDataMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaSettings',
            '_mapSettingsData'
        );

        $mapSettingsDataMethod->setAccessible(true);

        $exception = null;

        try {
            $mapSettingsDataMethod->invoke($captchaSettings);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'SonderPlugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $mapSettingsDataMethod->invokeArgs(
                $captchaSettings,
                [CaptchaSettingsTest::INVALID_SETTINGS_DATA]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $mapSettingsDataMethod->invokeArgs($captchaSettings, [$settingsData]);

        $this->assertEquals(
            CaptchaSettingsTest::HASH_SALT,
            $captchaSettings->getHashSalt()
        );

        $this->assertEquals(
            CaptchaSettingsTest::DATA_DIR_PATH,
            $captchaSettings->getDataDirPath()
        );

        $this->assertEquals(
            CaptchaSettingsTest::IMAGE_URL_TEMPLATE,
            $captchaSettings->getImageUrlTemplate()
        );

        $this->assertEquals(
            CaptchaSettingsTest::LANGUAGE,
            $captchaSettings->getLanguage()
        );
    }

    /**
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testCheckSettingsData(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData = $this->getSettingsData();

        $checkSettingsDataMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaSettings',
            '_checkSettingsData'
        );

        $checkSettingsDataMethod->setAccessible(true);

        $checkSettingsDataMethod->invokeArgs(
            $captchaSettings,
            [$settingsData]
        );

        $settingsData['hash_salt'] = null;
        $exception = null;

        try {
            $checkSettingsDataMethod->invokeArgs(
                $captchaSettings,
                [$settingsData]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException',
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
                'Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $settingsData = null;
        $exception = null;

        try {
            $checkSettingsDataMethod->invokeArgs(
                $captchaSettings,
                [$settingsData]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $settingsData = CaptchaSettingsTest::INVALID_SETTINGS_DATA;
        $exception = null;

        try {
            $checkSettingsDataMethod->invokeArgs(
                $captchaSettings,
                [$settingsData]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);
    }

    /**
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testSetHashSalt(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();

        $setHashSaltMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaSettings',
            '_setHashSalt'
        );

        $setHashSaltMethod->setAccessible(true);

        $exception = null;

        try {
            $setHashSaltMethod->invoke($captchaSettings);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $settingsDataList = CaptchaSettingsTest::SETTINGS_DATA_LIST;

        foreach ($settingsDataList as $settingsData) {
            $hashSalt = $settingsData['hash_salt'];

            $setHashSaltMethod->invokeArgs($captchaSettings, [$hashSalt]);

            $this->assertEquals($captchaSettings->getHashSalt(), $hashSalt);
        }
    }

    /**
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testSetDataDirPath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $settingsData = $this->getSettingsData();

        $setDataDirPathMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaSettings',
            '_setDataDirPath'
        );

        $setDataDirPathMethod->setAccessible(true);

        $exception = null;

        try {
            $setDataDirPathMethod->invoke($captchaSettings);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $setDataDirPathMethod->invokeArgs(
                $captchaSettings,
                [CaptchaSettingsTest::INVALID_DATA_DIR_PATH]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $dataDirPath = $settingsData['data_dir_path'];

        $setDataDirPathMethod->invokeArgs($captchaSettings, [$dataDirPath]);

        $this->assertEquals($captchaSettings->getDataDirPath(), $dataDirPath);
    }

    /**
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testSetImageUrlTemplate(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();

        $setImageUrlTemplateMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaSettings',
            '_setImageUrlTemplate'
        );

        $setImageUrlTemplateMethod->setAccessible(true);

        $setImageUrlTemplateMethod->invoke($captchaSettings);

        $this->assertEquals(
            CaptchaSettings::DEFAULT_IMAGE_URL_TEMPLATE,
            $captchaSettings->getImageUrlTemplate()
        );

        $settingsDataList = CaptchaSettingsTest::SETTINGS_DATA_LIST;

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
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testSetLanguage(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();

        $setLanguageMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaSettings',
            '_setLanguage'
        );

        $setLanguageMethod->setAccessible(true);

        $setLanguageMethod->invoke($captchaSettings);

        $this->assertEquals(
            CaptchaSettings::DEFAULT_LANGUAGE,
            $captchaSettings->getLanguage()
        );

        $settingsDataList = CaptchaSettingsTest::SETTINGS_DATA_LIST;

        foreach ($settingsDataList as $settingsData) {
            $language = $settingsData['language'];

            $setLanguageMethod->invokeArgs($captchaSettings, [$language]);

            $this->assertEquals(
                $captchaSettings->getLanguage(),
                $language
            );
        }
    }

    /**
     * @return CaptchaSettings
     * @throws CaptchaSettingsException
     */
    private function _getCaptchaSettingsInstance(): CaptchaSettings
    {
        return new CaptchaSettings($this->getSettingsData());
    }
}
