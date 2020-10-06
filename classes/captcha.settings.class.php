<?php
namespace Core\Plugins\Captcha\Classes;

use Core\Plugins\Captcha\Interfaces\ICaptchaSettings;

use Core\Plugins\Captcha\Exceptions\CaptchaSettingsException;

class CaptchaSettings implements ICaptchaSettings
{
    const DEFAULT_LANGUAGE = 'en';

    const DEFAULT_DATA_DIR_PATH = __DIR__.'/../../../../res/captcha';

    const DEFAULT_IMAGE_URL_TEMPLATE = '/captcha/img/';

    const HASH_SALT_ARRAY_KEY = 'hash_salt';

    const DATA_DIR_PATH_ARRAY_KEY = 'data_dir_path';

    const IMAGE_URL_TEMPLATE_ARRAY_KEY = 'image_url_template';

    const LANGUAGE_ARRAY_KEY = 'language';

    private $_hashSalt = null;

    private $_dataDirPath = null;

    private $_imageUrlTemplate = null;

    private $_language = null;

    public function __construct(?array $settingsData = null)
    {
        $this->_mapSettingsData($settingsData);
        unset($settingsData);
    }

    public function getHashSalt(): string
    {
        if (empty($this->_hashSalt)) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_SALT_IS_EMPTY,
                CaptchaSettingsException::CODE_SETTINGS_SALT_IS_EMPTY
            );
        }

        return $this->_hashSalt;
    }

    public function getDataDirPath(): string
    {
        if (empty($this->_dataDirPath)) {
            return static::DEFAULT_DATA_DIR_PATH;
        }

        return $this->_dataDirPath;
    }

    public function getImageUrlTemplate(): string
    {
        if (empty($this->_imageUrlTemplate)) {
            return static::DEFAULT_IMAGE_URL_TEMPLATE;
        }

        return $this->_imageUrlTemplate;
    }

    public function getLanguage(): string
    {
        if (empty($this->_language)) {
            return static::DEFAULT_LANGUAGE;
        }

        return $this->_language;
    }

    private function _mapSettingsData(?array $settingsData = null): void
    {
        if (empty($settingsData)) {
            $settingsData = [];
        }

        $this->_checkSettingsData($settingsData);

        if (
            !array_key_exists(static::DATA_DIR_PATH_ARRAY_KEY, $settingsData)
        ) {
            $settingsData[static::DATA_DIR_PATH_ARRAY_KEY] = null;
        }

        if (
            !array_key_exists(
                static::IMAGE_URL_TEMPLATE_ARRAY_KEY, $settingsData
            )
        ) {
            $settingsData[static::IMAGE_URL_TEMPLATE_ARRAY_KEY] = null;
        }

        if (!array_key_exists(static::LANGUAGE_ARRAY_KEY, $settingsData)) {
            $settingsData[static::LANGUAGE_ARRAY_KEY] = null;
        }

        $this->_setHashSalt($settingsData[static::HASH_SALT_ARRAY_KEY]);
        $this->_setDataDirPath($settingsData[static::DATA_DIR_PATH_ARRAY_KEY]);

        $this->_setImageUrlTemplate(
            $settingsData[static::IMAGE_URL_TEMPLATE_ARRAY_KEY]
        );

        $this->_setLanguage($settingsData[static::LANGUAGE_ARRAY_KEY]);
    }

    private function _checkSettingsData(?array $settingsData = null): void
    {
        if (empty($settingsData)) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_DATA_IS_EMPTY,
                CaptchaSettingsException::CODE_SETTINGS_DATA_IS_EMPTY
            );
        }

        if (!array_key_exists(static::HASH_SALT_ARRAY_KEY, $settingsData)) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_SALT_IS_NOT_SET,
                CaptchaSettingsException::CODE_SETTINGS_SALT_IS_NOT_SET
            );
        }

        if (empty($settingsData[static::HASH_SALT_ARRAY_KEY])) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_SALT_IS_EMPTY,
                CaptchaSettingsException::CODE_SETTINGS_SALT_IS_EMPTY
            );
        }
    }

    private function _setHashSalt(?string $hashSalt = null): void
    {
        if (empty($hashSalt)) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_SALT_IS_EMPTY,
                CaptchaSettingsException::CODE_SETTINGS_SALT_IS_EMPTY
            );
        }

        $this->_hashSalt = $hashSalt;
    }

    private function _setDataDirPath(?string $dataDirPath = null): void
    {
        if (empty($dataDirPath)) {
            $dataDirPath = static::DEFAULT_DATA_DIR_PATH;
        }

        if (!file_exists($dataDirPath) || !is_dir($dataDirPath)) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_DATA_DIR_NOT_FOUND,
                CaptchaSettingsException::CODE_SETTINGS_DATA_DIR_NOT_FOUND
            );
        }

        $this->_dataDirPath = $dataDirPath;
    }

    private function _setImageUrlTemplate(
        ?string $imageUrlTemplate = null
    ): void
    {
        if (empty($imageUrlTemplate)) {
            $imageUrlTemplate = static::DEFAULT_IMAGE_URL_TEMPLATE;
        }

        $this->_imageUrlTemplate = $imageUrlTemplate;
    }

    private function _setLanguage(?string $language = null): void
    {
        if (empty($language)) {
            $language = static::DEFAULT_LANGUAGE;
        }

        $this->_language = $language;
    }
}
