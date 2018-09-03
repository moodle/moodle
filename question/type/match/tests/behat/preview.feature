@qtype @qtype_match
Feature: Preview a Matching question
  As a teacher
  In order to check my Matching questions will work for students
  I need to preview them

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
      | questioncategory | qtype | name         | template |
      | Test questions   | match | matching-001 | foursubq |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" node in "Course administration"

  @javascript @_switch_window
  Scenario: Preview a Matching question and submit a correct response.
    When I click on "Edit" "link" in the "matching-001" "table_row"
    And I set the following fields to these values:
      | Shuffle    | 0   |
    And I press "id_submitbutton"
    When I click on "Preview" "link" in the "matching-001" "table_row"
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub0')]" to "amphibian"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub1')]" to "mammal"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub2')]" to "amphibian"
    And I press "Check"
    Then I should see "Well done!"
    And I should see "General feedback."
    And I switch to the main window

  @javascript @_switch_window
  Scenario: Preview a Matching question and submit a partially correct response.
    When I click on "Edit" "link" in the "matching-001" "table_row"
    And I set the following fields to these values:
      | Shuffle    | 0   |
    And I press "id_submitbutton"
    When I click on "Preview" "link" in the "matching-001" "table_row"
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub0')]" to "amphibian"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub1')]" to "insect"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub2')]" to "amphibian"
    And I press "Check"
    Then I should see "Parts, but only parts, of your response are correct."
    And I should see "General feedback."
    And I switch to the main window

  @javascript @_switch_window
  Scenario: Preview a Matching question and submit an incorrect response.
    When I click on "Edit" "link" in the "matching-001" "table_row"
    And I set the following fields to these values:
      | Shuffle    | 0   |
    And I press "id_submitbutton"
    When I click on "Preview" "link" in the "matching-001" "table_row"
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub0')]" to "mammal"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub1')]" to "insect"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub2')]" to "insect"
    And I press "Check"
    Then I should see "That is not right at all."
    And I should see "General feedback."
    And I switch to the main window
