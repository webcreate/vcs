<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Generic VCS event
 */
class VcsEvent extends Event
{
    protected $data;

    public function __construct(array $data = null)
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
