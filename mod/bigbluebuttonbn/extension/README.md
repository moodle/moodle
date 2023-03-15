## BigBlueButtonBN extension subplugins

The plugins are made to extend existing BigBlueButtonBN behaviour. For now, we have one extension point using as base classes that should be implemented.
* mod_instance_helper : inherit this class so all methods will be called when we either add/delete/or update a module instance.
The extension classes should be placed in your plugin with exactly the same name but in a different namespace for example 
* **\\bbbext_<YOUREXTENSION>\\bigbluebuttonbn\\mod_instance_helper** to extend hooks from the mod_instance_helper class.


Some examples are provided in the tests/fixtures/simple folder.
