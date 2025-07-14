@report @report_completion
Feature: See the completion for items in a course
  In order see completion data
  As a teacher
  I need to view completion report

  Background:
    Given the following "custom profile fields" exist:
      | datatype | shortname | name  |
      | text     | fruit     | Fruit |
    And the following "users" exist:
      | username | firstname | lastname  | email                | middlename | alternatename | firstnamephonetic | lastnamephonetic | profile_field_fruit |
      | teacher1 | Teacher   | 1         | teacher1@example.com |            | fred          |                   |                  |                     |
      | student1 | Grainne   | Beauchamp | student1@example.com | Ann        | Jill          | Gronya            | Beecham          | Kumquat             |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name       | intro      | course | idnumber | completion | completionview |
      | page     | PageName1  | PageDesc1  | C1     | PAGE1    | 1          | 1              |

  @javascript
  Scenario: The completion report respects user fullname setting
    Given the following config values are set as admin:
      | fullnamedisplay | firstname |
      | alternativefullnameformat | middlename, alternatename, firstname, lastname |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Page - PageName1 | 1 |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    When I navigate to "Reports" in current page administration
    And I click on "Course completion" "link" in the "region-main" "region"
    Then I should see "Ann, Jill, Grainne, Beauchamp"

  @javascript
  Scenario: The completion report displays custom user profile fields
    Given the following config values are set as admin:
      | showuseridentity | email,profile_field_fruit |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Page - PageName1 | 1 |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    When I navigate to "Reports" in current page administration
    And I click on "Course completion" "link" in the "region-main" "region"
    # We can't refer to table headings by name because they aren't on the first row.
    Then the following should exist in the "completionreport" table:
      | -1-               | -2-                  | -3-     |
      | Grainne Beauchamp | student1@example.com | Kumquat |
