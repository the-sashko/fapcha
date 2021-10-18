<?php

use Sonder\Plugins\Captcha\Classes\CaptchaEntity;
use Sonder\Plugins\Captcha\Classes\CaptchaSettings;
use Sonder\Plugins\Captcha\Exceptions\CaptchaEntityException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException;

final class CaptchaEntityTest extends CaptchaTest
{
    const TEXT = 'foo bar';

    const HASH = '2918553c85b053da3f1bf4777fc64cca112e956eb1e8f3b627def9ab5dd' .
    '31ac8';

    const IMAGE_FILE_NAME = 'test.png';

    const ENTITY_DATA_LIST = [
        [
            'text' => 'foo',
            'hash' => 'foo',
            'image_file_name' => 'foo.png'
        ],
        [
            'text' => 'bar',
            'hash' => 'bar',
            'image_file_name' => 'bar.png'
        ],
        [
            'text' => 'test',
            'hash' => 'test',
            'image_file_name' => 'test.png'
        ]
    ];

    const IMAGE_DIRECTORY_PATH = 'test';

    /**
     * @throws CaptchaEntityException
     * @throws CaptchaSettingsException
     */
    final public function testGetText(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity = $this->_getCaptchaEntityInstance();

        $this->assertEquals(
            CaptchaEntityTest::TEXT,
            $captchaEntity->getText()
        );

        $captchaEntity = new CaptchaEntity(
            '',
            CaptchaEntityTest::HASH,
            CaptchaEntityTest::IMAGE_FILE_NAME,
            $captchaSettings
        );

        $exception = null;

        try {
            $captchaEntity->getText();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaEntityException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);
    }

    /**
     * @throws CaptchaEntityException
     * @throws CaptchaSettingsException
     */
    final public function testGetHash(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity = $this->_getCaptchaEntityInstance();

        $this->assertEquals(
            CaptchaEntityTest::HASH,
            $captchaEntity->getHash()
        );

        $captchaEntity = new CaptchaEntity(
            CaptchaEntityTest::TEXT,
            '',
            CaptchaEntityTest::IMAGE_FILE_NAME,
            $captchaSettings
        );

        $exception = null;

        try {
            $captchaEntity->getHash();
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaEntityException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);
    }

    /**
     * @throws CaptchaEntityException
     * @throws CaptchaSettingsException
     */
    final public function testGetImageFilePath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity = $this->_getCaptchaEntityInstance();
        $imageFilePath = $captchaEntity->getImageFilePath();

        $assertRegexp = '/^%s\/img\/[\d]{4}\/[\d]{2}\/[\d]{2}\/[\d]{2}\/' .
            '[\d]{2}\/[\d]{2}\/%s$/sui';
        $assertRegexp = sprintf(
            $assertRegexp,
            str_replace('/', '\/', $captchaSettings->getDataDirPath()),
            CaptchaEntityTest::IMAGE_FILE_NAME
        );

        $this->assertTrue((bool)preg_match($assertRegexp, $imageFilePath));
    }

    /**
     * @throws CaptchaEntityException
     * @throws CaptchaSettingsException
     */
    final public function testGetImageUrlPath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity = $this->_getCaptchaEntityInstance();
        $imageUrlPath = $captchaEntity->getImageUrlPath();

        $assertRegexp = '/^%s[\d]{4}\/[\d]{2}\/[\d]{2}\/[\d]{2}\/' .
            '[\d]{2}\/[\d]{2}\/%s$/sui';
        $assertRegexp = sprintf(
            $assertRegexp,
            str_replace('/', '\/', $captchaSettings->getImageUrlTemplate()),
            CaptchaEntityTest::IMAGE_FILE_NAME
        );

        $this->assertTrue((bool)preg_match($assertRegexp, $imageUrlPath));
    }

    /**
     * @throws CaptchaEntityException
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testSetText(): void
    {
        $captchaEntity = $this->_getCaptchaEntityInstance();

        $setTextMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaEntity',
            '_setText'
        );

        $setTextMethod->setAccessible(true);

        $entityDataList = CaptchaEntityTest::ENTITY_DATA_LIST;

        foreach ($entityDataList as $entityData) {
            $text = $entityData['text'];

            $setTextMethod->invokeArgs($captchaEntity, [$text]);

            $this->assertEquals($captchaEntity->getText(), $text);
        }
    }

    /**
     * @throws CaptchaEntityException
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testSetHash(): void
    {
        $captchaEntity = $this->_getCaptchaEntityInstance();

        $setHashMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaEntity',
            '_setHash'
        );

        $setHashMethod->setAccessible(true);

        $entityDataList = CaptchaEntityTest::ENTITY_DATA_LIST;

        foreach ($entityDataList as $entityData) {
            $hash = $entityData['hash'];

            $setHashMethod->invokeArgs($captchaEntity, [$hash]);

            $this->assertEquals($captchaEntity->getHash(), $hash);
        }
    }

    /**
     * @throws CaptchaEntityException
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testSetImagePath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity = $this->_getCaptchaEntityInstance();

        $setImagePathMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaEntity',
            '_setImagePath'
        );

        $setImagePathMethod->setAccessible(true);

        $entityDataList = CaptchaEntityTest::ENTITY_DATA_LIST;

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
            $imageUrlPath = $captchaEntity->getImageUrlPath();

            $dataDirPath = $captchaSettings->getDataDirPath();
            $imageUrlTemplate = $captchaSettings->getImageUrlTemplate();

            $assertRegexp = '/^%s\/img\/[\d]{4}\/[\d]{2}\/[\d]{2}\/[\d]{2}\/' .
                '[\d]{2}\/[\d]{2}\/%s$/sui';

            $assertRegexp = sprintf(
                $assertRegexp,
                str_replace('/', '\/', $dataDirPath),
                $imageFileName
            );

            $this->assertTrue((bool)preg_match(
                $assertRegexp,
                $imageFilePath
            ));

            $assertRegexp = '/^%s[\d]{4}\/[\d]{2}\/[\d]{2}\/' .
                '[\d]{2}\/[\d]{2}\/[\d]{2}\/%s$/sui';

            $assertRegexp = sprintf(
                $assertRegexp,
                str_replace('/', '\/', $imageUrlTemplate),
                $imageFileName
            );

            $this->assertTrue((bool)preg_match(
                $assertRegexp,
                $imageUrlPath
            ));
        }
    }

    /**
     * @throws CaptchaEntityException
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testCreateImageDirectory(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity = $this->_getCaptchaEntityInstance();

        $createImageDirectoryMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaEntity',
            '_createImageDirectory'
        );

        $createImageDirectoryMethod->setAccessible(true);

        $directoryPath = $createImageDirectoryMethod->invokeArgs(
            $captchaEntity,
            [$captchaSettings]
        );

        $this->assertTrue((bool)preg_match(
            '/^[\d]{4}\/[\d]{2}\/[\d]{2}\/[\d]{2}\/[\d]{2}\/[\d]{2}$/sui',
            $directoryPath
        ));
    }

    /**
     * @throws CaptchaEntityException
     * @throws CaptchaSettingsException
     * @throws ReflectionException
     */
    final public function testImageUrlPath(): void
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();
        $captchaEntity = $this->_getCaptchaEntityInstance();

        $setImageUrlPathMethod = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaEntity',
            '_setImageUrlPath'
        );

        $setImageUrlPathMethod->setAccessible(true);

        $entityDataList = CaptchaEntityTest::ENTITY_DATA_LIST;

        foreach ($entityDataList as $entityData) {
            $imageFileName = $entityData['image_file_name'];

            $imageUrlTemplate = $captchaSettings->getImageUrlTemplate();

            $setImageUrlPathMethod->invokeArgs(
                $captchaEntity,
                [
                    $imageFileName,
                    CaptchaEntityTest::IMAGE_DIRECTORY_PATH,
                    $imageUrlTemplate
                ]
            );

            $imageUrlPath = sprintf(
                '%s%s/%s',
                $imageUrlTemplate,
                CaptchaEntityTest::IMAGE_DIRECTORY_PATH,
                $imageFileName
            );

            $this->assertEquals(
                $captchaEntity->getImageUrlPath(),
                $imageUrlPath
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

    /**
     * @return CaptchaEntity
     * @throws CaptchaEntityException
     * @throws CaptchaSettingsException
     */
    private function _getCaptchaEntityInstance(): CaptchaEntity
    {
        $captchaSettings = $this->_getCaptchaSettingsInstance();

        return new CaptchaEntity(
            CaptchaEntityTest::TEXT,
            CaptchaEntityTest::HASH,
            CaptchaEntityTest::IMAGE_FILE_NAME,
            $captchaSettings
        );
    }
}
