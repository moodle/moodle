@core @core_badges
Feature: Delete course badge already awarded
  As a teacher
  I can delete awarded course badge

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  @javascript
  Scenario Outline: Delete course badge already awarded to student
    Given the following "core_badges > Badges" exist:
      | name            | course | description                 | image                        | status | type |
      | Testing badge 1 | C1     | Testing badge 1 description | badges/tests/behat/badge.png | active | 2    |
    And the following "core_badges > Criterias" exist:
      | badge           | role           |
      | Testing badge 1 | editingteacher |
    And the following "core_badges > Issued badges" exist:
      | badge           | user     |
      | Testing badge 1 | student1 |
    When I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    And I click on "Student 1" "link"
    Then I should see "Testing badge 1"
    And I am on the "Course 1" course page
    # Navigate to Badges page in order to delete the badge
    And I navigate to "Badges" in current page administration
    # Delete the badge
    And I press "Delete" action in the "Testing badge 1" report row
    And I press "<deleteoption>"
    And the following <shouldtable> exist in the "reportbuilder-table" table:
      | Name            | Badge status |
      | Testing badge 1 | Archived     |
    And I <shouldmsg> see "There are no matching badges available for users to earn."
    # Confirm that badge is retained in the first case as awarded badge but not in the second.
    And I am on the "Course 1" "enrolled users" page
    And I click on "Student 1" "link"
    And I <shouldtable> see "Testing badge 1"

    Examples:
      | deleteoption                             | shouldtable | shouldmsg  |
      | Delete and keep existing issued badges   | should      | should not |
      | Delete and remove existing issued badges | should not  | should     |
