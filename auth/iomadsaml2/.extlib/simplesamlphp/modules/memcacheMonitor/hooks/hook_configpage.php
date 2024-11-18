<?php

/**
 * Hook to add the memcacheMonitor module to the config page.
 *
 * @param \SimpleSAML\XHTML\Template &$template The template that we should alter in this hook.
 * @return void
 */
function memcacheMonitor_hook_configpage(\SimpleSAML\XHTML\Template &$template)
{
    $template->data['links']['memcacheMonitor'] = [
        'href' => SimpleSAML\Module::getModuleURL('memcacheMonitor/memcachestat.php'),
        'text' => \SimpleSAML\Locale\Translate::noop('Memcache statistics'),
    ];
    $template->getLocalization()->addModuleDomain('memcacheMonitor');
}
