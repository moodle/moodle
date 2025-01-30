# Mock plugin: 'fake_fullfeatured'
A mock plugin, useful for testing various lower level core_component-related APIs, without coupling those tests to existing shipped plugins.

This is primarily used to test:
* core_component::xxx
* core_plugin_manager::xxx
* higher level Moodlelib APIs which use the below (e.g. methods dealing with callbacks, strings, etc.)

Can be extended with more core features (implementing more APIs, etc.) as needed.

Must be injected into core_compoent to be used.

## Features
* Supports the following subplugin types (optional):
  * fullsubtype
  * fulldeprecatedsubtype
* Provides the following subplugins (optional):
  * fullsubtype_example
  * fulldeprecatedsubtype_test

## Usage in phpunit
**Important:** Using this mock in the following way in tests causes a core_component cache rebuild, can impact other tests, and are slow! Use sparingly and always tag the unit test with ```@runInSeparateProcess```.

### Injecting the plugin type into core_component:
This is done at a low level and will cause core_component to rebuild, which will impact other tests.

    $this->add_full_mocked_plugintype(
        plugintype: 'fake',
        path: 'lib/tests/fixtures/fakeplugins/fake',
    );

If you want to add subplugin support (this will inject the plugin type 'fake' into the plugin types supporting subplugins, which then permits the loading of any plugins under *fullsubtype/* and *fulldeprecatedsubtype/*):

    $this->add_full_mocked_plugintype(
        plugintype: 'fake',
        path: 'lib/tests/fixtures/fakeplugins/fake',
        subpluginsupport: true
    );


