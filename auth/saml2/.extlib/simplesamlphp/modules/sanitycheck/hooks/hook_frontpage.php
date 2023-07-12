<?php

/**
 * Hook to add the modinfo module to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 * @return void
 */
function sanitycheck_hook_frontpage(&$links)
{
    assert(is_array($links));
    assert(array_key_exists('links', $links));

    $links['config']['sanitycheck'] = [
        'href' => SimpleSAML\Module::getModuleURL('sanitycheck/index.php'),
        'text' => '{sanitycheck:strings:link_sanitycheck}',
    ];
}

