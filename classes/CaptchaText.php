<?php

namespace Sonder\Plugins\Captcha\Classes;

use Exception;
use Sonder\Plugins\Captcha\Exceptions\CaptchaException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaStoreException;
use Sonder\Plugins\Captcha\Exceptions\CaptchaTextException;
use Sonder\Plugins\Captcha\Interfaces\ICaptchaStore;
use Sonder\Plugins\Captcha\Interfaces\ICaptchaText;
use Throwable;

final class CaptchaText implements ICaptchaText
{
    const GENDERS = [
        'male',
        'female',
        'neutral',
        'plural'
    ];

    const DICTIONARIES_DIR_PATH = __DIR__ . '/../res/dictionaries';

    /**
     * @param string|null $language
     * @param string|null $dataDirPath
     *
     * @return string
     *
     * @throws CaptchaTextException
     * @throws CaptchaStoreException
     */
    final public function get(
        ?string $language = null,
        ?string $dataDirPath = null
    ): string
    {
        if (empty($language)) {
            throw new CaptchaTextException(
                CaptchaTextException::MESSAGE_TEXT_LANGUAGE_IS_NOT_SET,
                CaptchaException::CODE_TEXT_LANGUAGE_IS_NOT_SET
            );
        }

        if (!$this->_isValidLanguage($language)) {
            $errorMessage = sprintf(
                '%s. Language: %s',
                CaptchaTextException::MESSAGE_TEXT_INVALID_LANGUAGE,
                $language
            );

            throw new CaptchaTextException(
                $errorMessage,
                CaptchaException::CODE_TEXT_INVALID_LANGUAGE
            );
        }

        if (empty($dataDirPath)) {
            throw new CaptchaTextException(
                CaptchaTextException::MESSAGE_TEXT_DATA_DIR_PATH_IS_NOT_SET,
                CaptchaException::CODE_TEXT_DATA_DIR_PATH_IS_NOT_SET
            );
        }

        $captchaStore = new CaptchaStore($dataDirPath);
        $gender = $this->_getGender();

        $dictionary = sprintf('%s_adjective_%s', $language, $gender);
        $adjective = $captchaStore->getRandomWord($dictionary);

        if (empty($adjective)) {
            $errorMessage = CaptchaTextException::MESSAGE_TEXT_WORD_IS_EMPTY;

            $errorMessage = sprintf(
                '%s. Dictionary: %s',
                $errorMessage,
                $dictionary
            );

            throw new CaptchaTextException(
                $errorMessage,
                CaptchaException::CODE_TEXT_WORD_IS_EMPTY
            );
        }

        $dictionary = sprintf('%s_noun_%s', $language, $gender);
        $noun = $captchaStore->getRandomWord($dictionary);

        if (empty($noun)) {
            $errorMessage = CaptchaTextException::MESSAGE_TEXT_WORD_IS_EMPTY;

            $errorMessage = sprintf(
                '%s. Dictionary: %s',
                $errorMessage,
                $dictionary
            );

            throw new CaptchaTextException(
                $errorMessage,
                CaptchaException::CODE_TEXT_WORD_IS_EMPTY
            );
        }

        return sprintf('%s %s', $adjective, $noun);
    }

    /**
     * @param string|null $dataDirPath
     *
     * @throws CaptchaStoreException
     * @throws CaptchaTextException
     */
    final public function update(?string $dataDirPath = null): void
    {
        if (empty($dataDirPath)) {
            throw new CaptchaTextException(
                CaptchaTextException::MESSAGE_TEXT_DATA_DIR_PATH_IS_NOT_SET,
                CaptchaException::CODE_TEXT_DATA_DIR_PATH_IS_NOT_SET
            );
        }

        $dictionaries = $this->_getDictionaries();

        if (empty($dictionaries)) {
            throw new CaptchaTextException(
                CaptchaTextException::MESSAGE_TEXT_DICTIONARIES_NOT_FOUND,
                CaptchaException::CODE_TEXT_DICTIONARIES_NOT_FOUND
            );
        }

        $captchaStore = new CaptchaStore(
            $dataDirPath,
            CaptchaStore::TEMPORARY_DATABASE_FILE_NAME
        );

        $captchaStore->createDatabase();

        foreach ($dictionaries as $dictionary => $dictionaryFilePath) {
            $words = $this->_getTextFromFile($dictionaryFilePath);
            $words = explode("\n", $words);
            $words = array_unique($words);

            $this->_insertDictionaryToStore(
                $dictionary,
                $words,
                $captchaStore
            );
        }

        $captchaStore->updateDatabase($dataDirPath);
    }

    /**
     * @param string|null $language
     *
     * @return bool
     *
     * @throws CaptchaTextException
     */
    private function _isValidLanguage(?string $language = null): bool
    {
        if (empty($language)) {
            return false;
        }

        $dictionariesMetaFilePath = sprintf(
            '%s/meta.json',
            CaptchaText::DICTIONARIES_DIR_PATH
        );

        $dictionariesMeta = $this->_getTextFromFile($dictionariesMetaFilePath);
        $dictionariesMeta = (array)json_decode($dictionariesMeta, true);

        $languages = array_keys($dictionariesMeta);

        return in_array($language, $languages);
    }

    /**
     * @param string|null $dictionary
     * @param array|null $words
     * @param ICaptchaStore|null $captchaStore
     *
     * @throws CaptchaTextException
     */
    private function _insertDictionaryToStore(
        ?string        $dictionary = null,
        ?array         $words = null,
        ?ICaptchaStore $captchaStore = null
    ): void
    {
        if (empty($dictionary)) {
            throw new CaptchaTextException(
                CaptchaTextException::MESSAGE_TEXT_DICTIONARY_IS_NOT_SET,
                CaptchaException::CODE_TEXT_DICTIONARY_IS_NOT_SET
            );
        }

        if (empty($words)) {
            throw new CaptchaTextException(
                CaptchaTextException::MESSAGE_TEXT_WORDS_ARE_NOT_SET,
                CaptchaException::CODE_TEXT_WORDS_ARE_NOT_SET
            );
        }

        if (empty($captchaStore)) {
            throw new CaptchaTextException(
                CaptchaTextException::MESSAGE_TEXT_STORE_IS_NOT_SET,
                CaptchaException::CODE_TEXT_STORE_IS_NOT_SET
            );
        }

        $captchaStore->createDictionary($dictionary);

        foreach ($words as $word) {
            $word = mb_convert_case((string)$word, MB_CASE_LOWER);
            $word = preg_replace('/\s/sui', '', $word);

            if (empty($word)) {
                continue;
            }

            $captchaStore->insertWord($word, $dictionary);
        }
    }

    /**
     * @return string
     */
    private function _getGender(): string
    {
        $countOfGenders = count(CaptchaText::GENDERS);

        return CaptchaText::GENDERS[rand(0, $countOfGenders - 1)];
    }

    /**
     * @return array|null
     *
     * @throws CaptchaTextException
     */
    private function _getDictionaries(): ?array
    {
        $dictionaries = [];

        $dictionariesMetaFilePath = sprintf(
            '%s/meta.json',
            CaptchaText::DICTIONARIES_DIR_PATH
        );

        $dictionariesMeta = $this->_getTextFromFile($dictionariesMetaFilePath);
        $dictionariesMeta = (array)json_decode($dictionariesMeta, true);

        if (!$this->_isDictionariesMetaHasCorrectFormat($dictionariesMeta)) {
            return null;
        }

        foreach ($dictionariesMeta as $language => $dictionaryMeta) {
            $this->_setDictionariesByLanguage(
                $dictionaries,
                $language,
                (bool)$dictionaryMeta['gender_derivatives']
            );
        }

        return $dictionaries;
    }

    /**
     * @param array|null $dictionariesMeta
     *
     * @return bool
     *
     * @throws CaptchaTextException
     */
    private function _isDictionariesMetaHasCorrectFormat(
        ?array $dictionariesMeta = null
    ): bool
    {
        if (empty($dictionariesMeta)) {
            return false;
        }

        foreach ($dictionariesMeta as $language => $dictionaryMeta) {
            if (
                empty($language) ||
                !is_array($dictionaryMeta) ||
                !array_key_exists('gender_derivatives', $dictionaryMeta) ||
                !is_scalar($dictionaryMeta['gender_derivatives'])
            ) {
                throw new CaptchaTextException(
                    CaptchaTextException::MESSAGE_TEXT_METADATA_HAS_BAD_FORMAT,
                    CaptchaException::CODE_TEXT_METADATA_HAS_BAD_FORMAT
                );
            }
        }

        return true;
    }

    /**
     * @param array $dictionaries
     * @param string|null $language
     * @param bool $isGenderDerivatives
     *
     * @throws CaptchaTextException
     */
    private function _setDictionariesByLanguage(
        array   &$dictionaries,
        ?string $language = null,
        bool    $isGenderDerivatives = false
    ): void
    {
        if (empty($language)) {
            throw new CaptchaTextException(
                CaptchaTextException::MESSAGE_TEXT_LANGUAGE_IS_NOT_SET,
                CaptchaException::CODE_TEXT_LANGUAGE_IS_NOT_SET
            );
        }

        $dictionaryDirPath = sprintf(
            '%s/%s',
            CaptchaText::DICTIONARIES_DIR_PATH,
            $language
        );

        $adjectiveMaleKey = sprintf('%s_adjective_male', $language);
        $adjectiveFemaleKey = sprintf('%s_adjective_female', $language);
        $adjectiveNeutralKey = sprintf('%s_adjective_neutral', $language);
        $adjectivePluralKey = sprintf('%s_adjective_plural', $language);

        $nounMaleKey = sprintf('%s_noun_male', $language);
        $nounFemaleKey = sprintf('%s_noun_female', $language);
        $nounNeutralKey = sprintf('%s_noun_neutral', $language);
        $nounPluralKey = sprintf('%s_noun_plural', $language);

        $adjectiveMaleFilePath = sprintf(
            '%s/adjective_male.txt',
            $dictionaryDirPath
        );

        $adjectiveFemaleFilePath = sprintf(
            '%s/adjective_female.txt',
            $dictionaryDirPath
        );

        $adjectiveNeutralFilePath = sprintf(
            '%s/adjective_neutral.txt',
            $dictionaryDirPath
        );

        $adjectivePluralFilePath = sprintf(
            '%s/adjective_plural.txt',
            $dictionaryDirPath
        );

        $nounMaleFilePath = sprintf('%s/noun_male.txt', $dictionaryDirPath);

        $nounFemaleFilePath = sprintf(
            '%s/noun_female.txt',
            $dictionaryDirPath
        );

        $nounNeutralFilePath = sprintf(
            '%s/noun_neutral.txt',
            $dictionaryDirPath
        );

        $nounPluralFilePath = sprintf(
            '%s/noun_plural.txt',
            $dictionaryDirPath
        );

        if (!$isGenderDerivatives) {
            $adjectiveFilePath = sprintf(
                '%s/adjective.txt',
                $dictionaryDirPath
            );

            $nounFilePath = sprintf('%s/noun.txt', $dictionaryDirPath);

            $adjectiveMaleFilePath = $adjectiveFilePath;
            $adjectiveFemaleFilePath = $adjectiveFilePath;
            $adjectiveNeutralFilePath = $adjectiveFilePath;
            $adjectivePluralFilePath = $adjectiveFilePath;

            $nounMaleFilePath = $nounFilePath;
            $nounFemaleFilePath = $nounFilePath;
            $nounNeutralFilePath = $nounFilePath;
            $nounPluralFilePath = $nounFilePath;
        }

        $dictionaries[$adjectiveMaleKey] = $adjectiveMaleFilePath;
        $dictionaries[$adjectiveFemaleKey] = $adjectiveFemaleFilePath;
        $dictionaries[$adjectiveNeutralKey] = $adjectiveNeutralFilePath;
        $dictionaries[$adjectivePluralKey] = $adjectivePluralFilePath;
        $dictionaries[$nounMaleKey] = $nounMaleFilePath;
        $dictionaries[$nounFemaleKey] = $nounFemaleFilePath;
        $dictionaries[$nounNeutralKey] = $nounNeutralFilePath;
        $dictionaries[$nounPluralKey] = $nounPluralFilePath;
    }

    /**
     * @param string|null $filePath
     *
     * @return string
     *
     * @throws CaptchaTextException
     */
    private function _getTextFromFile(?string $filePath = null): string
    {
        if (empty($filePath)) {
            throw new CaptchaTextException(
                CaptchaTextException::MESSAGE_TEXT_FILE_PATH_IS_EMPTY,
                CaptchaException::CODE_TEXT_FILE_PATH_IS_EMPTY
            );
        }

        try {
            return (string)file_get_contents($filePath);
        } catch (Throwable $thr) {
            $errorMessage = '%s. File: %s. Error: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CaptchaTextException::MESSAGE_TEXT_CAN_NOT_OPEN_FILE,
                $filePath,
                $thr->getMessage()
            );

            throw new CaptchaTextException(
                $errorMessage,
                CaptchaException::CODE_TEXT_CAN_NOT_OPEN_FILE
            );
        }
    }
}
