<?php
namespace Core\Plugins\Captcha\Interfaces;

interface ICaptchaSettings
{
    public function getHashSalt(): string;

    public function getDataDirPath(): string;

    public function getImageUrlTemplate(): string;

    public function getLanguage(): string;
}
