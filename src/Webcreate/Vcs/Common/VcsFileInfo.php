<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common;

class VcsFileInfo
{
    const BRANCH = 'branch';
    const TAG    = 'tag';

    const FILE = 'file';
    const DIR  = 'dir';

    protected $filename;
    protected $kind;
    protected $reference;
    protected $commit;
    protected $status;

    /**
     * Constructor.
     *
     * @param string          $filename  path to the file, for ex.: "path/to/file.txt"
     * @param array|Reference $reference
     * @param string          $kind      VcsFileInfo::FILE or VcsFileInfo::DIR
     */
    public function __construct($filename, $reference, $kind = self::FILE)
    {
        $this->filename  = $filename;
        $this->kind      = $kind;

        if (!$reference instanceof Reference) {
            list($name, $type) = $reference;
            $reference = new Reference($name, $type);
        }
        $this->reference = $reference;
    }

    public function setCommit($commit)
    {
        $this->commit = $commit;

        return $this;
    }

    public function getCommit()
    {
        return $this->commit;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function inBranch()
    {
        return $this->reference->getType() === self::BRANCH;
    }

    public function inTag()
    {
        return $this->reference->getType() === self::TAG;
    }

    public function getFilename()
    {
        return basename($this->filename);
    }

    public function getPathname()
    {
        return $this->filename;
    }

    public function isFile()
    {
        return $this->kind === self::FILE;
    }

    public function isDir()
    {
        return $this->kind === self::DIR;
    }

    public function getReferenceName()
    {
        return $this->reference->getName();
    }

    public function __toString()
    {
        return $this->filename;
    }
}
