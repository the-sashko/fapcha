<?php

namespace Sonder\Plugins\Captcha\Classes;

use Sonder\Plugins\Captcha\Exceptions\CaptchaException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException;
use Sonder\Plugins\Captcha\Interfaces\ICaptchaSettings;

final class CaptchaSettings implements ICaptchaSettings
{
    const DEFAULT_LANGUAGE = 'en';

    const DEFAULT_DATA_DIR_PATH = __DIR__ . '/../../../../captcha';

    const DEFAULT_IMAGE_URL_TEMPLATE = '/captcha/img/';

    const HASH_SALT_ARRAY_KEY = 'hash_salt';

    const DATA_DIR_PATH_ARRAY_KEY = 'data_dir_path';

    const IMAGE_URL_TEMPLATE_ARRAY_KEY = 'image_url_template';

    const LANGUAGE_ARRAY_KEY = 'language';

    /**
     * @var string|null
     */
    private ?string $_hashSalt = null;

    /**
     * @var string|null
     */
    private ?string $_dataDirPath = null;

    /**
     * @var string|null
     */
    private ?string $_imageUrlTemplate = null;

    /**
     * @var string|null
     */
    private ?string $_language = null;

    /**
     * @param array|null $settingsData
     *
     * @throws CaptchaSettingsException
     */
    final public function __construct(?array $settingsData = null)
    {
        $this->_mapSettingsData($settingsData);
        unset($settingsData);
    }

    /**
     * @return string
     *
     * @throws CaptchaSettingsException
     */
    final public function getHashSalt(): string
    {
        if (empty($this->_hashSalt)) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_SALT_IS_EMPTY,
                CaptchaException::CODE_SETTINGS_SALT_IS_EMPTY
            );
        }

        return $this->_hashSalt;
    }

    /**
     * @return string
     */
    final public function getDataDirPath(): string
    {
        if (!empty($this->_dataDirPath)) {
            return $this->_dataDirPath;
        }

        if (defined('APP_PROTECTED_DIR_PATH')) {
            return sprintf('%s/captcha', APP_PROTECTED_DIR_PATH);
        }

        return CaptchaSettings::DEFAULT_DATA_DIR_PATH;
    }

    /**
     * @return string
     */
    final public function getImageUrlTemplate(): string
    {
        if (empty($this->_imageUrlTemplate)) {
            return CaptchaSettings::DEFAULT_IMAGE_URL_TEMPLATE;
        }

        return $this->_imageUrlTemplate;
    }

    /**
     * @return string
     */
    final public function getLanguage(): string
    {
        if (empty($this->_language)) {
            return CaptchaSettings::DEFAULT_LANGUAGE;
        }

        return $this->_language;
    }

    /**
     * @param array|null $settingsData
     *
     * @throws CaptchaSettingsException
     */
    private function _mapSettingsData(?array $settingsData = null): void
    {
        if (empty($settingsData)) {
            $settingsData = [];
        }

        $this->_checkSettingsData($settingsData);

        if (
            !array_key_exists(
                CaptchaSettings::DATA_DIR_PATH_ARRAY_KEY,
                $settingsData
            )
        ) {
            $settingsData[CaptchaSettings::DATA_DIR_PATH_ARRAY_KEY] = null;
        }

        if (
            !array_key_exists(
                CaptchaSettings::IMAGE_URL_TEMPLATE_ARRAY_KEY,
                $settingsData
            )
        ) {
            $settingsData[CaptchaSettings::IMAGE_URL_TEMPLATE_ARRAY_KEY] = null;
        }

        if (
            !array_key_exists(
                CaptchaSettings::LANGUAGE_ARRAY_KEY,
                $settingsData
            )
        ) {
            $settingsData[CaptchaSettings::LANGUAGE_ARRAY_KEY] = null;
        }

        $this->_setHashSalt($settingsData[CaptchaSettings::HASH_SALT_ARRAY_KEY]);
        $this->_setDataDirPath($settingsData[CaptchaSettings::DATA_DIR_PATH_ARRAY_KEY]);

        $this->_setImageUrlTemplate(
            $settingsData[CaptchaSettings::IMAGE_URL_TEMPLATE_ARRAY_KEY]
        );

        $this->_setLanguage($settingsData[CaptchaSettings::LANGUAGE_ARRAY_KEY]);
    }

    /**
     * @param array|null $settingsData
     *
     * @throws CaptchaSettingsException
     */
    private function _checkSettingsData(?array $settingsData = null): void
    {
        if (empty($settingsData)) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_DATA_IS_EMPTY,
                CaptchaException::CODE_SETTINGS_DATA_IS_EMPTY
            );
        }

        if (
            !array_key_exists(
                CaptchaSettings::HASH_SALT_ARRAY_KEY,
                $settingsData
            )
        ) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_SALT_IS_NOT_SET,
                CaptchaException::CODE_SETTINGS_SALT_IS_NOT_SET
            );
        }

        if (empty($settingsData[CaptchaSettings::HASH_SALT_ARRAY_KEY])) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_SALT_IS_EMPTY,
                CaptchaException::CODE_SETTINGS_SALT_IS_EMPTY
            );
        }
    }

    /**
     * @param string|null $hashSalt
     *
     * @throws CaptchaSettingsException
     */
    private function _setHashSalt(?string $hashSalt = null): void
    {
        if (empty($hashSalt)) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_SALT_IS_EMPTY,
                CaptchaException::CODE_SETTINGS_SALT_IS_EMPTY
            );
        }

        $this->_hashSalt = $hashSalt;
    }

    /**
     * @param string|null $dataDirPath
     *
     * @throws CaptchaSettingsException
     */
    private function _setDataDirPath(?string $dataDirPath = null): void
    {
        if (
            !empty($dataDirPath) &&
            (
                !file_exists($dataDirPath) ||
                !is_dir($dataDirPath)
            )
        ) {
            throw new CaptchaSettingsException(
                CaptchaSettingsException::MESSAGE_SETTINGS_DATA_DIR_NOT_FOUND,
                CaptchaException::CODE_SETTINGS_DATA_DIR_NOT_FOUND
            );
        }

        $this->_dataDirPath = $dataDirPath;
    }

    /**
     * @param string|null $imageUrlTemplate
     */
    private function _setImageUrlTemplate(
        ?string $imageUrlTemplate = null
    ): void
    {
        if (empty($imageUrlTemplate)) {
            $imageUrlTemplate = CaptchaSettings::DEFAULT_IMAGE_URL_TEMPLATE;
        }

        $this->_imageUrlTemplate = $imageUrlTemplate;
    }

    /**
     * @param string|null $language
     */
    private function _setLanguage(?string $language = null): void
    {
        if (empty($language)) {
            $language = CaptchaSettings::DEFAULT_LANGUAGE;
        }

        $this->_language = $language;
    }
}
