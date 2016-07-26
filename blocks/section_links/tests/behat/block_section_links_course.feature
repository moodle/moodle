@block @block_section_links
Feature: The section links block allows users to quickly navigate around a moodle course
  In order to navigate a moodle course
  As a teacher
  I can use the section links block

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "5" and I fill the form with:
      | Assignment name | Test assignment 1 |
      | Description | Offline text |
      | assignsubmission_file_enabled | 0 |
    And I navigate to "Edit settings" node in "Course administration"
    And I set the following fields to these values:
      | id_numsections   | 20 |
      | id_coursedisplay | Show one section per page |
    And I press "Save and display"


  Scenario: Add the section links block to a course.
    Given I add the "Section links" block
    And I turn editing mode off
    And I should see "5" in the "Section links" "block"
    When I follow "5"
    Then I should see "Test assignment 1"

  Scenario: Add the section links block to a course and limit the sections displayed.
    Given I add the "Section links" block
    And I configure the "Section links" block
    And I set the following fields to these values:
      | id_config_numsections1 | 5 |
      | id_config_incby1 | 5 |
      | id_config_numsections2 | 40 |
      | id_config_incby2 | 10 |
    And I press "Save changes"
    And I turn editing mode off
    And I should see "5" in the "Section links" "block"
    When I follow "5"
    Then I should see "Test assignment 1"

  Scenario: Add the section links block to a course and limit the sections displayed using the alternative number of sections.
    Given I add the "Section links" block
    And I configure the "Section links" block
    And I set the following fields to these values:
      | id_config_numsections1 | 5 |
      | id_config_incby1 | 1 |
      | id_config_numsections2 | 10 |
      | id_config_incby2 | 5 |
    And I press "Save changes"
    And I turn editing mode off
    And I should see "5" in the "Section links" "block"
    When I follow "5"
    Then I should see "Test assignment 1"
