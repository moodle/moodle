@qtype @qtype_ddmarker
Feature: Test duplicating a quiz containing a drag and drop markers question
  As a teacher
  In order re-use my courses containing drag and drop markers questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype    | name         | template |
      | Test questions   | ddmarker | Drag markers | mkmap    |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | Drag markers | 1 |
    And the following config values are set as admin:
      | enableasyncbackup | 0 |
    And I log in as "admin"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Backup and restore a course containing a drag and drop markers question
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    And I am on the "Course 2" "core_question > course question bank" page
    And I choose "Edit question" action for "Drag markers" in the question bank
    Then the following fields match these values:
      | Question name                      | Drag markers                                                                                                                                               |
      | Question text                      | Please place the markers on the map of Milton Keynes and be aware that there is more than one railway station.                                             |
      | General feedback                   | The Open University is at the junction of Brickhill Street and Groveway. There are three railway stations, Wolverton, Milton Keynes Central and Bletchley. |
      | Default mark                       | 1                                                                                                                                                          |
      | id_shuffleanswers                  | 0                                                                                                                                                          |
      | id_drags_0_label                   | OU                                                                                                                                                         |
      | id_drags_0_noofdrags               | 1                                                                                                                                                          |
      | id_drags_1_label                   | Railway station                                                                                                                                            |
      | id_drags_1_noofdrags               | 3                                                                                                                                                          |
      | id_drops_0_shape                   | Circle                                                                                                                                                     |
      | id_drops_0_coords                  | 322,213;10                                                                                                                                                 |
      | id_drops_0_choice                  | OU                                                                                                                                                         |
      | id_drops_1_shape                   | Circle                                                                                                                                                     |
      | id_drops_1_coords                  | 144,84;10                                                                                                                                                  |
      | id_drops_1_choice                  | Railway station                                                                                                                                            |
      | id_drops_2_shape                   | Circle                                                                                                                                                     |
      | id_drops_2_coords                  | 195,180;10                                                                                                                                                 |
      | id_drops_2_choice                  | Railway station                                                                                                                                            |
      | id_drops_3_shape                   | Circle                                                                                                                                                     |
      | id_drops_3_coords                  | 267,302;10                                                                                                                                                 |
      | id_drops_3_choice                  | Railway station                                                                                                                                            |
      | For any correct response           | Well done!                                                                                                                                                 |
      | For any partially correct response | Parts, but only parts, of your response are correct.                                                                                                       |
      | id_shownumcorrect                  | 1                                                                                                                                                          |
      | For any incorrect response         | That is not right at all.                                                                                                                                  |
      | Penalty for each incorrect try     | 33.33333%                                                                                                                                                  |
      | Hint 1                             | You are trying to place four markers on the map.                                                                                                           |
      | id_hintshownumcorrect_0            | 1                                                                                                                                                          |
      | id_hintclearwrong_0                | 0                                                                                                                                                          |
      | id_hintoptions_0                   | 0                                                                                                                                                          |
      | Hint 2                             | You are trying to mark three railway stations.                                                                                                             |
      | id_hintshownumcorrect_1            | 1                                                                                                                                                          |
      | id_hintclearwrong_1                | 1                                                                                                                                                          |
      | id_hintoptions_1                   | 1                                                                                                                                                          |
