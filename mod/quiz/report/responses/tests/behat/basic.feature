@mod @mod_quiz @quiz @quiz_reponses
Feature: Basic use of the Responses report
  In order to see how my students are progressing
  As a teacher
  I need to see all their quiz responses

  Background: Using the Responses report
    Given the following "custom profile fields" exist:
      | datatype | shortname | name  |
      | text     | fruit     | Fruit |
    And the following "users" exist:
      | username | firstname | lastname | profile_field_fruit |
      | teacher  | The       | Teacher  |                     |
      | student1 | Student   | One      | Apple               |
      | student2 | Student   | Two      | Banana              |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "groups" exist:
      | course | idnumber | name    |
      | C1     | G1       | Group 1 |
      | C1     | G2       | Group 2 |
    And the following "group members" exist:
      | group | user     |
      | G1    | student1 |
      | G2    | student2 |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity | name   | intro              | course | idnumber | preferredbehaviour | groupmode |
      | quiz     | Quiz 1 | Quiz 1 description | C1     | quiz1    | interactive        | 2         |
    And the following "questions" exist:
      | questioncategory | qtype     | name | template |
      | Test questions   | numerical | NQ   | pi3tries |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark | displaynumber |
      | NQ       | 1    | 3.0     | 1a            |

  @javascript
  Scenario: Report works when there are no attempts
    When I am on the "Quiz 1" "mod_quiz > Responses report" page logged in as teacher
    Then I should see "Attempts: 0"
    And I should see "Nothing to display"
    And I set the field "Attempts from" to "enrolled users who have not attempted the quiz"

  @javascript
  Scenario: Report works when there are attempts
    Given user "student1" has started an attempt at quiz "Quiz 1"
    And user "student1" has checked answers in their attempt at quiz "Quiz 1":
      | slot | response |
      |   1  | 1.0      |
    And user "student1" has checked answers in their attempt at quiz "Quiz 1":
      | slot | response |
      |   1  | 3.0      |
    And user "student1" has checked answers in their attempt at quiz "Quiz 1":
      | slot | response |
      |   1  | 3.14     |
    And user "student1" has finished an attempt at quiz "Quiz 1"

    When I am on the "Quiz 1" "mod_quiz > Responses report" page logged in as teacher
    Then I should see "Attempts: 1"
    And I should see "Student One"
    And I should not see "Student Two"
    And I set the field "Attempts from" to "enrolled users who have, or have not, attempted the quiz"
    And I set the field "Which tries" to "All tries"
    And I should see "Response 1a"
    And I press "Show report"
    And "Student OneReview attempt" row "Response 1aSort by Response 1a Ascending" column of "responses" table should contain "1.0"
    And "Student OneReview attempt" row "Status" column of "responses" table should contain ""
    And "Finished" row "Grade/100.00Sort by Grade/100.00 Ascending" column of "responses" table should contain "33.33"
    And "Finished" row "Response 1aSort by Response 1a Ascending" column of "responses" table should contain "3.14"
    And "Student Two" row "Status" column of "responses" table should contain "-"
    And "Student Two" row "Response 1aSort by Response 1a Ascending" column of "responses" table should contain "-"

  @javascript
  Scenario: Report does not allow strange combinations of options
    Given I am on the "Quiz 1" "mod_quiz > Responses report" page logged in as teacher
    And the "Which tries" "select" should be enabled
    When I set the field "Attempts from" to "enrolled users who have not attempted the quiz"
    Then the "Which tries" "select" should be disabled

  @javascript
  Scenario: A teacher can search the user attempt by user profile field in the responses report.
    Given user "student1" has started an attempt at quiz "Quiz 1"
    And user "student1" has checked answers in their attempt at quiz "Quiz 1":
      | slot | response |
      | 1    | 1.0      |
    And user "student1" has finished an attempt at quiz "Quiz 1"
    And user "student2" has started an attempt at quiz "Quiz 1"
    And user "student2" has checked answers in their attempt at quiz "Quiz 1":
      | slot | response |
      | 1    | 3.0      |
    And user "student2" has finished an attempt at quiz "Quiz 1"
    And the following config values are set as admin:
      | showuseridentity | email,profile_field_fruit |
    And I am on the "Quiz 1" "mod_quiz > Responses report" page logged in as teacher
    And I set the field "Search users" to "Apple"
    And I wait until "Student Two" "option_role" does not exist
    And I click on "Student One" "list_item"
    And I wait until the page is ready
    Then the following should exist in the "responses" table:
      | First name / Last name |
      | Student One            |
    And the following should not exist in the "responses" table:
      | First name / Last name |
      | Student Two            |

  @javascript
  Scenario: A teacher can filter the user attempt by name in the responses report.
    Given user "student1" has started an attempt at quiz "Quiz 1"
    And user "student1" has checked answers in their attempt at quiz "Quiz 1":
      | slot | response |
      | 1    | 1.0      |
    And user "student1" has finished an attempt at quiz "Quiz 1"
    And user "student2" has started an attempt at quiz "Quiz 1"
    And user "student2" has checked answers in their attempt at quiz "Quiz 1":
      | slot | response |
      | 1    | 3.0      |
    And user "student2" has finished an attempt at quiz "Quiz 1"
    And I am on the "Quiz 1" "mod_quiz > Responses report" page logged in as teacher
    And I click on "Filter by name" "combobox"
    # To prevent the help icon from overlapping the apply button.
    And I change viewport size to "1200x1000"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.firstinitial" "css_element"
    And ".initialbarall.page-item.active" "css_element" should exist in the ".initialbar.lastinitial" "css_element"
    And I select "Z" in the "Last name" "core_grades > initials bar"
    And I press "Apply"
    And I should see "Nothing to display"
    And I should not see "Student One"
    And I should not see "Student Two"
    And I click on "Last (Z)" "combobox"
    And I select "S" in the "First name" "core_grades > initials bar"
    And I select "T" in the "Last name" "core_grades > initials bar"
    And I press "Apply"
    Then I should not see "Nothing to display"
    And the following should exist in the "responses" table:
      | First name / Last name |
      | Student Two            |
    And the following should not exist in the "responses" table:
      | First name / Last name |
      | Student One            |

  @javascript
  Scenario: A teacher can filter the user attempt by group in the responses report.
    Given user "student1" has started an attempt at quiz "Quiz 1"
    And user "student1" has checked answers in their attempt at quiz "Quiz 1":
      | slot | response |
      | 1    | 1.0      |
    And user "student1" has finished an attempt at quiz "Quiz 1"
    And user "student2" has started an attempt at quiz "Quiz 1"
    And user "student2" has checked answers in their attempt at quiz "Quiz 1":
      | slot | response |
      | 1    | 3.0      |
    And I am on the "Quiz 1" "mod_quiz > Responses report" page logged in as teacher
    And I click on "All participants" in the "group" search widget
    And I wait until "Group 1" "option_role" exists
    And I click on "Group 1" in the "group" search widget
    And the following should exist in the "responses" table:
      | First name / Last name |
      | Student One            |
    And the following should not exist in the "responses" table:
      | First name / Last name |
      | Student Two            |
    And I click on "Group 1" in the "group" search widget
    And I wait until "Group 2" "option_role" exists
    And I click on "Group 2" in the "group" search widget
    Then the following should not exist in the "responses" table:
      | First name / Last name |
      | Student One            |
    And the following should exist in the "responses" table:
      | First name / Last name |
      | Student Two            |
