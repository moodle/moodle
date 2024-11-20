# Testing
You can run the tests by running `./vendor/bin/phpunit` in your terminal from inside the plugin directory. To change the tests, take a look in [the `tests` directory of the plugin](../tests), you'll find that it's structured very similarly to [the `src/transformer` directory](../src/transformer). You should also notice that each test is made up of 4 files:

- [The `test.php` file](../tests/all/course_module_viewed/test.php) is used to run the test and specifies the directory containing the files listed below.
- [The `data.json` file](../tests/all/course_module_viewed/data.json) is used to mock the Moodle database for the test.
- [The `event.json` file](../tests/all/course_module_viewed/event.json) is used to mock the logstore event for the test.
- [The `statements.json` file](../tests/all/course_module_viewed/statements.json) is the expected output of the transformer during the test.
