@core @core_course
Feature: Activities can be moved between sections
  In order to rearrange my course contents
  As a teacher
  I need to move activities between sections

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course" exists:
      | fullname      | Course 1 |
      | shortname     | C1       |
      | format        | topics   |
      | coursedisplay | 0        |
      | numsections   | 5        |
      | initsections  | 1        |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | name               | course | idnumber  | section |
      | forum    | Test forum name    | C1     | 00001     | 1       |
      | forum    | Second forum name  | C1     | 00002     | 1       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Move activities in a single page course with Javascript disabled
    When I move "Test forum name" activity to section "2"
    Then I should see "Test forum name" in the "Section 2" "section"
    And I should not see "Test forum name" in the "Section 1" "section"

  Scenario: Move activities in the course home with Javascript disabled using paged mode
    Given I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Course layout | Show one section per page |
    And I press "Save and display"
    When I move "Test forum name" activity to section "2"
    Then I should see "Test forum name" in the "Section 2" "section"
    And I should not see "Test forum name" in the "Section 1" "section"

  Scenario: Move activities in a course section with Javascript disabled using paged mode
    Given I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Course layout | Show one section per page |
    And I press "Save and display"
    And I follow "Section 1"
    When I move "Second forum name" activity to section "1"
    Then "Second forum name" "link" should appear before "Test forum name" "link"

  @javascript
  Scenario: Move activity with javascript
    When I move "Test forum name" activity to section "3"
    Then I should see "Test forum name" in the "Section 3" "section"
