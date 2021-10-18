<?php

namespace Sonder\Plugins\Captcha\Interfaces;

interface ICaptchaPlugin
{
    /**
     * @return ICaptchaEntity
     */
    public function getEntity(): ICaptchaEntity;

    /**
     * @param string|null $text
     * @param string|null $hash
     *
     * @return bool
     */
    public function check(?string $text = null, ?string $hash = null): bool;

    public function updateByCron(): void;
}
