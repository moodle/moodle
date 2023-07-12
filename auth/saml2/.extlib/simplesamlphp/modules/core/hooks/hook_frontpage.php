<?php

declare(strict_types=1);

/**
 * Hook to add the modinfo module to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 * @return void
 */
function core_hook_frontpage(&$links)
{
    assert(is_array($links));
    assert(array_key_exists('links', $links));

    $links['links']['frontpage_welcome'] = [
        'href' => SimpleSAML\Module::getModuleURL('core/frontpage_welcome.php'),
        'text' => '{core:frontpage:welcome}',
        'shorttext' => '{core:frontpage:welcome}',
    ];
    $links['links']['frontpage_config'] = [
        'href' => SimpleSAML\Module::getModuleURL('core/frontpage_config.php'),
        'text' => '{core:frontpage:configuration}',
        'shorttext' => '{core:frontpage:configuration}',
    ];
    $links['links']['frontpage_auth'] = [
        'href' => SimpleSAML\Module::getModuleURL('core/frontpage_auth.php'),
        'text' => '{core:frontpage:auth}',
        'shorttext' => '{core:frontpage:auth}',
    ];
    $links['links']['frontpage_federation'] = [
        'href' => SimpleSAML\Module::getModuleURL('core/frontpage_federation.php'),
        'text' => '{core:frontpage:federation}',
        'shorttext' => '{core:frontpage:federation}',
    ];
}
