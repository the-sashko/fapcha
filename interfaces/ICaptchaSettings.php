<?php

namespace Sonder\Plugins\Captcha\Interfaces;

interface ICaptchaSettings
{
    /**
     * @return string
     */
    public function getHashSalt(): string;

    /**
     * @return string
     */
    public function getDataDirPath(): string;

    /**
     * @return string
     */
    public function getImageUrlTemplate(): string;

    /**
     * @return string
     */
    public function getLanguage(): string;
}
