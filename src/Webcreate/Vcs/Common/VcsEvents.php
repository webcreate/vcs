<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Webcreate\Vcs\Common;

class VcsEvents
{
    const PRE_CHECKOUT  = 'vcs.pre_checkout';
    const POST_CHECKOUT = 'vcs.post_checkout';
    const PRE_EXPORT    = 'vcs.pre_export';
    const POST_EXPORT   = 'vcs.post_export';
}
