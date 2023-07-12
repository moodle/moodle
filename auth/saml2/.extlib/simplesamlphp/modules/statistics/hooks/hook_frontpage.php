<?php

/**
 * Hook to add the modinfo module to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 * @return void
 */
function statistics_hook_frontpage(&$links)
{
    assert(is_array($links));
    assert(array_key_exists('links', $links));

    $links['config']['statistics'] = [
        'href' => SimpleSAML\Module::getModuleURL('statistics/showstats.php'),
        'text' => '{statistics:statistics:link_statistics}',
    ];
    $links['config']['statisticsmeta'] = [
        'href' => SimpleSAML\Module::getModuleURL('statistics/statmeta.php'),
        'text' => '{statistics:statistics:link_statistics_metadata}',
        'shorttext' => ['en' => 'Statistics metadata', 'no' => 'Statistikk metadata'],
    ];
}
