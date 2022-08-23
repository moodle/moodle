@mod @mod_forum @core_tag
Feature: Edited forum posts handle tags correctly
  In order to get forum posts properly labelled
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
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | course   | C1              |
      | activity | forum           |
      | name     | Test forum name |
    And the following forum discussions exist in course "Course 1":
      | user     | forum           | name                 | message              |
      | teacher1 | Test forum name | Teacher post subject | Teacher post message |

  @javascript
  Scenario: Forum post edition of custom tags works as expected
    Given I am on the "Course 1" Course page logged in as student1
    And I reply "Teacher post subject" post from "Test forum name" forum with:
      | Subject | Student post subject |
      | Message | Student post message |
      | Tags    | Tag1                 |
    Then I should see "Tag1" in the ".tag_list" "css_element"
    And I click on "Edit" "link" in the "//div[@aria-label='Student post subject by Student 1']" "xpath_element"
    Then I should see "Tag1" in the ".form-autocomplete-selection" "css_element"

  @javascript
  Scenario: Forum post edition of standard tags works as expected
    Given I log in as "admin"
    And I change window size to "large"
    And I navigate to "Appearance > Manage tags" in site administration
    And I follow "Default collection"
    And I follow "Add standard tags"
    And I set the field "Enter comma-separated list of new tags" to "OT1, OT2, OT3"
    And I press "Continue"
    And I am on the "Test forum name" "forum activity" page logged in as teacher1
    And I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    And I expand all fieldsets
    And I open the autocomplete suggestions list
    And I should see "OT1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT2" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "OT3" in the ".form-autocomplete-suggestions" "css_element"
    And I reply "Teacher post subject" post from "Test forum name" forum with:
      | Subject | Student post subject |
      | Message | Student post message |
      | Tags | OT1, OT3 |
    Then I should see "OT1" in the ".tag_list" "css_element"
    And I should see "OT3" in the ".tag_list" "css_element"
    And I should not see "OT2" in the ".tag_list" "css_element"
    And I click on "Edit" "link" in the "//div[@aria-label='Student post subject by Teacher 1']" "xpath_element"
    And I should see "OT1" in the ".form-autocomplete-selection" "css_element"
    And I should see "OT3" in the ".form-autocomplete-selection" "css_element"
    And I should not see "OT2" in the ".form-autocomplete-selection" "css_element"
