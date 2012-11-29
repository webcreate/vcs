<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs;

use Webcreate\Vcs\Common\FileInfo;
use Webcreate\Vcs\Common\Status;
use Webcreate\Vcs\Exception\NotFoundException;
use Webcreate\Vcs\Common\Commit;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webcreate\Vcs\Git\AbstractGit;
use Webcreate\Vcs\VcsInterface;

/**
 * Git client
 *
 * Note: This class should only contain methods that are also in the
 * VcsInterface. Other methods should go into the AbstractGit class.
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
class Git extends AbstractGit implements VcsInterface
{
    const PRETTY_FORMAT = '<logentry><commit>%H</commit>%n<date>%aD</date>%n<author>%an</author>%n<msg><![CDATA[%B]]></msg></logentry>';

    /**
     * Perform a clone operation
     *
     * @param string|null $dest
     */
    public function checkout($dest = null)
    {
        if (true === is_null($dest)) {
            $realdest = $this->cwd;
        }
        else {
            $realdest = $dest;
        }

        $result = $this->execute('clone', array('-b' => (string) $this->getPointer(), $this->url, $realdest));

        chdir($realdest);

        $this->setCwd($realdest);

        if (true === is_null($dest)) {
            $this->isTemporary = true;
        }
    }

    /**
     * @param string $path
     * @throws \RuntimeException
     * @return string
     */
    public function add($path)
    {
        if (!$this->hasCheckout) {
            throw new \RuntimeException('This operation requires an active checkout.');
        }

        return $this->execute('add', array($path));
    }

    /**
     * @param string $message
     * @throws \RuntimeException
     * @return string
     */
    public function commit($message)
    {
        if (!$this->hasCheckout) {
            throw new \RuntimeException('This operation requires an active checkout.');
        }

        return $this->execute('commit', array('-m' => $message));
    }

    /**
     * @param string $message
     * @throws \RuntimeException
     * @return string
     */
    public function status($path = null)
    {
        if (!$this->hasCheckout) {
            throw new \RuntimeException('This operation requires an active checkout.');
        }

        $args = array('--porcelain' => true);
        if (null !== $path) {
            $args[] = $path;
        }

        return $this->execute('status', $args);
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs.VcsInterface::import()
     */
    public function import($src, $path, $message)
    {
        if (!$this->hasCheckout) {
            $this->checkout();
        }

        $filesystem = new Filesystem();
        $filesystem->mirror($src, $this->cwd . $path);

        $test = $this->add('*');
        $test = $this->commit($message);
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs.VcsInterface::export()
     */
    public function export($path, $dest)
    {
        $this->setCwd($dest);
        $this->checkout();

        $filesystem = new Filesystem();
        $filesystem->remove($dest . '/.git');
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs.VcsInterface::ls()
     */
    public function ls($path)
    {
        if (!$this->hasCheckout) {
            $this->checkout();
        }

        if ($path == '/') {
            $path = '';
        }

        if (false === file_exists($this->cwd . '/' . $path)) {
            throw new NotFoundException(sprintf('The path \'%s\' is not found in %s', $path, $this->cwd));
        }

        $finder = new Finder();
        $files = $finder->in($this->cwd . '/' . $path)->exclude(".git")->ignoreDotFiles(false)->depth('== 0');

        $filelist = array();
        $entries = array();
        foreach($files as $file) {
            $log = $this->log(($path ? $path . '/' : '') . $file->getRelativePathname(), null, 1);

            $commit = reset($log);
            $kind = $file->isDir() ? FileInfo::DIR : FileInfo::FILE;
            $file = new FileInfo($file->getFilename(), $kind, $commit);

            $entries[] = $file;
        }

        return $entries;
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs.VcsInterface::log()
     * @todo finish implementation for revision parameter
     */
    public function log($path, $revision = null, $limit = null)
    {
        if (!$this->hasCheckout) {
            $this->checkout();
        }

        return $this->execute('log', array(
//                 '-r' => $revision ? $revision : false,
                '-n' => $limit ? $limit : false,
                '--pretty=' => self::PRETTY_FORMAT,
                $path
                )
        );
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs.VcsInterface::cat()
     */
    public function cat($path)
    {
        if (!$this->hasCheckout) {
            $this->checkout();
        }

        if (false === file_exists($this->cwd . '/' . $path)) {
            throw new NotFoundException(sprintf('The path \'%s\' is not found', $path));
        }

        return file_get_contents($this->cwd . '/' . $path);
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs.VcsInterface::diff()
     */
    public function diff($oldPath, $newPath, $oldRevision = 'HEAD',
            $newRevision = 'HEAD', $summary = true)
    {
        return $this->execute('diff', array(
                '--name-status' => $summary,
                $oldRevision,
                $newRevision,
                $oldPath,
                $newPath
        ));
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs.VcsInterface::branches()
     */
    public function branches()
    {
        $retval = $this->execute('ls-remote', array('--heads' => true, $this->getUrl()));

        $list = explode("\n", rtrim($retval));

        $branches = array();
        foreach($list as $line) {
            list ($hash, $ref) = explode("\t", $line);
            $branches[] = basename($ref);
        }

        return $branches;
    }

    /**
     * (non-PHPdoc)
     * @see Webcreate\Vcs.VcsInterface::tags()
     */
    public function tags()
    {
        $retval = $this->execute('ls-remote', array('--tags' => true, $this->getUrl()));

        if ('' === $retval) {
            return array();
        }

        $list = explode("\n", rtrim($retval));

        $tags = array();
        foreach($list as $line) {
            list ($hash, $ref) = explode("\t", $line);
            $tags[] = basename($ref);
        }

        return $tags;
    }
}
