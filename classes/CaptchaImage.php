<?php

namespace Sonder\Plugins\Captcha\Classes;

use GdImage;
use Imagick;
use ImagickDraw;
use ImagickDrawException;
use ImagickException;
use ImagickPixel;
use Sonder\Plugins\Captcha\Exceptions\CaptchaException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaImageException;
use Sonder\Plugins\Captcha\Interfaces\ICaptchaEntity;
use Sonder\Plugins\Captcha\Interfaces\ICaptchaImage;
use Throwable;

final class CaptchaImage implements ICaptchaImage
{
    const OUTPUT_IMAGE_FORMAT = 'png';

    const BACKGROUND_COLOR = '#FFFFFF';

    const TEXT_COLOR = '#997755';
    const TEXT_IDENT_X = 45;
    const TEXT_IDENT_Y = 80;

    const CHAR_ANGLE = 25;
    const CHAR_INDENT_X = 30;
    const CHAR_INDENT_Y = 53;

    const FONT_PATH = __DIR__ . '/../res/font.ttf';
    const FONT_SIZE = 50;
    const FONT_WEIGHT = 900;
    const FONT_STROKE_WIDTH = 1;

    const IMAGE_WIDTH = 500;
    const IMAGE_HEIGHT = 100;

    const IMAGE_TEMPORARY_WIDTH = 1000;
    const IMAGE_TEMPORARY_HEIGHT = 200;

    const IMAGE_BLUR_RADIUS = 0.8;
    const IMAGE_BLUR_SIGMA = 0.8;

    const IMAGE_SHARPEN_RADIUS = 2.0;
    const IMAGE_SHARPEN_SIGMA = 2.0;

    const IMAGE_BRIGHTNESS = -5;
    const IMAGE_CONTRAST = 5;

    const IMAGE_FILTER_SIZE_PROPORTION = 1.1;

    const STROKE_OPACITY = 1;
    const STROKE_WIDTH = 1;

    const BIG_DOT_SIZE = 2;
    const MIDDLE_DOT_SIZE = 1;
    const SMALL_DOT_SIZE = 0;

    const COUNT_OF_BIG_DOTS = 500;
    const COUNT_OF_MIDDLE_DOTS = 1000;
    const COUNT_OF_SMALL_DOTS = 5000;

    const COUNT_OF_LINES = 10;
    const COUNT_OF_COLORS = 16;

    const COUNT_OF_BLUR_FILTERS = 3;
    const COUNT_OF_RESIZE_FILTERS = 3;

    const RESIZE_FILTER_RATIO_X = 0.5;
    const RESIZE_FILTER_RATIO_Y = 0.375;

    const RESIZE_BLUR_FACTOR = 0.5;

    const IS_POSTERIZE_DITHER = true;

    /**
     * @var Imagick
     */
    private Imagick $_image;

    /**
     * @var GdImage|bool|null
     */
    private GdImage|bool|null $_gdImage = null;

    /**
     * @var ImagickDraw|null
     */
    private ?ImagickDraw $_draw = null;

    /**
     * @var ImagickPixel
     */
    private ImagickPixel $_textColor;

    /**
     * @var ImagickPixel
     */
    private ImagickPixel $_backgroundColor;

    final public function __construct()
    {
        $this->_textColor = new ImagickPixel(CaptchaImage::TEXT_COLOR);

        $this->_backgroundColor = new ImagickPixel(
            CaptchaImage::BACKGROUND_COLOR
        );
    }

    /**
     * @param ICaptchaEntity $captchaEntity
     *
     * @throws CaptchaImageException
     * @throws ImagickException
     */
    final public function create(ICaptchaEntity $captchaEntity): void
    {
        $text = $captchaEntity->getText();
        $text = mb_convert_case($text, MB_CASE_LOWER);

        try {
            $this->_createImageInstance(mb_strlen($text));
            $this->_createDrawInstance();

            $this->_writeText($text);

            $this->_drawLines();

            $this->_drawBigDots();
            $this->_drawSmallDots();
            $this->_drawMiddleDots();

            $this->_image->drawImage($this->_draw);
            $this->_draw->destroy();

            $this->_setFilters();

            $this->_image->brightnessContrastImage(15, -15);

            $this->_resizeImage(
                CaptchaImage::IMAGE_WIDTH,
                CaptchaImage::IMAGE_HEIGHT
            );
        } catch (Throwable $thr) {
            $errorMessage = $thr->getMessage();

            $errorMessage = sprintf(
                '%s. Error: %s',
                CaptchaImageException::MESSAGE_IMAGE_CREATE_ERROR,
                $errorMessage
            );

            throw new CaptchaImageException(
                $errorMessage,
                CaptchaException::CODE_IMAGE_CREATE_ERROR
            );
        }

        $this->_saveImage($captchaEntity->getImageFilePath());
    }

    /**
     * @param string|null $imageFilePath
     *
     * @throws CaptchaImageException
     * @throws ImagickException
     */
    private function _saveImage(?string $imageFilePath = null): void
    {
        if (empty($imageFilePath)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_FILE_PATH_IS_NOT_SET,
                CaptchaException::CODE_IMAGE_FILE_PATH_IS_NOT_SET
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

    /**
     * @throws CaptchaImageException
     *
     * @throws ImagickException
     */
    private function _setImageFormat(): void
    {
        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        $this->_image->setImageFormat(CaptchaImage::OUTPUT_IMAGE_FORMAT);
    }

    /**
     * @throws CaptchaImageException
     *
     * @throws ImagickException
     */
    private function _gdPostProcessing(): void
    {
        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        $this->_gdImage = imagecreatefromstring($this->_image->getImageBlob());

        $this->_image->destroy();

        imagefilter($this->_gdImage, IMG_FILTER_PIXELATE, 2);
    }

    /**
     * @param int|null $width
     * @param int|null $height
     *
     * @throws CaptchaImageException
     * @throws ImagickException
     */
    private function _resizeImage(
        ?int $width = null,
        ?int $height = null
    ): void
    {
        if (empty($width)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_WIDTH_IS_NOT_SET,
                CaptchaException::CODE_IMAGE_WIDTH_IS_NOT_SET
            );
        }

        if (empty($height)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_HEIGHT_IS_NOT_SET,
                CaptchaException::CODE_IMAGE_HEIGHT_IS_NOT_SET
            );
        }

        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        $this->_image->resizeImage(
            $width,
            $height,
            imagick::FILTER_BOX,
            CaptchaImage::RESIZE_BLUR_FACTOR
        );
    }

    /**
     * @throws CaptchaImageException
     * @throws ImagickException
     */
    private function _setFilters(): void
    {
        $this->_resizeImageForFilters();
        $this->_posterizeImage();
        $this->_setBlurFilters();
        $this->_setResizeFilters();
        $this->_setBrightnessAndContrast();
    }

    /**
     * @throws CaptchaImageException
     *
     * @throws ImagickException
     */
    private function _setBrightnessAndContrast(): void
    {
        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        $this->_image->brightnessContrastImage(
            CaptchaImage::IMAGE_BRIGHTNESS,
            CaptchaImage::IMAGE_CONTRAST
        );
    }

    /**
     * @throws CaptchaImageException
     *
     * @throws ImagickException
     */
    private function _resizeImageForFilters(): void
    {
        $width = CaptchaImage::IMAGE_WIDTH;
        $width = $width / CaptchaImage::IMAGE_FILTER_SIZE_PROPORTION;

        $height = CaptchaImage::IMAGE_HEIGHT;
        $height = $height / CaptchaImage::IMAGE_FILTER_SIZE_PROPORTION;

        $this->_resizeImage((int)$width, (int)$height);
    }

    /**
     * @throws CaptchaImageException
     *
     * @throws ImagickException
     */
    private function _setBlurFilters(): void
    {
        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        for ($i = 0; $i < CaptchaImage::COUNT_OF_BLUR_FILTERS; $i++) {
            $this->_image->blurImage(
                CaptchaImage::IMAGE_BLUR_RADIUS,
                CaptchaImage::IMAGE_BLUR_SIGMA
            );

            $this->_image->sharpenImage(
                CaptchaImage::IMAGE_SHARPEN_RADIUS,
                CaptchaImage::IMAGE_SHARPEN_SIGMA
            );
        }
    }

    /**
     * @throws CaptchaImageException
     *
     * @throws ImagickException
     */
    private function _posterizeImage(): void
    {
        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        $this->_image->posterizeImage(
            CaptchaImage::COUNT_OF_COLORS,
            CaptchaImage::IS_POSTERIZE_DITHER
        );
    }

    /**
     * @throws CaptchaImageException
     *
     * @throws ImagickException
     */
    private function _setResizeFilters(): void
    {
        for ($i = 0; $i < CaptchaImage::COUNT_OF_RESIZE_FILTERS; $i++) {
            $width = CaptchaImage::IMAGE_TEMPORARY_WIDTH *
                CaptchaImage::RESIZE_FILTER_RATIO_X;

            $height = CaptchaImage::IMAGE_TEMPORARY_HEIGHT *
                CaptchaImage::RESIZE_FILTER_RATIO_Y;

            $this->_resizeImage((int)$width, (int)$height);

            $this->_resizeImage(
                CaptchaImage::IMAGE_TEMPORARY_WIDTH,
                CaptchaImage::IMAGE_TEMPORARY_HEIGHT
            );
        }
    }

    /**
     * @param int $count
     * @param int $size
     *
     * @throws CaptchaImageException
     * @throws ImagickDrawException
     */
    private function _drawDots(int $count, int $size): void
    {
        $this->_checkDrewObject();

        for ($i = 0; $i < $count; $i++) {
            $this->_draw->setStrokeColor($this->_textColor);
            $this->_draw->setFillColor($this->_textColor);

            $this->_drawDot($size);

            $this->_draw->setStrokeColor($this->_backgroundColor);
            $this->_draw->setFillColor($this->_backgroundColor);

            $this->_drawDot($size);
        }
    }

    /**
     * @throws CaptchaImageException
     * @throws ImagickDrawException
     */
    private function _drawBigDots(): void
    {
        $this->_drawDots(
            CaptchaImage::COUNT_OF_BIG_DOTS,
            CaptchaImage::BIG_DOT_SIZE
        );
    }

    /**
     * @throws CaptchaImageException
     * @throws ImagickDrawException
     */
    private function _drawMiddleDots(): void
    {
        $this->_drawDots(
            CaptchaImage::COUNT_OF_MIDDLE_DOTS,
            CaptchaImage::MIDDLE_DOT_SIZE
        );
    }

    /**
     * @throws CaptchaImageException
     * @throws ImagickDrawException
     */
    private function _drawSmallDots(): void
    {
        $this->_drawDots(
            CaptchaImage::COUNT_OF_SMALL_DOTS,
            CaptchaImage::SMALL_DOT_SIZE
        );
    }

    /**
     * @param int $dotSize
     *
     * @throws CaptchaImageException
     */
    private function _drawDot(int $dotSize = -1): void
    {
        if ($dotSize < 0) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DOT_SIZE_HAS_BAD_FORMAT,
                CaptchaException::CODE_IMAGE_DOT_SIZE_HAS_BAD_FORMAT
            );
        }

        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }

        $positionX = rand(0, CaptchaImage::IMAGE_TEMPORARY_WIDTH);
        $positionY = rand(0, CaptchaImage::IMAGE_TEMPORARY_HEIGHT);

        if ($dotSize > 0) {
            $this->_draw->rectangle(
                $positionX,
                $positionY,
                $positionX + $dotSize,
                $positionY + $dotSize
            );
        }

        if ($dotSize < 1) {
            $this->_draw->point($positionX, $positionY);
        }
    }

    /**
     * @throws CaptchaImageException
     * @throws ImagickDrawException
     */
    private function _drawLines(): void
    {
        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }

        for ($i = 0; $i < CaptchaImage::COUNT_OF_LINES; $i++) {
            $this->_draw->setStrokeColor($this->_textColor);
            $this->_draw->setFillColor($this->_textColor);

            $this->_drawLine();

            $this->_draw->setStrokeColor($this->_backgroundColor);
            $this->_draw->setFillColor($this->_backgroundColor);

            $this->_drawLine();
        }
    }

    /**
     * @throws CaptchaImageException
     */
    private function _drawLine(): void
    {
        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }

        $positionY = [
            rand(0, CaptchaImage::IMAGE_TEMPORARY_WIDTH),
            rand(0, CaptchaImage::IMAGE_TEMPORARY_HEIGHT)
        ];

        $positionX = [
            rand(0, CaptchaImage::IMAGE_TEMPORARY_HEIGHT),
            rand(0, CaptchaImage::IMAGE_TEMPORARY_WIDTH)
        ];

        $this->_draw->line(
            array_shift($positionX),
            array_shift($positionY),
            array_shift($positionX),
            array_shift($positionY)
        );
    }

    /**
     * @param string|null $text
     *
     * @throws CaptchaImageException
     * @throws ImagickException
     */
    private function _writeText(?string $text = null): void
    {
        if (empty($text)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_TEXT_IS_NOT_SET,
                CaptchaException::CODE_IMAGE_TEXT_IS_NOT_SET
            );
        }

        if (empty($this->_image)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_IMAGICK_INSTANCE_IS_EMPTY
            );
        }

        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }

        for (
            $charPosition = 0;
            $charPosition <= mb_strlen($text) - 1;
            $charPosition++
        ) {
            $char = mb_substr($text, $charPosition, 1);
            $char = $char != ' ' ? $char : '';

            $charAngle = CaptchaImage::CHAR_ANGLE;

            $charIndentX = CaptchaImage::CHAR_INDENT_X;
            $charIndentY = CaptchaImage::CHAR_INDENT_Y;

            $angle = rand(-1 * $charAngle, $charAngle);

            $this->_image->annotateImage(
                $this->_draw,
                $charIndentX + $charIndentX * $charPosition,
                $charIndentY,
                $angle,
                $char
            );
        }

        $this->_image->resizeImage(
            CaptchaImage::IMAGE_TEMPORARY_WIDTH,
            CaptchaImage::IMAGE_TEMPORARY_HEIGHT,
            imagick::FILTER_BOX,
            CaptchaImage::RESIZE_BLUR_FACTOR
        );
    }

    /**
     * @param int|null $textLength
     *
     * @throws CaptchaImageException
     * @throws ImagickException
     */
    private function _createImageInstance(?int $textLength = null): void
    {
        if (empty($textLength)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_TEXT_LENGTH_IS_NOT_SET,
                CaptchaException::CODE_IMAGE_TEXT_LENGTH_IS_NOT_SET
            );
        }

        $this->_image = new Imagick();

        $textIndentX = CaptchaImage::TEXT_IDENT_X;
        $textIndentY = CaptchaImage::TEXT_IDENT_Y;

        $this->_image->newImage(
            $textIndentX + $textIndentX * $textLength,
            $textIndentY,
            new ImagickPixel(CaptchaImage::BACKGROUND_COLOR)
        );
    }

    /**
     * @throws ImagickDrawException
     * @throws ImagickException
     */
    private function _createDrawInstance(): void
    {
        $this->_draw = new ImagickDraw();

        $this->_draw->setFillColor($this->_textColor);
        $this->_draw->setFont(CaptchaImage::FONT_PATH);
        $this->_draw->setFontSize(CaptchaImage::FONT_SIZE);
        $this->_draw->setFontWeight(CaptchaImage::FONT_WEIGHT);
        $this->_draw->setStrokeColor($this->_textColor);
        $this->_draw->setStrokeWidth(CaptchaImage::FONT_STROKE_WIDTH);
        $this->_draw->setStrokeOpacity(CaptchaImage::STROKE_OPACITY);
        $this->_draw->setStrokeWidth(CaptchaImage::STROKE_WIDTH);
    }

    /**
     * @throws CaptchaImageException
     */
    private function _checkDrewObject(): void
    {
        if (empty($this->_draw)) {
            throw new CaptchaImageException(
                CaptchaImageException::MESSAGE_IMAGE_DRAW_INSTANCE_IS_EMPTY,
                CaptchaException::CODE_IMAGE_DRAW_INSTANCE_IS_EMPTY
            );
        }
    }
}
