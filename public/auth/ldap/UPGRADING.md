# auth_ldap Upgrade notes

## 5.2dev

### Removed

- - The following methods have been removed from `auth/ldap/lib.php`:
    - `\auth_plugin_ldap::auth_plugin_ldap()`
    - `\auth_plugin_ldap::iscreator()`
  - The `public/auth/ldap/cli/sync_users.php` file has been removed.

  For more information see [MDL-87426](https://tracker.moodle.org/browse/MDL-87426)

