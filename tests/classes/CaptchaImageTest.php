<?php
use Core\Plugins\Captcha\Classes\CaptchaEntity;
use Core\Plugins\Captcha\Classes\CaptchaSettings;
use Core\Plugins\Captcha\Classes\CaptchaImage;

/**
 * Class For Testing CaptchaPlugin Image Class
 */
class CaptchaImageTest extends CaptchaTest
{
    /**
     * @var string Sample Data Directory Path For Unit Tests
     */
    const DATA_DIR_PATH = __DIR__.'/../tmp';

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
     * @var int Sample Captcha Image Width For Unit Tests
     */
    const IMAGE_WIDTH = 100;

    /**
     * @var int Sample Captcha Image Height For Unit Tests
     */
    const IMAGE_HEIGHT = 100;

    /**
     * @var int Sample Captcha Image Dot Size For Unit Tests
     */
    const DOT_SIZE = 10;

    /**
     * Unit Test Of CaptchaImage create Method
     */
    public function testCreate(): void
    {
        $captchaEntity = $this->_getCaptchaEntityInstance();
        $captchaImage  = new CaptchaImage();

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
     * Unit Test Of CaptchaImage _saveImage Method
     */
    public function testSaveImage(): void
    {
        $captchaImage = new CaptchaImage();

        $saveImageMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_saveImage'
        );

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $saveImageMethod->setAccessible(true);
        $createImageInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $saveImageMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $imageFilePath = static::DATA_DIR_PATH.'/'.static::IMAGE_FILE_NAME;

        $exception = null;

        try {
            $saveImageMethod->invokeArgs($captchaImage, [$imageFilePath]);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
        );

        $saveImageMethod->invokeArgs($captchaImage, [$imageFilePath]);
    }

    /**
     * Unit Test Of CaptchaImage _setImageFormat Method
     */
    public function testSetImageFormat(): void
    {
        $captchaImage = new CaptchaImage();

        $setImageFormatMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_setImageFormat'
        );

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $setImageFormatMethod->setAccessible(true);
        $createImageInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $setImageFormatMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
        );

        $setImageFormatMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _gdPostProcessing Method
     */
    public function testGdPostProcessing(): void
    {
        $captchaImage = new CaptchaImage();

        $gdPostProcessingMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_gdPostProcessing'
        );

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $setImageFormatMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_setImageFormat'
        );

        $gdPostProcessingMethod->setAccessible(true);
        $createImageInstanceMethod->setAccessible(true);
        $setImageFormatMethod->setAccessible(true);

        $exception = null;

        try {
            $gdPostProcessingMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
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
     * Unit Test Of CaptchaImage _resizeImage Method
     */
    public function testResizeImage(): void
    {
        $captchaImage = new CaptchaImage();

        $resizeImageMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_resizeImage'
        );

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $resizeImageMethod->setAccessible(true);
        $createImageInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $resizeImageMethod->invokeArgs($captchaImage, [null, null]);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $resizeImageMethod->invokeArgs(
                $captchaImage,
                [
                    static::IMAGE_WIDTH,
                    static::IMAGE_HEIGHT
                ]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
        );

        $resizeImageMethod->invokeArgs(
            $captchaImage,
            [
                static::IMAGE_WIDTH,
                static::IMAGE_HEIGHT
            ]
        );
    }

    /**
     * Unit Test Of CaptchaImage _setFilters Method
     */
    public function testSetFilters(): void
    {
        $captchaImage = new CaptchaImage();

        $setFiltersMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_setFilters'
        );

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $setFiltersMethod->setAccessible(true);
        $createImageInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $setFiltersMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
        );

        $setFiltersMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _setBrightnessAndContrast Method
     */
    public function testSetBrightnessAndContrast(): void
    {
        $captchaImage = new CaptchaImage();

        $setBrightnessAndContrastMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_setBrightnessAndContrast'
        );

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $setBrightnessAndContrastMethod->setAccessible(true);
        $createImageInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $setBrightnessAndContrastMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
        );

        $setBrightnessAndContrastMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _resizeImageForFilters Method
     */
    public function testResizeImageForFilters(): void
    {
        $captchaImage = new CaptchaImage();

        $resizeImageForFiltersMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_resizeImageForFilters'
        );

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $resizeImageForFiltersMethod->setAccessible(true);
        $createImageInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $resizeImageForFiltersMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
        );

        $resizeImageForFiltersMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _setBlurFilters Method
     */
    public function testSetBlurFilters(): void
    {
        $captchaImage = new CaptchaImage();

        $setBlurFiltersMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_setBlurFilters'
        );

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $setBlurFiltersMethod->setAccessible(true);
        $createImageInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $setBlurFiltersMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
        );

        $setBlurFiltersMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _posterizeImage Method
     */
    public function testPosterizeImage(): void
    {
        $captchaImage = new CaptchaImage();

        $posterizeImageMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_posterizeImage'
        );

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $posterizeImageMethod->setAccessible(true);
        $createImageInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $posterizeImageMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
        );

        $posterizeImageMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _setResizeFilters Method
     */
    public function testSetResizeFilters(): void
    {
        $captchaImage = new CaptchaImage();

        $setResizeFiltersMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_setResizeFilters'
        );

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $setResizeFiltersMethod->setAccessible(true);
        $createImageInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $setResizeFiltersMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
        );

        $setResizeFiltersMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _drawDots Method
     */
    public function testDrawDots(): void
    {
        $captchaImage = new CaptchaImage();

        $drawDotsMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_drawDots'
        );

        $createDrawInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createDrawInstance'
        );

        $drawDotsMethod->setAccessible(true);
        $createDrawInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $drawDotsMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawDotsMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _drawBigDots Method
     */
    public function testDrawBigDots(): void
    {
        $captchaImage = new CaptchaImage();

        $drawBigDotsMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_drawBigDots'
        );

        $createDrawInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createDrawInstance'
        );

        $drawBigDotsMethod->setAccessible(true);
        $createDrawInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $drawBigDotsMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawBigDotsMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _drawMiddleDots Method
     */
    public function testDrawMiddleDots(): void
    {
        $captchaImage = new CaptchaImage();

        $drawMiddleDotsMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_drawMiddleDots'
        );

        $createDrawInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createDrawInstance'
        );

        $drawMiddleDotsMethod->setAccessible(true);
        $createDrawInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $drawMiddleDotsMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawMiddleDotsMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _drawSmallDots Method
     */
    public function testDrawSmallDots(): void
    {
        $captchaImage = new CaptchaImage();

        $drawSmallDotsMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_drawSmallDots'
        );

        $createDrawInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createDrawInstance'
        );

        $drawSmallDotsMethod->setAccessible(true);
        $createDrawInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $drawSmallDotsMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawSmallDotsMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _drawDot Method
     */
    public function testDrawDot(): void
    {
        $captchaImage = new CaptchaImage();

        $drawDotMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_drawDot'
        );

        $createDrawInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createDrawInstance'
        );

        $drawDotMethod->setAccessible(true);
        $createDrawInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $drawDotMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $drawDotMethod->invokeArgs(
                $captchaImage,
                [static::DOT_SIZE]
            );
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawDotMethod->invokeArgs($captchaImage, [static::DOT_SIZE]);
    }

    /**
     * Unit Test Of CaptchaImage _drawLines Method
     */
    public function testDrawLines(): void
    {
        $captchaImage = new CaptchaImage();

        $drawLinesMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_drawLines'
        );

        $createDrawInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createDrawInstance'
        );

        $drawLinesMethod->setAccessible(true);
        $createDrawInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $drawLinesMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawLinesMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _drawLine Method
     */
    public function testDrawLine(): void
    {
        $captchaImage = new CaptchaImage();

        $drawLineMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_drawLine'
        );

        $createDrawInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createDrawInstance'
        );

        $drawLineMethod->setAccessible(true);
        $createDrawInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $drawLineMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $drawLineMethod->invoke($captchaImage);
    }

    /**
     * Unit Test Of CaptchaImage _writeText Method
     */
    public function testWriteText(): void
    {
        $captchaImage = new CaptchaImage();

        $writeTextMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_writeText'
        );

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $createDrawInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createDrawInstance'
        );

        $writeTextMethod->setAccessible(true);
        $createImageInstanceMethod->setAccessible(true);
        $createDrawInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $writeTextMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $exception = null;

        try {
            $writeTextMethod->invokeArgs($captchaImage, [static::TEXT]);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
        );

        $exception = null;

        try {
            $writeTextMethod->invokeArgs($captchaImage, [static::TEXT]);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createDrawInstanceMethod->invoke($captchaImage);

        $writeTextMethod->invokeArgs($captchaImage, [static::TEXT]);
    }

    /**
     * Unit Test Of CaptchaImage _createImageInstance Method
     */
    public function testCreateImageInstance(): void
    {
        $captchaImage = new CaptchaImage();

        $createImageInstanceMethod = new ReflectionMethod(
            'Core\Plugins\Captcha\Classes\CaptchaImage',
            '_createImageInstance'
        );

        $createImageInstanceMethod->setAccessible(true);

        $exception = null;

        try {
            $createImageInstanceMethod->invoke($captchaImage);
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Core\Plugins\Captcha\Exceptions\CaptchaImageException',
                $exception
            );
        }

        $this->assertNotEmpty($exception);

        $createImageInstanceMethod->invokeArgs(
            $captchaImage,
            [mb_strlen(static::TEXT)]
        );
    }

    private function _getCaptchaEntityInstance(): CaptchaEntity
    {
        $captchaSettings = new CaptchaSettings($this->getSettingsData());

        $captchaEntity = new CaptchaEntity(
            static::TEXT,
            static::HASH,
            static::IMAGE_FILE_NAME,
            $captchaSettings
        );

        return $captchaEntity;
    }
}
