# core_external (subsystem) Upgrade notes

## 5.3

### Deprecated

- The following legacy classes and functions have been deprecated and replaced with correctly namespaced alternatives:

   | Old class name                  | New class name                                |
   | ---                             | ---                                           |
   | `\external_api`                 | `\core_external\external_api`                 |
   | `\restricted_context_exception` | `\core_external\restricted_context_exception` |
   | `\external_description`         | `\core_external\external_description`         |
   | `\external_value`               | `\core_external\external_value`               |
   | `\external_format_value`        | `\core_external\external_format_value`        |
   | `\external_single_structure`    | `\core_external\external_single_structure`    |
   | `\external_multiple_structure`  | `\core_external\external_multiple_structure`  |
   | `\external_function_parameters` | `\core_external\external_function_parameters` |
   | `\external_util`                | `\core_external\util`                         |
   | `\external_files`               | `\core_external\external_files`               |
   | `\external_warnings`            | `\core_external\external_warnings`            |
   | `\external_settings`            | `\core_external\external_settings`            |

    | Old function name                            | New method name                                          |
    | ---                                          | ---                                                      |
    | `\external_generate_token()`                 | `\core_external\util::generate_token()`                  |
    | `\external_create_service_token()`           | `\core_external\util::generate_token()`                  |
    | `external_delete_descriptions()`             | `\core_external\util::delete_service_descriptions()`     |
    | `external_validate_format()`                 | `\core_external\util::validate_format()`                 |
    | `external_format_string()`                   | `\core_external\util::format_string()`                   |
    | `external_format_text()`                     | `\core_external\util::format_text()`                     |
    | `external_generate_token_for_current_user()` | `\core_external\util::generate_token_for_current_user()` |
    | `external_log_token_request()`               | `\core_external\util::log_token_request()`               |

  For more information see [MDL-81225](https://tracker.moodle.org/browse/MDL-81225)

## 4.5

### Changed

- The external function `core_webservice_external::get_site_info` now returns the default home page URL when needed.

  For more information see [MDL-82844](https://tracker.moodle.org/browse/MDL-82844)
