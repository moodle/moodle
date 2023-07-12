<?php

/**
 * Hook to add the modinfo module to the frontpage.
 *
 * @param array &$hookinfo  hookinfo
 * @return void
 */
function sanitycheck_hook_sanitycheck(&$hookinfo)
{
    assert(is_array($hookinfo));
    assert(array_key_exists('errors', $hookinfo));
    assert(array_key_exists('info', $hookinfo));

    $hookinfo['info'][] = '[sanitycheck] At least the sanity check itself is working :)';
}
