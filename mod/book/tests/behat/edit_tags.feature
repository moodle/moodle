@mod @mod_book @core_tag @javascript
Feature: Edited book chapters handle tags correctly
  In order to get book chapters properly labelled
  As a user
  I need to introduce the tags while editing

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "activity" exists:
      | activity    | book                |
      | course      | C1                  |
      | idnumber    | book1               |
      | name        | Test book           |
      | description | A book about dreams |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: Book chapter edition of custom tags works as expected
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test book"
    And I set the following fields to these values:
      | Chapter title | Dummy first chapter |
      | Content | Dream is the start of a journey |
      | Tags | Example, Chapter, Cool |
    And I press "Save changes"
    Then I should see "Example" in the ".book-tags" "css_element"
    And I should see "Chapter" in the ".book-tags" "css_element"
    And I should see "Cool" in the ".book-tags" "css_element"
    And I press "Turn editing on"
    And I follow "Edit chapter \"1. Dummy first chapter\""
    Then I should see "Example" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Chapter" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Cool" in the ".form-autocomplete-selection" "css_element"

  @javascript
  Scenario: Book chapter edition of standard tags works as expected
    Given I log in as "admin"
    And I navigate to "Appearance > Manage tags" in site administration
    And I follow "Default collection"
    And I follow "Add standard tags"
    And I set the field "Enter comma-separated list of new tags" to "OT1, OT2, OT3"
    And I press "Continue"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test book"
    And I open the autocomplete suggestions list
    And I should see "OT1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT2" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT3" in the ".form-autocomplete-suggestions" "css_element"
    When I set the following fields to these values:
      | Chapter title | Dummy first chapter |
      | Content | Dream is the start of a journey |
      | Tags | OT1, OT3 |
    And I press "Save changes"
    Then I should see "OT1" in the ".book-tags" "css_element"
    And I should see "OT3" in the ".book-tags" "css_element"
    And I should not see "OT2" in the ".book-tags" "css_element"
    And I press "Turn editing on"
    And I follow "Edit chapter \"1. Dummy first chapter\""
    And I should see "OT1" in the ".form-autocomplete-selection" "css_element"
    And I should see "OT3" in the ".form-autocomplete-selection" "css_element"
    And I should not see "OT2" in the ".form-autocomplete-selection" "css_element"
