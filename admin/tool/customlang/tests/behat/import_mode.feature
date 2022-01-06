@tool @tool_customlang @_file_upload
Feature: Within a moodle instance, an administrator should be able to import langstrings with several modes.
  In order to import modified langstrings in the adminsettings from one to another instance,
  As an admin
  I need to be able to import only some language customisation strings depending on some conditions.

  Background:
    # This is a very slow running feature and on slow databases can take minutes to complete.
    Given I mark this test as slow setting a timeout factor of 4

    # Add one customization.
    And I log in as "admin"
    And I navigate to "Language > Language customisation" in site administration
    And I set the field "lng" to "en"
    And I press "Open language pack for editing"
    And I press "Continue"
    And I set the field "Show strings of these components" to "moodle.php"
    And I set the field "String identifier" to "administrationsite"
    And I press "Show strings"
    And I set the field "core/administrationsite" to "Custom string example"
    And I press "Save changes to the language pack"
    And I should see "There are 1 modified strings."
    And I click on "Continue" "button"
    And I should see "Custom string example" in the "page-header" "region"

  @javascript
  Scenario: Update only customized strings
    When I set the field "lng" to "en"
    And I click on "Import custom strings" "button"
    And I press "Continue"
    And I upload "admin/tool/customlang/tests/fixtures/moodle.php" file to "Language component(s)" filemanager
    And I set the field "Import mode" to "Update only strings with local customisation"
    And I press "Import file"
    Then I should see "String core/administrationsite updated successfully."
    And I should see "Ignoring string core/language because it is not customised."
    And I should see "String core/nonexistentinvetedstring not found."
    And I click on "Continue" "button"
    And I should see "There are 1 modified strings."
    And I should not see "Uploaded custom string" in the "page-header" "region"
    And I click on "Save strings to language pack" "button"
    And I click on "Continue" "button"
    And I should not see "Custom string example" in the "page-header" "region"
    And I should see "Uploaded custom string" in the "page-header" "region"
    And I should not see "Another Uploaded string" in the "page-header" "region"

  @javascript
  Scenario: Create only new strings
    When I set the field "lng" to "en"
    And I click on "Import custom strings" "button"
    And I press "Continue"
    And I upload "admin/tool/customlang/tests/fixtures/moodle.php" file to "Language component(s)" filemanager
    And I set the field "Import mode" to "Create only strings without local customisation"
    And I press "Import file"
    Then I should see "Ignoring string core/administrationsite because it is already defined."
    And I should see "String core/language updated successfully."
    And I should see "String core/nonexistentinvetedstring not found."
    And I click on "Continue" "button"
    And I should see "There are 1 modified strings."
    And I should not see "Uploaded custom string" in the "page-header" "region"
    And I click on "Save strings to language pack" "button"
    And I click on "Continue" "button"
    And I should see "Custom string example" in the "page-header" "region"
    And I should not see "Uploaded custom string" in the "page-header" "region"
    And I should see "Another Uploaded string" in the "page-header" "region"

  @javascript
  Scenario: Import all strings
    When I set the field "lng" to "en"
    And I click on "Import custom strings" "button"
    And I press "Continue"
    And I upload "admin/tool/customlang/tests/fixtures/moodle.php" file to "Language component(s)" filemanager
    And I set the field "Import mode" to "Create or update all strings from the component(s)"
    And I press "Import file"
    Then I should see "String core/administrationsite updated successfully."
    And I should see "String core/language updated successfully."
    And I should see "String core/nonexistentinvetedstring not found."
    And I click on "Continue" "button"
    And I should see "There are 2 modified strings."
    And I should not see "Uploaded custom string" in the "page-header" "region"
    And I click on "Save strings to language pack" "button"
    And I click on "Continue" "button"
    And I should not see "Custom string example" in the "page-header" "region"
    And I should see "Uploaded custom string" in the "page-header" "region"
    And I should see "Another Uploaded string" in the "page-header" "region"
