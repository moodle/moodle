@block @block_section_links
Feature: The Section links block can be configured to display section name in addition to section number

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | numsections | coursedisplay |
      | Course 1 | C1        | 0        | 10          | 1             |
    And the following "activities" exist:
      | activity | name              | course | idnumber | section |
      | assign   | First assignment  | C1     | assign1  | 7       |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      | showsectionname | 1 | block_section_links |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Section links" block
    And I log out

  Scenario: Student can see section name under the Section links block
    Given I log in as "student1"
    When I am on "Course 1" course homepage
    Then I should see "7: Topic 7" in the "Section links" "block"
    And I follow "7: Topic 7"
    And I should see "First assignment"

  Scenario: Teacher can configure existing Section links block to display section number or section name
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I configure the "Section links" block
    And I set the following fields to these values:
      | Display section name | No |
    And I click on "Save changes" "button"
    Then I should not see "7: Topic 7" in the "Section links" "block"
    And I should see "7" in the "Section links" "block"
    And I follow "7"
    And I should see "First assignment"
