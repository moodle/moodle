@core @core_course
Feature: Sections can be moved
  In order to rearrange my course contents
  As a teacher
  I need to move sections up and down

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course" exists:
      | fullname      | Course 1   |
      | shortname     | C1         |
      | format        | topics     |
      | coursedisplay | 0          |
      | numsections   | 5          |
      |initsections   | 1          |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name               | course | idnumber | section |
      | forum    | Test forum name    | C1     | forum1   | 1       |
      | forum    | Second forum name  | C1     | forum1   | 3       |

  @javascript
  Scenario: Teacher can move a section to another location
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I open section "1" edit menu
    And I click on "Move" "link" in the "Section 1" "section"
    And I click on "Section 3" "link" in the "Move section" "dialogue"
    Then "Section 1" "section" should appear after "Section 3" "section"
    And I should see "Test forum name" in the "Section 1" "section"
    And I should see "Second forum name" in the "Section 3" "section"

  @javascript
  Scenario: Teacher can move a section under the general section
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I open section "3" edit menu
    And I click on "Move" "link" in the "Section 3" "section"
    And I click on "General" "link" in the "Move section" "dialogue"
    Then "General" "section" should appear before "Section 3" "section"
    Then "Section 3" "section" should appear before "Section 1" "section"
    And I should see "Test forum name" in the "Section 1" "section"
    And I should see "Second forum name" in the "Section 3" "section"
