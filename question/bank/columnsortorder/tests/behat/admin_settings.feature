@qbank @qbank_columnsortorder @javascript
Feature: Set default question bank column order and size
  In order to set sensible defaults for the question bank interface
  As an admin
  I want to hide, reorder, and resize columns

  Scenario: Admin can reorder question bank columns
    Given I change the window size to "large"
    And I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And I drag "Created by" "qbank_columnsortorder > column move handle" and I drop it in "T" "qbank_columnsortorder > column move handle"
    Then "Created by" "table_row" should appear before "T" "table_row"
    And I reload the page
    And "Created by" "table_row" should appear before "T" "table_row"
    And I follow "Preview"
    And "Created by" "qbank_columnsortorder > column header" should appear before "T" "qbank_columnsortorder > column header"

  Scenario: Disabling a question bank plugin removes its columns
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And I should see "Created by"
    And I click on "Manage question bank plugins" "link"
    And I click on "Disable" "link" in the "View creator" "table_row"
    And I click on "Column sort order" "link"
    Then "Currently disabled question bank plugins:" "text" should appear before "Created by" "text"
    And I click on "Manage question bank plugins" "link"
    And I click on "Enable" "link" in the "View creator" "table_row"
    And I click on "Column sort order" "link"
    Then I should not see "Currently disabled question bank plugins:"
    And I should see "Created by"

  Scenario: Admin can hide a column in site administration page
    Given I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And "Created by" "table_row" should exist
    When I click on "Actions menu" "link" in the "Created by" "table_row"
    And I choose "Remove" in the open action menu
    Then "Created by" "table_row" should not exist
    And I reload the page
    And "Created by" "table_row" should not exist
    And I follow "Preview"
    And "Created by" "qbank_columnsortorder > column header" should not exist

  Scenario: Admin can show a column in site administration page
    Given the following config values are set as admin:
      | config     | value                                                     | plugin                |
      | hiddencols | qbank_viewcreator\creator_name_column-creator_name_column | qbank_columnsortorder |
    And I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And "Created by" "table_row" should not exist
    When I press "Add columns"
    And I follow "Created by"
    Then "Created by" "table_row" should exist
    And I reload the page
    And "Created by" "table_row" should exist
    And I follow "Preview"
    And "Created by" "qbank_columnsortorder > column header" should exist

  Scenario: Admin can resize a column in site administration page
    Given I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And the field "Width of 'Question' in pixels" matches value ""
    When I set the field "Width of 'Question' in pixels" to "400"
    And I reload the page
    Then the field "Width of 'Question' in pixels" matches value "400"
    And I follow "Preview"
    And the "style" attribute of "Question" "qbank_columnsortorder > column header" should contain "width: 400px"

  Scenario: Custom fields can be reordered, resized, hidden and shown
    Given I change the window size to "large"
    And I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Question custom fields" in site administration
    And I press "Add a new category"
    And I click on "Add a new custom field" "link"
    And I follow "Checkbox"
    And I set the following fields to these values:
      | Name       | checkboxcustomcolumn |
      | Short name | chckcust             |
    And I press "Save changes"
    And I should see "checkboxcustomcolumn"
    And I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And "checkboxcustomcolumn" "table_row" should appear after "Comments" "table_row"
    When I drag "checkboxcustomcolumn" "qbank_columnsortorder > column move handle" and I drop it in "Comments" "qbank_columnsortorder > column move handle"
    And I set the field "Width of 'checkboxcustomcolumn' in pixels" to "200"
    And I follow "Preview"
    And "checkboxcustomcolumn" "qbank_columnsortorder > column header" should appear before "Comments" "qbank_columnsortorder > column header"
    And the "style" attribute of "checkboxcustomcolumn" "qbank_columnsortorder > column header" should contain "width: 200px"
    And I follow "Back"
    And "checkboxcustomcolumn" "table_row" should appear before "Comments" "table_row"
    And the field "Width of 'checkboxcustomcolumn' in pixels" matches value "200"
    And I click on "Actions menu" "link" in the "checkboxcustomcolumn" "table_row"
    And I choose "Remove" in the open action menu
    And "checkboxcustomcolumn" "table_row" should not exist
    And I follow "Preview"
    And "checkboxcustomcolumn" "qbank_columnsortorder > column header" should not exist
    And I follow "Back"
    And I press "Add columns"
    And I follow "checkboxcustomcolumn"
    And "checkboxcustomcolumn" "table_row" should exist
    And I follow "Preview"
    And "checkboxcustomcolumn" "qbank_columnsortorder > column header" should exist

  Scenario: Resetting columns on the admin page clears global settings
    Given the following config values are set as admin:
      | config       | value                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            | plugin                |
      | hiddencols   | qbank_usage\\question_last_used_column-question_last_used_column                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 | qbank_columnsortorder |
      | enabledcol   | qbank_comment\\comment_count_column-comment_count_column,qbank_viewquestionname\\question_name_idnumber_tags_column-question_name_idnumber_tags_column,core_question\\local\\bank\\edit_menu_column-edit_menu_column,qbank_editquestion\\question_status_column-question_status_column,qbank_history\\version_number_column-version_number_column,qbank_statistics\\columns\\discrimination_index:discrimination_index,qbank_statistics\\columns\\facility_index:facility_index,qbank_statistics\\columns\\discriminative_efficiency:discriminative_efficiency,qbank_usage\\question_usage_column-question_usage_column,qbank_usage\\question_last_used_column-question_last_used_column,qbank_viewcreator\\creator_name_column-creator_name_column,qbank_viewcreator\\modifier_name_column-modifier_name_column,qbank_viewquestiontype\\question_type_column-question_type_column                                                                                                                                                                                                                                                                                               | qbank_columnsortorder |
      | colsize      | [{"column":"qbank_comment\\\\comment_count_column-comment_count_column","width":"20"},{"column":"qbank_viewquestionname\\\\question_name_idnumber_tags_column-question_name_idnumber_tags_column","width":"300"},{"column":"qbank_editquestion\\\\question_status_column-question_status_column","width":"20"},{"column":"qbank_history\\\\version_number_column-version_number_column","width":"20"},{"column":"qbank_statistics\\\\columns\\\\discrimination_index:discrimination_index","width":"20"},{"column":"qbank_statistics\\\\columns\\\\facility_index:facility_index","width":"20"},{"column":"qbank_statistics\\\\columns\\\\discriminative_efficiency:discriminative_efficiency","width":"20"},{"column":"qbank_usage\\\\question_usage_column-question_usage_column","width":"20"},{"column":"qbank_viewcreator\\\\creator_name_column-creator_name_column","width":"200"},{"column":"qbank_viewcreator\\\\modifier_name_column-modifier_name_column","width":"200"},{"column":"qbank_viewquestiontype\\\\question_type_column-question_type_column","width":"20"},{"column":"core_question\\\\local\\\\bank\\\\edit_menu_column-edit_menu_column","width":"50"}] | qbank_columnsortorder |
    And I change the window size to "large"
    And I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And "Last used" "table_row" should not exist
    And "Comments" "table_row" should appear before "Question" "table_row"
    And the field "Width of 'Question' in pixels" matches value "300"
    When I follow "Reset columns"
    Then "Last used" "table_row" should exist
    And "Question" "table_row" should appear before "Comments" "table_row"
    And the field "Width of 'Question' in pixels" matches value ""

  Scenario: Deleting a custom field which is removed from the Column sort order
    Given the following "custom field categories" exist:
      | name              | component          | area     | itemid |
      | Category for test | qbank_customfields | question | 0      |
    And the following "custom fields" exist:
      | name    | category          | type | shortname | configdata         |
      | Field 1 | Category for test | text | f1        | {"visibility":"2"} |
      | Field 2 | Category for test | text | f2        | {"visibility":"2"} |
    And I change the window size to "large"
    When I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And "Field 1" "table_row" should exist
    And "Field 2" "table_row" should exist
    And I click on "Actions menu" "link" in the "Field 1" "table_row"
    And I choose "Remove" in the open action menu

    # Delete a question custom field.
    And I navigate to "Plugins > Question bank plugins > Question custom fields" in site administration
    And I click on "Delete" "link" in the "Field 1" "table_row"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    Then I should see "Column sort order"
    And "Field 2" "table_row" should exist
    And I click on "Actions menu" "link" in the "Field 2" "table_row"
    And I choose "Remove" in the open action menu

    # Delete the question custom category.
    And I navigate to "Plugins > Question bank plugins > Question custom fields" in site administration
    And I click on "[data-role='deletecategory']" "css_element"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I should not see "Category for test" in the "#customfield_catlist" "css_element"
    And I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And I should see "Column sort order"
