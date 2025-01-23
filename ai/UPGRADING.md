# core_ai (subsystem) Upgrade notes

## 5.0dev

### Deprecated

- The ai_provider_management_table has been refactored to inherit from flexible_table instead of plugin_management_table. As a result the methods get_plugintype and get_action_url are now unused and have been deprecated in the class.

  For more information see [MDL-82922](https://tracker.moodle.org/browse/MDL-82922)

