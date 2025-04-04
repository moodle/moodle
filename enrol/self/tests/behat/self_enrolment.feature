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
      | student2 | Student | 2 | student2@example.com |
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
    And I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
    And I log out
    When I am on "Course 1" course homepage
    And I press "Access as a guest"
    Then I should see "Guests cannot access this course. Please log in."
    And I press "Continue"
    And I should see "Log in"

  Scenario: Self-enrolment enabled
    Given I log in as "teacher1"
    When I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I press "Enrol me"
    Then I should see "New section"
    And I should not see "Enrolment options"

  @javascript
  Scenario: Self-enrolment enabled requiring an enrolment key
    Given I log in as "teacher1"
    When I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
      | Enrolment key | moodle_rules |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should see "An enrolment key will be required."
    And I press "Enrol me"
    And I set the following fields to these values:
      | Enrolment key | moodle_rules |
    And I click on "Enrol me" "button" in the "Test student enrolment" "dialogue"
    Then I should see "New section"
    And I should not see "Enrolment options"
    And I should not see "Enrol me in this course"

  Scenario: Self-enrolment disabled
    Given I log in as "student1"
    When I am on "Course 1" course homepage
    Then I should see "You cannot enrol yourself in this course"

  @javascript
  Scenario: Self-enrolment enabled requiring a group enrolment key
    Given I log in as "teacher1"
    When I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name     | Test student enrolment |
      | Enrolment key            | moodle_rules           |
      | Use group enrolment keys | Yes                    |
    And I am on the "Course 1" "groups" page
    And I press "Create group"
    And I set the following fields to these values:
      | Group name    | Group 1             |
      | Enrolment key | Test-groupenrolkey1 |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I press "Enrol me"
    And I set the following fields to these values:
      | Enrolment key | Test-groupenrolkey1 |
    And I click on "Enrol me" "button" in the "Test student enrolment" "dialogue"
    Then I should see "New section"
    And I should not see "Enrolment options"
    And I should not see "Enrol me in this course"
    And I am on the "Course 1" course page logged in as student2
    And I press "Enrol me"
    And I set the following fields to these values:
      | Enrolment key | moodle_rules |
    And I click on "Enrol me" "button" in the "Test student enrolment" "dialogue"
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to course participants
    And the following should exist in the "participants" table:
      | First name | Email address        | Roles   | Groups    |
      | Student 1  | student1@example.com | Student | Group 1   |
      | Student 2  | student2@example.com | Student | No groups |

  @javascript
  Scenario: Edit a self-enrolled user's enrolment from the course participants page
    Given I log in as "teacher1"
    When I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I press "Enrol me"
    And I should see "You are enrolled in the course"
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
    When I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I press "Enrol me"
    And I should see "You are enrolled in the course"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I click on "//a[@data-action='unenrol']" "xpath_element" in the "student1" "table_row"
    And I click on "Unenrol" "button" in the "Unenrol" "dialogue"
    Then I should not see "Student 1" in the "participants" "table"

  @javascript
  Scenario: Self unenrol as a self-enrolled student from the course
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And I am on the "C1" "Course" page logged in as "teacher1"
    When I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the field "Course full name" in the "General" "fieldset" to "<span lang=\"en\" class=\"multilang\">Course</span><span lang=\"it\" class=\"multilang\">Corso</span> 1"
    And I press "Save and display"
    And I log out
    And I am on the "C1" "Course" page logged in as "student1"
    And I press "Enrol me"
    And I should see "You are enrolled in the course"
    And I am on the "C1" "course" page
    And I navigate to "Unenrol me from this course" in current page administration
    And I click on "Continue" "button" in the "Confirm" "dialogue"
    Then I should see "You are unenrolled from the course \"Course 1\""

  @javascript
  Scenario: Self-enrolment enabled with simultaneous guest access
    Given I log in as "teacher1"
    And I am on the "Course 1" "enrolment methods" page
    And I click on "Enable" "link" in the "Self enrolment (Student)" "table_row"
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And I set the following fields to these values:
      | Allow guest access | Yes |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to "Enrol me in this course" in current page administration
    And I click on "Enrol me" "button"
    Then I should see "New section"
