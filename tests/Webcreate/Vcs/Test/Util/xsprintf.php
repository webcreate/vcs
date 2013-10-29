<?php

/**
 * Special sprintf function for window/unix-compatible quote replacements
 *
 * "%xs" will be replaced by "%s" wrapped in platform-specific quotes and
 * passed through sprintf.
 *
 * @return mixed
 */
function xsprintf()
{
    $args = func_get_args();
    $format = array_shift($args);

    $quote = defined('PHP_WINDOWS_VERSION_MAJOR') ? '"' : '\'';

    $format = str_replace('%xs', $quote . '%s' . $quote, $format);

    array_unshift($args, $format);

    return call_user_func_array('sprintf', $args);
}
