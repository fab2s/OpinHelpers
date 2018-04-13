<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Tests;

use fab2s\OpinHelpers\FileLock;

/**
 * Class FileLockTest
 */
class FileLockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider lockMethodCases
     *
     * @param string $lockMethod
     * @param string $lockCall
     */
    public function testLock($lockMethod, $lockCall)
    {
        $tmpFile = $this->getTmpFile();

        if (!$tmpFile) {
            $this->markTestSkipped('Could not generate temporary file');
        }

        $lock = (new FileLock($tmpFile, $lockMethod))->$lockCall();
        /* @var FileLock $lock */
        $this->assertTrue($lock->isLocked());

        if ($lockMethod === FileLock::LOCK_EXTERNAL) {
            $this->assertTrue(file_exists($tmpFile . '.lock'));
        } else {
            $this->assertFalse(file_exists($tmpFile . '.lock'));
        }

        // same process I know
        $otherLock = (new FileLock($tmpFile, $lockMethod))->$lockCall();
        /* @var FileLock $otherLock */
        $this->assertFalse($otherLock->isLocked());

        $lock->releaseLock();
        $this->assertFalse($lock->isLocked());
        $this->assertTrue($otherLock->$lockCall()->isLocked());

        $otherLock->__destruct();
        $this->assertFalse($otherLock->isLocked());
    }

    /**
     * @return array
     */
    public function lockMethodCases()
    {
        return [
            [
                FileLock::LOCK_SELF,
                'setLock',
            ],
            [
                FileLock::LOCK_SELF,
                'obtainLock',
            ],
            [
                FileLock::LOCK_EXTERNAL,
                'setLock',
            ],
            [
                FileLock::LOCK_EXTERNAL,
                'obtainLock',
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
