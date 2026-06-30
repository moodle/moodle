@qbank @qbank_exporttoxml
Feature: Use the qbank plugin manager page for exporttoxml
  In order to check the plugin behaviour with enable and disable

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher  | Teacher   | 1        | teacher@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name        | course | idnumber |
      | qbank      | Qbank 1     | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | questioncategory | name           |
      | Activity module | qbank1    | Top              | top            |
      | Activity module | qbank1    | top              | Default for C1 |
      | Activity module | qbank1    | Default for C1   | Subcategory    |
    And the following "questions" exist:
      | questioncategory      | qtype     | name           | questiontext                  |
      | Default for C1        | truefalse | First question | Answer the first question     |
      | Subcategory           | essay     | Essay Foo Bar  | Write about whatever you want |

  @javascript
  Scenario: Enable/disable exporttoxml column from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Export to XML"
    And I click on "Disable" "link" in the "Export to XML" "table_row"
    And I am on the "Qbank 1" "core_question > question bank" page
    And I apply question bank filter "Category" with value "Default for C1"
    Then the "Export as Moodle XML" action should not exist for the "First question" question in the question bank
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    Then I should not see question bulk action "Export to XML"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Export to XML" "table_row"
    And I am on the "Qbank 1" "core_question > question bank" page
    And I apply question bank filter "Category" with value "Default for C1"
    And the "Export as Moodle XML" action should exist for the "First question" question in the question bank
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    Then I should see question bulk action "exportselected"

  @javascript
  Scenario: Bulk export questions as Moodle XML
    Given I log in as "teacher"
    And I am on the "Qbank 1" "core_question > question bank" page
    And I apply question bank filter "Category" with value "Default for C1"
    And I should see "First question"
    And I should not see "Essay Foo Bar"
    And I click on "Also show questions from subcategories" "checkbox"
    And I click on "Apply filters" "button"
    And I should see "First question"
    And I should see "Essay Foo Bar"
    And I click on "First question" "checkbox"
    And I click on "Essay Foo Bar" "checkbox"
    And I click on "With selected" "button"
    And I should see question bulk action "exportselected"
    And I click on question bulk action "exportselected"
    #A dialogue appears to download the file which must be confirmed with ok. Therefore the next step does not work.
    #Then following "Download" should download between "1" and "180000" bytes
