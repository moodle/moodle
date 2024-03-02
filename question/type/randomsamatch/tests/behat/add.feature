@qtype @qtype_randomsamatch
Feature: Test creating a Random short-answer matching question
  As a teacher
  In order to test my students
  I need to be able to create a Random short-answer matching question

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name       |
      | Course       | C1        | Category 1 |
    And the following "questions" exist:
      | questioncategory | qtype       | name                              | template |
      | Category 1       | shortanswer | Short answer question A version 1 | frogtoad |
      | Category 1       | shortanswer | Short answer question B version 1 | frogtoad |
      | Category 1       | shortanswer | Short answer question C version 1 | frogtoad |

  Scenario: Create a too large size of options Random short-answer matching question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Random short-answer matching" question filling the form with:
      | Category                      | Category 1                   |
      | Question name                 | Random short-answer matching |
      | Question text                 | Random short-answer matching |
      | Default mark                  | 1                            |
      | Number of questions to select | 4                            |
    Then I should see "There is/are only 3 short answer questions in the category"

  Scenario: Create a Random short-answer matching question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    # Edit the first Short answer question so a version 2 is created.
    And I am on the "Short answer question A version 1" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question name | Short answer question A version 2          |
      | Question text | Short answer question A version 2          |
      | id_answer_0   | Short answer Question A Version 2 Answer 1 |
      | id_answer_1   | Short answer Question A Version 2 Answer 2 |
      | id_answer_2   | Short answer Question A Version 2 Answer 3 |
    And I press "id_submitbutton"
    And I should see "Short answer question A version 2"
    # Edit the second Short answer question so a version 2 is created.
    And I am on the "Short answer question B version 1" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question name | Short answer question B version 2          |
      | Question text | Short answer question B version 2          |
      | id_answer_0   | Short answer Question B Version 2 Answer 1 |
      | id_answer_1   | Short answer Question B Version 2 Answer 2 |
      | id_answer_2   | Short answer Question B Version 2 Answer 3 |
    And I press "id_submitbutton"
    And I should see "Short answer question B version 2"
    # Edit the third Short answer question so a version 2 is created.
    And I am on the "Short answer question C version 1" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question name | Short answer question C version 2          |
      | Question text | Short answer question C version 2          |
      | id_answer_0   | Short answer Question C Version 2 Answer 1 |
      | id_answer_1   | Short answer Question C Version 2 Answer 2 |
      | id_answer_2   | Short answer Question C Version 2 Answer 3 |
    And I press "id_submitbutton"
    And I should see "Short answer question C version 2"
    # Create the Random short-answer question.
    And I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Random short-answer matching" question filling the form with:
      | Category                      | Category 1                   |
      | Question name                 | Random short-answer matching |
      | Question text                 | Random short-answer matching |
      | Default mark                  | 1                            |
      | Number of questions to select | 3                            |
    And I should see "Random short-answer matching"
    And I am on the "Random short-answer matching" "core_question > preview" page logged in as teacher
    Then I should not see "Name an amphibian:"
    And I should see "Version 2"
