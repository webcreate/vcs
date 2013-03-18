<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common;

/**
 * Model for holding commit information
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
class Commit
{
    protected $revision;
    protected $date;
    protected $author;
    protected $message;

    /**
     * Constructor.
     *
     * @param string    $revision
     * @param \DateTime $date
     * @param string    $author
     * @param string    $message
     */
    public function __construct($revision, \DateTime $date, $author, $message = null)
    {
        $this->setRevision($revision);
        $this->setDate($date);
        $this->setAuthor($author);
        $this->setMessage($message);
    }

    /**
     * Return commit message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set commit message
     *
     * @param  string                       $message
     * @return \Webcreate\Vcs\Common\Commit
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Return author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set author
     *
     * @param  string                       $author
     * @return \Webcreate\Vcs\Common\Commit
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Return commit date
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set commit date
     *
     * @param  \DateTime                    $date
     * @return \Webcreate\Vcs\Common\Commit
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Return revision number/ID
     *
     * @return string
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Set revision number/ID
     *
     * @param  string                       $revision
     * @return \Webcreate\Vcs\Common\Commit
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }
}
