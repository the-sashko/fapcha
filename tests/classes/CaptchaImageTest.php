<?php

use Sonder\Plugins\Captcha\Classes\CaptchaEntity;
use Sonder\Plugins\Captcha\Classes\CaptchaImage;
use Sonder\Plugins\Captcha\Classes\CaptchaSettings;
use Sonder\Plugins\Captcha\Exceptions\CaptchaEntityException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaImageException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException;

final class CaptchaImageTest extends CaptchaTest
{
    const DATA_DIR_PATH = __DIR__ . '/../tmp';

    const TEXT = 'foo bar';

    const HASH = '2918553c85b053da3f1bf4777fc64cca112e956eb1e8f3b627def9ab5dd' .
    '31ac8';

    const IMAGE_FILE_NAME = 'test.png';

    const IMAGE_WIDTH = 100;

    const IMAGE_HEIGHT = 100;

    const DOT_SIZE = 10;

    /**
     * @throws CaptchaEntityException
     * @throws CaptchaImageException
     * @throws CaptchaSettingsException
     * @throws ImagickException
     */
    final public function testCreate(): void
    {
        $captchaEntity = $this->_getCaptchaEntityInstance();
        $captchaImage = new CaptchaImage();

        $imageFilePath = $captchaEntity->getImageFilePath();

        $this->assertFalse(
            file_exists($imageFilePath) &&
            is_file($imageFilePath)
        );

        $captchaImage->create($captchaEntity);

        $this->assertTrue(
            file_exists($imageFilePath) &&
            is_file($imageFilePath)
        );
    }

    /**
     * @throws ReflectionException
     */
    final public function testSaveImage(): void
    {
        $captchaImage = new CaptchaImage();

        $saveImageMethod = $this->_getReflectionMethod('_saveImage');
        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');

        $exception = null;

        try {
            $saveImageMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $imageFilePath = CaptchaImageTest::DATA_DIR_PATH . '/' . CaptchaImageTest::IMAGE_FILE_NAME;

        $exception = null;

        try {
            $saveImageMethod->invokeArgs($captchaImage, [$imageFilePath]);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );

        $saveImageMethod->invokeArgs($captchaImage, [$imageFilePath]);
    }

    /**
     * @throws ReflectionException
     */
    final public function testSetImageFormat(): void
    {
        $captchaImage = new CaptchaImage();

        $setImageFormatMethod = $this->_getReflectionMethod('_setImageFormat');
        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');

        $exception = null;

        try {
            $setImageFormatMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );

        $setImageFormatMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testGdPostProcessing(): void
    {
        $captchaImage = new CaptchaImage();

        $gdPostProcessingMethod = $this->_getReflectionMethod('_gdPostProcessing');
        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');
        $setImageFormatMethod = $this->_getReflectionMethod('_setImageFormat');

        $exception = null;

        try {
            $gdPostProcessingMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );

        $exception = null;

        try {
            $gdPostProcessingMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf('ImagickException', $exception);
        }

        $this->assertNotEmpty($exception);

        $setImageFormatMethod->invoke($captchaImage);

        $gdPostProcessingMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testResizeImage(): void
    {
        $captchaImage = new CaptchaImage();

        $resizeImageMethod = $this->_getReflectionMethod('_resizeImage');
        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');

        $exception = null;

        try {
            $resizeImageMethod->invokeArgs($captchaImage, [null, null]);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $resizeImageMethod->invokeArgs(
                $captchaImage,
                [
                    CaptchaImageTest::IMAGE_WIDTH,
                    CaptchaImageTest::IMAGE_HEIGHT
                ]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );

        $resizeImageMethod->invokeArgs(
            $captchaImage,
            [
                CaptchaImageTest::IMAGE_WIDTH,
                CaptchaImageTest::IMAGE_HEIGHT
            ]
        );
    }

    /**
     * @throws ReflectionException
     */
    final public function testSetFilters(): void
    {
        $captchaImage = new CaptchaImage();

        $setFiltersMethod = $this->_getReflectionMethod('_setFilters');
        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');

        $exception = null;

        try {
            $setFiltersMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );

        $setFiltersMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testSetBrightnessAndContrast(): void
    {
        $captchaImage = new CaptchaImage();

        $setBrightnessAndContrastMethod = $this->_getReflectionMethod('_setBrightnessAndContrast');
        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');

        $exception = null;

        try {
            $setBrightnessAndContrastMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );

        $setBrightnessAndContrastMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testResizeImageForFilters(): void
    {
        $captchaImage = new CaptchaImage();

        $resizeImageForFiltersMethod = $this->_getReflectionMethod('_resizeImageForFilters');
        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');

        $exception = null;

        try {
            $resizeImageForFiltersMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );

        $resizeImageForFiltersMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testSetBlurFilters(): void
    {
        $captchaImage = new CaptchaImage();

        $setBlurFiltersMethod = $this->_getReflectionMethod('_setBlurFilters');
        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');

        $exception = null;

        try {
            $setBlurFiltersMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );

        $setBlurFiltersMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testPosterizeImage(): void
    {
        $captchaImage = new CaptchaImage();

        $posterizeImageMethod = $this->_getReflectionMethod('_posterizeImage');
        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');

        $exception = null;

        try {
            $posterizeImageMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );

        $posterizeImageMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testSetResizeFilters(): void
    {
        $captchaImage = new CaptchaImage();

        $setResizeFiltersMethod = $this->_getReflectionMethod('_setResizeFilters');
        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');

        $exception = null;

        try {
            $setResizeFiltersMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );

        $setResizeFiltersMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testDrawDots(): void
    {
        $captchaImage = new CaptchaImage();

        $drawDotsMethod = $this->_getReflectionMethod('_drawDots');
        $createDrawInstanceMethod = $this->_getReflectionMethod('_createDrawInstance');

        $exception = null;

        try {
            $drawDotsMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawDotsMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testDrawBigDots(): void
    {
        $captchaImage = new CaptchaImage();

        $drawBigDotsMethod = $this->_getReflectionMethod('_drawBigDots');
        $createDrawInstanceMethod = $this->_getReflectionMethod('_createDrawInstance');

        $exception = null;

        try {
            $drawBigDotsMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawBigDotsMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testDrawMiddleDots(): void
    {
        $captchaImage = new CaptchaImage();

        $drawMiddleDotsMethod = $this->_getReflectionMethod('_drawMiddleDots');
        $createDrawInstanceMethod = $this->_getReflectionMethod('_createDrawInstance');

        $exception = null;

        try {
            $drawMiddleDotsMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawMiddleDotsMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testDrawSmallDots(): void
    {
        $captchaImage = new CaptchaImage();

        $drawSmallDotsMethod = $this->_getReflectionMethod('_drawSmallDots');
        $createDrawInstanceMethod = $this->_getReflectionMethod('_createDrawInstance');

        $exception = null;

        try {
            $drawSmallDotsMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawSmallDotsMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testDrawDot(): void
    {
        $captchaImage = new CaptchaImage();

        $createDrawInstanceMethod = $this->_getReflectionMethod('_createDrawInstance');
        $drawDotMethod = $this->_getReflectionMethod('_drawDot');

        $exception = null;

        try {
            $drawDotMethod->invokeArgs(
                $captchaImage,
                [CaptchaImageTest::DOT_SIZE]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $drawDotMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawDotMethod->invokeArgs($captchaImage, [CaptchaImageTest::DOT_SIZE]);
    }

    /**
     * @throws ReflectionException
     */
    final public function testDrawLines(): void
    {
        $captchaImage = new CaptchaImage();

        $drawLinesMethod = $this->_getReflectionMethod('_drawLines');
        $createDrawInstanceMethod = $this->_getReflectionMethod('_createDrawInstance');

        $exception = null;

        try {
            $drawLinesMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawLinesMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testDrawLine(): void
    {
        $captchaImage = new CaptchaImage();

        $drawLineMethod = $this->_getReflectionMethod('_writeText');
        $createDrawInstanceMethod = $this->_getReflectionMethod('_createDrawInstance');

        $exception = null;

        try {
            $drawLineMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawLineMethod->invoke($captchaImage);
    }

    /**
     * @throws ReflectionException
     */
    final public function testWriteText(): void
    {
        $captchaImage = new CaptchaImage();

        $writeTextMethod = $this->_getReflectionMethod('_writeText');
        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');
        $createDrawInstanceMethod = $this->_getReflectionMethod('_createDrawInstance');

        $exception = null;

        try {
            $writeTextMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $writeTextMethod->invokeArgs($captchaImage, [CaptchaImageTest::TEXT]);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );

        $exception = null;

        try {
            $writeTextMethod->invokeArgs($captchaImage, [CaptchaImageTest::TEXT]);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $writeTextMethod->invokeArgs($captchaImage, [CaptchaImageTest::TEXT]);
    }

    /**
     * @throws ReflectionException
     */
    final public function testCreateImageInstance(): void
    {
        $captchaImage = new CaptchaImage();


        $createImageInstanceMethod = $this->_getReflectionMethod('_createImageInstance');

        $exception = null;

        try {
            $createImageInstanceMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Sonder\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(CaptchaImageTest::TEXT)]
        );
    }

    /**
     * @return CaptchaEntity
     * @throws CaptchaEntityException
     * @throws CaptchaSettingsException
     */
    private function _getCaptchaEntityInstance(): CaptchaEntity
    {
        $captchaSettings = new CaptchaSettings($this->getSettingsData());

        return new CaptchaEntity(
            CaptchaImageTest::TEXT,
            CaptchaImageTest::HASH,
            CaptchaImageTest::IMAGE_FILE_NAME,
            $captchaSettings
        );
    }

    /**
     * @param string $methodName
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    private function _getReflectionMethod(string $methodName): ReflectionMethod
    {
        $methodInstance = new ReflectionMethod(
            'Sonder\Plugins\Captcha\Classes\CaptchaImage',
            $methodName
        );

        $methodInstance->setAccessible(true);

        return $methodInstance;
    }
}
