<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math\OpinHelpers;

/**
 * Abstract class MathBaseAbstract
 */
abstract class MathBaseAbstract
{
    /**
     * Default precision
     */
    const PRECISION = 9;

    /**
     * base <= 64 charlist
     */
    const BASECHAR_64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

    /**
     * base <= 62 char list
     */
    const BASECHAR_62 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    /**
     * base <= 36 charlist
     */
    const BASECHAR_36 = '0123456789abcdefghijklmnopqrstuvwxyz';

    /**
     * base char cache for all supported bases (bellow 64)
     *
     * @var string[]
     */
    protected static $baseChars = [
        36 => self::BASECHAR_36,
        62 => self::BASECHAR_62,
        64 => self::BASECHAR_64,
    ];

    /**
     *  if set, will be used as default for all consecutive instances
     *
     * @var int
     */
    protected static $globalPrecision;

    /**
     * Used in static context, aligned with $globalPrecision, default to self::PRECISION
     *
     * @var int
     */
    protected static $staticPrecision = self::PRECISION;

    /**
     * @var bool
     */
    protected static $gmpSupport;

    /**
     * @var string
     */
    protected $number;

    /**
     * Instance precision, initialized with globalPrecision, default to self::PRECISION
     *
     * @var int
     */
    protected $precision = self::PRECISION;

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return bool
     */
    public function isPositive()
    {
        return $this->number[0] !== '-';
    }

    /**
     * @return bool
     */
    public function hasDecimals()
    {
        return strpos($this->number, '.') !== false;
    }

    /**
     * @return $this
     */
    public function normalize()
    {
        $this->number = static::normalizeNumber($this->number);

        return $this;
    }

    /**
     * @param int $precision
     *
     * @return $this
     */
    public function setPrecision($precision)
    {
        // even INT_32 should be enough precision
        $this->precision = max(0, (int) $precision);

        return $this;
    }

    /**
     * @param int $precision
     */
    public static function setGlobalPrecision($precision)
    {
        // even INT_32 should be enough precision
        static::$globalPrecision = max(0, (int) $precision);
        static::$staticPrecision = static::$globalPrecision;
    }

    /**
     * @param bool $disable
     *
     * @return bool
     */
    public static function gmpSupport($disable = false)
    {
        if ($disable) {
            return static::$gmpSupport = false;
        }

        return static::$gmpSupport = function_exists('gmp_init');
    }

    /**
     * There is no way around it, if you want to trust bcmath
     * you need to feed it with VALID numbers
     * Things like '1.1.1' or '12E16'are all 0 in bcmath world
     *
     * @param mixed $number
     *
     * @return bool
     */
    public static function isNumber($number)
    {
        return (bool) preg_match('`^([+-]{1})?([0-9]+(\.[0-9]+)?|\.[0-9]+)$`', $number);
    }

    /**
     * removes preceding / trailing 0, + and ws
     *
     * @param string      $number
     * @param string|null $default
     *
     * @return string|null
     */
    public static function normalizeNumber($number, $default = null)
    {
        if (!static::isNumber($number)) {
            return $default;
        }

        $sign   = $number[0] === '-' ? '-' : '';
        $number = ltrim((string) $number, '0+-');

        if (strpos($number, '.') !== false) {
            // also clear trailing 0
            list($number, $dec) = explode('.', $number);
            $dec                = rtrim($dec, '0.');
            $number             = ($number ? $number : '0') . ($dec ? '.' . $dec : '');
        }

        return $number ? $sign . $number : '0';
    }

    /**
     * @param int $base
     * @param int $max
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function getBaseChar($base, $max = 64)
    {
        $base = (int) $base;
        if ($base < 2 || $base > $max || $base > 64) {
            throw new \InvalidArgumentException('Argument base is not valid, base 2 to 64 are supported');
        }

        if (!isset(static::$baseChars[$base])) {
            if ($base > 62) {
                static::$baseChars[$base] = ($base == 64) ? static::BASECHAR_64 : substr(static::BASECHAR_64, 0, $base);
            } elseif ($base > 36) {
                static::$baseChars[$base] = ($base == 62) ? static::BASECHAR_62 : substr(static::BASECHAR_62, 0, $base);
            } else {
                static::$baseChars[$base] = ($base == 36) ? static::BASECHAR_36 : substr(static::BASECHAR_36, 0, $base);
            }
        }

        return static::$baseChars[$base];
    }

    /**
     * Convert a from a given base (up to 62) to base 10.
     *
     * WARNING This method requires ext-gmp
     *
     * @param string $number
     * @param int    $fromBase
     * @param int    $toBase
     *
     * @return string
     *
     * @internal param int $base
     */
    public static function baseConvert($number, $fromBase = 10, $toBase = 62)
    {
        return gmp_strval(gmp_init($number, $fromBase), $toBase);
    }

    /**
     * @param string     $number
     * @param string|int $base
     * @param string     $baseChar
     *
     * @return string
     */
    protected static function bcDec2Base($number, $base, $baseChar)
    {
        $result    = '';
        $numberLen = strlen($number);
        // Now loop through each digit in the number
        for ($i = $numberLen - 1; $i >= 0; --$i) {
            $char = $number[$i]; // extract the last char from the number
            $ord  = strpos($baseChar, $char); // get the decimal value
            if ($ord === false || $ord > $base) {
                throw new \InvalidArgumentException('Argument number is invalid');
            }

            // Now convert the value+position to decimal
            $result = bcadd($result, bcmul($ord, bcpow($base, ($numberLen - $i - 1))));
        }

        return $result ? $result : '0';
    }

    /**
     * @param string|static $number
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected static function validateInputNumber($number)
    {
        if ($number instanceof static) {
            return $number->getNumber();
        }

        $number = trim($number);
        if (!static::isNumber($number)) {
            throw new \InvalidArgumentException('Argument number is not valid');
        }

        return $number;
    }

    /**
     * @param int|string $integer
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected static function validatePositiveInteger($integer)
    {
        $integer = max(0, (int) $integer);
        if (!$integer) {
            throw new \InvalidArgumentException('Argument number is not valid');
        }

        return (string) $integer;
    }
}