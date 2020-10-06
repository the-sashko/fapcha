<?php
use Core\Plugins\Captcha\Interfaces\ICaptchaPlugin;
use Core\Plugins\Captcha\Interfaces\ICaptchaEntity;

use Core\Plugins\Captcha\Classes\CaptchaSettings;
use Core\Plugins\Captcha\Classes\CaptchaText;
use Core\Plugins\Captcha\Classes\CaptchaImage;
use Core\Plugins\Captcha\Classes\CaptchaEntity;

use Core\Plugins\Captcha\Exceptions\CaptchaPluginException;

class CaptchaPlugin implements ICaptchaPlugin
{
    const DEFAULT_LANGUAGE = 'en';

    private $_settings = null;
    private $_text     = null;
    private $_image    = null;

    public function __construct()
    {
        $this->_text     = new CaptchaText();
        $this->_image    = new CaptchaImage();
    }

    public function setSettings(?array $settingsData = null): void
    {
        $this->_settings = new CaptchaSettings($settingsData);
    }

    public function getEntity(): ICaptchaEntity
    {
        if (empty($this->_settings)) {
            throw new CaptchaPluginException(
                CaptchaPluginException::MESSAGE_PLUGIN_SETTINGS_ARE_NOT_SET,
                CaptchaPluginException::CODE_PLUGIN_SETTINGS_ARE_NOT_SET
            );
        }

        $language    = $this->_settings->getLanguage();
        $dataDirPath = $this->_settings->getDataDirPath();

        $text          = $this->_text->get($language, $dataDirPath);
        $hash          = $this->_getHash($text);
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

    public function check(?string $text = null, ?string $hash = null): bool
    {
        if (empty($text) || empty($hash)) {
            return false;
        }

        return $hash == $this->_getHash($text);
    }

    public function cron(): void
    {
        if (empty($this->_settings)) {
            throw new CaptchaPluginException(
                CaptchaPluginException::MESSAGE_PLUGIN_SETTINGS_ARE_NOT_SET,
                CaptchaPluginException::CODE_PLUGIN_SETTINGS_ARE_NOT_SET
            );
        }

        $this->_text->update($this->_settings->getDataDirPath());
    }

    private function _getHash(?string $text = null): string
    {
        if (empty($text)) {
            throw new CaptchaPluginException(
                CaptchaPluginException::MESSAGE_PLUGIN_TEXT_IS_NOT_SET,
                CaptchaPluginException::CODE_PLUGIN_TEXT_IS_NOT_SET
            );
        }

        if (empty($this->_settings)) {
            throw new CaptchaPluginException(
                CaptchaPluginException::MESSAGE_PLUGIN_SETTINGS_ARE_NOT_SET,
                CaptchaPluginException::CODE_PLUGIN_SETTINGS_ARE_NOT_SET
            );
        }

        $hashSalt = $this->_settings->getHashSalt();

        $hash = sprintf('%s%s%s', $hashSalt, hash('md5', $hashSalt), $text);

        return hash('sha256', $hash);
    }

    private function _getFileName(?string $hash = null): string
    {
        if (empty($hash)) {
            throw new CaptchaPluginException(
                CaptchaPluginException::MESSAGE_PLUGIN_HASH_IS_NOT_SET,
                CaptchaPluginException::CODE_PLUGIN_HASH_IS_NOT_SET
            );
        }

        return sprintf('%s.png', $hash);
    }
}
