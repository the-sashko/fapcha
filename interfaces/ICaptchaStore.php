<?php
namespace Core\Plugins\Captcha\Interfaces;

interface ICaptchaStore
{
    public function getRandomWord(?string $dictionary = null): ?string;

    public function createDictionary(?string $dictionary = null): void;

    public function insertWord(
        ?string $word       = null,
        ?string $dictionary = null
    ): void;

    public function createDatabase(): void;

    public function updateDatabase(?string $dataDirPath = null): bool;
}
