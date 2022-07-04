<?php

use Sonder\Plugins\Captcha\Exceptions\CaptchaPluginException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException;
use Sonder\Plugins\CaptchaPlugin;

final class CaptchaPluginTest extends CaptchaTest
{
    private const DATABASE_FILE_PATH = __DIR__ . '/../tmp/dictionaries.db';

    private const INVALID_DATA_DIR_PATH = __DIR__ . '/../tmp/test/';

    private const INVALID_LANGUAGE = 'invalid';

    private const INVALID_SETTINGS_DATA = [
        'foo' => 'bar'
    ];

    private const HASH = '2918553c85b053da3f1bf4777fc64cca112e956eb1e8f3b627def9ab5dd31ac8';

    private const INVALID_HASH = 'test';

    private const TEXT = 'foo bar';

    private const INVALID_TEXT = 'test';

    private const TEXT_HASH_LIST = [
        'foo' => '5bcd0a1d41c47a7d1e035f4f5b5875fd11b72754621e2fdd1c52398eea9e724f',
        'bar' => 'f37427a58ae520260d398b102cf2690dd02855d8d759ac6f90589e56d419db16',
        'test' => 'ce7630043285aa43ea869a5f48632d75463ddf3d88d67d52994e9380e5b398f1',
        'qwerty' => 'b18bf10bd87bef93c7f71c3ba39f8048a5f6a62f7ba8fb43614dec818fb550ff'
    ];

    /**
     * @param array|null $settingsData
     */
    final public function testSetSettings(?array $settingsData = null): void
    {
        $exception = null;

        try {
            new CaptchaPlugin();
        } catch (Throwable $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            new CaptchaPlugin(CaptchaPluginTest::INVALID_SETTINGS_DATA);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            new CaptchaPlugin($settingsData);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException',
                $exception
            );
        }

        $this->assertEmpty($exception);
    }

    /**
     * @throws CaptchaPluginException
     * @throws CaptchaSettingsException
     */
    final public function testCheck(): void
    {
        $captchaPlugin = new CaptchaPlugin($this->getSettingsData());

        $this->assertTrue(
            $captchaPlugin->check(
                CaptchaPluginTest::TEXT,
                CaptchaPluginTest::HASH
            )
        );

        $this->assertFalse(
            $captchaPlugin->check(
                CaptchaPluginTest::INVALID_TEXT,
                CaptchaPluginTest::INVALID_HASH
            )
        );

        $this->assertFalse(
            $captchaPlugin->check(
                CaptchaPluginTest::INVALID_TEXT,
                CaptchaPluginTest::HASH
            )
        );

        $this->assertFalse(
            $captchaPlugin->check(
                CaptchaPluginTest::TEXT,
                CaptchaPluginTest::INVALID_HASH
            )
        );

        $this->assertFalse($captchaPlugin->check());
        $this->assertFalse($captchaPlugin->check());
    }

    /**
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testGetHash(): void
    {
        $captchaPlugin = new CaptchaPlugin($this->getSettingsData());

        $getHashMethod = new ReflectionMethod('CaptchaPlugin', '_getHash');
        $getHashMethod->setAccessible(true);

        foreach (CaptchaPluginTest::TEXT_HASH_LIST as $text => $hash) {
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
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testGetFileName(): void
    {
        $captchaPlugin = new CaptchaPlugin($this->getSettingsData());

        $getFileNameMethod = new ReflectionMethod(
            'CaptchaPlugin',
            '_getFileName'
        );

        $getFileNameMethod->setAccessible(true);

        $hash = [
            CaptchaPluginTest::HASH
        ];

        $this->assertEquals(
            $getFileNameMethod->invokeArgs($captchaPlugin, $hash),
            sprintf('%s.png', CaptchaPluginTest::HASH)
        );
    }
}
