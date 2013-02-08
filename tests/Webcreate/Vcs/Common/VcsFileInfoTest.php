<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

use Webcreate\Vcs\Common\VcsFileInfo;

class VcsFileInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $fileInfo = new VcsFileInfo('conveyor.yml', array('master', VcsFileInfo::BRANCH));

        $this->assertInstanceOf('Webcreate\Vcs\Common\VcsFileInfo', $fileInfo);

        $fileInfo = new VcsFileInfo('conveyor.yml', array('master', VcsFileInfo::BRANCH), VcsFileInfo::FILE);

        $this->assertInstanceOf('Webcreate\Vcs\Common\VcsFileInfo', $fileInfo);
    }

    public function testInBranch()
    {
        $fileInfo = new VcsFileInfo('conveyor.yml', array('master', VcsFileInfo::BRANCH));

        $this->assertTrue($fileInfo->inBranch());
        $this->assertFalse($fileInfo->inTag());
    }

    public function testInTag()
    {
        $fileInfo = new VcsFileInfo('conveyor.yml', array('master', VcsFileInfo::TAG));

        $this->assertTrue($fileInfo->inTag());
        $this->assertFalse($fileInfo->inBranch());
    }

    public function testGetFilename()
    {
        $fileInfo = new VcsFileInfo('conveyor.yml', array('master', VcsFileInfo::BRANCH));

        $this->assertEquals('conveyor.yml', $fileInfo->getFilename());

        $fileInfo = new VcsFileInfo('path/to/file', array('master', VcsFileInfo::BRANCH));

        $this->assertEquals('file', $fileInfo->getFilename());
    }

    public function testGetPathname()
    {
        $fileInfo = new VcsFileInfo('conveyor.yml', array('master', VcsFileInfo::BRANCH));

        $this->assertEquals('conveyor.yml', $fileInfo->getPathname());

        $fileInfo = new VcsFileInfo('path/to/file', array('master', VcsFileInfo::BRANCH));

        $this->assertEquals('path/to/file', $fileInfo->getPathname());
    }

    public function testIsFile()
    {
        $fileInfo = new VcsFileInfo('conveyor.yml', array('master', VcsFileInfo::BRANCH));

        $this->assertTrue($fileInfo->isFile());
        $this->assertFalse($fileInfo->isDir());
    }

    public function testIsDir()
    {
        $fileInfo = new VcsFileInfo('path/to/conveyor', array('master', VcsFileInfo::BRANCH), VcsFileInfo::DIR);

        $this->assertTrue($fileInfo->isDir());
        $this->assertFalse($fileInfo->isFile());
    }

    public function testGetReferenceName()
    {
        $fileInfo = new VcsFileInfo('path/to/conveyor', array('master', VcsFileInfo::BRANCH), VcsFileInfo::DIR);

        $this->assertEquals('master', $fileInfo->getReferenceName());
    }
}