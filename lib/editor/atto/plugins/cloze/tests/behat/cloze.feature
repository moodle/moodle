@editor @editor_atto @atto @atto_cloze @_bug_phantomjs
Feature: Atto cloze editor button
  As a teacher
  In order to create cloze questions
  I need to use an editing tool.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname |
      | teacher  | Teacher   |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name                 | questiontext |
      | Test questions   | shortanswer | shortanswer question | Rabbit       |
    And I log in as "admin"
    And I navigate to "Atto toolbar settings" node in "Site administration > Plugins > Text editors > Atto HTML editor"
    And I set the field "Toolbar config" to "other = html, cloze"
    And I press "Save changes"
    And I follow "Site home"
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"
    And I click on "Create a new question ..." "button"
    And I click on "Embedded answers" "radio" in the "Choose a question type to add" "dialogue"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"

@javascript @atto_shortanswer
  Scenario: Insert the button into question text of existing question
    When I set the field "Question text" to "Bunny"
    And I select the text in the "Question text" Atto editor
    And I click on "Cloze editor" "button"
    And I click on "SHORTANSWER" "radio" in the "Cloze editor" "dialogue"
    And I click on "Add" "button" in the "Cloze editor" "dialogue"
    And I set the field with xpath "//input[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_feedback ')]" to "Funny"
    And I click on "Insert" "button" in the "Cloze editor" "dialogue"
    Then I should see "{1:SHORTANSWER:~%100%Bunny#Funny}"

@javascript @atto_multichoice
  Scenario: Create a multiple choice question
    When I set the field "Question text" to "<p> blind mice.</p>"
    And I click on "Cloze editor" "button"
    And I click on "MULTICHOICE_S" "radio" in the "Cloze editor" "dialogue"
    And I click on "Add" "button" in the "Cloze editor" "dialogue"
    And I set the field with xpath "//div[@class='atto_cloze']//li[1]//input[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_answer ')]" to "Three"
    And I click on "//div[@class='atto_cloze']//a[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_add ')]" "xpath_element"
    And I set the field with xpath "//div[@class='atto_cloze']//li[2]//select[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_fraction ')]" to "Incorrect"
    And I click on "//div[@class='atto_cloze']//li[2]//a[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_add ')]" "xpath_element"
    And I set the field with xpath "//div[@class='atto_cloze']//li[2]//input[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_answer ')]" to "Four"
    And I set the field with xpath "//div[@class='atto_cloze']//li[1]//input[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_feedback ')]" to "Right"
    And I set the field with xpath "//div[@class='atto_cloze']//li[3]//input[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_answer ')]" to "Five"
    And I click on "Insert" "button" in the "Cloze editor" "dialogue"
    Then I should see "{1:MULTICHOICE_S:~%100%Three#Right~Four~Five}"

@javascript @atto_numerical
  Scenario: Create a numerical question
    When I set the field "Question text" to "<p> blind mice.</p>"
    And I click on "Cloze editor" "button"
    And I click on "NUMERICAL" "radio" in the "Cloze editor" "dialogue"
    And I click on "Add" "button" in the "Cloze editor" "dialogue"
    And I set the field with xpath "//div[@class='atto_cloze']//li[1]//input[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_answer ')]" to "3"
    And I set the field with xpath "//div[@class='atto_cloze']//li[1]//input[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_tolerance ')]" to "0.5"
    And I set the field with xpath "//input[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_feedback ')]" to "Three is correct"
    And I click on "Insert" "button" in the "Cloze editor" "dialogue"
    Then I should see "{1:NUMERICAL:~%100%3:0.5#Three is correct}"

@javascript @atto_cloze_import
  Scenario: Read a subquestion into the edit form
    When I set the field "Question text" to "{1:SHORTANSWER:~%100%Bunny#Funny}"
    And I select the text in the "Question text" Atto editor
    And I click on "Cloze editor" "button"
    And I set the field with xpath "//div[@class='atto_cloze']//li[1]//input[contains(concat(' ', normalize-space(@class), ' '), ' atto_cloze_answer ')]" to "10# Hare"
    And I click on "Insert" "button" in the "Cloze editor" "dialogue"
    Then I should see "{1:SHORTANSWER:~%100%10\# Hare#Funny}"
