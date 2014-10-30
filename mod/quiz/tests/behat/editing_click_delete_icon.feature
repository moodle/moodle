@mod @mod_quiz
Feature: Edit quiz page - delete
  In order to change the layout of a quiz I built
  As a teacher
  I need to be able to delete questions.

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
    And the following "activities" exist:
      | activity   | name   | course | idnumber |
      | quiz       | Quiz 1 | C1     | quiz1    |
    And I log in as "teacher1"
    And I follow "Course 1"

  @javascript
  Scenario: Delete questions by clicking on the delete icon.
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Question A |
      | Question text | Answer me  |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Question B   |
      | Question text | Answer again |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Question C |
      | Question text | And again  |
    And I click on the "Add" page break icon after question "Question B"

    # Confirm the starting point.
    Then I should see "Question A" on quiz page "1"
    And I should see "Question B" on quiz page "1"
    And I should see "Question C" on quiz page "2"
    And I should see "Total of marks: 3.00"
    And I should see "Questions: 3"
    And I should see "This quiz is open"

    # Delete last question in last page. Page contains multiple questions
    When I delete "Question C" in the quiz by clicking the delete icon
    Then I should see "Question A" on quiz page "1"
    And I should see "Question B" on quiz page "1"
    And I should not see "Question C" on quiz page "2"
    And I should see "Total of marks: 2.00"
    And I should see "Questions: 2"

    # Delete last question in last page. The page contains multiple questions and there are multiple pages.
    When I click on the "Add" page break icon after question "Question A"
    Then I should see "Question B" on quiz page "2"
    And the "Remove" page break icon after question "Question A" should exist
    And I delete "Question B" in the quiz by clicking the delete icon
    Then I should see "Question A" on quiz page "1"
    And I should not see "Page 2"
    And I should not see "Question B" on quiz page "2"
    And the "Remove" page break icon after question "Question A" should not exist
    And I should see "Total of marks: 1.00"

    # Delete last remaining question in the last remaining page.
    And I delete "Question A" in the quiz by clicking the delete icon
    Then I should not see "Question A" on quiz page "1"
    And I should not see "Page 1"
    And I should see "Total of marks: 0.00"

  @javascript @edit_quiz_delete_start
  Scenario: Delete questions from the start of the list.
    # Add more questions.
    When I add a "Description" question to the "Quiz 1" quiz with:
    | Question name | Question A |
    | Question text | Answer A   |

    And I add a "True/False" question to the "Quiz 1" quiz with:
    | Question name | Question B |
    | Question text | Answer B   |

    And I add a "Description" question to the "Quiz 1" quiz with:
    | Question name | Question C |
    | Question text | Answer C   |

    And I add a "True/False" question to the "Quiz 1" quiz with:
    | Question name | Question D |
    | Question text | Answer D   |

    And I add a "True/False" question to the "Quiz 1" quiz with:
    | Question name | Question E |
    | Question text | Answer E   |

    Then "Question A" should have number "i" on the edit quiz page
    And "Question B" should have number "1" on the edit quiz page
    And "Question C" should have number "i" on the edit quiz page
    And "Question D" should have number "2" on the edit quiz page
    And "Question E" should have number "3" on the edit quiz page

    # Delete from first question in the last remaining page. Are the page breaks updated?
    When I delete "Question A" in the quiz by clicking the delete icon
    Then "Question B" should have number "1" on the edit quiz page
    And "Question C" should have number "i" on the edit quiz page
    And "Question D" should have number "2" on the edit quiz page
    And "Question E" should have number "3" on the edit quiz page

    When I click on the "Add" page break icon after question "Question C"
    Then I should see "Page 1"
    And I should see "Question B" on quiz page "1"
    And I should see "Question C" on quiz page "1"
    Then I should see "Page 2"
    And I should see "Question D" on quiz page "2"
    And I should see "Question E" on quiz page "2"

    # Test reorder of pages
    When I click on the "Add" page break icon after question "Question B"
    Then I should see "Page 1"
    And I should see "Question B" on quiz page "1"
    Then I should see "Page 2"
    And I should see "Question C" on quiz page "2"
    Then I should see "Page 3"
    And I should see "Question D" on quiz page "3"
    And I should see "Question E" on quiz page "3"
