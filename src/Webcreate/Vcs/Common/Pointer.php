<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common;

class Pointer
{
    const TYPE_BRANCH = 'branch';
    const TYPE_TAG    = 'tag';

    protected $name;
    protected $type;

    public function __construct($name, $type = self::TYPE_BRANCH)
    {
        $this->setName($name);
        $this->setType($type);
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}