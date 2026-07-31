<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OpinHelpers\Tests;

use fab2s\OpinHelpers\Bom;
use fab2s\OpinHelpers\FileLock;
use fab2s\OpinHelpers\Math;
use fab2s\OpinHelpers\Strings;
use fab2s\OpinHelpers\Utf8;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * Class DeprecationTest
 */
class DeprecationTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    #[DataProvider('deprecationProvider')]
    public function test_deprecation(string $class, array $methods)
    {
        $this->assertTrue(class_exists($class), $class);

        $reflexion = new ReflectionClass($class);

        foreach ($methods as $method) {
            $this->assertSame($method, $reflexion->getMethod($method)->name);
        }
    }

    public static function deprecationProvider(): array
    {
        return [
            [
                Math::class,
                [
                    'number',
                    'fromBase',
                    'toBase',
                ],
            ],
            [
                \fab2s\Math\Math::class,
                [
                    'number',
                    'fromBase',
                    'toBase',
                ],
            ],
            [
                FileLock::class,
                [
                    'open',
                    'getHandle',
                    'obtainLock',
                ],
            ],
            [
                \fab2s\FileLock\FileLock::class,
                [
                    'open',
                    'getHandle',
                    'obtainLock',
                ],
            ],
            [
                Bom::class,
                [
                    'extract',
                    'drop',
                    'getBomEncoding',
                ],
            ],
            [
                \fab2s\Bom\Bom::class,
                [
                    'extract',
                    'drop',
                    'getBomEncoding',
                ],
            ],
            [
                Utf8::class,
                [
                    'strrpos',
                    'normalize',
                    'replaceMb4',
                ],
            ],
            [
                \fab2s\Utf8\Utf8::class,
                [
                    'strrpos',
                    'normalize',
                    'replaceMb4',
                ],
            ],
            [
                Strings::class,
                [
                    'filter',
                    'normalizeWs',
                    'normalizeEol',
                ],
            ],
            [
                \fab2s\Strings\Strings::class,
                [
                    'filter',
                    'normalizeWs',
                    'normalizeEol',
                ],
            ],
        ];
    }

    /**
     * @return bool|string
     */
    protected function getTmpFile()
    {
        return tempnam(sys_get_temp_dir(), 'Fl_');
    }
}
