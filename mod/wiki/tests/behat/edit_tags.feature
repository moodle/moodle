@mod @mod_wiki
Feature: Edited wiki pages handle tags correctly
  In order to get wiki pages properly labelled
  As a user
  I need to introduce the tags while editing

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Wiki" to section "1" and I fill the form with:
      | Wiki name | Test wiki name |
      | Description | Test wiki description |
      | First page name | First page |
      | Wiki mode | Collaborative wiki |
    And I log out

  Scenario: Wiki page edition of custom tags works as expected
    Given I log in as "student1"
    And I follow "Course 1"
    And I follow "Test wiki name"
    And I press "Create page"
    When I set the following fields to these values:
      | HTML format | Student page contents to be tagged |
      | Other tags (enter tags separated by commas) | Example, Page, Cool |
    And I press "Save"
    Then I should see "Example" in the ".wiki-tags" "css_element"
    And I should see "Page" in the ".wiki-tags" "css_element"
    And I should see "Cool" in the ".wiki-tags" "css_element"
    And I follow "Edit"
    And the field "Other tags (enter tags separated by commas)" matches value "Example, Page, Cool"
    And I press "Cancel"

  Scenario: Wiki page edition of official tags works as expected
    Given I log in as "admin"
    And I expand "Site administration" node
    And I expand "Appearance" node
    And I follow "Manage tags"
    And I set the field "otagsadd" to "OT1, OT2, OT3"
    And I press "Add official tags"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test wiki name"
    And I press "Create page"
    And the "tags[officialtags][]" select box should contain "OT1"
    And the "tags[officialtags][]" select box should contain "OT2"
    And the "tags[officialtags][]" select box should contain "OT3"
    When I set the following fields to these values:
      | HTML format | Student page contents to be tagged |
      | tags[officialtags][] | OT1, OT3 |
    And I press "Save"
    Then I should see "OT1" in the ".wiki-tags" "css_element"
    And I should see "OT3" in the ".wiki-tags" "css_element"
    And I should not see "OT2" in the ".wiki-tags" "css_element"
    And I follow "Edit"
    And the field "tags[officialtags][]" matches value "OT1, OT3"
    And the field "tags[officialtags][]" does not match value "OT2"
    And I press "Cancel"
