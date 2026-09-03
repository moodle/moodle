# editor_tiny Upgrade notes

## 5.3dev

### Added

- The editor follows the colour mode of the page it is on. The skin is chosen when the editor is set up, so an editor which is already open keeps the skin it started with until the page is loaded again, and the colour mode attribute is repeated on the root of the content iframe so that styles loaded through content_css can respond to it.
  A plugin which styles the editor content, or its own dialogs, should take its colours the same way any other plugin does rather than assuming a light editor. Icons supplied to the toolbar are referenced by URL inside an <image> element, so they are an isolated document that neither currentColor nor a fill from the page stylesheet can reach; a monochrome icon drawn in a dark colour is lightened for dark mode by a filter on the svg[data-buttonsource="moodle"] wrapper.

  For more information see [MDL-68037](https://tracker.moodle.org/browse/MDL-68037)
- The Accordion and List Styles (advlist) bundled TinyMCE plugins have been enabled by default. HTMLPurifier now also allows the HTML5 <details> and <summary> elements, and the lower-greek CSS list-style-type value, so content produced by these plugins is preserved when saved.

  For more information see [MDL-88618](https://tracker.moodle.org/browse/MDL-88618)

## 5.0

### Added

- New external function `editor_tiny_get_configuration`.
  TinyMCE subplugins can provide configuration to the new external function by implementing the `plugin_with_configuration_for_external` interface and/or overriding the `is_enabled_for_external` method.

  For more information see [MDL-84353](https://tracker.moodle.org/browse/MDL-84353)

## 4.5

### Changed

- The `helplinktext` language string is no longer required by editor plugins, instead the `pluginname` will be used in the help dialogue.

  For more information see [MDL-81572](https://tracker.moodle.org/browse/MDL-81572)
