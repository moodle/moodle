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
      | name        | course | description             | image                        | status | type |
      | <badgename> | C1     | <badgename> description | badges/tests/behat/badge.png | active | 2    |
    And the following "core_badges > Criterias" exist:
      | badge       | role           |
      | <badgename> | editingteacher |
    And the following "core_badges > Issued badges" exist:
      | badge       | user     |
      | <badgename> | student1 |
    When I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    And I click on "Student 1" "link"
    Then I should see "<badgename>"
    And I am on the "Course 1" course page
    # Navigate to Manage Badges page in order to delete the badge
    And I navigate to "Badges > Manage badges" in current page administration
    # Delete the badge
    And I press "Delete" action in the "<badgename>" report row
    And I press "<deleteoption>"
    And I am on the "Course 1" "enrolled users" page
    And I click on "Student 1" "link"
    # Confirm that Badge 1 is retained as awarded badge but Badge 2 is not
    And I <visibility> see "<badgename>"
    And I am on the "Course 1" course page
    # Navigate to Badges page to confirm that no badges exist, hence, Manage badges would not exist
    And I navigate to "Badges" in current page administration
    # Confirm that badges are sucessfully deleted
    And I should see "There are currently no badges available for users to earn."

    Examples:
      | badgename | deleteoption                             | visibility |
      | Badge 1   | Delete and keep existing issued badges   | should     |
      | Badge 2   | Delete and remove existing issued badges | should not |
