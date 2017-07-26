@core @core_user
Feature: Edit user enrolment
  In order to manage students' enrolments
  As a teacher
  I need to be able to view enrolment details and edit student enrolments in the course participants page

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email                 |
      | teacher1  | Teacher   | 1        | teacher1@example.com  |
      | student1  | Student   | 1        | student1@example.com  |
      | student2  | Student   | 2        | student2@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course | role           | status |
      | teacher1  | C1     | editingteacher |    0   |
      | student1  | C1     | student        |    0   |
      | student2  | C1     | student        |    1   |

  @javascript
  Scenario: Edit a user's enrolment
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I click on "//a[@data-action='editenrolment']" "xpath_element" in the "student1" "table_row"
    And I should see "Edit Student 1's enrolment"
    And I set the field "Status" to "Suspended"
    And I click on "Save changes" "button"
    And I click on "//a[@data-action='editenrolment']" "xpath_element" in the "student2" "table_row"
    And I should see "Edit Student 2's enrolment"
    And I set the field "timeend[enabled]" to "1"
    And I set the field "timeend[day]" to "1"
    And I set the field "timeend[month]" to "January"
    And I set the field "timeend[year]" to "2017"
    And I set the field "Status" to "Active"
    And I click on "Save changes" "button"
    Then I should see "Suspended" in the "student1" "table_row"
    And I should see "Not current" in the "student2" "table_row"

  @javascript
  Scenario: Unenrol a student
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I click on "//a[@data-action='unenrol']" "xpath_element" in the "student1" "table_row"
    And I click on "Yes" "button"
    Then I should not see "Student 1" in the "participants" "table"

  @javascript
  Scenario: View a student's enrolment details
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I click on "//a[@data-action='showdetails']" "xpath_element" in the "student1" "table_row"
    Then I should see "Enrolment details"
    And I should see "Student 1" in the "Full name" "table_row"
    And I should see "Active" in the "//td[@class='user-enrol-status']" "xpath_element"
    And I press "Cancel"
    And I click on "//a[@data-action='showdetails']" "xpath_element" in the "student2" "table_row"
    And I should see "Enrolment details"
    And I should see "Student 2" in the "Full name" "table_row"
    And I should see "Suspended" in the "//td[@class='user-enrol-status']" "xpath_element"

  # Without JS, the user should be redirected to the original edit enrolment form.
  Scenario: Edit a user's enrolment without JavaScript
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I click on "//a[@data-action='editenrolment']" "xpath_element" in the "student1" "table_row"
    And I should see "Student 1"
    And I set the field "Status" to "Suspended"
    And I click on "Save changes" "button"
    And I click on "//a[@data-action='editenrolment']" "xpath_element" in the "student2" "table_row"
    And I should see "Student 2"
    And I set the field "timeend[enabled]" to "1"
    And I set the field "timeend[day]" to "1"
    And I set the field "timeend[month]" to "January"
    And I set the field "timeend[year]" to "2017"
    And I set the field "Status" to "Active"
    And I click on "Save changes" "button"
    Then I should see "Suspended" in the "student1" "table_row"
    And I should see "Not current" in the "student2" "table_row"

  # Without JS, the user should be redirected to the original unenrol confirmation page.
  Scenario: Unenrol a student
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I click on "//a[@data-action='unenrol']" "xpath_element" in the "student1" "table_row"
    And I click on "Continue" "button"
    Then I should not see "Student 1" in the "participants" "table"
