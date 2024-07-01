@mod @mod_quiz @quiz @quiz_overview
Feature: Basic use of the Grades report
  In order to easily get an overview of quiz attempts
  As a teacher
  I need to use the Grades report

  Background:
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And the following "custom profile fields" exist:
      | datatype | shortname | name  |
      | text     | fruit     | Fruit |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber | profile_field_fruit |
      | teacher1 | T1        | Teacher1 | teacher1@example.com | T1000    |                     |
      | student1 | S1        | Student1 | student1@example.com | S1000    | Apple               |
      | student2 | S2        | Student2 | student2@example.com | S2000    | Banana              |
      | student3 | S3        | Student3 | student3@example.com | S3000    | Pear                |
      | student4 | Four      | Student  | student4@example.com | S4000    | Melon               |
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
    And the following "groups" exist:
      | course | idnumber | name    |
      | C1     | G1       | <span class="multilang" lang="en">English</span><span class="multilang" lang="es">Spanish</span> |
      | C1     | G2       | Group 2                                                                                          |
    And the following "group members" exist:
      | group | user     |
      | G1    | student1 |
      | G1    | student2 |
      | G2    | student4 |
      | G2    | student3 |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | groupmode |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | 2         |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext         |
      | Test questions   | description | Intro | Welcome to this quiz |
      | Test questions   | truefalse   | TF1   | First question       |
      | Test questions   | truefalse   | TF2   | Second question      |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark | displaynumber |
      | Intro    | 1    |         |               |
      | TF1      | 1    |         |               |
      | TF2      | 1    | 3.0     | 2a            |
    And user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      |   2  | True     |
      |   3  | False    |
    And user "student2" has attempted "Quiz 1" with responses:
      | slot | response |
      |   2  | True     |
      |   3  | True     |
    And user "student4" has attempted "Quiz 1" with responses:
      | slot | response |
      | 2    | False    |
      | 3    | False    |

  @javascript
  Scenario: Using the Grades report
    # Basic check of the Grades report
    When I am on the "Quiz 1" "quiz activity" page logged in as teacher1
    And I navigate to "Results" in current page administration
    Then I should see "Attempts: 3"

    # Verify that the right columns are visible
    And I should see "Q. 1"
    And I should see "Q. 2a"
    And I should not see "Q. 3"

    # Check student1's grade
    And I should see "25.00" in the "S1 Student1" "table_row"
    # And student2's grade
    And I should see "100.00" in the "S2 Student2" "table_row"

    # Check changing the form parameters
    And I set the field "Attempts from" to "enrolled users who have not attempted the quiz"
    And I press "Show report"
    # Note: teachers should not appear in the report.
    # Check student3's grade
    And I should see "-" in the "S3 Student3" "table_row"

    And I set the field "Attempts from" to "enrolled users who have, or have not, attempted the quiz"
    And I press "Show report"
    # Check student1's grade
    And I should see "25.00" in the "S1 Student1" "table_row"
    # Check student2's grade
    And I should see "100.00" in the "S2 Student2" "table_row"
    # Check student3's grade
    And I should see "-" in the "S3 Student3" "table_row"

    And I set the field "Attempts from" to "all users who have attempted the quiz"
    And I press "Show report"
    # Check student1's grade
    And I should see "25.00" in the "S1 Student1" "table_row"
    # Check student2's grade
    And I should see "100.00" in the "S2 Student2" "table_row"

    # Verify groups are displayed correctly.
    And I click on "All participants" in the "group" search widget
    And I wait until "English" "option_role" exists
    And I click on "English" in the "group" search widget
    And I should see "Number of students in group 'English' achieving grade ranges"

  @javascript
  Scenario: View custom user profile fields in the grades report
    Given the following config values are set as admin:
      | showuseridentity | email,profile_field_fruit |
    And I am on the "Quiz 1" "quiz activity" page logged in as teacher1
    And I navigate to "Results" in current page administration
    Then I should see "Apple" in the "S1 Student1" "table_row"
    And I should see "Banana" in the "S2 Student2" "table_row"

  @javascript
  Scenario: A teacher can search the user attempt by user profile field in the grades report.
    Given the following config values are set as admin:
      | showuseridentity | email,profile_field_fruit |
    And I am on the "Quiz 1" "quiz activity" page logged in as teacher1
    And I navigate to "Results" in current page administration
    And I set the field "Search users" to "Apple"
    And I wait until "S2 Student2" "option_role" does not exist
    And I click on "S1 Student1" "list_item"
    And I wait until the page is ready
    Then the following should exist in the "attempts" table:
      | First name / Last name |
      | S1 Student1            |
    And the following should not exist in the "attempts" table:
      | First name / Last name |
      | S2 Student2            |
      | Four Student           |

  @javascript
  Scenario: A teacher can filter the user attempt by name in the grades report.
    When I am on the "Quiz 1" "quiz activity" page logged in as teacher1
    And I navigate to "Results" in current page administration
    And I click on "Filter by name" "combobox"
    # To prevent the help icon from overlapping the apply button.
    And I change viewport size to "1200x1000"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I select "Z" in the "Last name" "core_grades > initials bar"
    And I press "Apply"
    And I should see "Nothing to display"
    And I should not see "S1 Student1"
    And I should not see "S2 Student2"
    And I click on "Last (Z)" "combobox"
    And I select "F" in the "First name" "core_grades > initials bar"
    And I select "S" in the "Last name" "core_grades > initials bar"
    And I press "Apply"
    Then I should not see "Nothing to display"
    And the following should exist in the "attempts" table:
      | First name / Last name |
      | Four Student           |
    And the following should not exist in the "attempts" table:
      | First name / Last name |
      | S1 Student1            |
      | S2 Student2            |

  @javascript
  Scenario: A teacher can filter the user attempt by group in the grades report.
    When I am on the "Quiz 1" "quiz activity" page logged in as teacher1
    And I navigate to "Results" in current page administration
    And I click on "All participants" in the "group" search widget
    And I wait until "Group 2" "option_role" exists
    And I click on "Group 2" in the "group" search widget
    And the following should exist in the "attempts" table:
      | First name / Last name |
      | Four Student           |
    And the following should not exist in the "attempts" table:
      | First name / Last name |
      | S1 Student1            |
      | S2 Student2            |
    And I click on "Group 2" in the "group" search widget
    And I wait until "English" "option_role" exists
    And I click on "English" in the "group" search widget
    Then the following should not exist in the "attempts" table:
      | First name / Last name |
      | Four Student           |
    And the following should exist in the "attempts" table:
      | First name / Last name |
      | S1 Student1            |
      | S2 Student2            |
