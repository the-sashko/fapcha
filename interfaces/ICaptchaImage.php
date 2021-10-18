<?php

namespace Sonder\Plugins\Captcha\Interfaces;

interface ICaptchaImage
{
    /**
     * @param ICaptchaEntity $captchaEntity
     */
    public function create(ICaptchaEntity $captchaEntity): void;
}
