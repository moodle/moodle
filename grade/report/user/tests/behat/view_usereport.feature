@core @core_grades @gradereport_user
Feature: We can use the user report
  As a user
  I browse to the User report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |

    Scenario: Verify we can view a user grade report with no users enrolled.
      Given I log in as "admin"
      And I am on site homepage
      And I follow "Course 1"
      And I navigate to "Grades" node in "Course administration"
      And I select "User report" from the "Grade report" singleselect
      And I select "All users (0)" from the "Select all or one user" singleselect
      Then I should see "No students enrolled in this course yet"
