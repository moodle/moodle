@qtype @qtype_ddimageortext
Feature: Test duplicating a quiz containing a drag and drop onto image question
  As a teacher
  In order re-use my courses containing drag and drop onto image questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype         | name            | template |
      | Test questions   | ddimageortext | Drag onto image | xsection |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | Drag onto image | 1 |

  @javascript
  Scenario: Backup and restore a course containing a drag and drop onto image question
    When I am on the "Course 1" course page logged in as admin
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    And I am on the "Course 2" "core_question > course question bank" page
    And I choose "Edit question" action for "Drag onto image" in the question bank
    Then the following fields match these values:
      | Question name                       | Drag onto image                                      |
      | General feedback                    | <p>More information about the major features of the Earth's surface can be found in Block 3, Section 6.2.</p> |
      | Default mark                        | 1                                                    |
      | Shuffle                             | 0                                                    |
      | id_drags_0_dragitemtype             | Draggable text                                       |
      | id_drags_0_draggroup                | 1                                                    |
      | id_draglabel_0                      | island<br/>arc                                       |
      | id_drags_1_dragitemtype             | Draggable text                                       |
      | id_drags_1_draggroup                | 1                                                    |
      | id_draglabel_1                      | mid-ocean<br/>ridge                                  |
      | id_drags_2_dragitemtype             | Draggable text                                       |
      | id_drags_2_draggroup                | 1                                                    |
      | id_draglabel_2                      | abyssal<br/>plain                                    |
      | id_drags_3_dragitemtype             | Draggable text                                       |
      | id_drags_3_draggroup                | 1                                                    |
      | id_draglabel_3                      | continental<br/>rise                                 |
      | id_drags_4_dragitemtype             | Draggable text                                       |
      | id_drags_4_draggroup                | 1                                                    |
      | id_draglabel_4                      | ocean<br/>trench                                     |
      | id_drags_5_dragitemtype             | Draggable text                                       |
      | id_drags_5_draggroup                | 1                                                    |
      | id_draglabel_5                      | continental<br/>slope                                |
      | id_drags_6_dragitemtype             | Draggable text                                       |
      | id_drags_6_draggroup                | 1                                                    |
      | id_draglabel_6                      | mountain<br/>belt                                    |
      | id_drags_7_dragitemtype             | Draggable text                                       |
      | id_drags_7_draggroup                | 1                                                    |
      | id_draglabel_7                      | continental<br/>shelf                                |
      | id_drops_0_xleft                    | 53                                                   |
      | id_drops_0_ytop                     | 17                                                   |
      | id_drops_0_choice                   | 7. mountain<br>belt                                      |
      | id_drops_1_xleft                    | 172                                                  |
      | id_drops_1_ytop                     | 2                                                    |
      | id_drops_1_choice                   | 8. continental<br>shelf                                  |
      | id_drops_2_xleft                    | 363                                                  |
      | id_drops_2_ytop                     | 31                                                   |
      | id_drops_2_choice                   | 5. ocean<br>trench                                       |
      | id_drops_3_xleft                    | 440                                                  |
      | id_drops_3_ytop                     | 13                                                   |
      | id_drops_3_choice                   | 3. abyssal<br>plain                                      |
      | id_drops_4_xleft                    | 115                                                  |
      | id_drops_4_ytop                     | 74                                                   |
      | id_drops_4_choice                   | 6. continental<br>slope                                  |
      | id_drops_5_xleft                    | 210                                                  |
      | id_drops_5_ytop                     | 94                                                   |
      | id_drops_5_choice                   | 4. continental<br>rise                                   |
      | id_drops_6_xleft                    | 310                                                  |
      | id_drops_6_ytop                     | 87                                                   |
      | id_drops_6_choice                   | 1. island<br>arc                                         |
      | id_drops_7_xleft                    | 479                                                  |
      | id_drops_7_ytop                     | 84                                                   |
      | id_drops_7_choice                   | 2. mid-ocean<br>ridge                                    |
      | For any correct response            | Well done!                                           |
      | For any partially correct response  | Parts, but only parts, of your response are correct. |
      | id_shownumcorrect                   | 1                                                    |
      | For any incorrect response          | That is not right at all.                            |
      | Penalty for each incorrect try      | 33.33333%                                            |
      | Hint 1                              | Incorrect placements will be removed.                |
      | id_hintclearwrong_0                 | 1                                                    |
      | id_hintshownumcorrect_0             | 1                                                    |
      | id_hintclearwrong_1                 | 0                                                    |
      | id_hintshownumcorrect_1             | 1                                                    |
      | Hint 3                              | Incorrect placements will be removed.                |
      | id_hintclearwrong_2                 | 1                                                    |
      | id_hintshownumcorrect_2             | 1                                                    |
      | id_hintclearwrong_3                 | 0                                                    |
      | id_hintshownumcorrect_3             | 1                                                    |
