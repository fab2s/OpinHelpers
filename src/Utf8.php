<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OpinHelpers;

/**
 * UTF8 string manipulations
 */
class Utf8
{
    /**
     * utf8 charset name in mb dialect
     */
    const ENC_UTF8 = 'UTF-8';

    /**
     * \Normalizer::NFC
     */
    const NORMALIZE_NFC = 4;

    /**
     * \Normalizer::NFD
     */
    const NORMALIZE_NFD = 2;

    /**
     * @var bool
     */
    protected static $supportNormalizer;

    /**
     * strrpos
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     *
     * @return int|false
     */
    public static function strrpos($haystack, $needle, $offset = null)
    {
        // Emulate strrpos behaviour (no warning)
        if (empty($haystack)) {
            return false;
        }

        return mb_strrpos($haystack, $needle, (int) $offset, static::ENC_UTF8);
    }

    /**
     * strpos
     *
     * @param string   $haystack
     * @param string   $needle
     * @param int|null $offset
     *
     * @return int|false
     */
    public static function strpos($haystack, $needle, $offset = null)
    {
        return mb_strpos($haystack, $needle, (int) $offset, static::ENC_UTF8);
    }

    /**
     * strtolower
     *
     * @param string $string
     *
     * @return string
     */
    public static function strtolower($string)
    {
        return mb_strtolower($string, static::ENC_UTF8);
    }

    /**
     * strtoupper
     *
     * @param string $string
     *
     * @return string
     */
    public static function strtoupper($string)
    {
        return mb_strtoupper($string, static::ENC_UTF8);
    }

    /**
     * substr
     *
     * @param string   $string
     * @param int      $offset
     * @param int|null $length
     *
     * @return string
     */
    public static function substr($string, $offset, $length = null)
    {
        return mb_substr($string, $offset, $length === null ? mb_strlen($string, static::ENC_UTF8) : $length, static::ENC_UTF8);
    }

    /**
     * strlen
     *
     * @param string $string
     *
     * @return int
     */
    public static function strlen($string)
    {
        return mb_strlen($string, static::ENC_UTF8);
    }

    /**
     * ucfirst
     *
     * @param string $string
     *
     * @return string
     */
    public static function ucfirst($string)
    {
        switch (static::strlen($string)) {
            case 0:
                return '';
            case 1:
                return static::strtoupper($string);
            default:
                return static::strtoupper(static::substr($string, 0, 1)) . static::substr($string, 1);
        }
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function ucwords($string)
    {
        return mb_convert_case($string, MB_CASE_TITLE, static::ENC_UTF8);
    }

    /**
     * ord
     *
     * @param string $chr
     *
     * @return int|null
     */
    public static function ord($chr)
    {
        if (static::strlen($chr) > 1) {
            $chr = static::substr($chr, 0, 1);
        }

        // we do want to use strlen here !
        switch (strlen($chr)) {
            case 1:
                return ord($chr);
            case 2:
                return ((ord($chr[0]) & 0x1F) << 6) | (ord($chr[1]) & 0x3F);
            case 3:
                return ((ord($chr[0]) & 0x0F) << 12) | ((ord($chr[1]) & 0x3F) << 6) | (ord($chr[2]) & 0x3F);
            case 4:
                return ((ord($chr[0]) & 0x07) << 18) | ((ord($chr[1]) & 0x3F) << 12) | ((ord($chr[2]) & 0x3F) << 6) | (ord($chr[3]) & 0x3F);
            case 0:
                return 0;
            default:
                // should just never happen
                return null;
        }
    }

    /**
     * chr
     *
     * @param int $num
     *
     * @return string
     */
    public static function chr($num)
    {
        // prolly the fastest
        return mb_convert_encoding('&#' . (int) $num . ';', static::ENC_UTF8, 'HTML-ENTITIES');
    }

    /**
     * normalize an utf8 string to canonical form
     * Default to NFC
     *
     * @see https://stackoverflow.com/a/7934397/7630496
     *
     * @param string $string
     * @param int    $canonicalForm
     *
     * @return string
     */
    public static function normalize($string, $canonicalForm = self::NORMALIZE_NFC)
    {
        if (static::$supportNormalizer) {
            return \Normalizer::normalize($string, $canonicalForm);
        }

        return $string;
    }

    /**
     * tels if a string contains utf8 chars (which may not be valid)
     *
     * @param string $string
     *
     * @return bool
     */
    public static function hasUtf8($string)
    {
        // From http://w3.org/International/questions/qa-forms-utf-8.html
        // non-overlong 2-byte|excluding overlong|straight 3-byte|excluding surrogates|planes 1-3|planes 4-15|plane 16
        return (bool) preg_match('%(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF] |\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+%xs', $string);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isUtf8($string)
    {
        return (bool) preg_match('//u', $string);
    }

    /**
     * Remove any 4byte multi bit chars, useful to make sure we can insert in utf8-nonMb4 db tables
     *
     * @param string $string
     * @param string $replace
     *
     * @return string
     */
    public static function replaceMb4($string, $replace = '')
    {
        return preg_replace('%(?:
            \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}        # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
        )%xs', $replace, $string);
    }

    /**
     * @param bool $disable
     *
     * @return bool
     */
    public static function normalizerSupport($disable = false)
    {
        if ($disable) {
            return static::$supportNormalizer = false;
        }

        return static::$supportNormalizer = function_exists('normalizer_normalize');
    }
}

// OMG a dynamic static anti pattern ^^
Utf8::normalizerSupport();
