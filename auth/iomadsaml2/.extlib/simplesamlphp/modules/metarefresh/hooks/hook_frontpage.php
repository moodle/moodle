<?php

/**
 * Hook to add links to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 * @return void
 */
function metarefresh_hook_frontpage(&$links)
{
    assert(is_array($links));
    assert(array_key_exists('links', $links));

    $links['federation'][] = [
        'href' => SimpleSAML\Module::getModuleURL('metarefresh/fetch.php'),
        'text' => '{metarefresh:metarefresh:frontpage_link}',
    ];
}
