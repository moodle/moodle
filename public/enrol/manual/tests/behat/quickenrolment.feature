@enrol @enrol_manual
Feature: Teacher can search and enrol users one by one into the course
  In order to quickly enrol particular students into my course
  As a teacher
  I can search for the students and enrol them into the course

  Background:
    Given the following "custom profile fields" exist:
      | datatype | shortname  | name           |
      | text     | customid   | Custom user id |
    And the following "users" exist:
      | username    | firstname | lastname | email                   | profile_field_customid |
      | teacher001  | Teacher   | 001      | teacher001@example.com  |                        |
      | student001  | Student   | 001      | student001@example.com  | Q994                   |
      | student002  | Student   | 002      | student002@example.com  | Q008                   |
      | student003  | Student   | 003      | student003@example.com  | Z442                   |
      | student004  | Student   | 004      | student004@example.com  |                        |
      | student005  | Student   | 005      | student005@example.com  |                        |
      | student006  | Student   | 006      | student006@example.com  |                        |
      | student007  | Student   | 007      | student007@example.com  |                        |
      | student008  | Student   | 008      | student008@example.com  |                        |
      | student009  | Student   | 009      | student009@example.com  |                        |
      | student010  | Student   | 010      | student010@example.com  |                        |
      | student011  | Student   | 011      | student011@example.com  |                        |
      | student012  | Student   | 012      | student012@example.com  |                        |
      | student013  | Student   | 013      | student013@example.com  |                        |
      | student014  | Student   | 014      | student014@example.com  |                        |
      | student015  | Student   | 015      | student015@example.com  |                        |
      | student016  | Student   | 016      | student016@example.com  |                        |
      | student017  | Student   | 017      | student017@example.com  |                        |
      | student018  | Student   | 018      | student018@example.com  |                        |
      | student019  | Student   | 019      | student019@example.com  |                        |
      | student020  | Student   | 020      | student020@example.com  |                        |
      | student021  | Student   | 021      | student021@example.com  |                        |
      | student022  | Student   | 022      | student022@example.com  |                        |
      | student023  | Student   | 023      | student023@example.com  |                        |
      | student024  | Student   | 024      | student024@example.com  |                        |
      | student025  | Student   | 025      | student025@example.com  |                        |
      | student026  | Student   | 026      | student026@example.com  |                        |
      | student027  | Student   | 027      | student027@example.com  |                        |
      | student028  | Student   | 028      | student028@example.com  |                        |
      | student029  | Student   | 029      | student029@example.com  |                        |
      | student030  | Student   | 030      | student030@example.com  |                        |
      | student031  | Student   | 031      | student031@example.com  |                        |
      | student032  | Student   | 032      | student032@example.com  |                        |
      | student033  | Student   | 033      | student033@example.com  |                        |
      | student034  | Student   | 034      | student034@example.com  |                        |
      | student035  | Student   | 035      | student035@example.com  |                        |
      | student036  | Student   | 036      | student036@example.com  |                        |
      | student037  | Student   | 037      | student037@example.com  |                        |
      | student038  | Student   | 038      | student038@example.com  |                        |
      | student039  | Student   | 039      | student039@example.com  |                        |
      | student040  | Student   | 040      | student040@example.com  |                        |
      | student041  | Student   | 041      | student041@example.com  |                        |
      | student042  | Student   | 042      | student042@example.com  |                        |
      | student043  | Student   | 043      | student043@example.com  |                        |
      | student044  | Student   | 044      | student044@example.com  |                        |
      | student045  | Student   | 045      | student045@example.com  |                        |
      | student046  | Student   | 046      | student046@example.com  |                        |
      | student047  | Student   | 047      | student047@example.com  |                        |
      | student048  | Student   | 048      | student048@example.com  |                        |
      | student049  | Student   | 049      | student049@example.com  |                        |
      | student050  | Student   | 050      | student050@example.com  |                        |
      | student051  | Student   | 051      | student051@example.com  |                        |
      | student052  | Student   | 052      | student052@example.com  |                        |
      | student053  | Student   | 053      | student053@example.com  |                        |
      | student054  | Student   | 054      | student054@example.com  |                        |
      | student055  | Student   | 055      | student055@example.com  |                        |
      | student056  | Student   | 056      | student056@example.com  |                        |
      | student057  | Student   | 057      | student057@example.com  |                        |
      | student058  | Student   | 058      | student058@example.com  |                        |
      | student059  | Student   | 059      | student059@example.com  |                        |
      | student060  | Student   | 060      | student060@example.com  |                        |
      | student061  | Student   | 061      | student061@example.com  |                        |
      | student062  | Student   | 062      | student062@example.com  |                        |
      | student063  | Student   | 063      | student063@example.com  |                        |
      | student064  | Student   | 064      | student064@example.com  |                        |
      | student065  | Student   | 065      | student065@example.com  |                        |
      | student066  | Student   | 066      | student066@example.com  |                        |
      | student067  | Student   | 067      | student067@example.com  |                        |
      | student068  | Student   | 068      | student068@example.com  |                        |
      | student069  | Student   | 069      | student069@example.com  |                        |
      | student070  | Student   | 070      | student070@example.com  |                        |
      | student071  | Student   | 071      | student071@example.com  |                        |
      | student072  | Student   | 072      | student072@example.com  |                        |
      | student073  | Student   | 073      | student073@example.com  |                        |
      | student074  | Student   | 074      | student074@example.com  |                        |
      | student075  | Student   | 075      | student075@example.com  |                        |
      | student076  | Student   | 076      | student076@example.com  |                        |
      | student077  | Student   | 077      | student077@example.com  |                        |
      | student078  | Student   | 078      | student078@example.com  |                        |
      | student079  | Student   | 079      | student079@example.com  |                        |
      | student080  | Student   | 080      | student080@example.com  |                        |
      | student081  | Student   | 081      | student081@example.com  |                        |
      | student082  | Student   | 082      | student082@example.com  |                        |
      | student083  | Student   | 083      | student083@example.com  |                        |
      | student084  | Student   | 084      | student084@example.com  |                        |
      | student085  | Student   | 085      | student085@example.com  |                        |
      | student086  | Student   | 086      | student086@example.com  |                        |
      | student087  | Student   | 087      | student087@example.com  |                        |
      | student088  | Student   | 088      | student088@example.com  |                        |
      | student089  | Student   | 089      | student089@example.com  |                        |
      | student090  | Student   | 090      | student090@example.com  |                        |
      | student091  | Student   | 091      | student091@example.com  |                        |
      | student092  | Student   | 092      | student092@example.com  |                        |
      | student093  | Student   | 093      | student093@example.com  |                        |
      | student094  | Student   | 094      | student094@example.com  |                        |
      | student095  | Student   | 095      | student095@example.com  |                        |
      | student096  | Student   | 096      | student096@example.com  |                        |
      | student097  | Student   | 097      | student097@example.com  |                        |
      | student098  | Student   | 098      | student098@example.com  |                        |
      | student099  | Student   | 099      | student099@example.com  |                        |
    And the following "courses" exist:
      | fullname   | shortname | format | startdate       |
      | Course 001 | C001      | weeks  | ##1 month ago## |
    And the following "course enrolments" exist:
      | user       | course | role           | timestart       |
      | teacher001 | C001   | editingteacher | ##1 month ago## |
    And I log in as "teacher001"
    And I am on "Course 001" course homepage

  @javascript
  Scenario: Teacher can search and enrol one particular student
    Given I navigate to course participants
    And I press "Enrol users"
    When I set the field "Select users" to "student001"
    And I should see "Student 001"
    And I click on "Enrol users" "button" in the "Enrol users" "dialogue"
    Then I should see "Active" in the "Student 001" "table_row"
    And I should see "1 enrolled users"

  @javascript
  Scenario: Searching for a non-existing user
    Given I navigate to course participants
    And I press "Enrol users"
    And I click on "Select users" "field"
    And I type "qwertyuiop"
    Then I should see "No suggestions"

  @javascript
  Scenario: If there are less than 100 matching users, all are displayed for selection
    Given I navigate to course participants
    And I press "Enrol users"
    When I click on "Select users" "field"
    And I type "example.com"
    Then "Student 099" "autocomplete_suggestions" should exist

  @javascript
  Scenario: If there are more than 100 matching users, inform there are too many.
    Given the following "users" exist:
      | username    | firstname | lastname | email                   |
      | student100  | Student   | 100      | student100@example.com  |
      | student101  | Student   | 101      | student101@example.com  |
    And I navigate to course participants
    And I press "Enrol users"
    When I click on "Select users" "field"
    And I type "example.com"
    Then I should see "Too many users (>100) to show"

  @javascript
  Scenario: Changing the Maximum users per page setting affects the enrolment pop-up.
    Given the following config values are set as admin:
      | maxusersperpage | 5 |
    And I navigate to course participants
    And I press "Enrol users"
    When I click on "Select users" "field"
    And I type "student00"
    Then I should see "Too many users (>5) to show"

  @javascript
  Scenario: Change the Show user identity setting affects the enrolment pop-up.
    Given I log out
    When I log in as "admin"
    Then the following "users" exist:
      | username    | firstname | lastname | email                   | phone1     | phone2     | department | institution | city    | country  |
      | student100  | Student   | 100      | student100@example.com  | 1234567892 | 1234567893 | ABC1       | ABC2        | CITY1   | GB       |
    And the following config values are set as admin:
      | showuseridentity | idnumber,email,city,country,phone1,phone2,department,institution |
    When I am on "Course 001" course homepage
    Then I navigate to course participants
    And I press "Enrol users"
    And I click on "Select users" "field"
    And I type "student100@example.com"
    Then I should see "student100@example.com, CITY1, GB, 1234567892, 1234567893, ABC1, ABC2"
    # Remove identity field in setting User policies
    And the following config values are set as admin:
      | showuseridentity | idnumber,email,phone1,phone2,department,institution |
    And I am on "Course 001" course homepage
    And I navigate to course participants
    And I press "Enrol users"
    And I click on "Select users" "field"
    And I type "student100@example.com"
    And I should see "student100@example.com, 1234567892, 1234567893, ABC1, ABC2"

  @javascript
  Scenario: Custom user profile fields work for search and display, if user has permission
    Given the following config values are set as admin:
      | showuseridentity | email,profile_field_customid |
    And I navigate to course participants
    And I press "Enrol users"
    When I set the field "Select users" to "Q994"
    Then I should see "student001@example.com, Q994"
    And I click on "Cancel" "button" in the "Enrol users" "dialogue"
    And the following "permission overrides" exist:
      | capability                   | permission | role           | contextlevel | reference |
      | moodle/site:viewuseridentity | Prevent    | editingteacher | Course       | C001      |
    And I press "Enrol users"
    # Do this by keyboard because the 'I set the field' step doesn't let you set it to a missing value.
    And I press tab
    And I press tab
    And I press tab
    And I type "Q994"
    And I should see "No suggestions"

  @javascript
  Scenario: Add participant to a group
    Given I navigate to course participants
    When I press "Enrol users"
    # No group created yet.
    Then I should not see "Add to group" in the "Enrol users" "dialogue"
    And I should not see "Show groups" in the "Enrol users" "dialogue"
    And I click on "Cancel" "button" in the "Enrol users" "dialogue"
    And I navigate to course participants
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C001   | G1       |
    # Now we have a group, we can test adding a user.
    And I press "Enrol users"
    And I set the field "Select users" to "student001"
    And I click on "showgroups" "checkbox"
    And I set the field "Add to group" to "Group 1"
    And I click on "Enrol users" "button" in the "Enrol users" "dialogue"
    And I should see "Group 1" in the "Student 001" "table_row"

# The following tests are commented out as a result of MDL-66339.
#  @javascript
#  Scenario: Enrol user from participants page
#    Given I navigate to course participants
#    # Enrol user to course
#    And I press "Enrol users"
#    And I set the field "Select users" to "example.com"
#    And I expand the "Select users" autocomplete
#    When I click on "Student 099" item in the autocomplete list
#    Then I should see "Student 099" in the list of options for the "Select users" autocomplete
#    And I click on "Show more" "button"
#    # Fill data to input duration
#    And "input[name='timeend[enabled]'][checked=checked]" "css_element" should not exist
#    And the "Enrolment duration" "select" should be enabled
#    And I set the field "duration" to "2"
#    # Fill data to input end time
#    And I set the field "Starting from" to "2"
#    And I set the field "timeend[enabled]" to "1"
#    And I set the field "timeend[day]" to "10"
#    And the "Enrolment duration" "select" should be disabled
#    And I click on "Enrol users" "button" in the "Enrol users" "dialogue"
#    And I am on "Course 001" course homepage
#    And I navigate to course participants
#    And I should see "Student 099" in the "participants" "table"
#    And I click on "Edit enrolment" "icon" in the "Student 099" "table_row"
#    And the field "timeend[day]" matches value "10"
#
#  @javascript
#  Scenario: Update Enrol user
#    Given I am on "Course 001" course homepage
#    And I navigate to course participants
#    When I click on "Edit enrolment" "icon" in the "Teacher 001" "table_row"
#    Then the "Enrolment duration" "select" should be enabled
#    # Fill duration
#    And "input[name='timeend[enabled]'][checked=checked]" "css_element" should not exist
#    And the "Enrolment duration" "select" should be enabled
#    And I set the field "duration" to "2"
#    # Fill end time
#    And I set the field "timeend[enabled]" to "1"
#    And I set the field "timeend[day]" to "28"
#    And the "Enrolment duration" "select" should be disabled
#    And I press "Save changes"
#    And I click on "Edit enrolment" "icon" in the "Teacher 001" "table_row"
#    And the field "timeend[day]" matches value "28"
