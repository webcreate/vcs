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
    protected $cwd;
    protected $svn;

    public function __construct(Svn $svn, $cwd)
    {
        $this->svn = $svn;
        $this->cwd = $cwd;
    }

    public function checkout()
    {
        $this->svn->execute('checkout', array($this->svn->getSvnUrl(''), $this->cwd));
    }

    public function add($path)
    {
        if (!file_exists($this->cwd . '/' . $path)) {
            throw new NotFoundException(sprintf('Path %s not found', $this->cwd . '/' . $path));
        }

        $this->chdir();

        $result = $this->status($path);

        foreach($result as $fileInfo) {
            if ($fileInfo->getStatus() === Status::UNVERSIONED) {
                $this->svn->execute('add', array($fileInfo->getName()));
            }
        }
    }

    public function commit($message)
    {
        $this->chdir();

        return $this->svn->execute('commit', array('-m' => $message));
    }

    public function status($path = null)
    {
        $this->chdir();

        $args = array();
        if (null !== $path) {
            $args[] = $path;
        }

        return $this->svn->execute('status', $args);
    }

    protected function chdir()
    {
        if (getcwd() !== $this->cwd) {
            chdir($this->cwd);
        }
    }
}