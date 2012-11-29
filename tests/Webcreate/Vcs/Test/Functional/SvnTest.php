<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Test\Functional;

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
        $this->svndir = $this->tmpdir . '/svn-repos';
        $this->wcdir = $this->tmpdir . '/svn-wc';

        $this->setupSvn();

        $parser = new CliParser();
        $adapter = new CliAdapter('/usr/local/bin/svn', new Cli(), $parser);
        $client = new Svn('file://' . $this->svndir, $adapter);

        return $client;
    }

    protected function setupSvn()
    {
        $commandlist = array(
            sprintf('mkdir -p %s', $this->tmpdir),
            sprintf('cd %s && svnadmin create %s', $this->tmpdir, basename($this->svndir)),
            sprintf('cd %s && svn checkout file:///%s %s', $this->tmpdir, $this->svndir, basename($this->wcdir)),
            sprintf('rsync -r --exclude=.svn %s %s', __DIR__ . '/../Fixtures/skeleton/svn/', $this->wcdir),
            sprintf('cd %s && svn add *', $this->wcdir),
            sprintf('cd %s && svn ci -m "added skeleton"', $this->wcdir),
            sprintf('cd %s && svn up', $this->wcdir),
        );

        foreach($commandlist as $commandline) {
            $process = new Process($commandline);
            if ($process->run() <> 0) {
                $this->markTestSkipped('Error: ' . $process->getErrorOutput());
            }
        }
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