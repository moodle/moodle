@tool @tool_customlang @_file_upload
Feature: Within a moodle instance, an administrator should be able to import modified langstrings.
  In order to import modified langstrings in the adminsettings from one to another instance,
  As an admin
  I need to be able to import the zips and php files of the language customisation of a language.

  Background:
    # This is a very slow running test and on slow databases can take minutes to complete.
    Given I mark this test as slow setting a timeout factor of 4

    And I log in as "admin"
    And I navigate to "Language > Language customisation" in site administration
    And I set the field "lng" to "en"
    And I click on "Import custom strings" "button"
    And I press "Continue"

  @javascript
  Scenario: Import a PHP file to add a new core lang customization
    When I upload "admin/tool/customlang/tests/fixtures/tool_customlang.php" file to "Language component(s)" filemanager
    And I press "Import file"
    Then I should see "String tool_customlang/pluginname updated successfully."
    And I should see "String tool_customlang/nonexistentinvetedstring not found."
    And I click on "Continue" "button"
    And I should see "There are 1 modified strings."
    And I click on "Save strings to language pack" "button"
    And I click on "Continue" "button"
    And I should see "An amazing import feature" in the "page-header" "region"

  @javascript
  Scenario: Try to import a PHP file from a non existent component
    When I upload "admin/tool/customlang/tests/fixtures/mod_fakecomponent.php" file to "Language component(s)" filemanager
    And I press "Import file"
    Then I should see "Missing component mod_fakecomponent."

  @javascript
  Scenario: Import a zip file with some PHP files in it.
    When I upload "admin/tool/customlang/tests/fixtures/customlang.zip" file to "Language component(s)" filemanager
    And I press "Import file"
    Then I should see "String core/administrationsite updated successfully."
    And I should see "String core/language updated successfully."
    And I should see "String core/nonexistentinvetedstring not found."
    And I should see "String tool_customlang/pluginname updated successfully."
    And I should see "String tool_customlang/nonexistentinvetedstring not found."
    And I should see "Missing component mod_fakecomponent."
    And I click on "Continue" "button"
    And I should see "There are 3 modified strings."
    And I click on "Save strings to language pack" "button"
    And I click on "Continue" "button"
    And I should see "Uploaded custom string" in the "page-header" "region"
    And I should see "Another Uploaded string" in the "page-header" "region"
    And I should see "An amazing import feature" in the "page-header" "region"
