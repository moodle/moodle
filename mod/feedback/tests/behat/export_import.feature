@mod @mod_feedback
Feature: Exporting and importing feedbacks
  In order to quickly copy feedbacks across courses and sites
  As a teacher
  I need to be able to export and import feedbacks

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher | Teacher   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher | C1     | editingteacher |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name                | course | idnumber    |
      | feedback   | Learning experience | C1     | feedback0   |

  Scenario: Export sample feedback and compare with the fixture
    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I follow "Learning experience"
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I add a "Information" question to the feedback with:
      | Question         | this is an information question |
      | Label            | info                            |
      | Information type | Course                          |
    And I add a "Label" question to the feedback with:
      | Contents | label text |
    And I add a "Longer text answer" question to the feedback with:
      | Question         | this is a longer text answer |
      | Label            | longertext                   |
      | Required         | 1                            |
    And I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 1 |
      | Label            | multichoice1                |
      | Multiple choice type | Multiple choice - single answer |
      | Multiple choice values | option a\noption b\noption c  |
    And I select "Add a page break" from the "Add question" singleselect
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | this is a multiple choice 2        |
      | Label                          | multichoice2                       |
      | Multiple choice type           | Multiple choice - multiple answers |
      | Hide the "Not selected" option | Yes                                |
      | Multiple choice values         | option d\noption e\noption f       |
      | Dependence item                | multichoice1                       |
      | Dependence value               | option a                           |
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | this is a multiple choice 3        |
      | Label                          | multichoice3                       |
      | Multiple choice type           | Multiple choice - single answer allowed (drop-down menu) |
      | Multiple choice values         | option g\noption h\noption i                           |
    And I add a "Multiple choice (rated)" question to the feedback with:
      | Question               | this is a multiple choice rated |
      | Label                  | multichoice4                    |
      | Multiple choice type   | Multiple choice - single answer |
      | Multiple choice values | 0/option k\n1/option l\n5/option m |
    And I add a "Numeric answer" question to the feedback with:
      | Question               | this is a numeric answer |
      | Label                  | numeric                  |
      | Range to               | 100                      |
    And I add a "Short text answer" question to the feedback with:
      | Question               | this is a short text answer |
      | Label                  | shorttext                   |
      | Maximum characters accepted | 200                    |
    And I follow "Templates"
    Then following "Export questions" should export feedback identical to "mod/feedback/tests/fixtures/testexport.xml"
    And I log out

  @javascript @_file_upload
  Scenario: Import feedback deleting old items
    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I follow "Learning experience"
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I add a "Numeric answer" question to the feedback with:
      | Question               | Existing question |
      | Label                  | numeric           |
      | Range to               | 100               |
    And I follow "Templates"
    And I follow "Import questions"
    And I upload "mod/feedback/tests/fixtures/testexport.xml" file to "File" filemanager
    And I press "Yes"
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    Then I should not see "Existing question"
    And I should see "this is an information question"
    And I should see "label text"
    And I should see "this is a longer text answer"
    And I should see "this is a multiple choice 1"
    And I should see "this is a multiple choice 2"
    And I should see "this is a multiple choice 3"
    And I should see "this is a multiple choice rated"
    And I should see "this is a numeric answer"
    And I should see "this is a short text answer"
    And I log out

  @javascript @_file_upload
  Scenario: Import feedback appending new items
    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I follow "Learning experience"
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I add a "Numeric answer" question to the feedback with:
      | Question               | Existing question |
      | Label                  | numeric           |
      | Range to               | 100               |
    And I follow "Templates"
    And I follow "Import questions"
    And I set the field "Append new items" to "1"
    And I upload "mod/feedback/tests/fixtures/testexport.xml" file to "File" filemanager
    And I press "Yes"
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    Then I should see "Existing question"
    And "Existing question" "text" should appear before "this is an information question" "text"
    And I should see "this is an information question"
    And I should see "label text"
    And I should see "this is a longer text answer"
    And I should see "this is a multiple choice 1"
    And I should see "this is a multiple choice 2"
    And I should see "this is a multiple choice 3"
    And I should see "this is a multiple choice rated"
    And I should see "this is a numeric answer"
    And I should see "this is a short text answer"
    And I log out
