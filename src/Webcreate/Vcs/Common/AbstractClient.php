<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common;

use Webcreate\Vcs\Common\Adapter\AdapterInterface;

/**
 * Abstract base class for VCS clients.
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
abstract class AbstractClient
{
    /**
     * Client repository url
     *
     * @var string
     */
    protected $url;

    /**
     * Adapter for VCS backend
     *
     * @var Webcreate\Vcs\Common\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * Reference to current branch or tag
     *
     * @var Webcreate\Vcs\Common\Reference
     */
    protected $head;

    /**
     * Constructor.
     *
     * @param string           $url     Url of the repository
     * @param AdapterInterface $adapter adapter
     */
    public function __construct($url, AdapterInterface $adapter)
    {
        $this->setUrl($url);
        $this->setAdapter($adapter);
    }

    /**
     * Sets the adapter
     *
     * @param  AdapterInterface                     $adapter
     * @return \Webcreate\Vcs\Common\AbstractClient
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $adapter->setClient($this);

        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Returns the current adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Sets the VCS url
     *
     * @param  string                               $url
     * @return \Webcreate\Vcs\Common\AbstractClient
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Return the VCS url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set HEAD
     *
     * @param  array|Reference                      $reference
     * @return \Webcreate\Vcs\Common\AbstractClient
     */
    public function setHead($reference)
    {
        if ($reference instanceof Reference) {
            $this->head = $reference;
        } elseif (is_array($reference)) {
            list ($name, $type) = $reference;
            $this->head = new Reference($name, $type);
        }

        return $this;
    }

    /**
     * Get internal pointer
     *
     * @return \Webcreate\Vcs\Common\Reference
     */
    public function getHead()
    {
        return $this->head;
    }
}
