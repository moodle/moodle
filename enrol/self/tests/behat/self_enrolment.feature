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
    And I follow "Course 1"
    And I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
    And I log out
    When I follow "Course 1"
    And I press "Log in as a guest"
    Then I should see "Guests cannot access this course. Please log in."
    And I press "Continue"
    And I should see "Log in"

  Scenario: Self-enrolment enabled
    Given I log in as "teacher1"
    And I follow "Course 1"
    When I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    And I press "Enrol me"
    Then I should see "Topic 1"
    And I should not see "Enrolment options"

  Scenario: Self-enrolment enabled requiring an enrolment key
    Given I log in as "teacher1"
    And I follow "Course 1"
    When I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
      | Enrolment key | moodle_rules |
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    And I set the following fields to these values:
      | Enrolment key | moodle_rules |
    And I press "Enrol me"
    Then I should see "Topic 1"
    And I should not see "Enrolment options"
    And I should not see "Enrol me in this course"

  Scenario: Self-enrolment disabled
    Given I log in as "student1"
    And I am on site homepage
    When I follow "Course 1"
    Then I should see "You can not enrol yourself in this course"

  Scenario: Self-enrolment enabled requiring a group enrolment key
    Given I log in as "teacher1"
    And I follow "Course 1"
    When I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
      | Enrolment key | moodle_rules |
      | Use group enrolment keys | Yes |
    And I follow "Course 1"
    And I navigate to "Users > Groups" in current page administration
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group 1 |
      | Enrolment key | Test-groupenrolkey1 |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    And I set the following fields to these values:
      | Enrolment key | Test-groupenrolkey1 |
    And I press "Enrol me"
    Then I should see "Topic 1"
    And I should not see "Enrolment options"
    And I should not see "Enrol me in this course"
