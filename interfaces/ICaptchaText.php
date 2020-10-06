<?php
namespace Core\Plugins\Captcha\Interfaces;

interface ICaptchaText
{
    public function get(
        ?string $language    = null,
        ?string $dataDirPath = null
    ): string;

    public function update(?string $dataDirPath = null): void;
}
