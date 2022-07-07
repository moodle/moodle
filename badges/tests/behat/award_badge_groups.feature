@core @core_badges @_file_upload
Feature: Award badges with separate groups
  In order to award badges to users for their achievements
  As a teacher
  I need to award badges only to users in the groups I have access

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | teacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Class A | C1 | CA |
      | Class B | C1 | CB |
    And the following "group members" exist:
      | user | group |
      | student1 | CB |
      | teacher1 | CB |
      | student2 | CA |
      | teacher2 | CA |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Group mode" to "Separate groups"
    And I press "Save and display"
    And I navigate to "Badges > Add a new badge" in current page administration
    And I follow "Add a new badge"
    And I set the following fields to these values:
      | Name | Course Badge |
      | Description | Course badge description |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    And I set the field "Teacher" to "1"
    And I set the field "Non-editing teacher" to "1"
    # Set to ANY of the roles awards badge.
    And I set the field "Any of the selected roles awards the badge" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I log out

  @javascript
  Scenario: Award course badge as non-editing teacher with only one group
    When I log in as "teacher2"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    And I follow "Manage badges"
    And I follow "Course Badge"
    And I press "Award badge"
    And I set the field "role" to "Non-editing teacher"
    # Teacher 2 should see a "Separate groups" label with the group he is in
    Then I should see "Separate groups: Class A"
    # Teacher 2 should only see the users who belong to the same group as he does
    And I should see "Student 2"
    And I should not see "Student 1"
    # Non-editing teacher can award the badge
    And I set the field "potentialrecipients[]" to "Student 2 (student2@example.com)"
    And I press "Award badge"
    And I follow "Course Badge"
    And I should see "Recipients (1)"
    And I log out
    And I log in as "student2"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    And I should see "Course Badge"
    And I log out

  @javascript
  Scenario: Award course badge as non-editing teacher with more than one group
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I follow "Groups"
    And I set the field "groups" to "Class B (2)"
    And I press "Add/remove users"
    And I set the field "addselect" to "Teacher 2 (teacher2@example.com)"
    And I press "Add"
    And I log out
    When I log in as "teacher2"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    And I follow "Manage badges"
    And I follow "Course Badge"
    And I press "Award badge"
    And I set the field "role" to "Non-editing teacher"
    # Teacher 2 should see a "Separate groups" label and a dropdown menu with the groups he belongs to
    And I set the field "Separate groups" to "Class A"
    Then I should see "Student 2"
    And I should not see "Student 1"
    And I set the field "Separate groups" to "Class B"
    And I should see "Student 1"
    And I should not see "Student 2"
    And I log out

  @javascript
  Scenario: Award course badge as non-editing teacher without any group
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I follow "Groups"
    And I set the field "groups" to "Class A (2)"
    And I press "Add/remove users"
    And I set the field "removeselect" to "Teacher 2 (teacher2@example.com)"
    And I press "Remove"
    And I press "Back to groups"
    And I log out
    When I log in as "teacher2"
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Manage badges" in current page administration
    And I follow "Manage badges"
    And I follow "Course Badge"
    And I press "Award badge"
    # Teacher 2 shouldn't be able to go further
    Then I should see "Sorry, but you need to be part of a group to see this page."
