@qbank @qbank_columnsortorder @javascript
Feature: An plugin column can be reordered and displayed in the question bank view.
    In order to reorganise the question bank view columns
    As an admin
    I need to be able to reorder them

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

  Scenario: Teacher can see proper view
    Given I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "teacher1"
    And I set the field "category" to "Question category 1"
    And I should see "Test question to be seen"
    Then I should see "Teacher 1"

  Scenario: Reordering question bank columns
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And I drag "Created by (creator_name_column)" "text" and I drop it in "T (question_type_column)" "text"
    And I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "teacher1"
    And I set the field "category" to "Question category 1"
    Then ".creatorname" "css_element" should appear before ".qtype" "css_element"

  Scenario: Disabling and enabling column display is proper
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And I should see "Created by (creator_name_column)"
    And I click on "Manage question bank plugins" "link"
    And I click on "Disable" "link" in the "View creator" "table_row"
    And I click on "Column sort order" "link"
    Then "Currently disabled question bank plugins:" "text" should appear before "Created by" "text"
    And I should not see "Created by (creator_name_column)"
    And I click on "Manage question bank plugins" "link"
    And I click on "Enable" "link" in the "View creator" "table_row"
    And I click on "Column sort order" "link"
    Then I should not see "Currently disabled question bank plugins:"
    And I should see "Created by (creator_name_column)"

  Scenario: Custom fields are reorderable
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Question custom fields" in site administration
    And I press "Add a new category"
    And I click on "Add a new custom field" "link"
    And I follow "Checkbox"
    And I set the following fields to these values:
      | Name       | checkboxcustomcolumn |
      | Short name | chckcust             |
    And I press "Save changes"
    Then I should see "checkboxcustomcolumn"
    And I navigate to "Plugins > Question bank plugins > Column sort order" in site administration
    And I should see "checkboxcustomcolumn"
    And I change the window size to "large"
    And I drag "checkboxcustomcolumn" "text" and I drop it in "T (question_type_column)" "text"
    Then "checkboxcustomcolumn" "text" should appear before "T (question_type_column)" "text"
    And I click on "Manage question bank plugins" "link"
    And I click on "Disable" "link" in the "Question custom fields" "table_row"
    And I click on "Column sort order" "link"
    Then "Currently disabled question bank plugins:" "text" should appear before "chckcust" "text"
    And I click on "Manage question bank plugins" "link"
    And I click on "Enable" "link" in the "Question custom fields" "table_row"
    And I click on "Column sort order" "link"
    Then I should not see "Currently disabled question bank plugins:"
    And I should see "checkboxcustomcolumn"
