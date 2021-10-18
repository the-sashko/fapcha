<?php

namespace Sonder\Plugins\Captcha\Classes;

use Sonder\Plugins\Captcha\Exceptions\CaptchaEntityException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaException;
use Sonder\Plugins\Captcha\Interfaces\ICaptchaEntity;
use Sonder\Plugins\Captcha\Interfaces\ICaptchaSettings;

final class CaptchaEntity implements ICaptchaEntity
{
    /**
     * @var string|null
     */
    private ?string $_text = null;

    /**
     * @var string|null
     */
    private ?string $_hash = null;

    /**
     * @var string|null
     */
    private ?string $_imageFilePath = null;

    /**
     * @var string|null
     */
    private ?string $_imageURLPath = null;

    /**
     * @param string $text
     * @param string $hash
     * @param string $imageFileName
     * @param ICaptchaSettings $captchaSettings
     *
     * @throws CaptchaEntityException
     */
    final public function __construct(
        string           $text,
        string           $hash,
        string           $imageFileName,
        ICaptchaSettings $captchaSettings
    )
    {
        $this->_setText($text);
        $this->_setHash($hash);
        $this->_setImagePath($imageFileName, $captchaSettings);
    }

    /**
     * @return string
     *
     * @throws CaptchaEntityException
     */
    final public function getText(): string
    {
        if (empty($this->_text)) {
            throw new CaptchaEntityException(
                CaptchaEntityException::MESSAGE_ENTITY_TEXT_IS_EMPTY,
                CaptchaException::CODE_ENTITY_TEXT_IS_EMPTY
            );
        }

        return $this->_text;
    }

    /**
     * @return string
     *
     * @throws CaptchaEntityException
     */
    final public function getHash(): string
    {
        if (empty($this->_hash)) {
            throw new CaptchaEntityException(
                CaptchaEntityException::MESSAGE_ENTITY_HASH_IS_EMPTY,
                CaptchaException::CODE_ENTITY_HASH_IS_EMPTY
            );
        }

        return $this->_hash;
    }

    /**
     * @return string
     *
     * @throws CaptchaEntityException
     */
    final public function getImageFilePath(): string
    {
        if (empty($this->_imageFilePath)) {

            throw new CaptchaEntityException(
                CaptchaEntityException::MESSAGE_ENTITY_FILE_PATH_IS_EMPTY,
                CaptchaException::CODE_ENTITY_FILE_PATH_IS_EMPTY
            );
        }

        return $this->_imageFilePath;
    }

    /**
     * @return string
     *
     * @throws CaptchaEntityException
     */
    final public function getImageUrlPath(): string
    {
        if (empty($this->_imageURLPath)) {
            throw new CaptchaEntityException(
                CaptchaEntityException::MESSAGE_ENTITY_URL_PATH_IS_EMPTY,
                CaptchaException::CODE_ENTITY_URL_PATH_IS_EMPTY
            );
        }

        return $this->_imageURLPath;
    }

    /**
     * @param string $text
     */
    private function _setText(string $text): void
    {
        $this->_text = $text;
    }

    /**
     * @param string $hash
     */
    private function _setHash(string $hash): void
    {
        $this->_hash = $hash;
    }

    /**
     * @param string $imageFileName
     * @param ICaptchaSettings $captchaSettings
     *
     * @throws CaptchaEntityException
     */
    private function _setImagePath(
        string           $imageFileName,
        ICaptchaSettings $captchaSettings
    ): void
    {
        $imageDirectoryPath = $this->_createImageDirectory($captchaSettings);

        $this->_imageFilePath = sprintf(
            '%s/img/%s/%s',
            $captchaSettings->getDataDirPath(),
            $imageDirectoryPath,
            $imageFileName
        );

        $imageUrlTemplate = $captchaSettings->getImageUrlTemplate();

        $this->_setImageUrlPath(
            $imageFileName,
            $imageDirectoryPath,
            $imageUrlTemplate
        );
    }

    /**
     * @param ICaptchaSettings $captchaSettings
     *
     * @return string
     *
     * @throws CaptchaEntityException
     */
    private function _createImageDirectory(
        ICaptchaSettings $captchaSettings
    ): string
    {
        $directoryPath = sprintf(
            '%s/%s/%s/%s/%s/%s',
            date('Y'),
            date('m'),
            date('d'),
            date('H'),
            date('i'),
            date('s')
        );

        $directoryFullPath = sprintf(
            '%s/img/%s',
            $captchaSettings->getDataDirPath(),
            $directoryPath
        );

        if (!file_exists($directoryFullPath) || !is_dir($directoryFullPath)) {
            mkdir($directoryFullPath, 0775, true);
        }

        if (!file_exists($directoryFullPath) || !is_dir($directoryFullPath)) {
            throw new CaptchaEntityException(
                CaptchaEntityException::MESSAGE_ENTITY_CAN_NOT_CREATE_DIR,
                CaptchaException::CODE_ENTITY_CAN_NOT_CREATE_DIR
            );
        }

        return $directoryPath;
    }

    /**
     * @param string $imageFilePath
     * @param string $imageDirectoryPath
     * @param string $imageUrlTemplate
     */
    private function _setImageUrlPath(
        string $imageFilePath,
        string $imageDirectoryPath,
        string $imageUrlTemplate
    ): void
    {
        $this->_imageURLPath = sprintf(
            '%s%s/%s',
            $imageUrlTemplate,
            $imageDirectoryPath,
            $imageFilePath
        );
    }
}
