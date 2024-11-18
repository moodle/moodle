<?php

/**
 * Hook to add the consentAdmin module to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 * @return void
 */
function consentAdmin_hook_frontpage(&$links)
{
    assert(is_array($links));
    assert(array_key_exists('links', $links));

    $links['config'][] = [
        'href' => SimpleSAML\Module::getModuleURL('consentAdmin/consentAdmin.php'),
        'text' => '{consentAdmin:consentadmin:link_consentAdmin}',
    ];
}

