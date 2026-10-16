@mod @mod_assign
Feature: Assign override reason
  In order to explain why an override was granted
  As a teacher
  I need to be able to add a reason to an override

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
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
    And the following "activities" exist:
      | activity | name                 | intro                   | course | assignsubmission_onlinetext_enabled |
      | assign   | Test assignment name | Submit your online text | C1     | 1                                   |

  @javascript
  Scenario: Add a user override with a reason
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user | Student 1 |
      | Due date      | ##1 Jan 2030 08:00## |
      | Reason        | Extension granted for medical reasons |
    And I press "Save"
    And I should see "Extension granted for medical reasons" in the "Reason for override" "table_row"
    And I click on "Edit" "link" in the "Student 1" "table_row"
    And the field "Reason for override" matches value "Extension granted for medical reasons"
    And I set the following fields to these values:
      | Reason | Updated reason after review |
    And I press "Save"
    Then I should see "Updated reason after review" in the "Reason for override" "table_row"

  @javascript
  Scenario: Add a group override with a reason
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Overrides" in current page administration
    And I set the field "Overrides" to "Group overrides"
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group | Group 1 |
      | Due date       | ##1 Jan 2030 08:00## |
      | Reason         | Additional time approved for this group |
    And I press "Save"
    And I should see "Additional time approved for this group" in the "Reason for override" "table_row"
    And I click on "Edit" "link" in the "Group 1" "table_row"
    And the field "Reason for override" matches value "Additional time approved for this group"
    And I set the following fields to these values:
      | Reason | Updated group reason after review |
    And I press "Save"
    Then I should see "Updated group reason after review" in the "Reason for override" "table_row"
