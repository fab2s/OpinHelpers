<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OpinHelpers;

/**
 * class Bom
 */
class Bom
{
    /**
     * UTF8 BOM & encoding
     */
    const BOM_UTF8 = "\xEF\xBB\xBF";
    const ENC_UTF8 = 'UTF-8';

    /**
     * UTF16_BE BOM & encoding
     */
    const BOM_UTF16_BE = "\xFE\xFF";
    const ENC_UTF16_BE = 'UTF-16BE';

    /**
     * UTF16_LE BOM & encoding
     */
    const BOM_UTF16_LE = "\xFF\xFE";
    const ENC_UTF16_LE = 'UTF-16LE';

    /**
     * UTF32_BE BOM & encoding
     */
    const BOM_UTF32_BE = "\x00\x00\xFE\xFF";
    const ENC_UTF32_BE = 'UTF-32BE';

    /**
     * UTF32_LE BOM & encoding
     */
    const BOM_UTF32_LE = "\xFF\xFE\x00\x00";
    const ENC_UTF32_LE = 'UTF-32LE';

    /**
     * UTF8 | UTF16_BE | UTF32_LE | UTF16_LE | UTF32_BE
     */
    const BOM_REGEX = '\xEF\xBB\xBF|\xFE\xFF|\xFF\xFE\x00\x00|\xFF\xFE|\x00\x00\xFE\xFF';

    /**
     * @var string[]
     */
    protected static $boms = [
        self::ENC_UTF8     => self::BOM_UTF8,
        self::ENC_UTF16_BE => self::BOM_UTF16_BE,
        self::ENC_UTF16_LE => self::BOM_UTF16_LE,
        self::ENC_UTF32_BE => self::BOM_UTF32_BE,
        self::ENC_UTF32_LE => self::BOM_UTF32_LE,
    ];

    /**
     * @var string[]
     */
    protected static $smob = [
        self::BOM_UTF8     => self::ENC_UTF8,
        self::BOM_UTF16_BE => self::ENC_UTF16_BE,
        self::BOM_UTF16_LE => self::ENC_UTF16_LE,
        self::BOM_UTF32_BE => self::ENC_UTF32_BE,
        self::BOM_UTF32_LE => self::ENC_UTF32_LE,
    ];

    /**
     * @param string $string
     *
     * @return string|null
     */
    public static function extract($string)
    {
        return preg_match('`^(' . static::BOM_REGEX . ')`', $string, $match) ? $match[1] : null;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function drop($string)
    {
        return preg_replace('`^(' . static::BOM_REGEX . ')`', '', $string);
    }

    /**
     * @param string $bom
     *
     * @return string|null
     */
    public static function getBomEncoding($bom)
    {
        return isset(static::$smob[$bom]) ? static::$smob[$bom] : null;
    }

    /**
     * @param string $encoding
     *
     * @return null|string
     */
    public static function getEncodingBom($encoding)
    {
        return isset(static::$boms[$encoding]) ? static::$boms[$encoding] : null;
    }

    /**
     * @return array
     */
    public static function getBoms()
    {
        return static::$boms;
    }
}
