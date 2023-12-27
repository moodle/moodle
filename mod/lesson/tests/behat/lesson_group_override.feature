@mod @mod_lesson
Feature: Lesson group override
  In order to grant a student special access to a lesson
  As a teacher
  I need to create an override for that user.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Tina | Teacher1 | teacher1@example.com |
      | student1 | Sam1 | Student1 | student1@example.com |
      | student2 | Sam2 | Student2 | student2@example.com |
      | student3 | Sam3 | Student3 | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    Given the following "group members" exist:
      | user     | group   |
      | student1 | G1 |
      | student2 | G2 |
      | student3 | G1 |
    And the following "activities" exist:
      | activity | name             | groupmode  | course | idnumber |
      | lesson   | Test lesson name | 1          | C1     | lesson1  |
    And the following "mod_lesson > page" exist:
      | lesson           | qtype     | title                 | content             |
      | Test lesson name | truefalse | True/false question 1 | Cat is an amphibian |
    And the following "mod_lesson > answers" exist:
      | page                  | answer        | response | jumpto        | score |
      | True/false question 1 | False         | Correct  | Next page     | 1     |
      | True/false question 1 | True          | Wrong    | This page     | 0     |

  Scenario: Add, modify then delete a group override
    Given I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    And I set the following fields to these values:
      | Override group      | Group 1 |
      | id_deadline_enabled | 1 |
      | deadline[day]       | 1 |
      | deadline[month]     | January |
      | deadline[year]      | 2020 |
      | deadline[hour]      | 08 |
      | deadline[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    Then I click on "Edit" "link" in the "region-main" "region"
    And I set the following fields to these values:
      | deadline[year] | 2030 |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I click on "Delete" "link"
    And I press "Continue"
    And I should not see "Group 1"

  Scenario: Duplicate a user override
    Given I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    When I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    And I set the following fields to these values:
      | Override group      | Group 1 |
      | id_deadline_enabled | 1 |
      | deadline[day]       | 1 |
      | deadline[month]     | January |
      | deadline[year]      | 2020 |
      | deadline[hour]      | 08 |
      | deadline[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    Then I click on "copy" "link"
    And I set the following fields to these values:
      | Override group | Group 2  |
      | deadline[year] | 2030 |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I should see "Group 2"

  Scenario: Allow a single group to have re-take the lesson
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Re-takes allowed | 0 |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    And I set the following fields to these values:
      | Override group   | Group 1 |
      | Re-takes allowed | 1 |
    And I press "Save"
    And I should see "Re-takes allowed"
    Given I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I am on the "Test lesson name" "lesson activity" page
    Then I should not see "You are not allowed to retake this lesson."
    And I should see "Cat is an amphibian"
    Given I am on the "Test lesson name" "lesson activity" page logged in as student2
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I am on the "Test lesson name" "lesson activity" page
    And I should see "You are not allowed to retake this lesson."

  Scenario: Allow a single group to have a different password
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Password protected lesson | Yes |
      | id_password               | moodle_rules |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    And I set the following fields to these values:
      | Override group            | Group 1 |
      | Password protected lesson | 12345 |
    And I press "Save"
    And I should see "Password protected lesson"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    Then I should see "Test lesson name is a password protected lesson"
    And I should not see "Cat is an amphibian"
    And I set the field "userpassword" to "moodle_rules"
    And I press "Continue"
    And I should see "Login failed, please try again..."
    And I should see "Test lesson name is a password protected lesson"
    And I set the field "userpassword" to "12345"
    And I press "Continue"
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    And I should see "Test lesson name is a password protected lesson"
    And I should not see "Cat is an amphibian"
    And I set the field "userpassword" to "12345"
    And I press "Continue"
    And I should see "Login failed, please try again..."
    And I should see "Test lesson name is a password protected lesson"
    And I set the field "userpassword" to "moodle_rules"
    And I press "Continue"

  Scenario: Allow a group to have a different due date
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | id_deadline_enabled | 1 |
      | deadline[day]       | 1 |
      | deadline[month]     | January |
      | deadline[year]      | 2000 |
      | deadline[hour]      | 08 |
      | deadline[minute]    | 00 |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    And I set the following fields to these values:
      | Override group      | Group 1 |
      | id_deadline_enabled | 1 |
      | deadline[day]       | 1 |
      | deadline[month]     | January |
      | deadline[year]      | 2030 |
      | deadline[hour]      | 08 |
      | deadline[minute]    | 00 |
    And I press "Save"
    And I should see "Lesson closes"
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    Then the activity date in "Test lesson name" should contain "Closed: Saturday, 1 January 2000, 8:00"
    And I should not see "Cat is an amphibian"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "Cat is an amphibian"

  Scenario: Allow a group to have a different start date
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | id_available_enabled | 1 |
      | available[day]       | 1 |
      | available[month]     | January |
      | available[year]      | 2030 |
      | available[hour]      | 08 |
      | available[minute]    | 00 |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    And I set the following fields to these values:
      | Override group       | Group 1 |
      | id_available_enabled | 1 |
      | available[day]       | 1 |
      | available[month]     | January |
      | available[year]      | 2015 |
      | available[hour]      | 08 |
      | available[minute]    | 00 |
    And I press "Save"
    And I should see "Lesson opens"
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    Then the activity date in "Test lesson name" should contain "Opens: Tuesday, 1 January 2030, 8:00"
    And I should not see "Cat is an amphibian"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "Cat is an amphibian"

  Scenario: Allow a single group to have multiple attempts at each question
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Re-takes allowed | 1 |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    And I set the following fields to these values:
      | Override group             | Group 1 |
      | Maximum number of attempts per question | 2 |
    And I press "Save"
    And I should see "Maximum number of attempts per question"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    Then I press "Continue"
    And I should see "Congratulations - end of lesson reached"

  @javascript
  Scenario: Add both a user and group override and verify that both are applied correctly
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    When I set the following fields to these values:
      | id_available_enabled | 1 |
      | available[day]       | 1 |
      | available[month]     | January |
      | available[year]      | 2040 |
      | available[hour]      | 08 |
      | available[minute]    | 00 |
    And I press "Save and display"
    And I am on the "Test lesson name" "lesson activity" page
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    And I set the following fields to these values:
      | Override group       | Group 1 |
      | id_available_enabled | 1 |
      | available[day]       | 1 |
      | available[month]     | January |
      | available[year]      | 2030 |
      | available[hour]      | 08 |
      | available[minute]    | 00 |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I am on the "Test lesson name" "lesson activity" page
    And I navigate to "Overrides" in current page administration
    And I follow "Add user override"
    And I set the following fields to these values:
      | Override user        | Student1 |
      | id_available_enabled | 1 |
      | available[day]       | 1 |
      | available[month]     | January |
      | available[year]      | 2031 |
      | available[hour]      | 08 |
      | available[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2031, 8:00"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And the activity date in "Test lesson name" should contain "Opens: Wednesday, 1 January 2031, 8:00"
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    And the activity date in "Test lesson name" should contain "Opens: Sunday, 1 January 2040, 8:00"
    And I am on the "Test lesson name" "lesson activity" page logged in as student3
    And the activity date in "Test lesson name" should contain "Opens: Tuesday, 1 January 2030, 8:00"

  Scenario: Override a group when teacher is in no group, and does not have accessallgroups permission, and the activity's group mode is 'separate groups'
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name     | course | idnumber | groupmode |
      | lesson   | Lesson 2 | C1     | lesson2  | 1         |
    When I am on the "Lesson 2" "lesson activity" page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    Then I should see "No groups you can access."
    And I should not see "Add group override"

  Scenario: A teacher without accessallgroups permission should only be able to add group override for their groups, when the activity's group mode is 'separate groups'
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name     | course | idnumber | groupmode |
      | lesson   | Lesson 2 | C1     | lesson2  | 1         |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
    When I am on the "Lesson 2" "lesson activity" page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    Then the "Override group" select box should contain "Group 1"
    And the "Override group" select box should not contain "Group 2"

  Scenario: A teacher without accessallgroups permission should only be able to see the group overrides for their groups, when the activity's group mode is 'separate groups'
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name     | course | idnumber | groupmode |
      | lesson   | Lesson 2 | C1     | lesson2  | 1         |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
    And I am on the "Lesson 2" "lesson activity" page logged in as admin
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    And I set the following fields to these values:
      | Override group       | Group 1 |
      | id_available_enabled | 1       |
      | available[day]       | 1       |
      | available[month]     | January |
      | available[year]      | 2020    |
      | available[hour]      | 08      |
      | available[minute]    | 00      |
    And I press "Save and enter another override"
    And I set the following fields to these values:
      | Override group       | Group 2 |
      | id_available_enabled | 1       |
      | available[day]       | 1       |
      | available[month]     | January |
      | available[year]      | 2020    |
      | available[hour]      | 08      |
      | available[minute]    | 00      |
    And I press "Save"
    When I am on the "Lesson 2" "lesson activity" page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    Then I should see "Group 1" in the ".generaltable" "css_element"
    And I should not see "Group 2" in the ".generaltable" "css_element"

  Scenario: "Not visible" groups should not be available for group overrides
    Given the following "groups" exist:
      | name                                 | course | idnumber | visibility | participation |
      | Visible to everyone/Participation         | C1     | VP       | 0          | 1             |
      | Only Only visible to members/Participation     | C1     | MP       | 1          | 1             |
      | Only see own membership                   | C1     | O        | 2          | 0             |
      | Not visible                          | C1     | N        | 3          | 0             |
      | Visible to everyone/Non-Participation     | C1     | VN       | 0          | 0             |
      | Only visible to members/Non-Participation | C1     | MN       | 1          | 0             |
    When I am on the "lesson1" Activity page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    Then I should see "Visible to everyone/Participation" in the "Override group" "select"
    And I should see "Visible to everyone/Non-Participation" in the "Override group" "select"
    And I should see "Only visible to members" in the "Override group" "select"
    And I should see "Only visible to members/Non-Participation" in the "Override group" "select"
    And I should see "Only see own membership" in the "Override group" "select"
    And I should not see "Not visible" in the "Override group" "select"
