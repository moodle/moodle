# core_cache (subsystem) Upgrade notes

## 4.5

### Added

- The following classes have been renamed and now support autoloading.

  Existing classes are currently unaffected.

  | Old class name                     | New class name                                     |
  | ---                                | ---                                                |
  | `\cache_definition`                | `\core_cache\definition`                           |
  | `\cache_request`                   | `\core_cache\request_cache`                        |
  | `\cache_session`                   | `\core_cache\session_cache`                        |
  | `\cache_cached_object`             | `\core_cache\cached_object`                        |
  | `\cache_config`                    | `\core_cache\config`                               |
  | `\cache_config_writer`             | `\core_cache\config_writer`                        |
  | `\cache_config_disabled`           | `\core_cache\disabled_config`                      |
  | `\cache_disabled`                  | `\core_cache\disabled_cache`                       |
  | `\config_writer`                   | `\core_cache\config_writer`                        |
  | `\cache_data_source`               | `\core_cache\data_source_interface`                |
  | `\cache_data_source_versionable`   | `\core_cache\versionable_data_source_interface`    |
  | `\cache_exception`                 | `\core_cache\exception/cache_exception`            |
  | `\cache_factory`                   | `\core_cache\factory`                              |
  | `\cache_factory_disabled`          | `\core_cache\disabled_factory`                     |
  | `\cache_helper`                    | `\core_cache\helper`                               |
  | `\cache_is_key_aware`              | `\core_cache\key_aware_cache_interface`            |
  | `\cache_is_lockable`               | `\core_cache\lockable_cache_interface`             |
  | `\cache_is_searchable`             | `\core_cache\searchable_cache_interface`           |
  | `\cache_is_configurable`           | `\core_cache\configurable_cache_interface`         |
  | `\cache_loader`                    | `\core_cache\loader_interface`                     |
  | `\cache_loader_with_locking`       | `\core_cache\loader_with_locking_interface`        |
  | `\cache_lock_interface`            | `\core_cache\cache_lock_interface`                 |
  | `\cache_store`                     | `\core_cache\store`                                |
  | `\cache_store_interface`           | `\core_cache\store_interface`                      |
  | `\cache_ttl_wrapper`               | `\core_cache\ttl_wrapper`                          |
  | `\cacheable_object`                | `\core_cache\cacheable_object_interface`           |
  | `\cacheable_object_array`          | `\core_cache\cacheable_object_array`               |
  | `\cache_definition_mappings_form`  | `\core_cache\form/cache_definition_mappings_form`  |
  | `\cache_definition_sharing_form`   | `\core_cache\form/cache_definition_sharing_form`   |
  | `\cache_lock_form`                 | `\core_cache\form/cache_lock_form`                 |
  | `\cache_mode_mappings_form`        | `\core_cache\form/cache_mode_mappings_form`        |

  For more information see [MDL-82158](https://tracker.moodle.org/browse/MDL-82158)
