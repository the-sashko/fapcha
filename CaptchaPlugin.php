<?php

namespace Sonder\Plugins;

use ImagickException;
use Sonder\Plugins\Captcha\Classes\CaptchaEntity;
use Sonder\Plugins\Captcha\Classes\CaptchaImage;
use Sonder\Plugins\Captcha\Classes\CaptchaSettings;
use Sonder\Plugins\Captcha\Classes\CaptchaText;
use Sonder\Plugins\Captcha\Exceptions\CaptchaEntityException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaImageException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaPluginException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaSettingsException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaTextException;
use Sonder\Plugins\Captcha\Interfaces\ICaptchaEntity;
use Sonder\Plugins\Captcha\Interfaces\ICaptchaPlugin;

final class CaptchaPlugin implements ICaptchaPlugin
{

    /**
     * @var CaptchaSettings
     */
    private CaptchaSettings $_settings;

    /**
     * @var CaptchaText
     */
    private CaptchaText $_text;

    /**
     * @var CaptchaImage
     */
    private CaptchaImage $_image;

    /**
     * @param array|null $settingsData
     *
     * @throws CaptchaSettingsException
     */
    final public function __construct(?array $settingsData = null)
    {
        $this->_text = new CaptchaText();
        $this->_image = new CaptchaImage();
        $this->_settings = new CaptchaSettings($settingsData);
    }

    /**
     * @return ICaptchaEntity
     *
     * @throws CaptchaEntityException
     * @throws CaptchaImageException
     * @throws CaptchaPluginException
     * @throws CaptchaSettingsException
     * @throws CaptchaStoreException
     * @throws CaptchaTextException
     * @throws ImagickException
     */
    final public function getEntity(): ICaptchaEntity
    {
        if (empty($this->_settings)) {
            throw new CaptchaPluginException(
                CaptchaPluginException::MESSAGE_PLUGIN_SETTINGS_ARE_NOT_SET,
                Captcha\Exceptions\CaptchaException::CODE_PLUGIN_SETTINGS_ARE_NOT_SET
            );
        }

        $language = $this->_settings->getLanguage();
        $dataDirPath = $this->_settings->getDataDirPath();

        $text = $this->_text->get($language, $dataDirPath);
        $hash = $this->_getHash($text);
        $imageFileName = $this->_getFileName($hash);

        $entity = new CaptchaEntity(
            $text,
            $hash,
            $imageFileName,
            $this->_settings
        );

        $this->_image->create($entity);

        return $entity;
    }

    /**
     * @param string|null $text
     * @param string|null $hash
     *
     * @return bool
     *
     * @throws CaptchaPluginException
     * @throws CaptchaSettingsException
     */
    final public function check(
        ?string $text = null,
        ?string $hash = null
    ): bool
    {
        if (empty($text) || empty($hash)) {
            return false;
        }

        return $hash == $this->_getHash($text);
    }

    /**
     * @throws CaptchaPluginException
     * @throws CaptchaStoreException
     * @throws CaptchaTextException
     */
    final public function updateByCron(): void
    {
        if (empty($this->_settings)) {
            throw new CaptchaPluginException(
                CaptchaPluginException::MESSAGE_PLUGIN_SETTINGS_ARE_NOT_SET,
                CaptchaException::CODE_PLUGIN_SETTINGS_ARE_NOT_SET
            );
        }

        $this->_text->update($this->_settings->getDataDirPath());
    }

    /**
     * @param string|null $text
     *
     * @return string
     *
     * @throws CaptchaPluginException
     * @throws CaptchaSettingsException
     */
    private function _getHash(?string $text = null): string
    {
        if (empty($text)) {
            throw new CaptchaPluginException(
                CaptchaPluginException::MESSAGE_PLUGIN_TEXT_IS_NOT_SET,
                CaptchaException::CODE_PLUGIN_TEXT_IS_NOT_SET
            );
        }

        if (empty($this->_settings)) {
            throw new CaptchaPluginException(
                CaptchaPluginException::MESSAGE_PLUGIN_SETTINGS_ARE_NOT_SET,
                CaptchaException::CODE_PLUGIN_SETTINGS_ARE_NOT_SET
            );
        }

        $hashSalt = $this->_settings->getHashSalt();

        $hash = sprintf('%s%s%s', $hashSalt, hash('md5', $hashSalt), $text);

        return hash('sha256', $hash);
    }

    /**
     * @param string|null $hash
     *
     * @return string
     *
     * @throws CaptchaPluginException
     */
    private function _getFileName(?string $hash = null): string
    {
        if (empty($hash)) {
            throw new CaptchaPluginException(
                CaptchaPluginException::MESSAGE_PLUGIN_HASH_IS_NOT_SET,
                CaptchaException::CODE_PLUGIN_HASH_IS_NOT_SET
            );
        }

        return sprintf('%s.png', $hash);
    }
}
