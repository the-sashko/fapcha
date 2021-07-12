<?php

/**
 * Class For Testing CaptchaPlugin
 */
class CaptchaPluginTest extends CaptchaTest
{
    /**
     * @var string Sample Database Path For Unit Tests
     */
    const DATABASE_FILE_PATH = __DIR__.'/../tmp/dictionaries.db';

    /**
     * @var string Sample Invalid Data Directory Path For Unit Tests
     */
    const INVALID_DATA_DIR_PATH = __DIR__.'/../tmp/test/';

    /**
     * @var string Sample Invalid Language Value For Unit Tests
     */
    const INVALID_LANGUAGE = 'invalid';

    /**
     * @var array Sample Invalid Settings Data For Unit Tests
     */
    const INVALID_SETTINGS_DATA = [
        'foo' => 'bar'
    ];

    /**
     * @var string Sample Captcha Hash For Unit Tests
     */
    const HASH = '2918553c85b053da3f1bf4777fc64cca112e956eb1e8f3b627def9ab5dd'.
                 '31ac8';

    /**
     * @var string Sample Invalid Captcha Hash For Unit Tests
     */
    const INVALID_HASH = 'test';

    /**
     * @var string Sample Captcha Text For Unit Tests
     */
    const TEXT = 'foo bar';

    /**
     * @var string Sample Invalid Captcha Text For Unit Tests
     */
    const INVALID_TEXT = 'test';

    /**
     * @var array Sample Captcha List Of Texts And Hashes For Unit Tests
     */
    const TEXT_HASH_LIST = [
        'foo'    => '5bcd0a1d41c47a7d1e035f4f5b5875fd11b72754621e2fdd1c52398e'.
                    'ea9e724f',
        'bar'    => 'f37427a58ae520260d398b102cf2690dd02855d8d759ac6f90589e56'.
                    'd419db16',
        'test'   => 'ce7630043285aa43ea869a5f48632d75463ddf3d88d67d52994e9380'.
                    'e5b398f1',
        'qwerty' => 'b18bf10bd87bef93c7f71c3ba39f8048a5f6a62f7ba8fb43614dec81'.
                    '8fb550ff'
    ];

    /**
     * Unit Test Of CaptchaPlugin setSetings Method
     */
    public function testSetSettings(?array $settingsData = null): void
    {
        $captchaPlugin = new CaptchaPlugin();

        $captchaPlugin->setSettings($this->getSettingsData());

        $exception = null;

        try {
            $captchaPlugin->setSettings();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $captchaPlugin->setSettings(static::INVALID_SETTINGS_DATA);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);
    }

    /**
     * Unit Test Of CaptchaPlugin updateByCron Method
     */
    public function testUpdateByCron(): void
    {
        $this->removeStore();

        $captchaPlugin = new CaptchaPlugin();

        $exception = null;

        try {
            $captchaPlugin->updateByCron();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaPluginException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $settingsData                  = $this->getSettingsData();
        $settingsData['data_dir_path'] = static::INVALID_DATA_DIR_PATH;

        $exception = null;

        try {
            $captchaPlugin->setSettings($settingsData);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $captchaPlugin->setSettings($this->getSettingsData());
        $captchaPlugin->updateByCron();

        $this->assertTrue(file_exists(static::DATABASE_FILE_PATH));
        $this->assertTrue(is_file(static::DATABASE_FILE_PATH));

        $dictioniesData = file_get_contents(static::DATABASE_FILE_PATH);

        $sampleDictioniesData = file_get_contents(static::DATABASE_FILE_PATH);

        $this->assertEquals($dictioniesData, $sampleDictioniesData);
    }

    /**
     * Unit Test Of CaptchaPlugin getEntity Method
     */
    public function testGetEntity(): void
    {
        $this->prepareStore();

        $captchaPlugin = new CaptchaPlugin();

        $exception = null;

        try {
            $captchaPlugin->getEntity();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaPluginException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $settingsData             = $this->getSettingsData();
        $settingsData['language'] = static::INVALID_LANGUAGE;

        $captchaPlugin->setSettings($settingsData);

        $exception = null;

        try {
            $captchaPlugin->getEntity();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaTextException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $captchaPlugin->setSettings($this->getSettingsData());

        $captchaEntity = $captchaPlugin->getEntity();

        $imageUrl = $captchaEntity->getImageUrlPath();

        $imageUrlMatchingRegexp = $this->_getImageUrlMatchingRegexp();

        $this->assertTrue((bool) preg_match(
            $imageUrlMatchingRegexp,
            $imageUrl
        ));

        $imageFilePath = $this->_getImageFilePathFromUrl($imageUrl);

        $this->assertTrue(file_exists($imageFilePath));
        $this->assertTrue(is_file($imageFilePath));

        $this->assertEquals($captchaEntity->getHash(), static::HASH);
    }

    /**
     * Unit Test Of CaptchaPlugin check Method
     */
    public function testCheck(): void
    {
        $captchaPlugin = new CaptchaPlugin();
        $captchaPlugin->setSettings($this->getSettingsData());

        $this->assertTrue($captchaPlugin->check(static::TEXT, static::HASH));

        $this->assertFalse($captchaPlugin->check(
            static::INVALID_TEXT,
            static::INVALID_HASH
        ));

        $this->assertFalse($captchaPlugin->check(
            static::INVALID_TEXT,
            static::HASH
        ));

        $this->assertFalse($captchaPlugin->check(
            static::TEXT,
            static::INVALID_HASH
        ));

        $this->assertFalse($captchaPlugin->check());
        $this->assertFalse($captchaPlugin->check(null, null));
    }

    /**
     * Unit Test Of CaptchaPlugin _getHash check Method
     */
    public function testGetHash(): void
    {
        $captchaPlugin = new CaptchaPlugin();
        $captchaPlugin->setSettings($this->getSettingsData());

        $getHashMethod = new ReflectionMethod('CaptchaPlugin', '_getHash');
        $getHashMethod->setAccessible(true);

        foreach(static::TEXT_HASH_LIST as $text => $hash) {
            $text = [
                $text
            ];

            $this->assertEquals(
                $hash,
                $getHashMethod->invokeArgs($captchaPlugin, $text)
            );
        }
    }

    /**
     * Unit Test Of CaptchaPlugin _getFileName check Method
     */
    public function testGetFileName(): void
    {
        $captchaPlugin = new CaptchaPlugin();
        $captchaPlugin->setSettings($this->getSettingsData());

        $getFileNameMethod = new ReflectionMethod(
            'CaptchaPlugin',
            '_getFileName'
        );

        $getFileNameMethod->setAccessible(true);

        $hash = [
            static::HASH
        ];

        $this->assertEquals(
            $getFileNameMethod->invokeArgs($captchaPlugin, $hash),
            sprintf('%s.png', static::HASH)
        );
    }

    /**
     * @return string Regexp For Matching Image URL
     */
    private function _getImageUrlMatchingRegexp(): string
    {
        $imageUrlMatchingRegexp = '/^%s[\d]{4}\/[\d]{2}\/[\d]{2}\/[\d]{2}\/'.
                                  '[\d]{2}\/[\d]{2}\/%s\.png$/sui';

        return sprintf(
            $imageUrlMatchingRegexp,
            str_replace('/', '\/', static::IMAGE_URL_TEMPLATE),
            static::HASH
        );
    }

    /**
     * @var string Image URL
     *
     * @return string Image File Path
     */
    private function _getImageFilePathFromUrl(string $imageUrl): string
    {
        $imageFilePathRegexp = sprintf(
            '/^%s(.*?)$/su',
            str_replace('/', '\/', static::IMAGE_URL_TEMPLATE)
        );

        return preg_replace(
            $imageFilePathRegexp,
            static::DATA_DIR_PATH.'/img/$1',
            $imageUrl
        );
    }
}
