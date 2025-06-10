@mod @mod_attendance
Feature: Teachers and Students can record session attendance
  In order to record session attendance
  As a student
  I need to be able to mark my own attendance to a session
  And as a teacher
  I need to be able to mark any students attendance to a session
  In order to report on session attendance
  As a teacher
  I need to be able to export session attendance and run reports
  In order to contact students with poor attendance
  As a teacher
  I need the ability to message a group of students with low attendance

  Background:
    Given the following "courses" exist:
      | fullname | shortname | summary                             | category | timecreated   | timemodified  |
      | Course 1 | C1        | Prove the attendance activity works | 0        | ##yesterday## | ##yesterday## |
    And the following "users" exist:
      | username    | firstname | lastname | email            | idnumber | department       | institution |
      | student1    | Sam       | Student  | student1@asd.com | 1234     | computer science | University of Nottingham |
      | teacher1    | Teacher   | One      | teacher1@asd.com | 5678     | computer science | University of Nottingham |
    And the following "course enrolments" exist:
      | course | user     | role           | timestart     |
      | C1     | student1 | student        | ##yesterday## |
      | C1     | teacher1 | editingteacher | ##yesterday## |
    And the following "activities" exist:
      | activity   | name       | course |
      | attendance | Attendance | C1     |

  @javascript
  Scenario: Students can mark their own attendance and teacher can hide specific status from students.
    Given I am on the "Attendance" "mod_attendance > View" page logged in as "teacher1"
    And I click on "Add session" "button"
    And I set the field "Allow students to record own attendance" to "1"
    And I set the following fields to these values:
      | id_sestime_starthour | 00 |
      | id_sestime_endhour   | 23 |
      | id_sestime_endminute | 55 |
    And I click on "id_submitbutton" "button"
    And I log out
    And I am on the "Attendance" "mod_attendance > View" page logged in as "student1"
    And I follow "Submit attendance"
    And I should see "Excused"
    And I log out
    And I am on the "Attendance" "mod_attendance > View" page logged in as "teacher1"
    And I click on "More" "link" in the ".secondary-navigation" "css_element"
    And I select "Status set" from secondary navigation
    And I set the field with xpath "//*[@id='statusrow3']/td[5]/select" to "0"
    And I press "Update"
    And I log out
    And I am on the "Attendance" "mod_attendance > View" page logged in as "student1"
    And I follow "Submit attendance"
    And I should not see "Excused"
    And I set the field "Present" to "1"
    And I press "Save changes"
    And I should see "Self-recorded"
    And I log out
    When I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then "Attendance taken by student" "link" should exist

  @javascript
  Scenario: If allowed, students can mark their own attendance before the session starts and teacher can choose which statuses are available.
    Given I am on the "Attendance" "mod_attendance > View" page logged in as "teacher1"
    And I click on "Add session" "button"
    And I set the field "Allow students to record own attendance" to "1"
    And I set the following fields to these values:
      | id_sessiondate       | ##tomorrow## |
      | id_sestime_starthour | 00 |
      | id_sestime_endhour   | 23 |
      | id_sestime_endminute | 55 |
    And I click on "id_submitbutton" "button"
    And I click on "More" "link" in the ".secondary-navigation" "css_element"
    And I select "Status set" from secondary navigation
    And I set the field with xpath "//*[@id='statusrow2']/td[6]/input" to "1"
    And I set the field with xpath "//*[@id='statusrow4']/td[6]/input" to "1"
    And I press "Update"
    And I log out
    And I am on the "Attendance" "mod_attendance > View" page logged in as "student1"
    And I follow "Report future absence"
    And I should see "Absent"
    And I should not see "Excused"
    And I set the field "Late" to "1"
    And I press "Save changes"
    And I should see "Self-recorded"
    And I log out
    When I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then "Attendance taken by student" "link" should exist

  @javascript
  Scenario: Teachers can view below % report and send a message
    Given I am on the "Attendance" "mod_attendance > View" page logged in as "teacher1"
    And I click on "Add session" "button"
    And I set the following fields to these values:
      | id_sestime_starthour | 01 |
      | id_sestime_endhour   | 02 |
    And I click on "id_submitbutton" "button"
    And I am on the "Attendance" "mod_attendance > Report" page
    And I follow "Below"
    And I set the field "cb_selector" to "1"
    And I click on "Send a message" "button"
    And I should see "Message body"
    And I should see "student1@asd.com"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then "Attendance report viewed" "link" should exist

  @javascript
  Scenario: Export report includes id number, department and institution
    Given I log in as "admin"
    And I navigate to "Users > Permissions > User policies" in site administration
    And the following config values are set as admin:
      | showuseridentity | idnumber,email,phone1,phone2,department,institution |

    And I log out
    And I am on the "Attendance" "mod_attendance > View" page logged in as "teacher1"
    And I click on "Add session" "button"
    And I set the following fields to these values:
      | id_sestime_starthour | 01 |
      | id_sestime_endhour   | 02 |
    And I click on "id_submitbutton" "button"
    And I follow "Export"
    Then the field "id_ident_idnumber" matches value "1"
    And the field "id_ident_institution" matches value "1"
    And the field "id_ident_department" matches value "1"

  @javascript
  Scenario: Test enabling custom user profile field
    # Add custom field.
    Given I log in as "admin"
    And I navigate to "Users > Accounts > User profile fields" in site administration
    And I click on "Create a new profile field" "link"
    And I click on "Text input" "link"
    And I set the following fields to these values:
      | Short name | superfield  |
      | Name       | Super field |
    And I click on "Save changes" "button"

    And I navigate to "Plugins > Activity modules > Attendance" in site administration
    And the "Export custom user profile fields" select box should contain "Super field"

  @javascript
  Scenario: Test adding custom user profile
    # Add custom field.
    Given I log in as "admin"
    And I navigate to "Users > Accounts > User profile fields" in site administration
    And I click on "Create a new profile field" "link"
    And I click on "Text input" "link"
    And I set the following fields to these values:
      | Short name | superfield  |
      | Name       | Super field |
    And I click on "Save changes" "button"

    And the following config values are set as admin:
    | customexportfields | superfield | attendance |

    And I log out
    And I am on the "Attendance" "mod_attendance > View" page logged in as "teacher1"
    And I click on "Add session" "button"
    And I set the following fields to these values:
      | id_sestime_starthour | 01 |
      | id_sestime_endhour   | 02 |
    And I click on "id_submitbutton" "button"
    And I follow "Export"
    Then the field "id_ident_superfield" matches value "1"

  # Removed dependency on behat_download to allow automated Travis CI tests to pass.
  # It would be good to add these back at some point.
