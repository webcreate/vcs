<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Test\Functional;

use Webcreate\Vcs\Git\Parser\CliParser;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Webcreate\Util\Cli;
use Webcreate\Vcs\Common\Adapter\CliAdapter;
use Webcreate\Vcs\Git;

class GitTest extends AbstractTest
{
    public function getClient()
    {
        $this->tmpdir = sys_get_temp_dir() . '/' . uniqid('wbcrte-git-');
        $this->vcsdir = $this->tmpdir . '/git-repos';
        $this->wcdir = $this->tmpdir . '/git-wc';

        $this->setupGit();

        $parser = new CliParser();
        $adapter = new CliAdapter('/usr/local/bin/git', new Cli(), $parser);
        $client = new Git('file:///' . $this->vcsdir, $adapter);

        return $client;
    }

    protected function setupGit()
    {
        $commandlist = array(
            sprintf('mkdir -p %s', $this->tmpdir),
            sprintf('cd %s && git init --bare %s', $this->tmpdir, basename($this->vcsdir)),
            sprintf('cd %s && git clone file:///%s %s', $this->tmpdir, $this->vcsdir, basename($this->wcdir)),
            sprintf('rsync -r --exclude=.svn %s %s', __DIR__ . '/../Fixtures/skeleton/git/', $this->wcdir),
            sprintf('cd %s && git add *', $this->wcdir),
            sprintf('cd %s && git commit -m "added skeleton"', $this->wcdir),
            sprintf('cd %s && git push origin master', $this->wcdir),

            // add a branch
            sprintf('cd %s && git branch feature1', $this->wcdir),
            sprintf('cd %s && git push origin feature1', $this->wcdir),
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
                array('README.md'),
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