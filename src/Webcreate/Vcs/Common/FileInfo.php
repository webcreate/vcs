<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common;

/**
 * Model for holding VCS file information
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
class FileInfo
{
    const DIR  = 'dir';
    const FILE = 'file';

    protected $name;
    protected $kind;
    protected $commit;
    protected $status;

    /**
     * Constructor.
     *
     * @param string                       $name   filename
     * @param FileInfo::DIR|FileInfo::FILE $kind
     * @param Commit                       $commit
     * @param string                       $status
     */
    public function __construct($name, $kind, Commit $commit = null, $status = null)
    {
        $this->setName($name);
        $this->setKind($kind);

        if (null !== $commit) {
            $this->setCommit($commit);
        }
        $this->setStatus($status);
    }

    /**
     * Return filename
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set filename
     *
     * @param string $name
     * @return Webcreate\Vcs\Common\FileInfo
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDir()
    {
        return $this->kind === self::DIR;
    }

    /**
     * @return boolean
     */
    public function isFile()
    {
        return $this->kind === self::FILE;
    }

    /**
     * Set kind
     *
     * @param FileInfo::DIR|FileInfo::FILE $kind
     * @return \Webcreate\Vcs\Common\FileInfo
     */
    public function setKind($kind)
    {
        $this->kind = $kind;
        return $this;
    }

    /**
     * Return commit info
     *
     * @return Commit
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * Set commit info
     *
     * @param Commit $commit
     * @return \Webcreate\Vcs\Common\FileInfo
     */
    public function setCommit(Commit $commit)
    {
        $this->commit = $commit;
        return $this;
    }

    /**
     * Return status info
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status info
     *
     * @param string $status
     * @return \Webcreate\Vcs\Common\FileInfo
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}