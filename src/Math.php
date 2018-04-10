<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OpinHelpers;

/**
 * Class Math
 */
class Math
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
     * Math constructor.
     *
     * @param string|static $number
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($number)
    {
        if (isset(static::$globalPrecision)) {
            $this->precision = static::$globalPrecision;
        }

        $this->number = static::validateInputNumber($number);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return static::normalizeNumber($this->number);
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public static function number($number)
    {
        return new static($number);
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
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function add(...$numbers)
    {
        foreach ($numbers as $number) {
            $this->number = bcadd($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function sub(...$numbers)
    {
        foreach ($numbers as $number) {
            $this->number = bcsub($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function mul(...$numbers)
    {
        foreach ($numbers as $number) {
            $this->number = bcmul($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function div(...$numbers)
    {
        foreach ($numbers as $number) {
            $this->number = bcdiv($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function sqrt()
    {
        $this->number = bcsqrt($this->number, $this->precision);

        return $this;
    }

    /**
     * @param string $exponent
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function pow($exponent)
    {
        $this->number = bcpow($this->number, static::validatePositiveInteger($exponent), $this->precision);

        return $this;
    }

    /**
     * @param string $modulus
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function mod($modulus)
    {
        $this->number = bcmod($this->number, static::validatePositiveInteger($modulus));

        return $this;
    }

    /**
     * @param string $exponent
     * @param string $modulus
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function powMod($exponent, $modulus)
    {
        $this->number = bcpowmod($this->number, static::validatePositiveInteger($exponent), static::validatePositiveInteger($modulus));

        return $this;
    }

    /**
     * @param int $precision
     *
     * @return $this
     */
    public function round($precision = 0)
    {
        $precision = max(0, (int) $precision);
        if ($this->hasDecimals()) {
            if ($this->isPositive()) {
                $this->number = bcadd($this->number, '0.' . str_repeat('0', $precision) . '5', $precision);

                return $this;
            }

            $this->number = bcsub($this->number, '0.' . str_repeat('0', $precision) . '5', $precision);
        }

        return $this;
    }

    /**
     * @param int    $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     *
     * @return string
     */
    public function format($decimals = 0, $decPoint = '.', $thousandsSep = ' ')
    {
        $decimals = max(0, (int) $decimals);
        $dec      = '';
        // do not mutate
        $number   = (new static($this))->round($decimals)->normalize();
        $sign     = $number->isPositive() ? '' : '-';
        if ($number->abs()->hasDecimals()) {
            list($number, $dec) = explode('.', (string) $number);
        }

        if ($decimals) {
            $dec = sprintf("%'0-" . $decimals . 's', $dec);
        }

        return $sign . preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/", $thousandsSep, $number) . ($decimals ? $decPoint . $dec : '');
    }

    /**
     * @return $this
     */
    public function ceil()
    {
        if ($this->hasDecimals()) {
            if ($this->isPositive()) {
                $this->number = bcadd($this->number, (preg_match('`\.[0]*$`', $this->number) ? '0' : '1'), 0);

                return $this;
            }

            $this->number = bcsub($this->number, '0', 0);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function floor()
    {
        if ($this->hasDecimals()) {
            if ($this->isPositive()) {
                $this->number = bcadd($this->number, 0, 0);

                return $this;
            }

            $this->number = bcsub($this->number, (preg_match('`\.[0]*$`', $this->number) ? '0' : '1'), 0);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function abs()
    {
        $this->number = ltrim($this->number, '-');

        return $this;
    }

    /**
     * @param string $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function gte($number)
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) >= 0);
    }

    /**
     * @param string $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function gt($number)
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) === 1);
    }

    /**
     * @param string $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function lte($number)
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) <= 0);
    }

    /**
     * @param string $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function lt($number)
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) === -1);
    }

    /**
     * @param string $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function eq($number)
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) === 0);
    }

    /**
     * returns the highest number among all arguments
     *
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function max(...$numbers)
    {
        foreach ($numbers as $number) {
            if (bccomp(static::validateInputNumber($number), $this->number, $this->precision) === 1) {
                $this->number = $number;
            }
        }

        return $this;
    }

    /**
     * returns the smallest number among all arguments
     *
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function min(...$numbers)
    {
        foreach ($numbers as $number) {
            if (bccomp(static::validateInputNumber($number), $this->number, $this->precision) === -1) {
                $this->number = $number;
            }
        }

        return $this;
    }

    /**
     * convert decimal value to any other base bellow or equals to 64
     *
     * @param int $base
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function toBase($base)
    {
        if ($this->normalize()->hasDecimals()) {
            throw new \InvalidArgumentException('Argument number is not an integer in ' . __METHOD__);
        }

        // do not mutate, only support positive integers
        $number = ltrim((string) $this, '-');
        if (static::$gmpSupport && $base <= 62) {
            return static::baseConvert($number, 10, $base);
        }

        $result   = '';
        $baseChar = static::getBaseChar($base);
        while (bccomp($number, 0) != 0) { // still data to process
            $rem    = bcmod($number, $base); // calc the remainder
            $number = bcdiv(bcsub($number, $rem), $base);
            $result = $baseChar[$rem] . $result;
        }

        $result = $result ? $result : $baseChar[0];

        return (string) $result;
    }

    /**
     * convert any based value bellow or equals to 64 to its decimal value
     *
     * @param string $number
     * @param int    $base
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public static function fromBase($number, $base)
    {
        // cleanup
        $number   = trim($number);
        $baseChar = static::getBaseChar($base);
        // Convert string to lower case since base36 or less is case insensitive
        if ($base < 37) {
            $number = strtolower($number);
        }

        // clean up the input string if it uses particular input formats
        switch ($base) {
            case 16:
                // remove 0x from start of string
                if (substr($number, 0, 2) === '0x') {
                    $number = substr($number, 2);
                }
                break;
            case 8:
                // remove the 0 from the start if it exists - not really required
                if ($number[0] === 0) {
                    $number = substr($number, 1);
                }
                break;
            case 2:
                // remove an 0b from the start if it exists
                if (substr($number, 0, 2) === '0b') {
                    $number = substr($number, 2);
                }
                break;
            case 64:
                // remove padding chars: =
                $number = rtrim($number, '=');
                break;
        }

        // only support positive integers
        $number = ltrim($number, '-');
        if ($number === '' || strpos($number, '.') !== false) {
            throw new \InvalidArgumentException('Argument number is not an integer');
        }

        if (trim($number, $baseChar[0]) === '') {
            return new static('0');
        }

        if (static::$gmpSupport && $base <= 62) {
            return new static(static::baseConvert($number, $base, 10));
        }

        // By now we know we have a correct base and number
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

        return new static($result ? $result : '0');
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
     * @return int
     *
     * @internal param int $base
     */
    public static function baseConvert($number, $fromBase = 10, $toBase = 62)
    {
        return gmp_strval(gmp_init($number, $fromBase), $toBase);
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
        return preg_match('`^([+-]{1})?([0-9]+(\.[0-9]+)?|\.[0-9]+)$`', $number);
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
     * @param int $integer
     *
     * @return mixed
     */
    protected static function validatePositiveInteger($integer)
    {
        $integer = max(0, (int) $integer);
        if (!$integer) {
            throw new \InvalidArgumentException('Argument number is not valid');
        }

        return $integer;
    }
}

// OMG a dynamic static anti pattern ^^
Math::gmpSupport();
