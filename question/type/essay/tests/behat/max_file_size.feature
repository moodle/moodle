@qtype @qtype_essay
Feature: In an essay question, let the question author choose the maxbytes for attachments
In order to constrain student submissions for marking
As a teacher
I need to choose the appropriate maxbytes for attachments
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name          | template         | attachments | maxbytes |
      | Test questions   | essay | essay-1-20MB  | editor           | 1           | 20971520 |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript @_switch_window
  Scenario: Preview an Essay question and see the allowed maximum file sizes and number of attachments.
    When I choose "Preview" action for "essay-1-20MB" in the question bank
    And I switch to "questionpreview" window
    And I should see "Please write a story about a frog."
    And I should see "Maximum file size: 20MB, maximum number of files: 1"
    And I switch to the main window
