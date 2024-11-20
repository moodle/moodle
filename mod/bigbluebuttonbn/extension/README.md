## BigBlueButtonBN extension subplugins

The plugins are made to extend existing BigBlueButtonBN behaviour. For now, we have two extensions points using as base classes that should be implemented.
* action_url_addons: inherit this class and redefine the execute method so add new parameter when we send an action url to the BigBlueButton server.
* mod_instance_helper : inherit this class so all methods will be called when we either add/delete/or update a module instance.
  The extension classes should be placed in your plugin with exactly the same name but in a different namespace for example
* **\\bbbext_<YOUREXTENSION>\\bigbluebuttonbn\\mod_instance_helper** to extend hooks from the mod_instance_helper class.


Some examples are provided in the tests/fixtures/simple folder.
