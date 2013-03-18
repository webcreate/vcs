<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Svn;

use Webcreate\Vcs\Common\Status;
use Webcreate\Vcs\Exception\NotFoundException;
use Webcreate\Vcs\Svn;

/**
 * Methods that operate on a working copy (a checkout).
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
class WorkingCopy
{
    /**
     * Path of working copy
     *
     * @var string
     */
    protected $cwd;

    /**
     * Svn client
     *
     * @var \Webcreate\Vcs\Svn
     */
    protected $svn;

    /**
     * Constructor.
     *
     * @param Svn    $svn Svn client
     * @param string $cwd path to the working copy directory
     */
    public function __construct(Svn $svn, $cwd)
    {
        $this->svn = $svn;
        $this->cwd = $cwd;
    }

    /**
     * Perform a checkout
     *
     * @throws NotFoundException
     */
    public function checkout()
    {
        $this->svn->execute('checkout', array($this->svn->getSvnUrl(''), $this->cwd));
    }

    /**
     * Add file or directory to Svn
     *
     * @param  string            $path
     * @throws NotFoundException
     */
    public function add($path)
    {
        if (!file_exists($this->cwd . '/' . $path)) {
            throw new NotFoundException(sprintf('Path %s not found', $this->cwd . '/' . $path));
        }

        $result = $this->status($path);

        foreach ($result as $fileInfo) {
            if ($fileInfo->getStatus() === Status::UNVERSIONED) {
                $this->svn->execute('add', array($fileInfo->getPathname()), $this->cwd);
            }
        }
    }

    /**
     * Commit modified and/or added files to Svn
     *
     * @param string $message commit message
     * @return string
     */
    public function commit($message)
    {
        return $this->svn->execute('commit', array('-m' => $message), $this->cwd);
    }

    /**
     * Get the status of the working copy
     *
     * @param  string                              $path
     * @return \Webcreate\Vcs\Common\VcsFileInfo[]
     */
    public function status($path = null)
    {
        $args = array();
        if (null !== $path) {
            $args[] = $path;
        }

        return $this->svn->execute('status', $args, $this->cwd);
    }
}
