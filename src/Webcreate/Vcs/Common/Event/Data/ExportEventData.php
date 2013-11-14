<?php

namespace Webcreate\Vcs\Common\Event\Data;

use Webcreate\Vcs\Common\Event\VcsEventData;
use Webcreate\Vcs\Common\Reference;

class ExportEventData extends VcsEventData
{
    protected $head;
    protected $path;
    protected $dest;

    /**
     * @param Reference $head
     * @param $path
     * @param $dest
     */
    public function __construct(Reference $head, $path, $dest)
    {
        $this->head = $head;
        $this->path = $path;
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

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }
}
