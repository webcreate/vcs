<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Test\Util;

use Symfony\Component\Process\Process;

class GitReposGenerator
{
    protected $skeletonDir;

    public function __construct($skeletonDir)
    {
        $this->skeletonDir = $skeletonDir;
    }

    public function generate($dest)
    {
        $reposdir = $dest . '/git-repos';
        $wcdir    = $dest . '/git-wc';

        $commandlist = array(
            sprintf('mkdir -p %s', $dest),
            sprintf('cd %s && git init --bare %s', $dest, basename($reposdir)),
            sprintf('cd %s && git clone file:///%s %s', $dest, $reposdir, basename($wcdir)),
            sprintf('rsync -r --exclude=.svn %s %s', $this->skeletonDir, $wcdir),
            sprintf('cd %s && git add *', $wcdir),
            sprintf('cd %s && git commit -m "added skeleton"', $wcdir),
            sprintf('cd %s && git push origin master', $wcdir),

            // add a branch
            sprintf('cd %s && git branch feature1', $wcdir),
            sprintf('cd %s && git push origin feature1', $wcdir),
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