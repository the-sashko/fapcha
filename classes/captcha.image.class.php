<?php
namespace Core\Plugins\Captcha\Classes;

use Core\Plugins\Captcha\Interfaces\ICaptchaImage;
use Core\Plugins\Captcha\Interfaces\ICaptchaEntity;

use Core\Plugins\Captcha\Exceptions\CaptchaImageException;

class CaptchaImage implements ICaptchaImage
{
    const OUTPUT_IMAGE_FORMAT = 'png';

    const BACKGROUND_COLOR = '#FFFFFF';

    const TEXT_COLOR   = '#997755';
    const TEXT_IDENT_X = 45;
    const TEXT_IDENT_Y = 80;

    const CHAR_ANGLE    = 25;
    const CHAR_INDENT_X = 30;
    const CHAR_INDENT_Y = 53;

    const FONT_PATH         = __DIR__.'/../res/font.ttf';
    const FONT_SIZE         = 50;
    const FONT_WEIGHT       = 900;
    const FONT_STROKE_WIDHT = 1;

    const IMAGE_WIDTH  = 500;
    const IMAGE_HEIGHT = 100;

    const IMAGE_TEMPORARY_WIDHT  = 1000;
    const IMAGE_TEMPORARY_HEIGHT = 200;

    const IMAGE_BLUR_RADIUS = 0.8;
    const IMAGE_BLUR_SIGMA  = 0.8;

    const IMAGE_SHARPEN_RADIUS = 2.0;
    const IMAGE_SHARPEN_SIGMA  = 2.0;

    const IMAGE_BRIGHTNESS = -5;
    const IMAGE_CONTRAST   = 5;

    const IMAGE_FILTER_SIZE_PROPORTION = 1.1;

    const STROKE_OPACITY = 1;
    const STROKE_WIDTH   = 1;

    const BIG_DOT_SIZE    = 2;
    const MIDDLE_DOT_SIZE = 1;
    const SMALL_DOT_SIZE  = 0;

    const COUNT_OF_BIG_DOTS    = 500;
    const COUNT_OF_MIDDLE_DOTS = 1000;
    const COUNT_OF_SMALL_DOTS  = 5000;

    const COUNT_OF_LINES  = 10;
    const COUNT_OF_COLORS = 16;

    const COUNT_OF_BLUR_FILTRES   = 3;
    const COUNT_OF_RESIZE_FILTERS = 3;

    const RESIZE_FILTER_RATIO_X = 0.5;
    const RESIZE_FILTER_RATIO_Y = 0.375;

    const RESIZE_BLUR_FACTOR = 0.5;

    const IS_POSTERIZE_DITHER = true;

    private $_image   = null;
    private $_gdImage = null;
    private $_draw    = null;

    public function create(ICaptchaEntity $captchaEntity): void
    {
        $text = $captchaEntity->getText();
        $text = mb_convert_case($text, MB_CASE_LOWER);

        try {
            $this->_createImageInstance(mb_strlen($text));
            $this->_createDrawInstance();

            $this->_writeText($text);

            $this->_drawLines();
            $this->_drawDots();

            $this->_image->drawImage($this->_draw);
            $this->_draw->destroy();

            $this->_setFilters();

            $this->_image->brightnessContrastImage(15, -15);

            $this->_resizeImage(static::IMAGE_WIDTH, static::IMAGE_HEIGHT);
        } catch (\Exception $exp) {
            $errorMessage = $exp->getMessage();

            $errorMessage = sprintf(
                '%s. Error: %s',
                CaptchaImageException::MESSAGE_IMAGE_CREATE_ERROR,
                $errorMessage
            );

            throw new CaptchaImageException(
                $errorMessage,
                CaptchaImageException::CODE_IMAGE_CREATE_ERROR
            );
        }

        $this->_saveImage($captchaEntity->getImageFilePath());
    }

    private function _saveImage(?string $imageFilePath = null): void
    {
        if (empty($imageFilePath)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_FILE_PATH_IS_NOT_SET,
                CaptchaImageException::CODE_IMAGE_FILE_PATH_IS_NOT_SET
            );
        }

        if (file_exists($imageFilePath) && is_file($imageFilePath)) {
            unlink($imageFilePath);
        }

        $this->_setImageFormat();

        $this->_gdPostProcessing();

        imagepng($this->_gdImage, $imageFilePath);
        imagedestroy($this->_gdImage);

        chmod($imageFilePath, 0775);
    }

    private function _setImageFormat(): void
    {
        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        $this->_image->setImageFormat(static::OUTPUT_IMAGE_FORMAT);
    }

    private function _gdPostProcessing(): void
    {
        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        $this->_gdImage = imagecreatefromstring($this->_image->getImageBlob());

        $this->_image->destroy();

        imagefilter($this->_gdImage, IMG_FILTER_PIXELATE, 2);
    }

    private function _resizeImage(
        ?int $width  = null,
        ?int $height = null
    ): void
    {
        if (empty($width)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_WIDTH_IS_NOT_SET,
                CaptchaImageException::CODE_IMAGE_WIDTH_IS_NOT_SET
            );
        }

        if (empty($height)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_HEIGHT_IS_NOT_SET,
                CaptchaImageException::CODE_IMAGE_HEIGHT_IS_NOT_SET
            );
        }

        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        $this->_image->resizeImage(
            $width,
            $height,
            \imagick::FILTER_BOX,
            static::RESIZE_BLUR_FACTOR
        );
    }

    private function _setFilters(): void
    {
        $this->_resizeImageForFilters();
        $this->_posterizeImage();
        $this->_setBlurFilters();
        $this->_setResizeFilters();
        $this->_setBrightnessAndContrast();
    }

    private function _setBrightnessAndContrast(): void
    {
        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        $this->_image->brightnessContrastImage(
            static::IMAGE_BRIGHTNESS,
            static::IMAGE_CONTRAST
        );
    }

    private function _resizeImageForFilters(): void
    {
        $this->_resizeImage(
            (int) (static::IMAGE_WIDTH / static::IMAGE_FILTER_SIZE_PROPORTION),
            (int) (static::IMAGE_HEIGHT / static::IMAGE_FILTER_SIZE_PROPORTION)
        );
    }

    private function _setBlurFilters(): void
    {
        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        for ($i = 0; $i < static::COUNT_OF_BLUR_FILTRES; $i++) {
            $this->_image->blurImage(
                static::IMAGE_BLUR_RADIUS,
                static::IMAGE_BLUR_SIGMA
            );

            $this->_image->sharpenImage(
                static::IMAGE_SHARPEN_RADIUS,
                static::IMAGE_SHARPEN_SIGMA
            );
        }
    }

    private function _posterizeImage(): void
    {
        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        $this->_image->posterizeImage(
            static::COUNT_OF_COLORS,
            static::IS_POSTERIZE_DITHER
        );
    }

    private function _setResizeFilters(): void
    {
        for ($i = 0; $i < static::COUNT_OF_RESIZE_FILTERS; $i++) {
            $width = static::IMAGE_TEMPORARY_WIDHT *
                     static::RESIZE_FILTER_RATIO_X;

            $height = static::IMAGE_TEMPORARY_HEIGHT *
                      static::RESIZE_FILTER_RATIO_Y;

            $this->_resizeImage((int) $width, (int) $height);

            $this->_resizeImage(
                static::IMAGE_TEMPORARY_WIDHT,
                static::IMAGE_TEMPORARY_HEIGHT
            );
        }
    }

    private function _drawDots(): void
    {
        $this->_drawBigDots();
        $this->_drawMiddleDots();
        $this->_drawSmallDots();
    }

    private function _drawBigDots(): void
    {
        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }

        for ($i = 0; $i < static::COUNT_OF_BIG_DOTS; $i++) {
            $this->_draw->setStrokeColor(static::TEXT_COLOR);
            $this->_draw->setFillColor(static::TEXT_COLOR);

            $this->_drawDot(static::BIG_DOT_SIZE);

            $this->_draw->setStrokeColor(static::BACKGROUND_COLOR);
            $this->_draw->setFillColor(static::BACKGROUND_COLOR);

            $this->_drawDot(static::BIG_DOT_SIZE);
        }
    }

    private function _drawMiddleDots(): void
    {
        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }

        for ($i = 0; $i < static::COUNT_OF_MIDDLE_DOTS; $i++) {

            $this->_draw->setStrokeColor(static::TEXT_COLOR);
            $this->_draw->setFillColor(static::TEXT_COLOR);

            $this->_drawDot(static::MIDDLE_DOT_SIZE);

            $this->_draw->setStrokeColor(static::BACKGROUND_COLOR);
            $this->_draw->setFillColor(static::BACKGROUND_COLOR);

            $this->_drawDot(static::MIDDLE_DOT_SIZE);
        }
    }

    private function _drawSmallDots(): void
    {
        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }

        for ($i = 0; $i < static::COUNT_OF_SMALL_DOTS; $i++) {

            $this->_draw->setStrokeColor(static::TEXT_COLOR);
            $this->_draw->setFillColor(static::TEXT_COLOR);

            $this->_drawDot(static::SMALL_DOT_SIZE);

            $this->_draw->setStrokeColor(static::BACKGROUND_COLOR);
            $this->_draw->setFillColor(static::BACKGROUND_COLOR);

            $this->_drawDot(static::SMALL_DOT_SIZE);
        }
    }

    private function _drawDot(int $dotSize = -1): bool
    {
        if ($dotSize < 0) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DOT_SIZE_HAS_BAD_FORMAT,
                CaptchaImageException::CODE_IMAGE_DOT_SIZE_HAS_BAD_FORMAT
            );
        }

        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }

        $positionX = rand(0, static::IMAGE_TEMPORARY_WIDHT);
        $positionY = rand(0, static::IMAGE_TEMPORARY_HEIGHT);

        if ($dotSize > 0) {
            $this->_draw->rectangle(
                $positionX,
                $positionY,
                $positionX + $dotSize,
                $positionY + $dotSize
            );

            return true;
        }

        $this->_draw->point($positionX, $positionY);
        return true;
    }

    private function _drawLines(): void
    {
        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }

        for ($i = 0; $i < static::COUNT_OF_LINES; $i++) {
            $this->_draw->setStrokeColor(static::TEXT_COLOR);
            $this->_draw->setFillColor(static::TEXT_COLOR);

            $this->_drawLine();

            $this->_draw->setStrokeColor(static::BACKGROUND_COLOR);
            $this->_draw->setFillColor(static::BACKGROUND_COLOR);

            $this->_drawLine();
        }
    }

    private function _drawLine(): void
    {
        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }

        $positionY = [
            rand(0, static::IMAGE_TEMPORARY_WIDHT),
            rand(0, static::IMAGE_TEMPORARY_HEIGHT)
        ];

        $positionX = [
            rand(0, static::IMAGE_TEMPORARY_HEIGHT),
            rand(0, static::IMAGE_TEMPORARY_WIDHT)
        ];

        $this->_draw->line(
            array_shift($positionX),
            array_shift($positionY),
            array_shift($positionX),
            array_shift($positionY)
        );
    }

    private function _writeText(?string $text = null): void
    {
        if (empty($text)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_TEXT_IS_NOT_SET,
                CaptchaImageException::CODE_IMAGE_TEXT_IS_NOT_SET
            );
        }

        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }

        if (empty($text)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_TEXT_IS_EMPTY,
                CaptchaImageException::CODE_IMAGE_TEXT_IS_EMPTY
            );
        }

        for (
            $charPosition = 0;
            $charPosition <= mb_strlen($text) - 1;
            $charPosition++
        ) {
            $char = mb_substr($text, $charPosition, 1);
            $char = $char != ' ' ? $char : '';

            $angle = rand(-1 * static::CHAR_ANGLE, static::CHAR_ANGLE);
            $this->_image->annotateImage(
                $this->_draw,
                static::CHAR_INDENT_X + static::TEXT_IDENT_X * $charPosition,
                static::CHAR_INDENT_Y,
                $angle,
                $char
            );
        }

        $this->_image->resizeImage(
            static::IMAGE_TEMPORARY_WIDHT,
            static::IMAGE_TEMPORARY_HEIGHT,
            \imagick::FILTER_BOX,
            static::RESIZE_BLUR_FACTOR
        );
    }

    private function _createImageInstance(?int $textLength = null): void
    {
        if (empty($textLength)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_TEXT_LENGTH_IS_NOT_SET,
                CaptchaImageException::CODE_IMAGE_TEXT_LENGTH_IS_NOT_SET
            );
        }

        $this->_image = new \Imagick();

        $this->_image->newImage(
            static::TEXT_IDENT_X + static::TEXT_IDENT_X * $textLength,
            static::TEXT_IDENT_Y,
            new \ImagickPixel(static::BACKGROUND_COLOR)
        );
    }

    private function _createDrawInstance(): void
    {
        $this->_draw = new \ImagickDraw();

        $this->_draw->setFillColor(static::TEXT_COLOR);
        $this->_draw->setFont(static::FONT_PATH);
        $this->_draw->setFontSize(static::FONT_SIZE);
        $this->_draw->setFontWeight(static::FONT_WEIGHT);
        $this->_draw->setStrokeColor(static::TEXT_COLOR);
        $this->_draw->setStrokeWidth(static::FONT_STROKE_WIDHT);
        $this->_draw->setStrokeOpacity(static::STROKE_OPACITY);
        $this->_draw->setStrokeWidth(static::STROKE_WIDTH);
    }
}
