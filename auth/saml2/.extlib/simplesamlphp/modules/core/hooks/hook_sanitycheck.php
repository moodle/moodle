<?php

declare(strict_types=1);

/**
 * Hook to do sanitycheck
 *
 * @param array &$hookinfo  hookinfo
 * @return void
 */
function core_hook_sanitycheck(&$hookinfo)
{
    assert(is_array($hookinfo));
    assert(array_key_exists('errors', $hookinfo));
    assert(array_key_exists('info', $hookinfo));

    $config = \SimpleSAML\Configuration::getInstance();

    if ($config->getString('auth.adminpassword', '123') === '123') {
        $hookinfo['errors'][] = '[core] Password in config.php is not set properly';
    } else {
        $hookinfo['info'][] = '[core] Password in config.php is set properly';
    }

    if ($config->getString('technicalcontact_email', 'na@example.org') === 'na@example.org') {
        $hookinfo['errors'][] = '[core] In config.php technicalcontact_email is not set properly';
    } else {
        $hookinfo['info'][] = '[core] In config.php technicalcontact_email is set properly';
    }

    if (version_compare(phpversion(), '7.1', '>=')) {
        $hookinfo['info'][] = '[core] You are running a PHP version suitable for SimpleSAMLphp.';
    } else {
        $hookinfo['errors'][] = '[core] You are running an old PHP installation. ' .
            'Please check the requirements for your SimpleSAMLphp version and upgrade.';
    }

    $info = [];
    $mihookinfo = [
        'info' => &$info,
    ];
    $availmodules = SimpleSAML\Module::getModules();
    SimpleSAML\Module::callHooks('moduleinfo', $mihookinfo);
    foreach ($info as $mi => $i) {
        if (isset($i['dependencies']) && is_array($i['dependencies'])) {
            foreach ($i['dependencies'] as $dep) {
                if (!in_array($dep, $availmodules, true)) {
                    $hookinfo['errors'][] = '[core] Module dependency not met: ' . $mi . ' requires ' . $dep;
                }
            }
        }
    }
}
