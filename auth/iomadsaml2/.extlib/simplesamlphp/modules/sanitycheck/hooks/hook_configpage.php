<?php

/**
 * Hook to add the sanitycheck link to the config page.
 *
 * @param \SimpleSAML\XHTML\Template $template The template that we should alter in this hook.
 * @return void
 */
function sanitycheck_hook_configpage(\SimpleSAML\XHTML\Template &$template)
{
    $template->data['links']['sanitycheck'] = [
        'href' => SimpleSAML\Module::getModuleURL('sanitycheck/index.php'),
        'text' => \SimpleSAML\Locale\Translate::noop('Sanity check of your SimpleSAMLphp setup'),
    ];
    $template->getLocalization()->addModuleDomain('sanitycheck');
}
