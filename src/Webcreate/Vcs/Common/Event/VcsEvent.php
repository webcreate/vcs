<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Generic VCS event
 */
class VcsEvent extends Event
{
    protected $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
