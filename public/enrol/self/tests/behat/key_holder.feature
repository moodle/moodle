@enrol @enrol_self
Feature: Users can be defined as key holders in courses where self enrolment is allowed
  In order to participate in courses
  As a user
  I need to auto enrol me in courses

  Background:
    Given the following "roles" exist:
      | shortname | name       | context_course | enrol/self:holdkey |
      | keyholder | Key holder | 1              | allow              |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | manager1 | Manager | 1 | manager1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And I log in as "admin"
    And I navigate to "Appearance > Courses" in site administration
    And I set the following fields to these values:
      | Key holder | 1 |
    And I press "Save changes"
    And the following "course enrolments" exist:
      | user | course | role |
      | manager1 | C1 | keyholder |
    And I log out

  @javascript
  Scenario: The key holder name is displayed on site home page
    Given I log in as "admin"
    When I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
      | Enrolment key | moodle_rules |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should see "An enrolment key will be required."
    And I press "Enrol me"
    And I should see "You should have received this enrolment key from:"
    And I should see "Manager 1"
    And I set the following fields to these values:
      | Enrolment key | moodle_rules |
    And I click on "Enrol me" "button" in the "Test student enrolment" "dialogue"
    Then I should see "New section"
    And I should not see "Enrolment options"
    And I should not see "Enrol me in this course"
