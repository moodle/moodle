@core @core_user @javascript
Feature: The student can navigate to their grades page and user grade report.
  In order to view my grades and the user grade report
  As a user
  I need to log in and browse to my grades.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | parent1 | Parent | 1 | parent1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
      | Course 2 | C2 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | teacher1 | C1 | editingteacher |
      | student1 | C2 | student |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | grade |
      | assign | C1 | a1 | Test assignment one | Submit something! | 300 |
      | assign | C1 | a2 | Test assignment two | Submit something! | 100 |
      | assign | C1 | a3 | Test assignment three | Submit something! | 150 |
      | assign | C2 | a4 | Test assignment four | Submit something! | 150 |
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "150.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "67.00" to the user "Student 1" for the grade item "Test assignment two"
    And I press "Save changes"
    And I log out

  Scenario: Navigation to Grades and the user grade report.
    When I log in as "student1"
    And I follow "Grades" in the user menu
    Then the following should exist in the "overview-grade" table:
    | Course name | Grade |
    | Course 2 | - |
    | Course 1 | 217.00 |
    And I click on "Course 1" "link" in the "region-main" "region"
    And the following should exist in the "user-grade" table:
    | Grade item | Calculated weight | Grade | Range | Percentage | Contribution to course total |
    | Test assignment one | 75.00 % | 150.00 | 0–300 | 50.00 % | 37.50 % |
    | Test assignment two | 25.00 % | 67.00  | 0–100 | 67.00 % | 16.75 % |
    | Test assignment three | 0.00 %( Empty ) | - | 0–150 | - | 0.00 % |

  Scenario: Change Grades settings to go to a custom url.
    Given the following config values are set as admin:
      | grade_mygrades_report  | external             |
      | gradereport_mygradeurl | /badges/mybadges.php |
    And I log in as "student1"
    And I follow "Grades" in the user menu
    Then I should see "My badges from Acceptance test site web site"

  Scenario: Log in as a parent and view a childs grades.
    Given the following "role" exists:
      | shortname                            | Parent |
      | name                                 | Parent |
      | context_user                         | 1      |
      | moodle/user:editprofile              | allow  |
      | moodle/user:viewalldetails           | allow  |
      | moodle/user:viewuseractivitiesreport | allow  |
      | moodle/user:viewdetails              | allow  |
    And the following "blocks" exist:
      | blockname | contextlevel | reference | pagetypepattern | defaultregion |
      | mentees   | System       | 1         | site-index      | side-pre      |
    When I log in as "admin"
    And I am on the "student1" "user > profile" page
    And I click on "Preferences" "link" in the ".profile_tree" "css_element"
    And I follow "Assign roles relative to this user"
    And I follow "Parent"
    And I set the field "Potential users" to "Parent 1 (parent1@example.com)"
    And I click on "Add" "button" in the "#page-content" "css_element"
    And I log out
    And I log in as "parent1"
    And I am on site homepage
    And I follow "Student 1"
    And I follow "Grades overview"
    Then the following should exist in the "overview-grade" table:
    | Course name | Grade |
    | Course 2 | - |
    | Course 1 | 217.00 |
    And I click on "Course 1" "link" in the "region-main" "region"
    And the following should exist in the "user-grade" table:
    | Grade item | Calculated weight | Grade | Range | Percentage | Contribution to course total |
    | Test assignment one | 75.00 % | 150.00 | 0–300 | 50.00 % | 37.50 % |
    | Test assignment two | 25.00 % | 67.00  | 0–100 | 67.00 % | 16.75 % |
    | Test assignment three | 0.00 %( Empty ) | - | 0–150 | - | 0.00 % |
