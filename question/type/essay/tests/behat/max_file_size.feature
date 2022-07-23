@qtype @qtype_essay
Feature: In an essay question, let the question author choose the maxbytes for attachments
  In order to constrain student submissions for marking
  As a teacher
  I need to choose the appropriate maxbytes for attachments

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category | maxbytes |
      | Course 1 | C1        | 0        | 1048576  |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name          | template         | attachments | maxbytes |
      | Test questions   | essay | essay-1-512KB | editor           | 1           | 524288   |
      | Test questions   | essay | essay-1-max   | editor           | 1           | 0        |

  @javascript @_switch_window
  Scenario: Preview an Essay question and see the allowed maximum file sizes and number of attachments.
    When I am on the "essay-1-512KB" "core_question > preview" page logged in as teacher
    Then I should see "Please write a story about a frog."
    And I should see "Maximum file size: 512 KB, maximum number of files: 1"

  @javascript @_switch_window
  Scenario: Preview an Essay question with Course upload limit and see the allowed maximum file size.
    When I am on the "essay-1-max" "core_question > preview" page logged in as teacher
    Then I should see "Please write a story about a frog."
    And I should see "Maximum file size: 1 MB, maximum number of files: 1"
