<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Test\Util;

use Symfony\Component\Process\Process;

class SvnReposGenerator
{
    protected $skeletonDir;

    public function __construct($skeletonDir)
    {
        $this->skeletonDir = $skeletonDir;
    }

    public function generate($dest)
    {
        $reposdir = $dest . '/svn-repos';
        $wcdir    = $dest . '/svn-wc';

        $commandlist = array(
                sprintf('mkdir -p %s', $dest),
                sprintf('cd %s && svnadmin create %s', $dest, basename($reposdir)),
                sprintf('cd %s && svn checkout file:///%s %s', $dest, $reposdir, basename($wcdir)),
                sprintf('rsync -r --exclude=.svn %s %s', $this->skeletonDir, $wcdir),
                sprintf('cd %s && svn add *', $wcdir),
                sprintf('cd %s && svn ci -m "added skeleton"', $wcdir),
                sprintf('cd %s && svn up', $wcdir),
        );

        foreach($commandlist as $commandline) {
            $process = new Process($commandline);
            if ($process->run() <> 0) {
                throw new \PHPUnit_Framework_SkippedTestError('Error: ' . $process->getErrorOutput());
            }
        }

        return array($reposdir, $wcdir);
    }
}