<?php

/**
 * Hook to add the cron module to the config page.
 *
 * @param \SimpleSAML\XHTML\Template &$template The template that we should alter in this hook.
 * @return void
 */
function cron_hook_configpage(\SimpleSAML\XHTML\Template &$template)
{
    $template->data['links']['cron'] = [
        'href' => SimpleSAML\Module::getModuleURL('cron/croninfo.php'),
        'text' => \SimpleSAML\Locale\Translate::noop('Cron module information page'),
    ];
    $template->getLocalization()->addModuleDomain('cron');
}
