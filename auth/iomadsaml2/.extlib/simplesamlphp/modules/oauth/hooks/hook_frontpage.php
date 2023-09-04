<?php

/**
 * Hook to add link to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 * @return void
 */
function oauth_hook_frontpage(&$links)
{
    assert(is_array($links));
    assert(array_key_exists('links', $links));

    $links['federation']['oauthregistry'] = [
        'href' => SimpleSAML\Module::getModuleURL('oauth/registry.php'),
        'text' => '{core:frontpage:link_oauth}',
    ];
}
