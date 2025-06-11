@mod @mod_wiki
Feature: Groups can have separate content on a wiki
  In order to create a wiki with my group
  As a user
  I need to view and add wiki pages by group

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student1@example.com |
      | student4 | Student   | 4        | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
    Given the following "groups" exist:
      | name    | course | idnumber | participation |
      | Group 1 | C1     | G1       | 1             |
      | Group 2 | C1     | G2       | 1             |
      | Group 3 | C1     | G3       | 0             |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G2    |
      | student3 | G3    |
    And the following "activities" exist:
      | activity | course | name          | idnumber | wikimode      | firstpagetitle  | groupmode |
      | wiki     | C1     | Separate wiki | wiki1    | collaborative | Separate page 1 | 1         |
      | wiki     | C1     | Visible wiki  | wiki2    | collaborative | Visible page 1  | 2         |
    And the following wiki pages exist:
      | wiki  | title           | content      | group |
      | wiki1 | Separate page 1 | Group 1 page | G1    |
      | wiki1 | Separate page 1 | Group 2 page | G2    |
      | wiki2 | Visible page 1  | Group 1 page | G1    |
      | wiki2 | Visible page 1  | Group 2 page | G2    |
    And the following wiki pages exist:
      | wiki  | title           | content       |
      | wiki1 | Separate page 1 | No group page |
      | wiki2 | Visible page 1  | No group page |

  Scenario Outline: Teacher can see all participation group wikis
    Given I am on the "<wiki>" "wiki activity" page logged in as teacher1
    And I should see "All participants" in the "<mode> groups" "select"
    And I should see "Group 1" in the "<mode> groups" "select"
    And I should see "Group 2" in the "<mode> groups" "select"
    And I should not see "Group 3" in the "<mode> groups" "select"
    And I should see "No group page"
    And I should not see "Group 1 page"
    And I should not see "Group 2 page"
    When I select "Group 1" from the "<mode> groups" singleselect
    Then I should not see "No group page"
    And I should see "Group 1 page"
    And I should not see "Group 2 page"

    Examples:
      | wiki  | mode     |
      | wiki1 | Separate |
      | wiki2 | Visible  |

  Scenario Outline: Teacher can add a page to any participation group's wiki
    Given I am on the "<wiki>" "wiki activity" page logged in as teacher1
    And I select "Edit" from the "jump" singleselect
    And I set the field "HTML format" to "[[Internal link]]"
    And I press "Save"
    When I follow "Internal link"
    And I should see "New page"
    Then I should see "All participants" in the "Group" "select"
    And I should see "Group 1" in the "Group" "select"
    And I should see "Group 2" in the "Group" "select"
    And I should not see "Group 3" in the "Group" "select"

    Examples:
      | wiki  |
      | wiki1 |
      | wiki2 |

  Scenario Outline: Students should only see their participation groups' own wiki in separate groups mode
    Given I am on the "wiki1" "wiki activity" page logged in as <user>
    Then I should see "Separate groups: <group>"
    And "Separate groups" "select" should not exist
    And I should see "<page>"

    Examples:
      | user     | group            | page           |
      | student1 | Group 1          | Group 1 page   |
      | student2 | Group 2          | Group 2 page   |
      # The view page throws an exception if the user is not in a group, so we cannot test
      # student3 and student4.

  Scenario Outline: Students can see all participation groups' own wikis in visible groups mode
    Given I am on the "wiki2" "wiki activity" page logged in as <user>
    And I should see "All participants" in the "Visible groups" "select"
    And I should see "Group 1" in the "Visible groups" "select"
    And I should see "Group 2" in the "Visible groups" "select"
    And I should not see "Group 3" in the "Visible groups" "select"
    And I should see "<page>"

    Examples:
      | user     | page         |
      | student1 | Group 1 page |
      | student2 | Group 2 page |
      | student3 | Group 1 page |
      | student4 | Group 1 page |
