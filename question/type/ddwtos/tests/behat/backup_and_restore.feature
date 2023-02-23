@qtype @qtype_ddwtos
Feature: Test duplicating a quiz containing a drag and drop into text question
  As a teacher
  In order re-use my courses containing drag and drop into text questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype  | name         | template |
      | Test questions   | ddwtos | Drag to text | fox      |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | Drag to text | 1 |

  @javascript
  Scenario: Backup and restore a course containing a drag and drop into text question
    When I am on the "Course 1" course page logged in as admin
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    And I am on the "Course 2" "core_question > course question bank" page
    And I choose "Edit question" action for "Drag to text" in the question bank
    Then the following fields match these values:
      | Question name                       | Drag to text                                         |
      | Question text                       | The [[1]] brown [[2]] jumped over the [[3]] dog.     |
      | General feedback                    | This sentence uses each letter of the alphabet.      |
      | Default mark                        | 1                                                    |
      | Shuffle                             | 0                                                    |
      | id_choices_0_answer                 | quick                                                |
      | id_choices_0_choicegroup            | 1                                                    |
      | id_choices_1_answer                 | fox                                                  |
      | id_choices_1_choicegroup            | 2                                                    |
      | id_choices_2_answer                 | lazy                                                 |
      | id_choices_2_choicegroup            | 3                                                    |
      | id_choices_3_answer                 | slow                                                 |
      | id_choices_3_choicegroup            | 1                                                    |
      | id_choices_4_answer                 | dog                                                  |
      | id_choices_4_choicegroup            | 2                                                    |
      | id_choices_5_answer                 | assiduous                                            |
      | id_choices_5_choicegroup            | 3                                                    |
      | For any correct response            | Well done!                                           |
      | For any partially correct response  | Parts, but only parts, of your response are correct. |
      | id_shownumcorrect                   | 0                                                    |
      | For any incorrect response          | That is not right at all.                            |
