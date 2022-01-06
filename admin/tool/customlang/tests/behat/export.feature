@tool @tool_customlang
Feature: Within a moodle instance, an administrator should be able to export modified langstrings.
  In order to export modified langstrings in the adminsettings of the instance,
  As an admin
  I need to be able to export the php-files of the language customisation of a language.

  Background:
    # This is a very slow running feature and on slow databases can take minutes to complete.
    Given I mark this test as slow setting a timeout factor of 4

  @javascript
  Scenario: Export button should not appear if no customization is made
    Given I log in as "admin"
    And I navigate to "Language > Language customisation" in site administration
    And I set the field "lng" to "en"
    Then I should see "Open language pack for editing"
    And I should not see "Export custom strings"

  @javascript
  Scenario: Export button should not appear if no customization is saved into langpack
    Given I log in as "admin"
    And I navigate to "Language > Language customisation" in site administration
    And I set the field "lng" to "en"
    And I press "Open language pack for editing"
    And I press "Continue"
    And I set the field "Show strings of these components" to "moodle.php"
    And I set the field "String identifier" to "accept"
    And I press "Show strings"
    And I set the field "core/accept" to "Accept-custom_export"
    When I press "Apply changes and continue editing"
    And I navigate to "Language > Language customisation" in site administration
    And I set the field "lng" to "en"
    Then I should see "Open language pack for editing"
    And I should see "There are 1 modified strings."
    And I should not see "Export custom strings"

  @javascript
  Scenario: Export the php-file including a customised langstring.
    Given I log in as "admin"
    And I navigate to "Language > Language customisation" in site administration
    And I set the field "lng" to "en"
    And I press "Open language pack for editing"
    And I press "Continue"
    And I set the field "Show strings of these components" to "moodle.php"
    And I set the field "String identifier" to "accept"
    And I press "Show strings"
    And I set the field "core/accept" to "Accept-custom_export"
    When I press "Save changes to the language pack"
    And I should see "There are 1 modified strings."
    And I click on "Continue" "button"
    Then I set the field "lng" to "en"
    And I click on "Export custom strings" "button"
    And I set the field "Select component(s) to export" to "moodle.php"
