<?php

namespace Sonder\Plugins\Captcha\Interfaces;

interface ICaptchaText
{
    /**
     * @param string|null $language
     * @param string|null $dataDirPath
     *
     * @return string
     */
    public function get(
        ?string $language = null,
        ?string $dataDirPath = null
    ): string;

    /**
     * @param string|null $dataDirPath
     */
    public function update(?string $dataDirPath = null): void;
}
