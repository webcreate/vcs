<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

use Webcreate\Util\Cli;
use Webcreate\Vcs\Svn\Svnadmin;

class SvnadminTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->svndir = sys_get_temp_dir();
    }

    public function testCreate()
    {
        $cli = $this->getMock('Webcreate\\Util\\Cli', array('execute', 'getOutput', 'getErrorOutput'));
        $cli
            ->expects($this->once())
            ->method('execute')
            ->with('/usr/local/bin/svnadmin create \''.$this->svndir.'/test_test\'')
            ->will($this->returnValue(0))
        ;

        $svnadmin = new Svnadmin($this->svndir, '/usr/local/bin/svnadmin', $cli);
        $svnadmin->create('test_test');
    }
}