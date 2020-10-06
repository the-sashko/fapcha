<?php
namespace Core\Plugins\Captcha\Classes;

use Core\Plugins\Captcha\Interfaces\ICaptchaEntity;
use Core\Plugins\Captcha\Interfaces\ICaptchaSettings;

use Core\Plugins\Captcha\Exceptions\CaptchaEntityException;

class CaptchaEntity implements ICaptchaEntity
{
    private $_text = null;

    private $_hash = null;

    private $_imageFilePath = null;

    private $_imageURLPath = null;

    public function __construct(
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

    public function getText(): string
    {
        if (empty($this->_text)) {
            throw new CaptchaEntityException(
                CaptchaEntityException::MESSAGE_ENTITY_TEXT_IS_EMPTY,
                CaptchaEntityException::CODE_ENTITY_TEXT_IS_EMPTY
            );
        }

        return $this->_text;
    }

    public function getHash(): string
    {
        if (empty($this->_hash)) {
            throw new CaptchaEntityException(
                CaptchaEntityException::MESSAGE_ENTITY_HASH_IS_EMPTY,
                CaptchaEntityException::CODE_ENTITY_HASH_IS_EMPTY
            );
        }

        return $this->_hash;
    }

    public function getImageFilePath(): string
    {
        if (empty($this->_imageFilePath)) {

            throw new CaptchaEntityException(
                CaptchaEntityException::MESSAGE_ENTITY_FILE_PATH_IS_EMPTY,
                CaptchaEntityException::CODE_ENTITY_FILE_PATH_IS_EMPTY
            );
        }

        return $this->_imageFilePath;
    }

    public function getImageUrlPath(): string
    {
        if (empty($this->_imageURLPath)) {
            throw new CaptchaEntityException(
                CaptchaEntityException::MESSAGE_ENTITY_URL_PATH_IS_EMPTY,
                CaptchaEntityException::CODE_ENTITY_URL_PATH_IS_EMPTY
            );
        }

        return $this->_imageURLPath;
    }

    private function _setText(string $text): void
    {
        $this->_text = $text;
    }

    private function _setHash(string $hash): void
    {
        $this->_hash = $hash;
    }

    private function _setImagePath(
        string           $imageFileName,
        ICaptchaSettings $captchaSettings
    ): void
    {
        $imageDirectoryPath = $this->_createImageDerictory($captchaSettings);

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

    private function _createImageDerictory(
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
                CaptchaEntityException::CODE_ENTITY_CAN_NOT_CREATE_DIR
            );
        }

        return $directoryPath;
    }

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
