@mod @mod_quiz
Feature: Edit quiz page - adding things
  In order to build the quiz I want my students to attempt
  As a teacher
  I need to be able to add questions to the quiz.

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
      | activity   | name    | intro                                     | course | idnumber  |
      | quiz       | Quiz 1  | Quiz 1 for testing the Add menu           | C1     | quiz1     |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"

  @javascript
  Scenario: Add some new question to the quiz using '+ a new question' options of the 'Add' menu.
    When I open the "last" add to quiz menu
    And I follow "a new question"
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 01 new"
    And I set the field "Question text" to "Please write 200 words about Essay 01"
    And I press "id_submitbutton"
    And I should see "Essay 01 new" on quiz page "1"

    And I open the "Page 1" add to quiz menu
    And I follow "a new question"
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 02 new"
    And I set the field "Question text" to "Please write 200 words about Essay 02"
    And I press "id_submitbutton"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"

    And I open the "Page 1" add to quiz menu
    And I follow "a new question"
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 03 new"
    And I set the field "Question text" to "Please write 300 words about Essay 03"
    And I press "id_submitbutton"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"
    And I should see "Essay 03 new" on quiz page "1"

    And I open the "Page 1" add to quiz menu
    And I follow "a new question"
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 04 new"
    And I set the field "Question text" to "Please write 300 words about Essay 04"
    And I press "id_submitbutton"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"
    And I should see "Essay 03 new" on quiz page "1"
    And I should see "Essay 04 new" on quiz page "1"

    # Repaginate as two questions per page.
    And I should not see "Page 2"
    When I press "Repaginate"
    Then I should see "Repaginate with"
    And I set the field "menuquestionsperpage" to "2"
    And I click on "Go" "button" in the "Repaginate" "dialogue"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"
    And I should see "Essay 03 new" on quiz page "2"
    And I should see "Essay 04 new" on quiz page "2"

    # Add a question to page 2.
    When I open the "Page 2" add to quiz menu
    And I choose "a new question" in the open action menu
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay for page 2"
    And I set the field "Question text" to "Please write 200 words about Essay for page 2"
    And I press "id_submitbutton"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"
    And I should see "Essay 03 new" on quiz page "2"
    And I should see "Essay 04 new" on quiz page "2"
    And I should see "Essay for page 2" on quiz page "2"

  @javascript
  Scenario: Add questions from question bank to the quiz. In order to be able to
      add questions from question bank to the quiz, first we create some new questions
      in various categories and add them to the question bank.

    # Create a couple of sub categories.
    Given the following "question categories" exist:
      | contextlevel    | reference | questioncategory | name           |
      | Activity module | quiz1     | Test questions   | Subcat 1       |
      | Activity module | quiz1     | Test questions   | Subcat 2       |
    When I am on the "Quiz 1" "mod_quiz > question categories" page

    And I select "Questions" from the "Question bank tertiary navigation" singleselect
    And I should see "Question bank"

    # Create the Essay 01 question.
    When I press "Create a new question ..."
    And I set the field "item_qtype_essay" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 01"
    And I set the field "Question text" to "Please write 100 words about Essay 01"
    And I press "id_submitbutton"
    Then I should see "Question bank"
    And I should see "Essay 01"

    # Create the Essay 02 question.
    When I press "Create a new question ..."
    And I set the field "item_qtype_essay" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 02"
    And I set the field "Question text" to "Please write 200 words about Essay 02"
    And I press "id_submitbutton"
    Then I should see "Question bank"
    And I should see "Essay 02"

    # Create the Essay 03 question.
    And I wait until the page is ready
    When I press "Create a new question ..."
    And I set the field "item_qtype_essay" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 03"
    And I set the field "Question text" to "Please write 300 words about Essay 03"
    And I press "id_submitbutton"
    Then I should see "Question bank"
    And I should see "Essay 03"

    # Create the TF 01 question.
    When I press "Create a new question ..."
    And I set the field "item_qtype_truefalse" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    Then I should see "Adding a True/False question"
    And I set the field "Question name" to "TF 01"
    And I set the field "Question text" to "The correct answer is true"
    And I set the field "Correct answer" to "True"
    And I press "id_submitbutton"
    Then I should see "Question bank"
    And I should see "TF 01"

    # Create the TF 02 question.
    When I press "Create a new question ..."
    And I set the field "item_qtype_truefalse" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    Then I should see "Adding a True/False question"
    And I set the field "Question name" to "TF 02"
    And I set the field "Question text" to "The correct answer is false"
    And I set the field "Correct answer" to "False"
    And I press "id_submitbutton"
    Then I should see "Question bank"
    And I should see "TF 02"

    # Add questions from question bank using the Add menu.
    # Add Essay 03 from question bank.
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    Then the "Add selected questions to the quiz" "button" should be disabled
    And I click on "Select" "checkbox" in the "Essay 03" "table_row"
    Then the "Add selected questions to the quiz" "button" should be enabled
    And I click on "Add to quiz" "link" in the "Essay 03" "table_row"
    And I should see "Essay 03" on quiz page "1"

    # Add Essay 01 from question bank.
    And I open the "Page 1" add to quiz menu
    And I follow "from question bank"
    And I click on "Add to quiz" "link" in the "Essay 01" "table_row"
    And I should see "Essay 03" on quiz page "1"
    And I should see "Essay 01" on quiz page "1"

    # Add Esay 02 from question bank.
    And I open the "Page 1" add to quiz menu
    And I follow "from question bank"
    And I click on "Add to quiz" "link" in the "Essay 02" "table_row"
    And I should see "Essay 03" on quiz page "1"
    And I should see "Essay 01" on quiz page "1"
    And I should see "Essay 02" on quiz page "1"

    # Add a random question.
    And I open the "Page 1" add to quiz menu
    And I follow "a random question"
    And I press "Add random question"
    And I should see "Essay 03" on quiz page "1"
    And I should see "Essay 01" on quiz page "1"
    And I should see "Essay 02" on quiz page "1"
    And I should see "Random" on quiz page "1"

    # Repaginate as one question per page.
    And I should not see "Page 2"
    When I press "Repaginate"
    Then I should see "Repaginate with"
    And I set the field "menuquestionsperpage" to "1"
    When I click on "Go" "button" in the "Repaginate" "dialogue"
    And I should see "Essay 03" on quiz page "1"
    And I should see "Essay 01" on quiz page "2"
    And I should see "Essay 02" on quiz page "3"
    And I should see "Random" on quiz page "4"

    # Add a random question to page 4.
    And I open the "Page 4" add to quiz menu
    And I choose "a new question" in the open action menu
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay for page 4"
    And I set the field "Question text" to "Please write 200 words about Essay for page 4"
    And I press "id_submitbutton"
    And I should see "Essay 03" on quiz page "1"
    And I should see "Essay 01" on quiz page "2"
    And I should see "Essay 02" on quiz page "3"
    And I should see "Random" on quiz page "4"
    And I should see "Essay for page 4" on quiz page "4"

  @accessibility @javascript
  Scenario: Check the accessibility of the quiz questions page
    Given the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
      | Test questions   | truefalse | Other question | Answer the first question |
    And quiz "Quiz 1" contains the following questions:
      | question          | page |
      | First question    | 1    |
    When I reload the page
    Then I should see "First question"
    And the page should meet accessibility standards
