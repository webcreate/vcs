<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Test\Functional;

use Webcreate\Vcs\Test\Util\SvnReposGenerator;

use Webcreate\Vcs\Svn\Parser\CliParser;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Webcreate\Vcs\Svn;
use Webcreate\Util\Cli;
use Webcreate\Vcs\Common\Adapter\CliAdapter;

class SvnTest extends AbstractTest
{
    public function getClient()
    {
        $this->tmpdir = sys_get_temp_dir() . '/' . uniqid('wbcrte-svn-');

        $svnReposGenerator = new SvnReposGenerator(__DIR__ . '/../Fixtures/skeleton/svn/');
        list($this->svndir, $this->wcdir) = $svnReposGenerator->generate($this->tmpdir);

        $bin = getenv('SVN_BIN') ? getenv('SVN_BIN') : '/usr/local/bin/svn';

        $parser = new CliParser();
        $adapter = new CliAdapter($bin, new Cli(), $parser);
        $client = new Svn('file://' . $this->svndir, $adapter);

        return $client;
    }

    public function existingPathProvider()
    {
        return array(
                array('Hello.txt'),
        );
    }

    public function existingSubfolderProvider()
    {
        return array(
                array('dir1'),
        );
    }

    public function tearDown()
    {
        parent::tearDown();

        $filesystem = new Filesystem();
        $filesystem->remove($this->tmpdir);
    }
}