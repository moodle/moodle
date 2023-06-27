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
      | activity   | name   | intro                           | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 for testing the Add menu | C1     | quiz1    |
    And I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I should see "Editing quiz: Quiz 1"

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
    Then I should see "Editing quiz: Quiz 1"
    And I should see "Essay 01 new" on quiz page "1"

    And I open the "Page 1" add to quiz menu
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

    And I open the "Page 1" add to quiz menu
    And I follow "a new question"
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 03 new"
    And I set the field "Question text" to "Please write 300 words about Essay 03"
    And I press "id_submitbutton"
    Then I should see "Editing quiz: Quiz 1"
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
    Then I should see "Editing quiz: Quiz 1"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Essay 02 new" on quiz page "1"
    And I should see "Essay 03 new" on quiz page "1"
    And I should see "Essay 04 new" on quiz page "1"

    # Repaginate as two questions per page.
    And I should not see "Page 2"
    When I press "Repaginate"
    Then I should see "Repaginate with"
    And I set the field "menuquestionsperpage" to "2"
    When I click on "Go" "button" in the "Repaginate" "dialogue"
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
    When I set the field "Question name" to "Essay for page 2"
    And I set the field "Question text" to "Please write 200 words about Essay for page 2"
    And I press "id_submitbutton"
    Then I should see "Editing quiz: Quiz 1"
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
    When I am on "Course 1" course homepage
    And I navigate to "Question bank > Categories" in current page administration
    Then I should see "Add category"
    Then I set the field "Parent category" to "Default for C1"
    And I set the field "Name" to "Subcat 1"
    And I set the field "Category info" to "This is sub category 1"
    Then I press "id_submitbutton"
    And I should see "Subcat 1"

    Then I set the field "Parent category" to "Default for C1"
    And I set the field "Name" to "Subcat 2"
    And I set the field "Category info" to "This is sub category 2"
    Then I press "id_submitbutton"
    And I should see "Subcat 2"

    And I follow "Question bank"
    Then I should see "Question bank"
    And I should see "Select a category"

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
    And I should see "Select a category"
    And I set the field "Select a category:" to "Subcat 1"
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
    And I set the field "Select a category" to "Default for C1"
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
    And I click on "Essay 03" "checkbox"
    Then the "Add selected questions to the quiz" "button" should be enabled
    And I click on "Add to quiz" "link" in the "Essay 03" "table_row"
    Then I should see "Editing quiz: Quiz 1"
    And I should see "Essay 03" on quiz page "1"

    # Add Essay 01 from question bank.
    And I open the "Page 1" add to quiz menu
    And I follow "from question bank"
    And I click on "Add to quiz" "link" in the "Essay 01" "table_row"
    Then I should see "Editing quiz: Quiz 1"
    And I should see "Essay 03" on quiz page "1"
    And I should see "Essay 01" on quiz page "1"

    # Add Esay 02 from question bank.
    And I open the "Page 1" add to quiz menu
    And I follow "from question bank"
    And I should see "Select a category"
    And I set the field "Select a category" to "Subcat 1"
    And I click on "Add to quiz" "link" in the "Essay 02" "table_row"
    Then I should see "Editing quiz: Quiz 1"
    And I should see "Essay 03" on quiz page "1"
    And I should see "Essay 01" on quiz page "1"
    And I should see "Essay 02" on quiz page "1"

    # Add a random question.
    And I open the "Page 1" add to quiz menu
    And I follow "a random question"
    And I press "Add random question"
    Then I should see "Editing quiz: Quiz 1"
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
    Then I should see "Editing quiz: Quiz 1"
    And I should see "Essay 03" on quiz page "1"
    And I should see "Essay 01" on quiz page "2"
    And I should see "Essay 02" on quiz page "3"
    And I should see "Random" on quiz page "4"
    And I should see "Essay for page 4" on quiz page "4"
