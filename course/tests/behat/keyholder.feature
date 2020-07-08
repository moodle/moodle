@core @core_course
Feature: Keyholder role is listed as course contact
  As a student I need to know who the keyholder is to enrol in a course

  Background:
    Given the following "roles" exist:
        | shortname | name      | archetype | context_coursecat | context_course | enrol/self:holdkey |
        | keyholder | Keyholder |           | 1                 | 1              | allow              |
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
      | Course 1 | C1 | topics | 0 | 5 | CAT1 |
    And I am on "Course 1" course homepage
    And I add "Self enrolment" enrolment method with:
      | Custom instance name | Test student enrolment |
      | Enrolment key        | letmein                |
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
