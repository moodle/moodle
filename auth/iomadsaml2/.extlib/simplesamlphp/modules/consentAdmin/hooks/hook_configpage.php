<?php
/**
 * Hook to add the consentAdmin module to the config page.
 *
 * @param \SimpleSAML\XHTML\Template $template The template that we should alter in this hook.
 * @return void
 */
function consentAdmin_hook_configpage(\SimpleSAML\XHTML\Template &$template)
{
    $template->data['links']['consentAdmin'] = [
        'href' => SimpleSAML\Module::getModuleURL('consentAdmin/consentAdmin.php'),
        'text' => \SimpleSAML\Locale\Translate::noop('Consent administration'),
    ];
    $template->getLocalization()->addModuleDomain('consentAdmin');
}
