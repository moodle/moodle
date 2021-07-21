@mod @mod_lesson
Feature: Lesson user override
  In order to grant a student special access to a lesson
  As a teacher
  I need to create an override for that user.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Tina | Teacher1 | teacher1@example.com |
      | student1 | Sam1 | Student1 | student1@example.com |
      | student2 | Sam2 | Student2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity | name             | intro                   | course | idnumber |
      | lesson   | Test lesson name | Test lesson description | C1     | lesson1  |
    And I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title           | True/false question 1 |
      | Page contents        | Cat is an amphibian |
      | id_answer_editor_0   | False |
      | id_response_editor_0 | Correct |
      | id_jumpto_0          | Next page |
      | id_answer_editor_1   | True |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1          | This page |
    And I press "Save page"
    And I log out

  @javascript
  Scenario: Add, modify then delete a user override
    Given I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user       | Student1 |
      | id_deadline_enabled | 1 |
      | deadline[day]       | 1 |
      | deadline[month]     | January |
      | deadline[year]      | 2020 |
      | deadline[hour]      | 08 |
      | deadline[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    Then I click on "Edit" "link" in the "Sam1 Student1" "table_row"
    And I set the following fields to these values:
      | deadline[year] | 2030 |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I click on "Delete" "link"
    And I press "Continue"
    And I should not see "Sam1 Student1"

  @javascript
  Scenario: Duplicate a user override
    Given I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user       | Student1 |
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
      | Override user  | Student2  |
      | deadline[year] | 2030 |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I should see "Sam2 Student2"

  @javascript
  Scenario: Allow a single user to have re-take the lesson
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Re-takes allowed | 0 |
    And I press "Save and display"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user    | Student1  |
      | Re-takes allowed | 1 |
    And I press "Save"
    And I should see "Re-takes allowed"
    And I log out
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    When I am on the "Test lesson name" "lesson activity" page
    Then I should not see "You are not allowed to retake this lesson."
    And I should see "Cat is an amphibian"
    And I log out
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I am on the "Test lesson name" "lesson activity" page
    And I should see "You are not allowed to retake this lesson."

  @javascript
  Scenario: Allow a single user to have a different password
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Password protected lesson | Yes |
      | id_password               | moodle_rules |
    And I press "Save and display"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user             | Student1  |
      | Password protected lesson | 12345 |
    And I press "Save"
    And I should see "Password protected lesson"
    And I log out
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
    And I log out
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    And I should see "Test lesson name is a password protected lesson"
    And I should not see "Cat is an amphibian"
    And I set the field "userpassword" to "12345"
    And I press "Continue"
    And I should see "Login failed, please try again..."
    And I should see "Test lesson name is a password protected lesson"
    And I set the field "userpassword" to "moodle_rules"
    And I press "Continue"

  @javascript
  Scenario: Allow a user to have a different due date
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | id_deadline_enabled | 1 |
      | deadline[day]       | 1 |
      | deadline[month]     | January |
      | deadline[year]      | 2000 |
      | deadline[hour]      | 08 |
      | deadline[minute]    | 00 |
    And I press "Save and display"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user       | Student1 |
      | id_deadline_enabled | 1 |
      | deadline[day]       | 1 |
      | deadline[month]     | January |
      | deadline[year]      | 2030 |
      | deadline[hour]      | 08 |
      | deadline[minute]    | 00 |
    And I press "Save"
    And I should see "Lesson closes"
    And I log out
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    And I wait until the page is ready
    Then the activity date in "Test lesson name" should contain "Closed: Saturday, 1 January 2000, 8:00"
    And I should not see "Cat is an amphibian"
    And I log out
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "Cat is an amphibian"

  @javascript
  Scenario: Allow a user to have a different start date
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | id_available_enabled | 1 |
      | available[day]       | 1 |
      | available[month]     | January |
      | available[year]      | 2030 |
      | available[hour]      | 08 |
      | available[minute]    | 00 |
    And I press "Save and display"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user        | Student1 |
      | id_available_enabled | 1 |
      | available[day]       | 1 |
      | available[month]     | January |
      | available[year]      | 2015 |
      | available[hour]      | 08 |
      | available[minute]    | 00 |
    And I press "Save"
    And I should see "Lesson opens"
    And I log out
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    And I wait until the page is ready
    Then the activity date in "Test lesson name" should contain "Opens: Tuesday, 1 January 2030, 8:00"
    And I should not see "Cat is an amphibian"
    And I log out
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "Cat is an amphibian"

  @javascript
  Scenario: Allow a single user to have multiple attempts at each question
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Re-takes allowed | 1 |
    And I press "Save and display"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user              | Student1  |
      | Maximum number of attempts per question | 2 |
    And I press "Save"
    And I should see "Maximum number of attempts per question"
    And I log out
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
    And I log out
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    Then I press "Continue"
    And I should see "Congratulations - end of lesson reached"

  Scenario: Override a user when teacher is in no group, and does not have accessallgroups permission, and the activity's group mode is 'separate groups'
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name     | intro                | course | idnumber | groupmode |
      | lesson   | Lesson 2 | Lesson 2 description | C1     | lesson2  | 1         |
    And I am on the "Lesson 2" "lesson activity" page logged in as teacher1
    And I navigate to "User overrides" in current page administration
    Then I should see "No groups you can access."
    And the "Add user override" "button" should be disabled

  Scenario: A teacher without accessallgroups permission should only be able to add user override for their group-mates, when the activity's group mode is 'separate groups'
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name     | intro                | course | idnumber | groupmode |
      | lesson   | Lesson 2 | Lesson 2 description | C1     | lesson2  | 1         |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
      | student1 | G1    |
      | student2 | G2    |
    When I am on the "Lesson 2" "lesson activity" page logged in as teacher1
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    Then the "Override user" select box should contain "Sam1 Student1, student1@example.com"
    And the "Override user" select box should not contain "Sam2 Student2, student2@example.com"

  @javascript
  Scenario: A teacher without accessallgroups permission should only be able to see the user override for their group-mates, when the activity's group mode is 'separate groups'
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name     | intro                | course | idnumber | groupmode |
      | lesson   | Lesson 2 | Lesson 2 description | C1     | lesson2  | 1         |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
      | student1 | G1    |
      | student2 | G2    |
    And I am on the "Lesson 2" "lesson activity" page logged in as admin
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user       | Student1 |
      | id_deadline_enabled | 1        |
      | deadline[day]       | 1        |
      | deadline[month]     | January  |
      | deadline[year]      | 2020     |
      | deadline[hour]      | 08       |
      | deadline[minute]    | 00       |
    And I press "Save and enter another override"
    And I set the following fields to these values:
      | Override user       | Student2 |
      | id_deadline_enabled | 1        |
      | deadline[day]       | 1        |
      | deadline[month]     | January  |
      | deadline[year]      | 2020     |
      | deadline[hour]      | 08       |
      | deadline[minute]    | 00       |
    And I press "Save"
    And I log out
    When I am on the "Lesson 2" "lesson activity" page logged in as teacher1
    And I navigate to "User overrides" in current page administration
    Then I should see "Student1" in the ".generaltable" "css_element"
    And I should not see "Student2" in the ".generaltable" "css_element"

  @javascript
  Scenario: Create a user override when the lesson is not available to the student
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Availability" to "Hide from students"
    And I click on "Save and display" "button"
    When I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user              | Student1 |
      | Maximum number of attempts per question | 2 |
    And I press "Save"
    Then I should see "This override is inactive"
    And "Edit" "icon" should exist in the "Sam1 Student1" "table_row"
    And "copy" "icon" should exist in the "Sam1 Student1" "table_row"
    And "Delete" "icon" should exist in the "Sam1 Student1" "table_row"
