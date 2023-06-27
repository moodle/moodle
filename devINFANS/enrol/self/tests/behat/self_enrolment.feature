@enrol @enrol_self
Feature: Users can auto-enrol themself in courses where self enrolment is allowed
  In order to participate in courses
  As a user
  I need to auto enrol me in courses

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

  # Note: Please keep the javascript tag on this Scenario to ensure that we
  # test use of the singleselect functionality.
  @javascript
  Scenario: Self-enrolment enabled as guest
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
    And I log out
    When I am on "Course 1" course homepage
    And I press "Log in as a guest"
    Then I should see "Guests cannot access this course. Please log in."
    And I press "Continue"
    And I should see "Log in"

  Scenario: Self-enrolment enabled
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I press "Enrol me"
    Then I should see "Topic 1"
    And I should not see "Enrolment options"

  Scenario: Self-enrolment enabled requiring an enrolment key
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
      | Enrolment key | moodle_rules |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I set the following fields to these values:
      | Enrolment key | moodle_rules |
    And I press "Enrol me"
    Then I should see "Topic 1"
    And I should not see "Enrolment options"
    And I should not see "Enrol me in this course"

  Scenario: Self-enrolment disabled
    Given I log in as "student1"
    When I am on "Course 1" course homepage
    Then I should see "You can not enrol yourself in this course"

  Scenario: Self-enrolment enabled requiring a group enrolment key
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
      | Enrolment key | moodle_rules |
      | Use group enrolment keys | Yes |
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group 1 |
      | Enrolment key | Test-groupenrolkey1 |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I set the following fields to these values:
      | Enrolment key | Test-groupenrolkey1 |
    And I press "Enrol me"
    Then I should see "Topic 1"
    And I should not see "Enrolment options"
    And I should not see "Enrol me in this course"

  @javascript
  Scenario: Edit a self-enrolled user's enrolment from the course participants page
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I press "Enrol me"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I click on "//a[@data-action='editenrolment']" "xpath_element" in the "student1" "table_row"
    And I should see "Edit Student 1's enrolment"
    And I set the field "Status" to "Suspended"
    And I click on "Save changes" "button"
    Then I should see "Suspended" in the "student1" "table_row"

  @javascript
  Scenario: Unenrol a self-enrolled student from the course participants page
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I press "Enrol me"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I click on "//a[@data-action='unenrol']" "xpath_element" in the "student1" "table_row"
    And I click on "Unenrol" "button" in the "Unenrol" "dialogue"
    Then I should not see "Student 1" in the "participants" "table"
