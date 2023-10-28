@core @core_course
Feature: Keyholder role is listed as course contact
  As a student I need to know who the keyholder is to enrol in a course

  Background:
    Given the following "role" exists:
        | shortname          | keyholder |
        | name               | Keyholder |
        | context_coursecat  | 1         |
        | context_course     | 1         |
        | enrol/self:holdkey | allow     |
    And the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "users" exist:
      | username   | firstname | lastname | email                  |
      | teacher1   | Teacher   | 1        | teacher1@example.com   |
      | keyholder1 | Keyholder | 1        | keyholder1@example.com |
      | student1   | Student   | 1        | teacher1@example.com   |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections | category |
      | Course 1 | C1        | topics | 0             | 5           | CAT1     |
    And I log in as "admin"
    And I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
      | Enrolment key | letmein |
    And I navigate to "Appearance > Courses" in site administration
    And I set the following fields to these values:
      | Keyholder | 1 |
    And I press "Save changes"
    And I log out

  Scenario: Keyholder assigned to a course
    Given the following "course enrolments" exist:
      | user       | course | role           |
      | teacher1   | C1     | editingteacher |
      | keyholder1 | C1     | keyholder      |
    When I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    Then I should see "Keyholder 1"

  Scenario: Keyholder assigned to a category
    Given the following "role assigns" exist:
      | user       | role      | contextlevel | reference |
      | keyholder1 | keyholder | Category     | CAT1      |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    When I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    Then I should see "Keyholder 1"
