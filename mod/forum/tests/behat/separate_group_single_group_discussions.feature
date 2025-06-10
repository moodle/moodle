@mod @mod_forum
Feature: Posting to groups in a separate group discussion when restricted to groupings
  In order to post to groups in a forum with separate groups and groupings
  As a teacher
  I need to have groups configured to post to a group

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | teacher1  | teacher1 | teacher1@example.com |
      | teacher2 | teacher2  | teacher2 | teacher2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | teacher |
    And the following "groups" exist:
      | name | course | idnumber |
      | G1G1 | C1 | G1G1 |
      | G1G2 | C1 | G1G2 |
      | G2G1 | C1 | G2G1 |
    And the following "groupings" exist:
      | name | course | idnumber |
      | G1   | C1     | G1       |
      | G2   | C1     | G2       |
    And the following "group members" exist:
      | user        | group |
      | teacher1    | G1G1  |
      | teacher1    | G1G2  |
      | teacher1    | G2G1  |
      | teacher2    | G1G1  |
      | teacher2    | G1G2  |
      | teacher2    | G2G1  |
    And the following "grouping groups" exist:
      | grouping | group |
      | G1       | G1G1    |
      | G1       | G1G2    |
      | G2       | G2G1    |
    And the following "activities" exist:
      | activity | course | idnumber | name                  | intro                      | type    | section | groupmode | grouping |
      | forum    | C1     | 00001    | Multiple groups forum | Standard forum description | general | 1       | 1         | G1       |
      | forum    | C1     | 00001    | Single groups forum   | Standard forum description | general | 1       | 1         | G2       |

  Scenario: Teacher with accessallgroups can post in all groups
    Given I am on the "Multiple groups forum" "forum activity" page logged in as teacher1
    When I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    Then the "Group" select box should contain "All participants"
    And the "Group" select box should contain "G1G1"
    And the "Group" select box should contain "G1G2"
    And I am on "Course 1" course homepage
    And I follow "Single groups forum"
    And I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    And the "Group" select box should contain "All participants"
    And the "Group" select box should contain "G2G1"
    And I should not see "Post a copy to all groups"

  Scenario: Teacher in all groups but without accessallgroups can post in either group but not to All Participants
    Given I am on the "Multiple groups forum" "forum activity" page logged in as teacher2
    When I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    Then the "Group" select box should not contain "All participants"
    And the "Group" select box should contain "G1G1"
    And the "Group" select box should contain "G1G2"
    And I am on "Course 1" course homepage
    And I follow "Single groups forum"
    And I click on "Add discussion topic" "link"
    And I click on "Advanced" "button"
    And I should see "G2G1"
    And "Group" "select" should not exist
