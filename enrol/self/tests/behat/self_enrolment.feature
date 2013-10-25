@enrol @enrol_self
Feature: Users can auto-enrol themself in courses where self enrolment is allowed
  In order to participate in courses
  As a user
  I need to auto enrol me in courses

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  @javascript
  Scenario: Self-enrolment enabled
    Given I log in as "teacher1"
    And I follow "Course 1"
    When I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I press "Enrol me"
    Then I should see "Topic 1"
    And I should not see "Enrolment options"

  @javascript
  Scenario: Self-enrolment enabled requiring an enrolment key
    Given I log in as "teacher1"
    And I follow "Course 1"
    When I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
      | Enrolment key | moodle_rules |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I fill the moodle form with:
      | Enrolment key | moodle_rules |
    And I press "Enrol me"
    Then I should see "Topic 1"
    And I should not see "Enrolment options"
    And I should not see "Enrol me in this course"

  @javascript
  Scenario: Self-enrolment disabled
    Given I log in as "student1"
    When I follow "Course 1"
    Then I should see "You can not enrol yourself in this course"

  @javascript
  Scenario: Self-enrolment enabled requiring a group enrolment key
    Given I log in as "teacher1"
    And I follow "Course 1"
    When I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
      | Enrolment key | moodle_rules |
      | Use group enrolment keys | Yes |
    And I follow "Groups"
    And I press "Create group"
    And I fill the moodle form with:
      | Group name | Group 1 |
      | Enrolment key | testgroupenrolkey |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I fill the moodle form with:
      | Enrolment key | testgroupenrolkey |
    And I press "Enrol me"
    Then I should see "Topic 1"
    And I should not see "Enrolment options"
    And I should not see "Enrol me in this course"