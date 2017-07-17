@mod @mod_wiki @core_tag @javascript
Feature: Edited wiki pages handle tags correctly
  In order to get wiki pages properly labelled
  As a user
  I need to introduce the tags while editing

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Wiki" to section "1" and I fill the form with:
      | Wiki name | Test wiki name |
      | Description | Test wiki description |
      | First page name | First page |
      | Wiki mode | Collaborative wiki |
    And I log out

  Scenario: Wiki page edition of custom tags works as expected
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test wiki name"
    And I press "Create page"
    When I set the following fields to these values:
      | HTML format | Student page contents to be tagged |
      | Tags | Example, Page, Cool |
    And I press "Save"
    Then I should see "Example" in the ".wiki-tags" "css_element"
    And I should see "Page" in the ".wiki-tags" "css_element"
    And I should see "Cool" in the ".wiki-tags" "css_element"
    And I follow "Edit"
    Then I should see "Example" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Page" in the ".form-autocomplete-selection" "css_element"
    Then I should see "Cool" in the ".form-autocomplete-selection" "css_element"
    And I press "Cancel"

  @javascript
  Scenario: Wiki page edition of standard tags works as expected
    Given I log in as "admin"
    And I navigate to "Appearance > Manage tags" in site administration
    And I follow "Default collection"
    And I follow "Add standard tags"
    And I set the field "Enter comma-separated list of new tags" to "OT1, OT2, OT3"
    And I press "Continue"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test wiki name"
    And I press "Create page"
    And I open the autocomplete suggestions list
    And I should see "OT1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT2" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT3" in the ".form-autocomplete-suggestions" "css_element"
    When I set the following fields to these values:
      | HTML format | Student page contents to be tagged |
      | Tags | OT1, OT3 |
    And I press "Save"
    Then I should see "OT1" in the ".wiki-tags" "css_element"
    And I should see "OT3" in the ".wiki-tags" "css_element"
    And I should not see "OT2" in the ".wiki-tags" "css_element"
    And I follow "Edit"
    And I should see "OT1" in the ".form-autocomplete-selection" "css_element"
    And I should see "OT3" in the ".form-autocomplete-selection" "css_element"
    And I should not see "OT2" in the ".form-autocomplete-selection" "css_element"
    And I press "Cancel"
