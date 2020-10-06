<?php
namespace Core\Plugins\Captcha\Interfaces;

interface ICaptchaPlugin
{
    public function setSettings(?array $settingsData = null): void;

    public function getEntity(): ICaptchaEntity;

    public function check(?string $text = null, ?string $hash = null): bool;

    public function cron(): void;
}
