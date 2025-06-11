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
      | Course 1 | C1        | 0        | 1         |
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
    And the following "core_badges > Badge" exists:
      | name        | Course Badge                 |
      | course      | C1                           |
      | description | Course badge description     |
      | image       | badges/tests/behat/badge.png |
      | status      | 0                            |
      | type        | 2                            |
    And I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Badges" in current page administration
    And I follow "Course Badge"
    And I select "Criteria" from the "jump" singleselect
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    And I set the field "Teacher" to "1"
    And I set the field "Non-editing teacher" to "1"
    # Set to ANY of the roles awards badge.
    And I set the field "Any of the selected roles awards the badge" to "1"
    And I press "Save"
    And I press "Enable access"
    And I click on "Enable" "button" in the "Confirm" "dialogue"

  @javascript
  Scenario: Award course badge as non-editing teacher with only one group
    When I log in as "teacher2"
    And I am on "Course 1" course homepage
    And I navigate to "Badges" in current page administration
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
    And I am on "Course 1" course homepage
    And I navigate to "Badges" in current page administration
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
    And I am on the "Course 1" "groups" page
    And I set the field "groups" to "Class B (2)"
    And I press "Add/remove users"
    And I set the field "addselect" to "Teacher 2 (teacher2@example.com)"
    And I press "Add"
    And I log out
    When I log in as "teacher2"
    And I am on "Course 1" course homepage
    And I navigate to "Badges" in current page administration
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
    And I am on the "Course 1" "groups" page
    And I set the field "groups" to "Class A (2)"
    And I press "Add/remove users"
    And I set the field "removeselect" to "Teacher 2 (teacher2@example.com)"
    And I press "Remove"
    And I press "Back to groups"
    And I log out
    When I log in as "teacher2"
    And I am on "Course 1" course homepage
    And I navigate to "Badges" in current page administration
    And I follow "Course Badge"
    And I press "Award badge"
    # Teacher 2 shouldn't be able to go further
    Then I should see "Sorry, but you need to be part of a group to see this page."

  @javascript
  Scenario: Editing teacher can award badge to members of separate groups
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Badges" in current page administration
    And I follow "Course Badge"
    And I press "Award badge"
    When I set the field "role" to "Teacher"
    # Confirm that editing teacher sees a separate groups dropdown menu.
    Then "Separate groups" "select" should exist
    And I should see "All participants" in the "Separate groups" "select"
    # Confirm that all participants are displayed when All participants is selected.
    And I should see "Student 1" in the "potentialrecipients[]" "select"
    And I should see "Student 2" in the "potentialrecipients[]" "select"
    And I should see "Teacher 1" in the "potentialrecipients[]" "select"
    And I should see "Teacher 2" in the "potentialrecipients[]" "select"
    And I set the field "Separate groups" to "Class A"
    # Confirm that only members of selected group are displayed
    And I should not see "Student 1" in the "potentialrecipients[]" "select"
    And I should not see "Teacher 1" in the "potentialrecipients[]" "select"
    And I should see "Student 2" in the "potentialrecipients[]" "select"
    And I should see "Teacher 2" in the "potentialrecipients[]" "select"
    And I set the field "Separate groups" to "Class B"
    And I should not see "Student 2" in the "potentialrecipients[]" "select"
    And I should not see "Teacher 2" in the "potentialrecipients[]" "select"
    And I should see "Student 1" in the "potentialrecipients[]" "select"
    And I should see "Teacher 1" in the "potentialrecipients[]" "select"

  @javascript
  Scenario Outline: Teacher can award badge to members of visible groups
    Given I am on the "Course 1" "course editing" page logged in as teacher1
    And I expand all fieldsets
    # Set the group mode to visible groups.
    And I set the field "Group mode" to "Visible groups"
    And I press "Save and display"
    When I am on the "Course 1" course page logged in as <loggedinuser>
    And I navigate to "Badges" in current page administration
    And I follow "Course Badge"
    And I press "Award badge"
    And I set the field "role" to "<awarder>"
    # Confirm that teachers see a visible groups dropdown menu.
    Then "Visible groups" "select" should exist
    # Confirm that My groups option group exists.
    And "optgroup[label='My groups']" "css_element" should exist in the "select[name='group']" "css_element"
    # Confirm that Other groups option group exists.
    And "optgroup[label='Other groups']" "css_element" should exist in the "select[name='group']" "css_element"
    # Confirm that all participants are displayed when All participants is selected.
    And I set the field "Visible groups" to "All participants"
    And I should see "Student 1" in the "potentialrecipients[]" "select"
    And I should see "Student 2" in the "potentialrecipients[]" "select"
    And I should see "Teacher 1" in the "potentialrecipients[]" "select"
    And I should see "Teacher 2" in the "potentialrecipients[]" "select"
    # Confirm that only members of selected group are displayed.
    And I set the field "Visible groups" to "Class A"
    And I should not see "Student 1" in the "potentialrecipients[]" "select"
    And I should not see "Teacher 1" in the "potentialrecipients[]" "select"
    And I should see "Student 2" in the "potentialrecipients[]" "select"
    And I should see "Teacher 2" in the "potentialrecipients[]" "select"
    And I set the field "Visible groups" to "Class B"
    And I should not see "Student 2" in the "potentialrecipients[]" "select"
    And I should not see "Teacher 2" in the "potentialrecipients[]" "select"
    And I should see "Student 1" in the "potentialrecipients[]" "select"
    And I should see "Teacher 1" in the "potentialrecipients[]" "select"

    Examples:
      | loggedinuser | awarder             |
      | teacher1     | Teacher             |
      | teacher2     | Non-editing teacher |

  @javascript
  Scenario Outline: Teacher can award badge to members when group mode is set to no groups
    Given I am on the "Course 1" "course editing" page logged in as teacher1
    And I expand all fieldsets
    # Set the group mode to no groups.
    And I set the field "Group mode" to "No groups"
    And I press "Save and display"
    When I am on the "Course 1" course page logged in as <loggedinuser>
    And I navigate to "Badges" in current page administration
    And I follow "Course Badge"
    And I press "Award badge"
    And I set the field "role" to "<awarder>"
    # Confirm that no group dropdowns don't exist.
    Then "Separate groups" "select" should not exist
    And "Visible groups" "select" should not exist
    # Confirm all participants are displayed.
    And I should see "Student 1" in the "potentialrecipients[]" "select"
    And I should see "Student 2" in the "potentialrecipients[]" "select"
    And I should see "Teacher 1" in the "potentialrecipients[]" "select"
    And I should see "Teacher 2" in the "potentialrecipients[]" "select"

    Examples:
      | loggedinuser | awarder             |
      | teacher1     | Teacher             |
      | teacher2     | Non-editing teacher |
