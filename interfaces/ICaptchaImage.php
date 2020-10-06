<?php
namespace Core\Plugins\Captcha\Interfaces;

interface ICaptchaImage
{
    public function create(ICaptchaEntity $captchaEntity): void;
}
