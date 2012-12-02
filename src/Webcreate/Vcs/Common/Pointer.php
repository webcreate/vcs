<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common;

/**
 * Model for holding information about the current
 * branch or tag.
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
class Pointer
{
    const TYPE_BRANCH = 'branch';
    const TYPE_TAG    = 'tag';

    protected $name;
    protected $type;

    /**
     * Constructor.
     *
     * @param string                                 $name name of the branch or tag
     * @param Pointer::TYPE_BRANCH|Pointer::TYPE_TAG $type
     */
    public function __construct($name, $type = self::TYPE_BRANCH)
    {
        $this->setName($name);
        $this->setType($type);
    }

    /**
     * Return the type
     *
     * @return Pointer::TYPE_BRANCH|Pointer::TYPE_TAG
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param Pointer::TYPE_BRANCH|Pointer::TYPE_TAG $type
     * @return \Webcreate\Vcs\Common\Pointer
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the branch or tag
     *
     * @param string $name name of the branch or tag
     * @return \Webcreate\Vcs\Common\Pointer
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}