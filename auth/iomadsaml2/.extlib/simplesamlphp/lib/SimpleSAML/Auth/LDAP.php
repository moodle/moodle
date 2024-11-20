<?php

declare(strict_types=1);

namespace SimpleSAML\Auth;

\SimpleSAML\Logger::warning("The class \SimpleSAML\Auth\LDAP has been moved to the ldap module, please use \SimpleSAML\Module\ldap\Auth\Ldap instead.");

/**
 * @deprecated To be removed in 2.0
 */
if (class_exists('\SimpleSAML\Module\ldap\Auth\Ldap')) {
    class_alias(\SimpleSAML\Module\ldap\Auth\Ldap::class, 'SimpleSAML\Auth\LDAP');
} else {
    throw new \Exception('The ldap module is either missing or disabled');
}
