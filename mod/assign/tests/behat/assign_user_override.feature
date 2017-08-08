@mod @mod_assign @javascript
Feature: Assign user override
  In order to grant a student special access to an assignment
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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_onlinetext_wordlimit_enabled | 1 |
      | assignsubmission_onlinetext_wordlimit | 10 |
      | assignsubmission_file_enabled | 0 |
      | gradingduedate[enabled]       | 0 |

  Scenario: Add, modify then delete a user override
    When I follow "Test assignment name"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user      | Student1 |
      | id_duedate_enabled | 1 |
      | duedate[day]       | 1 |
      | duedate[month]     | January |
      | duedate[year]      | 2020 |
      | duedate[hour]      | 08 |
      | duedate[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    Then I click on "Edit" "link" in the "Sam1 Student1" "table_row"
    And I set the following fields to these values:
      | duedate[year] | 2030 |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I click on "Delete" "link"
    And I press "Continue"
    And I should not see "Sam1 Student1"

  Scenario: Duplicate a user override
    When I follow "Test assignment name"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user      | Student1 |
      | id_duedate_enabled | 1 |
      | duedate[day]       | 1 |
      | duedate[month]     | January |
      | duedate[year]      | 2020 |
      | duedate[hour]      | 08 |
      | duedate[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    Then I click on "copy" "link"
    And I set the following fields to these values:
      | Override user  | Student2  |
      | duedate[year] | 2030 |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I should see "Sam2 Student2"

  Scenario: Allow a user to have a different due date
    When I follow "Test assignment name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | id_duedate_enabled | 1 |
      | id_allowsubmissionsfromdate_enabled | 0 |
      | id_cutoffdate_enabled | 0 |
      | duedate[day]       | 1 |
      | duedate[month]     | January |
      | duedate[year]      | 2000 |
      | duedate[hour]      | 08 |
      | duedate[minute]    | 00 |
    And I press "Save and display"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user       | Student1 |
      | id_duedate_enabled | 1 |
      | duedate[day]       | 1 |
      | duedate[month]     | January |
      | duedate[year]      | 2020 |
      | duedate[hour]      | 08 |
      | duedate[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should see "Saturday, 1 January 2000, 8:00"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "Wednesday, 1 January 2020, 8:00"

  Scenario: Allow a user to have a different cut off date
    When I follow "Test assignment name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | id_duedate_enabled | 0 |
      | id_allowsubmissionsfromdate_enabled | 0 |
      | id_cutoffdate_enabled | 1 |
      | cutoffdate[day]       | 1 |
      | cutoffdate[month]     | January |
      | cutoffdate[year]      | 2000 |
      | cutoffdate[hour]      | 08 |
      | cutoffdate[minute]    | 00 |
    And I press "Save and display"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user       | Student1 |
      | id_cutoffdate_enabled | 1 |
      | cutoffdate[day]       | 1 |
      | cutoffdate[month]     | January |
      | cutoffdate[year]      | 2020 |
      | cutoffdate[hour]      | 08 |
      | cutoffdate[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should not see "Make changes to your submission"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "Make changes to your submission"

  Scenario: Allow a user to have a different start date
    When I follow "Test assignment name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | id_duedate_enabled | 0 |
      | id_allowsubmissionsfromdate_enabled | 1 |
      | id_cutoffdate_enabled | 0 |
      | allowsubmissionsfromdate[day]       | 1 |
      | allowsubmissionsfromdate[month]     | January |
      | allowsubmissionsfromdate[year]      | 2020 |
      | allowsubmissionsfromdate[hour]      | 08 |
      | allowsubmissionsfromdate[minute]    | 00 |
    And I press "Save and display"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user        | Student1 |
      | id_allowsubmissionsfromdate_enabled | 1 |
      | allowsubmissionsfromdate[day]       | 1 |
      | allowsubmissionsfromdate[month]     | January |
      | allowsubmissionsfromdate[year]      | 2015 |
      | allowsubmissionsfromdate[hour]      | 08 |
      | allowsubmissionsfromdate[minute]    | 00 |
    And I press "Save"
    And I should see "Thursday, 1 January 2015, 8:00"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should see "This assignment will accept submissions from Wednesday, 1 January 2020, 8:00"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should not see "This assignment will accept submissions from Wednesday, 1 January 2020, 8:00"
