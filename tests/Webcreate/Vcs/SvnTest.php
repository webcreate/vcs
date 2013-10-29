<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

use Webcreate\Vcs\Svn;
use Webcreate\Vcs\Svn\Adapter\CliAdapter;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

require_once __DIR__ . "/Test/Util/xsprintf.php";

class SvnTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->username = 'user';
        $this->password = 'userpass';
        $this->url = 'svn://svnserver/repository';
        $this->bin = '/usr/local/bin/svn';

        $this->parser = $this->getMock('Webcreate\\Vcs\\Svn\\Parser\\CliParser', null);
        $this->cli = $this->getMock('Webcreate\\Util\\Cli', array('execute', 'getOutput', 'getErrorOutput'));
        $this->adapter = $this->getMock('Webcreate\\Vcs\Common\\Adapter\\CliAdapter', null, array($this->bin, $this->cli, $this->parser));
        $this->svn = $this->getMockBuilder('Webcreate\\Vcs\\Svn')
            ->setConstructorArgs(array($this->url, $this->adapter))
            ->setMethods(null)
        ;
    }

    public function testCheckoutCommandline()
    {
        $svn = $this->svn
            ->getMock()
        ;

        $dest = sys_get_temp_dir();

        $expected = xsprintf("%s checkout %xs %xs --non-interactive", $this->bin, $this->url . '/trunk', $dest);

        $this->cli
            ->expects($this->once())
            ->method('execute')
            ->with($expected)
            ->will($this->returnValue(0))
        ;

        $result = $svn->checkout($dest);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddThrowsExceptionNotCheckedOut()
    {
        $result = $this->svn->getMock()->add('/test/path');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddThrowsExceptionWhenPathNotExist()
    {
        $result = $this->svn->getMock()->add('/test/path');
    }

    public function testStatusCommandline()
    {
        $svn = $this->svn
            ->getMock()
        ;

        // first to a checkout
        $svn->checkout(sys_get_temp_dir());

        $expected = sprintf("%s status --non-interactive", $this->bin);

        $this->cli
            ->expects($this->once())
            ->method('execute')
            ->with($expected)
            ->will($this->returnValue(0))
        ;

        // disable the parsing
        $this->adapter->setParser(
            $this->getMock('Webcreate\\Vcs\\Svn\\Parser\\CliParser', array('parse'))
        );

        $result = $svn->status();
    }

    public function testLsExecutedCommandline()
    {
        $svn = $this->svn
            ->setMethods(array('parseLsXmlResult'))
            ->getMock()
        ;
        $svn->setCredentials('user', 'userpass');

        $path = '/path/to/test';

        $expected = xsprintf("%s list --xml %xs --non-interactive --username %xs --password %xs", $this->bin, $this->url . '/trunk' . $path, 'user', 'userpass');

        $this->cli
            ->expects($this->once())
            ->method('execute')
            ->with($expected)
            ->will($this->returnValue(0))
        ;

        // disable the parsing
        $this->adapter->setParser(
                $this->getMock('Webcreate\\Vcs\\Svn\\Parser\\CliParser', array('parse'))
        );

        $result = $svn->ls($path);
    }

    public function testLsParsingOfXmlResultFromSvnClient()
    {
        $svn = $this->svn
            ->getMock()
        ;

        $this->cli
            ->expects($this->any())
            ->method('getOutput')
            ->will($this->returnValue(file_get_contents(__DIR__.'/Test/Fixtures/svn_list.xml')))
        ;

        $result = $svn->ls('/path/to/test');

        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
    }

    /**
     * @dataProvider importProvider
     */
    public function testImportCommandline($path, $svnpath, $message, $expected)
    {
        $this->cli
            ->expects($this->once())
            ->method('execute')
            ->with($expected)
            ->will($this->returnValue(0))
        ;

        $result = $this->svn->getMock()->import($path, $svnpath, $message);
    }

    public function importProvider()
    {
        $this->setUp();

        return array(
            array('path/to/import', '/', 'Importing stuff', xsprintf("%s import %xs %xs -m %xs --non-interactive",
                    $this->bin,
                    'path/to/import',
                    $this->url . '/trunk',
                    'Importing stuff'
            )),
            array('path/to/import', '', 'Importing stuff', xsprintf("%s import %xs %xs -m %xs --non-interactive",
                    $this->bin,
                    'path/to/import',
                    $this->url . '/trunk',
                    'Importing stuff'
            ))
        );
    }

    /**
     * @dataProvider logProvider
     */
    public function testLogCommandline($path, $revision, $limit, $expected)
    {
        $svn = $this->svn
            ->setMethods(array('parseLogXmlResult'))
            ->getMock()
        ;
        $svn->setCredentials($this->username, $this->password);

        $this->cli
            ->expects($this->once())
            ->method('execute')
            ->with($expected)
            ->will($this->returnValue(0))
        ;

        // disable the parsing
        $this->adapter->setParser(
                $this->getMock('Webcreate\\Vcs\\Svn\\Parser\\CliParser', array('parse'))
        );

        $result = $svn->log($path, $revision, $limit);
    }

    public function testLogParsingOfXmlResultFromSvnClient()
    {
        $svn = $this->svn
            ->getMock()
        ;

        $this->cli
            ->expects($this->any())
            ->method('getOutput')
            ->will($this->returnValue(file_get_contents(__DIR__.'/Test/Fixtures/svn_log.xml')))
        ;

        $result = $svn->log('/path/to/test');

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf('Webcreate\\Vcs\\Common\\Commit', $result);
    }

    public function logProvider()
    {
        $this->setUp();

        return array(
            array(
                $path = '/',
                null,
                null,
                xsprintf("%s log --xml %xs --non-interactive --username %xs --password %xs",
                        $this->bin,
                        $this->url . '/trunk',
                        $this->username,
                        $this->password
                )
            ),
            array(
                $path = '',
                '1234',
                null,
                xsprintf("%s log -r %xs --xml %xs --non-interactive --username %xs --password %xs",
                        $this->bin,
                        '1234',
                        $this->url . '/trunk',
                        $this->username,
                        $this->password
                )
            ),
            array(
                $path = '/test',
                '1234',
                2,
                xsprintf("%s log -r %xs --limit %xs --xml %xs --non-interactive --username %xs --password %xs",
                        $this->bin,
                        '1234',
                        '2',
                        $this->url . '/trunk' . $path,
                        $this->username,
                        $this->password
                )
            ),
        );

    }

    public function testCatCommandline()
    {
        $svn = $this->svn
            ->getMock()
        ;
        $svn->setCredentials($this->username, $this->password);

        $expected = xsprintf("%s cat %xs --non-interactive --username %xs --password %xs",
                $this->bin,
                $this->url . '/trunk/test',
                $this->username,
                $this->password
        );

        $this->cli
            ->expects($this->once())
            ->method('execute')
            ->with($expected)
            ->will($this->returnValue(0))
        ;

        $result = $svn->cat('/test');
    }

    public function testDiffSummaryCommandline()
    {
        $svn = $this->svn
            ->setMethods(array('parseDiffXmlResult'))
            ->getMock()
        ;
        $svn->setCredentials($this->username, $this->password);

        $expected = xsprintf("%s diff %xs %xs --summarize --xml --non-interactive --username %xs --password %xs",
                $this->bin,
                $this->url . '/trunk@2',
                $this->url . '/trunk@100',
                $this->username,
                $this->password
        );

        $this->cli
            ->expects($this->once())
            ->method('execute')
            ->with($expected)
            ->will($this->returnValue(0))
        ;

        // disable the parsing
        $this->adapter->setParser(
                $this->getMock('Webcreate\\Vcs\\Svn\\Parser\\CliParser', array('parse'))
        );

        $result = $svn->diff('/', '', 2, 100, true);
    }

    public function testDiffParsingOfXmlResultFromSvnClient()
    {
        $svn = $this->svn
            ->getMock()
        ;

        $this->cli
            ->expects($this->any())
            ->method('getOutput')
            ->will($this->returnValue(file_get_contents(__DIR__.'/Test/Fixtures/svn_diff_summarize.xml')))
        ;

        $result = $svn->diff('/trunk', '/trunk', 2, 100, true);

        $this->assertInternalType('array', $result);
        $this->assertCount(7, $result);
    }
}
