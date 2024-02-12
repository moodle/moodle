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

  Scenario: Move up and down a section with Javascript disabled in a single page course
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I move down section "1"
    Then "Section 1" "section" should appear after "Section 2" "section"
    And I should see "Test forum name" in the "Section 1" "section"
    And I move up section "2"
    And "Section 2" "section" should appear after "Section 1" "section"
    And I should see "Test forum name" in the "Section 1" "section"

  Scenario: Move up and down a section with Javascript disabled in the course home of a course using paged mode
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Course layout | Show one section per page |
    And I press "Save and display"
    When I move down section "1"
    Then "Section 1" "section" should appear after "Section 2" "section"
    And I should see "Test forum name" in the "Section 1" "section"
    And I move up section "2"
    And "Section 2" "section" should appear after "Section 1" "section"
    And I should see "Test forum name" in the "Section 1" "section"

  Scenario: Sections can not be moved with Javascript disabled in a section page of a course using paged mode
    Given I am on the "Course 1" course page logged in as "teacher1"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Course layout | Show one section per page |
    And I press "Save and display"
    When I click on "Section 2" "link" in the "region-main" "region"
    And I turn editing mode on
    Then "Section 1" "section" should not exist
    And "Section 3" "section" should not exist
    And "Move down" "link" should not exist
    And "Move up" "link" should not exist

  @javascript
  Scenario: Move section with javascript
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I open section "1" edit menu
    And I click on "Move" "link" in the "Section 1" "section"
    And I click on "Section 3" "link" in the ".modal-body" "css_element"
    Then "Section 1" "section" should appear after "Section 3" "section"
    And I should see "Test forum name" in the "Section 1" "section"
