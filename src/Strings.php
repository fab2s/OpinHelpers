<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OpinHelpers;

/**
 * class Strings
 */
class Strings
{
    /**
     * The canonical EOL for normalization
     */
    const EOL = "\n";

    /**
     * The canonical encoding
     */
    const ENCODING = 'UTF-8';

    /**
     * U+200B zero width space
     * U+FEFF zero width no-break space
     */
    const ZERO_WIDTH_WS_CLASS = '\x{200B}\x{FEFF}';

    /**
     * U+00A0  no-break space
     * U+2000  en quad
     * U+2001  em quad
     * U+2002  en space
     * U+2003  em space
     * U+2004  three-per-em space
     * U+2005  four-per-em space
     * U+2006  six-per-em space
     * U+2007  figure space
     * U+2008  punctuation space
     * U+2009  thin space
     * U+200A  hair space
     * U+202F  narrow no-break space
     * U+3000  ideographic space
     */
    const NON_STANDARD_WS_CLASS = '\x{00A0}\x{2000}-\x{200A}\x{202F}\x{3000}';

    /**
     * normalize EOL to LF and strip null bit
     *
     * @param string $string
     *
     * @return string
     */
    public static function filter($string)
    {
        /*
         * U+00 null bit
         * Zero width ws
         * normalized eol
         * normalized utf8
         */
        return Utf8::normalize(static::normalizeEol(preg_replace('`[\x{00}' . static::ZERO_WIDTH_WS_CLASS . ']{1,}`u', '', $string)));
    }

    /**
     * @param string $string
     * @param bool   $normalize
     * @param bool   $includeTabs
     *
     * @return string
     */
    public static function singleWsIze($string, $normalize = false, $includeTabs = true)
    {
        if ($normalize) {
            // multiple horizontal ws to a single low ws (eg ' ')
            return static::normalizeWs($string, $includeTabs);
        }

        return preg_replace('`(\h{1})(?:\1+)`u', '$1', $string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function singleLineIze($string)
    {
        return preg_replace("`\R{1,}`u", ' ', $string);
    }

    /**
     * @param $string string
     *
     * @return string
     */
    public static function dropZwWs($string)
    {
        return preg_replace('`[' . static::ZERO_WIDTH_WS_CLASS . ']{1,}`u', '', $string);
    }

    /**
     * @param string   $string
     * @param bool     $includeTabs    true to also replace tabs (\t) with ws ( )
     * @param int|null $maxConsecutive
     *
     * @return string
     */
    public static function normalizeWs($string, $includeTabs = true, $maxConsecutive = null)
    {
        // don't include regular ws unless we want to handle consecutive
        $extraWs = $includeTabs ? "\t" : '';
        $length  = '';
        $replace = ' ';
        if (isset($maxConsecutive)) {
            // as regular ws should be the majority, put it first
            $extraWs = " $extraWs";
            $length  = '{' . $maxConsecutive . ',}';
            $replace = str_repeat($replace, $maxConsecutive);
        }

        return preg_replace("`[$extraWs" . static::NON_STANDARD_WS_CLASS . "]$length`u", $replace, $string);
    }

    /**
     * @param string      $string
     * @param int|null    $maxConsecutive
     * @param string|null $eol
     *
     * @return string
     */
    public static function normalizeEol($string, $maxConsecutive = null, $eol = null)
    {
        $eol = $eol ?: static::EOL;
        if ($maxConsecutive === null) {
            return preg_replace('`\R`u', $eol, $string);
        }

        if ($maxConsecutive === 1) {
            return preg_replace('`\R{1,}`u', $eol, $string);
        }

        return preg_replace([
            // start with normalizing with LF (faster than CRLF)
            '`\R`u',
            // then remove high dupes
            "`\n{" . $maxConsecutive . ',}`u',
        ], [
            "\n",
            // restore EOL and set max consecutive
            str_repeat($eol, $maxConsecutive),
        ], $string);
    }

    /**
     * Normalizes a text document
     *
     * @param string $text
     *
     * @return string
     */
    public static function normalizeText($text)
    {
        return trim(static::filter($text));
    }

    /**
     * Normalizes a title
     *
     * @param string $title
     *
     * @return string
     */
    public static function normalizeTitle($title)
    {
        return static::normalizeWs(static::singleLineIze(static::normalizeText($title)), true, 1);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function normalizeName($name)
    {
        return Utf8::ucwords(static::normalizeTitle($name));
    }

    /**
     * wrapper for htmlspecialchars with utf-8 and ENT_COMPAT set as default
     *
     * @param string $string
     * @param int    $flag
     * @param bool   $hardEscape
     *
     * @return string
     */
    public static function escape($string, $flag = ENT_COMPAT, $hardEscape = true)
    {
        return htmlspecialchars($string, $flag, static::ENCODING, (bool) $hardEscape);
    }

    /**
     * wrapper for htmlspecialchars with utf-8 and ENT_COMPAT set
     * which prevents double encoding
     *
     * @param string $string
     * @param int    $flag
     *
     * @return string
     */
    public static function softEscape($string, $flag = ENT_COMPAT)
    {
        return static::escape($string, $flag, false);
    }

    /**
     * wrapper for htmlspecialchars_decode with ENT_COMPAT set
     *
     * @param string $string
     * @param int    $quoteStyle
     *
     * @return string
     */
    public static function unEscape($string, $quoteStyle = ENT_COMPAT)
    {
        return htmlspecialchars_decode($string, $quoteStyle);
    }

    /**
     * @param string      $string
     * @param string|null $from
     * @param string      $to
     *
     * @return string
     */
    public static function convert($string, $from = null, $to = self::ENCODING)
    {
        return mb_convert_encoding($string, $to, $from ? $from : static::detectEncoding($string));
    }

    /**
     * @param string $string
     *
     * @return string|null
     */
    public static function detectEncoding($string)
    {
        if (Utf8::isUtf8($string)) {
            return static::ENCODING;
        }

        if ($bom = Bom::extract($string)) {
            return Bom::getBomEncoding($bom);
        }

        return mb_detect_encoding($string, 'ISO-8859-1,Windows-1252', true) ?: null;
    }

    /**
     * Truly constant time string comparison for Timing Attack protection
     *
     * Many implementations will stop after length comparison which can
     * leak length (not much I agree, but what topic is this?), or try to
     * be smart at failing to compare portion of the $reference which again
     * could leak $reference length
     *
     * This method just goes through exactly the same number of operations
     * in every cases
     *
     * @param string $userInput
     * @param string $reference
     *
     * @return bool
     */
    public static function secureCompare($userInput, $reference)
    {
        if (strlen($userInput) !== strlen($reference)) {
            // use $reference as reference for actual constant time
            $comparison = $reference ^ $reference;
            // this make sure the result will be false
            $result = 1;
        } else {
            $comparison = $userInput ^ $reference;
            $result     = 0;
        }

        $len = strlen($comparison);
        for ($i = $len - 1; $i >= 0; --$i) {
            $result |= ord($comparison[$i]);
        }

        return !$result;
    }

    /**
     * Generate a pretty reliable hash to identify strings
     * Adding the length reduces collisions by quite a lot
     *
     * @param string $content
     *
     * @return string
     */
    public static function contentHash($content)
    {
        return strlen($content) . '_' . hash('sha256', $content);
    }
}
