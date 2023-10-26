@mod @mod_quiz @quiz @quiz_grading
Feature: Basic use of the Manual grading report
  In order to easily find students attempts that need manual grading
  As a teacher
  I need to use the manual grading report

  Background:
    Given the following "custom profile fields" exist:
      | datatype | shortname  | name           |
      | text     | username   | Username       |
      | text     | email      | Email address  |
      | text     | idnumber   | ID number      |
      | text     | frog       | Favourite frog |
    And the following config values are set as admin:
      | showuseridentity | username,idnumber,email,profile_field_frog |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber |  profile_field_frog |
      | teacher1 | T1        | Teacher1 | teacher1@example.com | T1000    |                     |
      | marker   | M1        | Marker   | marker@example.com   | M1000    |                     |
      | student1 | S1        | Student1 | student1@example.com | S1000    | little yellow frog  |
      | student2 | S2        | Student2 | student2@example.com | S2000    |                     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | marker   | C1     | teacher        |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "groupings" exist:
      | name         | course  | idnumber |
      | Tutor groups | C1      | tging    |
    And the following "groups" exist:
      | name         | course | idnumber |
      | Tutor group  | C1     | tg       |
      | Marker group | C1     | mg       |
    And the following "grouping groups" exist:
      | grouping | group |
      | tging    | tg    |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | tg    |
      | student1 | tg    |
      | marker   | mg    |
      | student2 | mg    |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name             | questiontext                         | answer 1 | grade |
      | Test questions   | shortanswer | Short answer 001 | Where is the capital city of France? | Paris    | 100%  |
    And the following "activities" exist:
      | activity | name   | course | idnumber | groupmode | grouping |
      | quiz     | Quiz 1 | C1     | quiz1    | 1         | tging    |
      | quiz     | Quiz 2 | C1     | quiz2    | 1         | tging    |
    And quiz "Quiz 1" contains the following questions:
      | question         | page |
      | Short answer 001 | 1    |

  Scenario: Manual grading report without attempts
    When I am on the "Quiz 1" "mod_quiz > Manual grading report" page logged in as "teacher1"
    Then I should see "Separate groups (Tutor groups)"
    And I should see "All participants"
    And I should see "Quiz 1"
    And I should see "Questions that need grading"
    And I should see "Nothing to display"
    And I follow "Also show questions that have been graded automatically"
    And I should see "Nothing to display"

  Scenario: Manual grading report with attempts
    Given user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      | 1    | Paris    |
    And I reload the page
    When I am on the "Quiz 1" "mod_quiz > Manual grading report" page logged in as "teacher1"
    Then I should see "Separate groups (Tutor groups)"
    And I should see "All participants"
    And I should see "Quiz 1"
    And I follow "Also show questions that have been graded automatically"
    And I should see "Short answer 001"
    And "Short answer 001" row "To grade" column of "questionstograde" table should contain "0"
    And "Short answer 001" row "Already graded" column of "questionstograde" table should contain "0"
    And I should see "Short answer 001"
    And "Short answer 001" row "To grade" column of "questionstograde" table should contain "0"
    And "Short answer 001" row "Already graded" column of "questionstograde" table should contain "0"
    # Go to the grading page.
    And I click on "update grades" "link" in the "Short answer 001" "table_row"
    And I should see "Grading attempts 1 to 1 of 1"
    # Test the display options.
    And I set the field "Order attempts by" to "ID number"
    And I press "Change options"
    # General feedback for Short answer 001 displays.
    And I should see "That is a bad answer."
    And I should see "The correct answer is: frog"
    # Adjust the mark for Student1
    And I set the field "Comment" to "I have adjusted your mark to 0.6"
    And I set the field "Mark" to "0.6"
    And I press "Save and show next"
    And I should see "All selected attempts have been graded. Returning to the list of questions."
    And "Short answer 001" row "To grade" column of "questionstograde" table should contain "0"
    And "Short answer 001" row "Already graded" column of "questionstograde" table should contain "1"

  Scenario: Manual grading settings are remembered as user preferences
    Given user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      | 1    | Paris    |
    When I am on the "Quiz 1" "mod_quiz > Manual grading report" page logged in as "teacher1"
    And I follow "Also show questions that have been graded automatically"
    And I click on "update grades" "link" in the "Short answer 001" "table_row"
    And I set the following fields to these values:
      | Questions per page | 42   |
      | Order attempts by  | Date |
    And I press "Change options"
    And I log out
    And I am on the "Quiz 1" "mod_quiz > Manual grading report" page logged in as "teacher1"
    And I follow "Also show questions that have been graded automatically"
    And I click on "update grades" "link" in the "Short answer 001" "table_row"
    Then the following fields match these values:
      | Questions per page | 42   |
      | Order attempts by  | Date |

  @javascript
  Scenario: Manual grading settings are validated
    Given user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      | 1    | Paris    |
    And I am on the "Quiz 1" "mod_quiz > Manual grading report" page logged in as "teacher1"
    And I follow "Also show questions that have been graded automatically"
    And I click on "update grades" "link" in the "Short answer 001" "table_row"
    When I set the following fields to these values:
      | Questions per page | 0 |
    Then I should see "You must enter a number that is greater than 0."
    And I set the following fields to these values:
      | Questions per page | -1 |
    And I press "Change options"
    And I should see "You must enter a number that is greater than 0."
    And I set the following fields to these values:
      | Questions per page | abc |
    And I press "Change options"
    And I should see "You must enter a number that is greater than 0."
    And I set the following fields to these values:
      | Questions per page | 1 |
    And I press "Change options"

  @javascript
  Scenario: Teacher can see user custom filed columns as additional user identity
    Given user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      | 1    | Paris    |
    When I am on the "Quiz 1" "mod_quiz > Manual grading report" page logged in as "teacher1"
    And I follow "Also show questions that have been graded automatically"
    And I click on "update grades" "link" in the "Short answer 001" "table_row"
    Then I should see "Attempt number 1 for S1 Student1 (student1, S1000, student1@example.com, little yellow frog)"
    And I should not see "You must enter a number that is greater than 0."

  Scenario: A marker cannot access the report in separate group
    Given user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      | 1    | frog     |
    And user "student2" has attempted "Quiz 1" with responses:
      | slot | response |
      | 1    | Duck     |
    When I am on the "Quiz 1" "mod_quiz > Manual grading report" page logged in as "marker"
    Then I should see "Quiz 1"
    And I should see "Separate groups: All participants"
    Then I should see "Sorry, but you need to be part of a group to see this page."

  @javascript
  Scenario: Manual grading report with attempts to be graded
    Given the following "questions" exist:
      | questioncategory | qtype | name     | user  | questiontext    |
      | Test questions   | essay | Essay Q1 | admin | Question 1 text |
    And quiz "Quiz 2" contains the following questions:
      | question | page |
      | Essay Q1 | 1    |
    And I log out
    When I am on the "Quiz 2" "mod_quiz > View" page logged in as "student1"
    And I press "Attempt quiz"
    And I set the field with xpath "//*[contains(concat(' ', @class, ' '), ' editor_atto_content ')]" to "This is my attempt 1"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I click on "Finish review" "link"
    And I press "Re-attempt quiz"
    And I set the field with xpath "//*[contains(concat(' ', @class, ' '), ' editor_atto_content ')]" to "This is my attempt 2"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I click on "Finish review" "link"
    And I press "Re-attempt quiz"
    And I set the field with xpath "//*[contains(concat(' ', @class, ' '), ' editor_atto_content ')]" to "This is my attempt 3"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I log out

    And I am on the "Quiz 2" "mod_quiz > Manual grading report" page logged in as "teacher1"
    And I follow "Also show questions that have been graded automatically"
    And I should see "Essay Q1"
    And "Essay Q1" row "To grade" column of "questionstograde" table should contain "3"
    And "Essay Q1" row "Already graded" column of "questionstograde" table should contain "0"
    # Go to the grading page.
    And I click on "grade" "link" in the "Essay Q1" "table_row"
    And I should see "Grading attempts 1 to 3 of 3"
    And I set the following fields to these values:
      | Questions per page | 1         |
      | Order attempts by  | ID number |
    And I press "Change options"
    And I should see "Grading attempts 1 to 1 of 3"
    # Adjust the mark for Student1
    And I set the field "Comment" to "I have adjusted your mark to 0.6"
    And I set the field "Mark" to "0.6"
    And I press "Save and show next"
    Then I should see "Grading attempts 1 to 1 of 2"
    And I press "Save and show next"
    And I should see "Grading attempts 2 to 2 of 2"
