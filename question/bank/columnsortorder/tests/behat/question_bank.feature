@qbank @qbank_columnsortorder @javascript
Feature: Set question bank column order and size
  In order customise my view of the question bank
  As a teacher
  I want to hide, reorder, and resize columns

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "activity" exists:
      | activity | quiz           |
      | course   | C1             |
      | name     | Test quiz Q001 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question category" exist:
      | contextlevel    | reference      | name                |
      | Activity module | Test quiz Q001 | Question category 1 |
    And the following "questions" exist:
      | questioncategory    | qtype | name                     | user     | questiontext                  | idnumber  |
      | Question category 1 | essay | Test question to be seen | teacher1 | Write about whatever you want | idnumber1 |
    And the following config values are set as admin:
      | config       | value                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          | plugin                |
      | hiddencols   | qbank_usage\question_last_used_column-question_last_used_column                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                | qbank_columnsortorder |
      | enabledcol   | qbank_comment\comment_count_column-comment_count_column,qbank_viewquestionname\question_name_idnumber_tags_column-question_name_idnumber_tags_column,core_question\local\bank\edit_menu_column-edit_menu_column,qbank_editquestion\question_status_column-question_status_column,qbank_history\version_number_column-version_number_column,qbank_statistics\columns\discrimination_index:discrimination_index,qbank_statistics\columns\facility_index:facility_index,qbank_statistics\columns\discriminative_efficiency:discriminative_efficiency,qbank_usage\question_usage_column-question_usage_column,qbank_usage\question_last_used_column-question_last_used_column,qbank_viewcreator\creator_name_column-creator_name_column,qbank_viewcreator\modifier_name_column-modifier_name_column,qbank_viewquestiontype\question_type_column-question_type_column                                                                                                                                                                                                                                                                               | qbank_columnsortorder |
      | colsize      | [{"column":"qbank_comment\\comment_count_column-comment_count_column","width":"20"},{"column":"qbank_viewquestionname\\question_name_idnumber_tags_column-question_name_idnumber_tags_column","width":"300"},{"column":"qbank_editquestion\\question_status_column-question_status_column","width":"20"},{"column":"qbank_history\\version_number_column-version_number_column","width":"20"},{"column":"qbank_statistics\\columns\\discrimination_index:discrimination_index","width":"20"},{"column":"qbank_statistics\\columns\\facility_index:facility_index","width":"20"},{"column":"qbank_statistics\\columns\\discriminative_efficiency:discriminative_efficiency","width":"20"},{"column":"qbank_usage\\question_usage_column-question_usage_column","width":"20"},{"column":"qbank_viewcreator\\creator_name_column-creator_name_column","width":"200"},{"column":"qbank_viewcreator\\modifier_name_column-modifier_name_column","width":"200"},{"column":"qbank_viewquestiontype\\question_type_column-question_type_column","width":"20"},{"column":"core_question\\local\\bank\\edit_menu_column-edit_menu_column","width":"50"}] | qbank_columnsortorder |

  Scenario: The teacher sees the question bank with global settings initially
    Given I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "teacher1"
    When I apply question bank filter "Category" with value "Question category 1"
    Then I should see "Test question to be seen"
    And "Last used" "qbank_columnsortorder > column header" should not exist
    And "Comments" "qbank_columnsortorder > column header" should appear before "Question" "qbank_columnsortorder > column header"
    And the "style" attribute of "Question" "qbank_columnsortorder > column header" should contain "width: 300px;"

  Scenario: User preference takes precedence over global defaults
    Given the following "user preferences" exist:
    | user      | preference                       | value                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
    | teacher1  | qbank_columnsortorder_hiddencols | qbank_comment\comment_count_column-comment_count_column                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
    | teacher1  | qbank_columnsortorder_enabledcol | qbank_viewquestionname\question_name_idnumber_tags_column-question_name_idnumber_tags_column,qbank_usage\question_last_used_column-question_last_used_column,core_question\local\bank\edit_menu_column-edit_menu_column,qbank_editquestion\question_status_column-question_status_column,qbank_history\version_number_column-version_number_column,qbank_statistics\columns\discrimination_index:discrimination_index,qbank_statistics\columns\facility_index:facility_index,qbank_statistics\columns\discriminative_efficiency:discriminative_efficiency,qbank_usage\question_usage_column-question_usage_column,qbank_usage\question_last_used_column-question_last_used_column,qbank_viewcreator\creator_name_column-creator_name_column,qbank_viewcreator\modifier_name_column-modifier_name_column,qbank_viewquestiontype\question_type_column-question_type_column                                                                                                                                                                                                                                                                       |
    | teacher1  | qbank_columnsortorder_colsize    | [{"column":"qbank_comment\\comment_count_column-comment_count_column","width":"20"},{"column":"qbank_viewquestionname\\question_name_idnumber_tags_column-question_name_idnumber_tags_column","width":"400"},{"column":"qbank_editquestion\\question_status_column-question_status_column","width":"20"},{"column":"qbank_history\\version_number_column-version_number_column","width":"20"},{"column":"qbank_statistics\\columns\\discrimination_index:discrimination_index","width":"20"},{"column":"qbank_statistics\\columns\\facility_index:facility_index","width":"20"},{"column":"qbank_statistics\\columns\\discriminative_efficiency:discriminative_efficiency","width":"20"},{"column":"qbank_usage\\question_usage_column-question_usage_column","width":"20"},{"column":"qbank_viewcreator\\creator_name_column-creator_name_column","width":"200"},{"column":"qbank_viewcreator\\modifier_name_column-modifier_name_column","width":"200"},{"column":"qbank_viewquestiontype\\question_type_column-question_type_column","width":"20"},{"column":"core_question\\local\\bank\\edit_menu_column-edit_menu_column","width":"50"}] |
    And I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "teacher1"
    When I apply question bank filter "Category" with value "Question category 1"
    Then "Comments" "qbank_columnsortorder > column header" should not exist
    And "Question" "qbank_columnsortorder > column header" should appear before "Last used" "qbank_columnsortorder > column header"
    And the "style" attribute of "Question" "qbank_columnsortorder > column header" should contain "width: 400px;"

  Scenario: Resetting user view goes back to global defaults
    Given the following "user preferences" exist:
      | user      | preference                       | value                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
      | teacher1  | qbank_columnsortorder_hiddencols |                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
      | teacher1  | qbank_columnsortorder_enabledcol | qbank_viewquestionname\question_name_idnumber_tags_column-question_name_idnumber_tags_column,qbank_usage\question_last_used_column-question_last_used_column,core_question\local\bank\edit_menu_column-edit_menu_column,qbank_editquestion\question_status_column-question_status_column,qbank_history\version_number_column-version_number_column,qbank_statistics\columns\discrimination_index:discrimination_index,qbank_statistics\columns\facility_index:facility_index,qbank_statistics\columns\discriminative_efficiency:discriminative_efficiency,qbank_usage\question_usage_column-question_usage_column,qbank_usage\question_last_used_column-question_last_used_column,qbank_viewcreator\creator_name_column-creator_name_column,qbank_viewcreator\modifier_name_column-modifier_name_column,qbank_viewquestiontype\question_type_column-question_type_column                                                                                                                                                                                                                                                                       |
      | teacher1  | qbank_columnsortorder_colsize    | [{"column":"qbank_comment\\comment_count_column-comment_count_column","width":"20"},{"column":"qbank_viewquestionname\\question_name_idnumber_tags_column-question_name_idnumber_tags_column","width":"400"},{"column":"qbank_editquestion\\question_status_column-question_status_column","width":"20"},{"column":"qbank_history\\version_number_column-version_number_column","width":"20"},{"column":"qbank_statistics\\columns\\discrimination_index:discrimination_index","width":"20"},{"column":"qbank_statistics\\columns\\facility_index:facility_index","width":"20"},{"column":"qbank_statistics\\columns\\discriminative_efficiency:discriminative_efficiency","width":"20"},{"column":"qbank_usage\\question_usage_column-question_usage_column","width":"20"},{"column":"qbank_viewcreator\\creator_name_column-creator_name_column","width":"200"},{"column":"qbank_viewcreator\\modifier_name_column-modifier_name_column","width":"200"},{"column":"qbank_viewquestiontype\\question_type_column-question_type_column","width":"20"},{"column":"core_question\\local\\bank\\edit_menu_column-edit_menu_column","width":"50"}] |
    And I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "teacher1"
    And I apply question bank filter "Category" with value "Question category 1"
    And "Last used" "qbank_columnsortorder > column header" should exist
    And "Question" "qbank_columnsortorder > column header" should appear before "Last used" "qbank_columnsortorder > column header"
    And the "style" attribute of "Question" "qbank_columnsortorder > column header" should contain "width: 400px;"
    When I follow "Reset columns"
    Then "Last used" "qbank_columnsortorder > column header" should not exist
    And "Comments" "qbank_columnsortorder > column header" should appear before "Question" "qbank_columnsortorder > column header"
    And the "style" attribute of "Question" "qbank_columnsortorder > column header" should contain "width: 300px;"

  Scenario: User can remove a column in the question bank
    Given I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "teacher1"
    And I apply question bank filter "Category" with value "Question category 1"
    And "Comments" "qbank_columnsortorder > column header" should exist
    And I click on "Actions menu" "link" in the "Comments" "qbank_columnsortorder > column header"
    And I choose "Remove" in the open action menu
    Then "Comments" "qbank_columnsortorder > column header" should not exist
    And I reload the page
    And "Comments" "qbank_columnsortorder > column header" should not exist

  Scenario: User can add a column in the question bank
    Given I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "teacher1"
    And I apply question bank filter "Category" with value "Question category 1"
    And "Last used" "qbank_columnsortorder > column header" should not exist
    When I press "Add columns"
    And I follow "Last used"
    Then "Last used" "qbank_columnsortorder > column header" should exist
    And I reload the page
    And "Last used" "qbank_columnsortorder > column header" should exist

  Scenario: User can resize a column in the question bank using modal dialog
    Given I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "teacher1"
    And I apply question bank filter "Category" with value "Question category 1"
    And the "style" attribute of "Question" "qbank_columnsortorder > column header" should contain "width: 300px"
    When I click on "Actions menu" "link" in the "Question" "qbank_columnsortorder > column header"
    And I choose "Resize" in the open action menu
    And I set the field "Column width (pixels)" to "400"
    And I press "Save changes"
    Then the "style" attribute of "Question" "qbank_columnsortorder > column header" should contain "width: 400px"
    And I reload the page
    And the "style" attribute of "Question" "qbank_columnsortorder > column header" should contain "width: 400px"

  Scenario: User can resize a column in the question bank by dragging
    Given I change the window size to "large"
    And I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "teacher1"
    And I apply question bank filter "Category" with value "Question category 1"
    And the "style" attribute of "Question" "qbank_columnsortorder > column header" should contain "width: 300px"
    When I hover "Question" "qbank_columnsortorder > column header"
    And I drag "Question" "qbank_columnsortorder > column resize handle" and I drop it in "Status" "qbank_columnsortorder > column header"
    And the "style" attribute of "Question" "qbank_columnsortorder > column header" should not contain "width: 300px"
    And I reload the page
    And the "style" attribute of "Question" "qbank_columnsortorder > column header" should not contain "width: 300px"

  Scenario: User can move a column in the question bank using modal dialog
    Given I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "teacher1"
    And I apply question bank filter "Category" with value "Question category 1"
    And "Comments" "qbank_columnsortorder > column header" should appear before "Question" "qbank_columnsortorder > column header"
    When I click on "Actions menu" "link" in the "Comments" "qbank_columnsortorder > column header"
    And I choose "Move" in the open action menu
    And I follow "After \"Question\""
    Then "Comments" "qbank_columnsortorder > column header" should appear after "Question" "qbank_columnsortorder > column header"
    And I reload the page
    And "Comments" "qbank_columnsortorder > column header" should appear after "Question" "qbank_columnsortorder > column header"

  Scenario: User can move a column in the question bank by dragging
    Given I change the window size to "large"
    And I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "teacher1"
    And I apply question bank filter "Category" with value "Question category 1"
    And "Comments" "qbank_columnsortorder > column header" should appear before "Question" "qbank_columnsortorder > column header"
    When I hover "Comments" "qbank_columnsortorder > column header"
    And I drag "Comments" "qbank_columnsortorder > column move handle" and I drop it in "Status" "qbank_columnsortorder > column header"
    Then "Comments" "qbank_columnsortorder > column header" should appear after "Question" "qbank_columnsortorder > column header"
    And I reload the page
    And "Comments" "qbank_columnsortorder > column header" should appear after "Question" "qbank_columnsortorder > column header"

  Scenario: Reordering with disabled columns
    When I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Disable" "link" in the "Question statistics" "table_row"
    And I click on "Enable" "link" in the "Question statistics" "table_row"
    And I click on "Disable" "link" in the "Question statistics" "table_row"
    And I am on the "Course 1" "core_question > course question bank" page
    Then I should see "Question bank"
    And "Create a new question" "button" should exist
    # Really, we are just checking the question bank displayed without errors.
