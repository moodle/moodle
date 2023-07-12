@core @core_grades @gradereport_user
Feature: We can use the user report
  As a user
  I browse to the User report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |

  Scenario: Verify we can view a user grade report with no users enrolled.
    When I am on the "Course 1" "grades > User report > View" page logged in as "admin"
    And I select "All users (0)" from the "Select all or one user" singleselect
    Then I should see "There are no students enrolled in this course."
