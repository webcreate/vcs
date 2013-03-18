<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common;

/**
 * Model for status information
 *
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 */
class Status
{
    const MODIFIED    = 'M';
    const ADDED       = 'A';
    const UNVERSIONED = '?';
    const DELETED     = 'D';
    const UNMODIFIED  = ' ';
}
