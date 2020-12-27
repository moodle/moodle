@tool @tool_customlang
Feature: Within a moodle instance, an administrator should be able to modify langstrings for the entire Moodle installation.
  In order to change langstrings in the adminsettings of the instance,
  As an admin
  I need to be able to access and change values in the the language customisation of the language pack.

  Background:
    Given I log in as "admin"
    And I navigate to "Language > Language customisation" in site administration
    And I set the field "lng" to "en"
    And I press "Open language pack for editing"
    And I press "Continue"
    And I set the field "Show strings of these components" to "moodle.php"
    And I set the field "String identifier" to "administrationsite"
    And I press "Show strings"
    And I set the field "core/administrationsite" to "Custom string example"

  @javascript
  Scenario: Edit an string but don't save it to lang pack.
    When I press "Apply changes and continue editing"
    Then I should see "Site administration" in the "page-header" "region"
    And I should not see "Custom string example" in the "page-header" "region"

  @javascript
  Scenario: Customize an string as admin and save it to lang pack.
    Given I press "Save changes to the language pack"
    And I should see "There are 1 modified strings."
    When I click on "Continue" "button"
    Then I should see "Custom string example" in the "page-header" "region"
    And I should not see "Site administration" in the "page-header" "region"
