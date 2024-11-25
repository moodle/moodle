@mod @mod_quiz @javascript
Feature: Editing question numbering of the existing questions already in a quiz
  In order to have better assessment
  As a teacher
  I want to be able to customided question numbering on the quiz editing page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | teacher1 | Teacher   | 1        | t1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name    | intro              | course | idnumber |
      | quiz       | Quiz 1  | Quiz 1 for testing | C1     | quiz1    |
      | quiz       | Quiz 2  | Quiz 2 for testing | C1     | quiz2    |
      | qbank      | Qbank 1 | Qbank for testing  | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | name                |
      | Activity module | qbank1    | Questions Category 1|
      | Activity module | qbank1    | Questions Category 2|
    Given the following "questions" exist:
      | questioncategory     | qtype       | name        | questiontext           |
      | Questions Category 1 | description | Description | This is decription ... |
      | Questions Category 1 | truefalse   | Question A  | This is question 01    |
      | Questions Category 1 | truefalse   | Question B  | This is question 02    |
      | Questions Category 1 | truefalse   | Question C  | This is question 03    |
      | Questions Category 1 | truefalse   | Question D  | This is question 04    |
    And quiz "Quiz 1" contains the following questions:
      | question    | page | displaynumber |
      | Description | 1    |               |
      | Question A  | 1    | 1.a           |
      | Question B  | 1    | 1.b           |
      | Question C  | 2    |               |
      | Question D  | 2    |               |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Section 1 | 1         | 0       |
      | Section 2 | 4         | 0       |

  Scenario: Showing customised and default question numbers on quiz editing page.
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I should see "Section 1"
    And I should see "i" on quiz page "1"
    And I should see "1.a" on quiz page "1"
    And I should see "1.b" on quiz page "1"
    And I should see "Section 2"
    And I should see "3" on quiz page "2"
    And I should see "4" on quiz page "2"

  Scenario: Showing customised and default question numbers on quiz view page and question navigation.
    Given I am on the "Quiz 1" "mod_quiz > View" page logged in as "teacher1"
    When I press "Preview quiz"
    Then I should see "Section 1" in the "Quiz navigation" "block"
    And I should see question "1.a" in section "Section 1" in the quiz navigation
    And I should see question "1.b" in section "Section 1" in the quiz navigation
    And I should see "Section 2" in the "Quiz navigation" "block"
    And I should see question "3" in section "Section 2" in the quiz navigation
    And I should see question "4" in section "Section 2" in the quiz navigation

    And I should see "Question 1.a"
    And I should see "Question 1.b"
    And I press "Next page"
    And I should see "Question 3"
    And I should see "Question 4"

  Scenario: Customised numbers are not used in shuffled sections, even if they exist in the database
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I set the field "Shuffle" to "1"
    When I am on the "Quiz 1" "mod_quiz > View" page
    And I press "Preview quiz"
    Then I should see "Section 1" in the "Quiz navigation" "block"
    And I should see question "1" in section "Section 1" in the quiz navigation
    And I should see question "2" in section "Section 1" in the quiz navigation
    And I should see "Section 2" in the "Quiz navigation" "block"
    And I should see question "3" in section "Section 2" in the quiz navigation
    And I should see question "4" in section "Section 2" in the quiz navigation

    And I should see "Question 1"
    And I should see "Question 2"
    And I press "Next page"
    And I should see "Question 3"
    And I should see "Question 4"

  Scenario: Showing long customised question numbers on quiz editing page and parcially hidden on question info and navigation.
    Given quiz "Quiz 2" contains the following questions:
      | question    | page | displaynumber    |
      | Question A  | 1    | ABCDEFGHIJKLMNOP |
      | Question B  | 2    | abcdefghijklmnop |
    And quiz "Quiz 2" contains the following sections:
      | heading   | firstslot | shuffle |
      | Section 1 | 1         | 0       |
      | Section 2 | 2         | 0       |
    When I am on the "Quiz 2" "mod_quiz > Edit" page logged in as "teacher1"
    And I should see "ABCDEFGHIJKLMNOP" on quiz page "1"
    And I should see "abcdefghijklmnop" on quiz page "2"

    And I am on the "Quiz 2" "mod_quiz > View" page logged in as "teacher1"
    And I press "Preview quiz"
    # Only "Question ABCDEFGH" is visible in the question info box.
    And I should see "Question ABCDEFGHIJKLMNOP"
    # Only 'ABC' is visible on the navigation button/link.
    And I should see question "ABCDEFGHIJKLMNOP" in section "Section 1" in the quiz navigation
    And I press "Next page"
    # Only "Question abcdefghij" is visible in the question info box.
    And I should see "Question abcdefghijklmnop"
    # Only 'abc' is visible on the navigation button/link.
    And I should see question "abcdefghijklmnop" in section "Section 2" in the quiz navigation

  Scenario: Shuffling questions within a section with customised question numbers.
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    Then I should see "Section 1"
    And I should see "i" on quiz page "1"
    And I should see "1.a" on quiz page "1"
    And I should see "1.b" on quiz page "1"
    And I should see "Section 2"
    And I should see "3" on quiz page "2"
    And I should see "4" on quiz page "2"

    And I set the field "Shuffle" to "1"
    And I should see "Section 1"
    And I should see "i" on quiz page "1"
    And I should see "1" on quiz page "1"
    And I should see "2" on quiz page "1"
    And I should see "Section 2"
    And I should see "3" on quiz page "2"
    And I should see "4" on quiz page "2"
    And I reload the page

    And I set the field "Shuffle" to "0"
    And I should see "Section 1"
    And I should see "i" on quiz page "1"
    And I should see "1.a" on quiz page "1"
    And I should see "1.b" on quiz page "1"
