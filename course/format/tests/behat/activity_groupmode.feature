@core @core_courseformat
Feature: Verify activity group mode interface.
  In order to edit the course activity group mode
  As a teacher
  I need to be able edit the group mode form the page course

  Background:
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | numsections      | 3        |
    And the following "groups" exist:
      | name | course | idnumber |
      | G1   | C1     | GI1      |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Teacher can see the group mode badges in both edit and no edit mode
    Given the following "activities" exist:
      | activity | name              | intro                  | course | idnumber | section | groupmode |
      | forum    | Activity sample 1 | Test forum description | C1     | sample1  | 1       | 0         |
      | forum    | Activity sample 2 | Test forum description | C1     | sample2  | 1       | 1         |
      | forum    | Activity sample 3 | Test forum description | C1     | sample3  | 1       | 2         |
    And I am on the "C1" "Course" page logged in as "teacher1"
    When I turn editing mode on
    Then "Visible groups" "icon" should not exist in the "Activity sample 1" "activity"
    And "Separate groups" "icon" should not exist in the "Activity sample 1" "activity"
    And "Visible groups" "icon" should not exist in the "Activity sample 2" "activity"
    And "Separate groups" "icon" should exist in the "Activity sample 2" "activity"
    And "Visible groups" "icon" should exist in the "Activity sample 3" "activity"
    And "Separate groups" "icon" should not exist in the "Activity sample 3" "activity"
    And I turn editing mode off
    And "Visible groups" "icon" should not exist in the "Activity sample 1" "activity"
    And "Separate groups" "icon" should not exist in the "Activity sample 1" "activity"
    And "Visible groups" "icon" should not exist in the "Activity sample 2" "activity"
    And "Separate groups" "icon" should exist in the "Activity sample 2" "activity"
    And "Visible groups" "icon" should exist in the "Activity sample 3" "activity"
    And "Separate groups" "icon" should not exist in the "Activity sample 3" "activity"

  @javascript
  Scenario: Teacher can edit the group mode using the activity group mode badge
    Given the following "activities" exist:
      | activity | name            | intro                  | course | idnumber | section | groupmode |
      | forum    | Activity sample | Test forum description | C1     | sample   | 1       | 1         |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I turn editing mode on
    And I click on "Separate groups" "icon" in the "Activity sample" "core_courseformat > Activity groupmode"
    And I click on "Visible groups" "link" in the "Activity sample" "core_courseformat > Activity groupmode"
    And "Separate groups" "icon" should not exist in the "Activity sample" "core_courseformat > Activity groupmode"
    And "Visible groups" "icon" should exist in the "Activity sample" "core_courseformat > Activity groupmode"
    When I click on "Visible groups" "icon" in the "Activity sample" "core_courseformat > Activity groupmode"
    And I click on "Separate groups" "link" in the "Activity sample" "core_courseformat > Activity groupmode"
    And "Visible groups" "icon" should not exist in the "Activity sample" "core_courseformat > Activity groupmode"
    And "Separate groups" "icon" should exist in the "Activity sample" "core_courseformat > Activity groupmode"
    Then I click on "Separate groups" "icon" in the "Activity sample" "core_courseformat > Activity groupmode"
    And I click on "No groups" "link" in the "Activity sample" "core_courseformat > Activity groupmode"
    And "Separate groups" "icon" should not exist in the "Activity sample" "core_courseformat > Activity groupmode"
    And "Visible groups" "icon" should not exist in the "Activity sample" "core_courseformat > Activity groupmode"
    And I open "Activity sample" actions menu
    And I click on "No groups" "icon" in the "Activity sample" "core_courseformat > Activity groupmode"
    And I click on "Separate groups" "link" in the "Activity sample" "core_courseformat > Activity groupmode"
    And "Separate groups" "icon" should exist in the "Activity sample" "core_courseformat > Activity groupmode"

  @javascript
  Scenario: Teacher can edit the group mode using the activity action menu
    Given the following "activities" exist:
      | activity | name            | intro                  | course | idnumber | section | groupmode |
      | forum    | Activity sample | Test forum description | C1     | sample   | 1       | 1         |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I turn editing mode on
    And I open "Activity sample" actions menu
    And I choose "Group mode > Visible groups" in the open action menu
    And "Separate groups" "icon" should not exist in the "Activity sample" "core_courseformat > Activity groupmode"
    And "Visible groups" "icon" should exist in the "Activity sample" "core_courseformat > Activity groupmode"
    When I open "Activity sample" actions menu
    And I choose "Group mode > Separate groups" in the open action menu
    And "Visible groups" "icon" should not exist in the "Activity sample" "core_courseformat > Activity groupmode"
    And "Separate groups" "icon" should exist in the "Activity sample" "core_courseformat > Activity groupmode"
    Then I open "Activity sample" actions menu
    And I choose "Group mode > No groups" in the open action menu
    And "Separate groups" "icon" should not exist in the "Activity sample" "core_courseformat > Activity groupmode"
    And "Visible groups" "icon" should not exist in the "Activity sample" "core_courseformat > Activity groupmode"
    And I open "Activity sample" actions menu
    And I choose "Group mode > Separate groups" in the open action menu
    And "Separate groups" "icon" should exist in the "Activity sample" "core_courseformat > Activity groupmode"
