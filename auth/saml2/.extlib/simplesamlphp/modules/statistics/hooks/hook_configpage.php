<?php

/**
 * Hook to add the statistics module to the config page.
 *
 * @param \SimpleSAML\XHTML\Template &$template The template that we should alter in this hook.
 * @return void
 */
function statistics_hook_configpage(\SimpleSAML\XHTML\Template &$template)
{
    $template->data['links']['statistics'] = [
        'href' => SimpleSAML\Module::getModuleURL('statistics/showstats.php'),
        'text' => \SimpleSAML\Locale\Translate::noop('Show statistics'),
    ];
    $template->data['links']['statisticsmeta'] = [
        'href' => SimpleSAML\Module::getModuleURL('statistics/statmeta.php'),
        'text' => \SimpleSAML\Locale\Translate::noop('Show statistics metadata'),
    ];
    $template->getLocalization()->addModuleDomain('statistics');
}
