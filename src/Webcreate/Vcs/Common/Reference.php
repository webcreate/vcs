<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common;

class Reference
{
    const BRANCH = 'branch';
    const TAG    = 'tag';

    protected $name;
    protected $type;
    protected $revision;

    public function __construct($name, $type = self::BRANCH, $revision = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->revision = $revision;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getRevision()
    {
        return $this->revision;
    }
}
