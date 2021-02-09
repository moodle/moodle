@mod @mod_quiz
Feature: Edit quiz page - pagination
  In order to build a quiz laid out in pages the way I want
  As a teacher
  I need to be able to add and remove pages, and repaginate.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |

    When I log in as "teacher1"
    And I am on the "Quiz 1" "mod_quiz > Edit" page

  @javascript
  Scenario: Repaginate questions with N question(s) per page as well as clicking
    on "add page break" or "Remove page break" icons to repaginate in any desired format.

    Then I should see "Editing quiz: Quiz 1"

    # Add the first Essay question.
    And I open the action menu in ".page-add-actions" "css_element"
    And I follow "a new question"
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 01 new"
    And I set the field "Question text" to "Please write 100 words about Essay 01"
    And I press "id_submitbutton"
    Then I should see "Editing quiz: Quiz 1"
    And I should see "Essay 01 new" on quiz page "1"

    # Add the second Essay question.
    And I open the action menu in ".page-add-actions" "css_element"
    And I follow "a new question"
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 02 new"
    And I set the field "Question text" to "Please write 200 words about Essay 02"
    And I press "id_submitbutton"
    Then I should see "Editing quiz: Quiz 1"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"

    # Start repaginating.
    And I should not see "Page 2"

    When I click on the "Add" page break icon after question "Essay 01 new"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "2"

    When I click on the "Remove" page break icon after question "Essay 01 new"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"
    And I should not see "Page 2"

    # Add the third Essay question.
    And I open the action menu in ".page-add-actions" "css_element"
    And I follow "a new question"
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 03 new"
    And I set the field "Question text" to "Please write 200 words about Essay 03"
    And I press "id_submitbutton"
    Then I should see "Editing quiz: Quiz 1"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"
    And I should see "Essay 03 new" on quiz page "1"
    And I should not see "Page 2"
    And I should not see "Page 3"

    When I click on the "Add" page break icon after question "Essay 02 new"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"
    And I should see "Essay 03 new" on quiz page "2"
    And I should not see "Page 3"

    When I click on the "Add" page break icon after question "Essay 01 new"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "2"
    And I should see "Essay 03 new" on quiz page "3"

    When I click on the "Remove" page break icon after question "Essay 02 new"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "2"
    And I should see "Essay 03 new" on quiz page "2"
    And I should not see "Page 3"

    When I click on the "Remove" page break icon after question "Essay 01 new"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"
    And I should see "Essay 03 new" on quiz page "1"
    And I should not see "Page 2"
    And I should not see "Page 3"

    # Repaginate one question per page.
    When I press "Repaginate"
    And I set the field "menuquestionsperpage" to "1"
    And I click on "Go" "button" in the "Repaginate" "dialogue"
    Then I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "2"
    And I should see "Essay 03 new" on quiz page "3"

    # Add the forth Essay question in a new page (Page 4).
    When I open the "Page 3" add to quiz menu
    And I choose "a new question" in the open action menu
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    When I set the field "Question name" to "Essay 04 new"
    And I set the field "Question text" to "Please write 300 words about Essay 04"
    And I press "id_submitbutton"
    Then I should see "Editing quiz: Quiz 1"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "2"
    And I should see "Essay 03 new" on quiz page "3"
    And I should see "Essay 04 new" on quiz page "3"

    When I click on the "Add" page break icon after question "Essay 03 new"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "2"
    And I should see "Essay 03 new" on quiz page "3"
    And I should see "Essay 04 new" on quiz page "4"

    # Repaginate with 2 questions per page.
    When I press "Repaginate"
    And I set the field "menuquestionsperpage" to "2"
    And I click on "Go" "button" in the "Repaginate" "dialogue"
    Then I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"
    And I should see "Essay 03 new" on quiz page "2"
    And I should see "Essay 04 new" on quiz page "2"

    # Repaginate with unlimited questions per page (All questions on Page 1).
    When I press "Repaginate"
    And I set the field "menuquestionsperpage" to "Unlimited"
    And I click on "Go" "button" in the "Repaginate" "dialogue"
    Then I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"
    And I should see "Essay 03 new" on quiz page "1"
    And I should see "Essay 04 new" on quiz page "1"
