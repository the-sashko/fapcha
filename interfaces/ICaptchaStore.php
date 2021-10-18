<?php

namespace Sonder\Plugins\Captcha\Interfaces;

interface ICaptchaStore
{
    /**
     * @param string|null $dictionary
     *
     * @return string|null
     */
    public function getRandomWord(?string $dictionary = null): ?string;

    /**
     * @param string|null $dictionary
     */
    public function createDictionary(?string $dictionary = null): void;

    /**
     * @param string|null $word
     * @param string|null $dictionary
     */
    public function insertWord(
        ?string $word = null,
        ?string $dictionary = null
    ): void;

    public function createDatabase(): void;

    /**
     * @param string|null $dataDirPath
     *
     * @return bool
     */
    public function updateDatabase(?string $dataDirPath = null): bool;
}
