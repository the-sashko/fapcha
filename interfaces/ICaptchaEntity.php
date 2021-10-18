<?php

namespace Sonder\Plugins\Captcha\Interfaces;

interface ICaptchaEntity
{
    /**
     * @return string
     */
    public function getText(): string;

    /**
     * @return string
     */
    public function getHash(): string;

    /**
     * @return string
     */
    public function getImageFilePath(): string;

    /**
     * @return string
     */
    public function getImageUrlPath(): string;
}
