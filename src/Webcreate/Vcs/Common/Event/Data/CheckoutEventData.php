<?php

namespace Webcreate\Vcs\Common\Event\Data;

use Webcreate\Vcs\Common\Event\VcsEventData;
use Webcreate\Vcs\Common\Reference;

class CheckoutEventData extends VcsEventData
{
    protected $head;
    protected $dest;

    /**
     * @param Reference $head
     * @param $dest
     */
    public function __construct(Reference $head, $dest)
    {
        $this->head = $head;
        $this->dest = $dest;
    }

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->dest;
    }

    /**
     * @return Reference
     */
    public function getHead()
    {
        return $this->head;
    }
}
