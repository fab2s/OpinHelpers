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
        $this->assertTrue(is_resource($lock->getHandle()));

        // same process I know
        $opened    = FileLock::open($tmpFile, 'wb');
        $otherLock = (new FileLock($tmpFile, $lockMethod))->$lockCall();
        /* @var FileLock $otherLock */
        $this->assertFalse($otherLock->isLocked());
        if ($lockMethod === FileLock::LOCK_EXTERNAL) {
            $this->assertTrue(file_exists($tmpFile . '.lock'));
            // since it is external, we can also lock Self
            $this->assertTrue($opened instanceof FileLock);
            $this->assertTrue(is_resource($opened->getHandle()));
            $this->assertFalse($opened->unLock()->isLocked());
        } else {
            $this->assertFalse(file_exists($tmpFile . '.lock'));
            $this->assertFalse($opened);
        }

        $this->assertFalse(is_resource($otherLock->getHandle()));
        $lock->unLock();
        $this->assertFalse(is_resource($lock->getHandle()));
        $this->assertFalse($lock->isLocked());
        $this->assertTrue($otherLock->$lockCall()->isLocked());
        $this->assertTrue(is_resource($otherLock->getHandle()));

        $otherLock->__destruct();
        $this->assertFalse($otherLock->isLocked());
        $this->assertFalse(is_resource($otherLock->getHandle()));
    }

    /**
     * @return array
     */
    public function lockMethodCases()
    {
        return [
            [
                FileLock::LOCK_SELF,
                'doLock',
            ],
            [
                FileLock::LOCK_SELF,
                'obtainLock',
            ],
            [
                FileLock::LOCK_EXTERNAL,
                'doLock',
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
