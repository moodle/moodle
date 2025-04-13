# mod_data Upgrade notes

## 5.0

### Deprecated

- The following unused capabilities have been deprecated:

  * `mod/data:comment`
  * `mod/data:managecomments`

  For more information see [MDL-84267](https://tracker.moodle.org/browse/MDL-84267)

### Removed

- Final deprecation and removal of the following classes:
    - data_preset_importer
    - data_preset_existing_importer
    - data_preset_upload_importer
    - data_import_preset_zip_form

  For more information see [MDL-75189](https://tracker.moodle.org/browse/MDL-75189)
- - Final deprecation of \mod_data_renderer::import_setting_mappings(). Please use \mod_data_renderer::importing_preset() instead. - Final deprecation of data_print_template() function. Please use mod_data\manager::get_template and mod_data\template::parse_entries instead. - Final deprecation of data_preset_name(). Please use preset::get_name_from_plugin() instead. - Final deprecation of data_get_available_presets(). Please use manager::get_available_presets() instead. - Final deprecation of data_get_available_site_presets(). Please use manager::get_available_saved_presets() instead. - Final deprecation of data_delete_site_preset(). Please use preset::delete() instead. - Final deprecation of is_directory_a_preset(). Please use preset::is_directory_a_preset() instead. - Final deprecation of data_presets_save(). Please use preset::save() instead. - Final deprecation of data_presets_generate_xml(). Please use preset::generate_preset_xml() instead. - Final deprecation of data_presets_export(). Please use preset::export() instead. - Final deprecation of data_user_can_delete_preset(). Please use preset::can_manage() instead. - Final deprecation of data_view(). Please use mod_data\manager::set_module_viewed() instead.

  For more information see [MDL-75189](https://tracker.moodle.org/browse/MDL-75189)

## 4.5

### Added

- The `\data_add_record()` method accepts a new `$approved` parameter to set the corresponding state of the new record.

  For more information see [MDL-81274](https://tracker.moodle.org/browse/MDL-81274)

### Deprecated

- The `\mod_data_renderer::render_fields_footer()` method has been deprecated as it's no longer used.

  For more information see [MDL-81321](https://tracker.moodle.org/browse/MDL-81321)
