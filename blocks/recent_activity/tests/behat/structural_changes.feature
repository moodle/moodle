@block @block_recent_activity
Feature: View structural changes in recent activity block
  In order to know when activities were changed
  As a user
  In need to see the structural changes in recent activity block

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username    | firstname | lastname | email            |
      | teacher1    | Terry1    | Teacher1 | teacher1@example.com |
      | assistant1  | Terry2    | Teacher2 | teacher2@example.com |
      | student1    | Sam1      | Student1 | student1@example.com |
      | student2    | Sam2      | Student2 | student2@example.com |
      | student3    | Sam3      | Student3 | student3@example.com |
    And the following "course enrolments" exist:
      | user        | course | role           |
      | teacher1    | C1     | editingteacher |
      | assistant1  | C1     | teacher        |
      | student1    | C1     | student        |
      | student2    | C1     | student        |
      | student3    | C1     | student        |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
    And the following "groupings" exist:
      | name        | course | idnumber |
      | Grouping 1  | C1     | GG1      |
      | Grouping 2  | C1     | GG2      |
      | Grouping 3  | C1     | GG3      |
    And the following "group members" exist:
      | user        | group |
      | student1    | G1    |
      | student2    | G2    |
      | student3    | G1    |
      | student3    | G2    |
      | assistant1  | G1    |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | G1    |
      | GG2      | G2    |
      | GG3      | G1    |
      | GG3      | G2    |

  Scenario: Check that Added module information is displayed respecting view capability
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Recent activity" block
    When I add a "Forum" to section "1" and I fill the form with:
      | name        | ForumVisibleGroups |
      | Description | No description     |
      | groupmode   | Visible groups     |
    And I add a "Forum" to section "1" and I fill the form with:
      | name        | ForumSeparateGroups |
      | Description | No description      |
      | groupmode   | Separate groups     |
    And I add a "Forum" to section "1" and I fill the form with:
      | name        | ForumHidden    |
      | Description | No description |
      | Availability | 0             |
    And I add a "Forum" to section "1" and I fill the form with:
      | name        | ForumNoGroups  |
      | Description | No description |
      | groupmode   | No groups      |
    And I add a "Forum" to section "2" and I fill the form with:
      | name                | ForumVisibleGroupsG1 |
      | Description         | No description       |
      | groupmode           | Visible groups       |
      | Grouping            | Grouping 1           |
      | Access restrictions | Grouping: Grouping 1 |
    And I add a "Forum" to section "2" and I fill the form with:
      | name                | ForumSeparateGroupsG1 |
      | Description         | No description        |
      | groupmode           | Separate groups       |
      | Grouping            | Grouping 1            |
      | Access restrictions | Grouping: Grouping 1  |
    And I add a "Forum" to section "3" and I fill the form with:
      | name                | ForumVisibleGroupsG2 |
      | Description         | No description       |
      | groupmode           | Visible groups       |
      | Grouping            | Grouping 2           |
      | Access restrictions | Grouping: Grouping 2 |
    And I add a "Forum" to section "3" and I fill the form with:
      | name                | ForumSeparateGroupsG2 |
      | Description         | No description        |
      | groupmode           | Separate groups       |
      | Grouping            | Grouping 2            |
      | Access restrictions | Grouping: Grouping 2  |
    Then I should see "ForumVisibleGroups" in the "Recent activity" "block"
    And I should see "ForumSeparateGroups" in the "Recent activity" "block"
    And I should see "ForumNoGroups" in the "Recent activity" "block"
    And I should see "ForumHidden" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG1" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG1" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG2" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG2" in the "Recent activity" "block"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should see "ForumVisibleGroups" in the "Recent activity" "block"
    And I should see "ForumSeparateGroups" in the "Recent activity" "block"
    And I should see "ForumNoGroups" in the "Recent activity" "block"
    And I should not see "ForumHidden" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG1" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG1" in the "Recent activity" "block"
    And I should not see "ForumVisibleGroupsG2" in the "Recent activity" "block"
    And I should not see "ForumSeparateGroupsG2" in the "Recent activity" "block"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I should see "ForumVisibleGroups" in the "Recent activity" "block"
    And I should see "ForumSeparateGroups" in the "Recent activity" "block"
    And I should see "ForumNoGroups" in the "Recent activity" "block"
    And I should not see "ForumHidden" in the "Recent activity" "block"
    And I should not see "ForumVisibleGroupsG1" in the "Recent activity" "block"
    And I should not see "ForumSeparateGroupsG1" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG2" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG2" in the "Recent activity" "block"
    And I log out
    And I log in as "student3"
    And I am on "Course 1" course homepage
    And I should see "ForumVisibleGroups" in the "Recent activity" "block"
    And I should see "ForumSeparateGroups" in the "Recent activity" "block"
    And I should see "ForumNoGroups" in the "Recent activity" "block"
    And I should not see "ForumHidden" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG1" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG1" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG2" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG2" in the "Recent activity" "block"
    And I log out
    # Teachers have capability to see all groups and hidden activities
    And I log in as "assistant1"
    And I am on "Course 1" course homepage
    And I should see "ForumHidden" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG1" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG1" in the "Recent activity" "block"
    And I should see "ForumVisibleGroupsG2" in the "Recent activity" "block"
    And I should see "ForumSeparateGroupsG2" in the "Recent activity" "block"
    And I log out

  Scenario: Updates and deletes in recent activity block
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Recent activity" block
    And I add a "Forum" to section "1" and I fill the form with:
      | name        | ForumNew       |
      | Description | No description |
    Then I should see "Added Forum" in the "Recent activity" "block"
    And I should see "ForumNew" in the "Recent activity" "block"
    And I log out
    And I wait "1" seconds
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should see "Added Forum" in the "Recent activity" "block"
    And I should see "ForumNew" in the "Recent activity" "block"
    And I log out
    # Update forum as a teacher after a second to ensure we have a new timestamp for recent activity.
    And I wait "1" seconds
    # Update forum as a teacher
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "ForumNew"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | name | ForumUpdated |
    And I press "Save and return to course"
    And I log out
    And I wait "1" seconds
    # Student 1 already saw that forum was created, now he can see that forum was updated
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see "Added Forum" in the "Recent activity" "block"
    And I should not see "ForumNew" in the "Recent activity" "block"
    And I should see "Updated Forum" in the "Recent activity" "block"
    And I should see "ForumUpdated" in the "Recent activity" "block"
    And I log out
    And I wait "1" seconds
    # Student 2 has bigger interval and he can see one entry that forum was created but with the new name
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I should see "Added Forum" in the "Recent activity" "block"
    And I should not see "ForumNew" in the "Recent activity" "block"
    And I should not see "Updated Forum" in the "Recent activity" "block"
    And I should see "ForumUpdated" in the "Recent activity" "block"
    And I log out
    And I wait "1" seconds
    # Delete forum as a teacher
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I delete "ForumUpdated" activity
    And I run all adhoc tasks
    And I log out
    And I wait "1" seconds
    # Students 1 and 2 see that forum was deleted
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see "Added Forum" in the "Recent activity" "block"
    And I should not see "ForumNew" in the "Recent activity" "block"
    And I should not see "Updated Forum" in the "Recent activity" "block"
    And I should not see "ForumUpdated" in the "Recent activity" "block"
    And I should see "Deleted Forum" in the "Recent activity" "block"
    And I log out
    And I wait "1" seconds
    # Student 3 never knew that forum was created, so he does not see anything
    And I log in as "student3"
    And I am on "Course 1" course homepage
    And I should not see "Added Forum" in the "Recent activity" "block"
    And I should not see "ForumNew" in the "Recent activity" "block"
    And I should not see "Updated Forum" in the "Recent activity" "block"
    And I should not see "ForumUpdated" in the "Recent activity" "block"
    And I should not see "Deleted Forum" in the "Recent activity" "block"
    And I log out
