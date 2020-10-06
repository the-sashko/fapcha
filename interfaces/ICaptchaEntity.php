<?php
namespace Core\Plugins\Captcha\Interfaces;

interface ICaptchaEntity
{
    public function getText(): string;

    public function getHash(): string;

    public function getImageFilePath(): string;

    public function getImageUrlPath(): string;
}
