@core @core_course @javascript
Feature: Keyholder role is listed as course contact
  As a student I need to know who the keyholder is to enrol in a course

  Background:
    Given I log in as "admin"
    And I am on site homepage
    And the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And I navigate to "Define roles" node in "Site administration > Users > Permissions"
    And I click on "Add a new role" "button"
    And I click on "Continue" "button"
    And I set the following fields to these values:
    | Short name | keyholder |
    | Custom full name | Keyholder |
    | contextlevel40 | 1 |
    | contextlevel50 | 1 |
    | enrol/self:holdkey | 1 |
    And I click on "Create this role" "button"
    And I navigate to "Courses" node in "Site administration > Appearance"
    And I click on "Keyholder" "checkbox"
    And I press "Save changes"
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | keyholder1 | Keyholder | 1 | keyholder1@example.com |
      | student1 | Student | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections | category |
      | Course 1 | C1 | topics | 0 | 5 | CAT1 |
    And I am on "Course 1" course homepage
    And I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
      | Enrolment key | letmein |
    And I log out

  Scenario: Keyholder assigned to a course
    When I log in as "admin"
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | keyholder1 | C1 | keyholder |
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    Then I should see "Keyholder 1"

  Scenario: Keyholder assigned to a category
    When I log in as "admin"
    And the following "role assigns" exist:
      | user    | role          | contextlevel | reference |
      | keyholder1 | keyholder       | Category     | CAT1      |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    Then I should see "Keyholder 1"
