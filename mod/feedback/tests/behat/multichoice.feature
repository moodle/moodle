@mod @mod_feedback
Feature: Testing multichoice questions in feedback
  In order to create feedbacks
  As a teacher
  I need to be able to create different types of multichoice questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity   | name                | course | idnumber    |
      | feedback   | Learning experience | C1     | feedback0   |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"

  @javascript
  Scenario: Non-rated single-answer multiple choice questions in feedback
    When I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 1 |
      | Label            | multichoice1                |
      | Multiple choice type | Multiple choice - single answer |
      | Multiple choice values | option a\noption b\noption c  |
    And I add a "Label" question to the feedback with:
      | Contents | this is the first page of the feedback |
    And I select "Add a page break" from the "Add question" singleselect
    And I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 2 |
      | Label            | multichoice2                |
      | Multiple choice type | Multiple choice - single answer |
      | Multiple choice values | option d\noption e\noption f  |
      | Required | 1 |
    And I add a "Label" question to the feedback with:
      | Contents | this is the second page of the feedback |
    And I select "Add a page break" from the "Add question" singleselect
    And I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 3 |
      | Label            | multichoice3                |
      | Multiple choice type | Multiple choice - single answer |
      | Multiple choice values | option g\noption h\noption i  |
      | Hide the "Not selected" option | Yes                   |
      | Dependence item                | multichoice2          |
      | Dependence value               | option d              |
    And I add a "Label" question to the feedback with:
      | Contents | this is the third page of the feedback |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I follow "Answer the questions..."
    # Examine the first page, select nothing, go to the next page
    Then the following fields match these values:
      | Not selected | 1 |
      | option a     | 0 |
      | option b     | 0 |
      | option c     | 0 |
    And "Previous page" "button" should not exist
    And "Submit your answers" "button" should not exist
    And I should see "this is the first page of the feedback"
    And I should not see "this is the second page of the feedback"
    And I should not see "this is the third page of the feedback"
    And I press "Next page"
    # Examine the second page, select nothing, try to go to the next page
    And I should see "Not selected"
    And the following fields match these values:
      | Not selected | 1 |
      | option d     | 0 |
      | option e     | 0 |
      | option f     | 0 |
    And "Previous page" "button" should exist
    And "Submit your answers" "button" should not exist
    And I should not see "this is the first page of the feedback"
    And I should see "this is the second page of the feedback"
    And I should not see "this is the third page of the feedback"
    And I press "Next page"
    # We are still on the second page because the field is required
    And I should see "Required" in the "form" "css_element"
    And I should see "this is the second page of the feedback"
    And I set the field "option e" to "1"
    And the following fields match these values:
      | Not selected | 0 |
      | option d     | 0 |
      | option e     | 1 |
      | option f     | 0 |
    And I press "Next page"
    # Now we are on the third page, element is not shown because of wrong dependency.
    And I should see "this is the third page of the feedback"
    And I should not see "this is a multiple choice 3"
    # Go back, check that values are preserved and change the option to enable dependency.
    And I press "Previous page"
    And the following fields match these values:
      | Not selected | 0 |
      | option d     | 0 |
      | option e     | 1 |
      | option f     | 0 |
    And I set the field "option d" to "1"
    And I press "Next page"
    # Now third page contains all items.
    And I should see "this is a multiple choice 3"
    And I should see "this is the third page of the feedback"
    And I should not see "Not selected"
    And the following fields match these values:
      | option g     | 0 |
      | option h     | 0 |
      | option i     | 0 |
    And "Previous page" "button" should exist
    And "Next page" "button" should not exist
    And "Submit your answers" "button" should exist
    And I set the field "option i" to "1"
    And I press "Submit your answers"
    And I log out
    # Student 2 tries to trick - he answers the third question and then
    # goes back and changes dependency question. Analysis should not show this answer!
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I follow "Answer the questions..."
    And I set the field "option a" to "1"
    And I press "Next page"
    And I set the field "option d" to "1"
    And I press "Next page"
    And I set the field "option g" to "1"
    And I press "Previous page"
    And I set the field "option f" to "1"
    And I press "Next page"
    And I press "Submit your answers"
    And I log out
    # Login as teacher and check analysis
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I navigate to "Analysis" in current page administration
    And I should see "Submitted answers: 2"
    And I should see "Questions: 3"
    And I show chart data for the "multichoice1" feedback
    And I should see "1 (50.00 %)" in the "option a" "table_row"
    And I should not see "%" in the "option b" "table_row"
    And I should not see "%" in the "option c" "table_row"
    And I show chart data for the "multichoice2" feedback
    And I should see "1 (50.00 %)" in the "option d" "table_row"
    And I should not see "%" in the "option e" "table_row"
    And I should see "1 (50.00 %)" in the "option f" "table_row"
    And I show chart data for the "multichoice3" feedback
    And I should not see "%" in the "option g" "table_row"
    And I should not see "%" in the "option h" "table_row"
    And I should see "1 (100.00 %)" in the "option i" "table_row"
    # Change the settings so we don't analyse empty submits
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I open the action menu in "//div[contains(@class, 'feedback_itemlist') and contains(.,'multichoice1')]" "xpath_element"
    And I choose "Edit question" in the open action menu
    And I set the field "Do not analyse empty submits" to "Yes"
    And I press "Save changes to question"
    And I follow "Analysis"
    And I show chart data for the "multichoice1" feedback
    And I should see "1 (100.00 %)" in the "option a" "table_row"
    And I should not see "%" in the "option b" "table_row"
    And I should not see "%" in the "option c" "table_row"
    And I log out

  @javascript
  Scenario: Non-rated multiple-answers multiple choice questions in feedback
    # Create a feedback with three pages, required and dependent questions.
    When I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 1 |
      | Label            | multichoice1                |
      | Multiple choice type | Multiple choice - multiple answers |
      | Multiple choice values | option a\noption b\noption c  |
    And I add a "Label" question to the feedback with:
      | Contents | this is the first page of the feedback |
    And I select "Add a page break" from the "Add question" singleselect
    And I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 2 |
      | Label            | multichoice2                |
      | Multiple choice type | Multiple choice - multiple answers |
      | Multiple choice values | option d\noption e\noption f  |
      | Required | 1 |
    And I add a "Label" question to the feedback with:
      | Contents | this is the second page of the feedback |
    And I select "Add a page break" from the "Add question" singleselect
    And I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 3 |
      | Label            | multichoice3                |
      | Multiple choice type | Multiple choice - multiple answers |
      | Multiple choice values | option g\noption h\noption i  |
      | Dependence item                | multichoice2          |
      | Dependence value               | option d              |
    And I add a "Label" question to the feedback with:
      | Contents | this is the third page of the feedback |
    And I log out
    # Login as the first student.
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I follow "Answer the questions..."
    # Examine the first page, select nothing, go to the next page
    And I should not see "Not selected"
    And the following fields match these values:
      | option a     | 0 |
      | option b     | 0 |
      | option c     | 0 |
    And "Previous page" "button" should not exist
    And "Submit your answers" "button" should not exist
    And I should see "this is the first page of the feedback"
    And I should not see "this is the second page of the feedback"
    And I should not see "this is the third page of the feedback"
    And I press "Next page"
    # Examine the second page, select nothing, try to go to the next page
    Then I should not see "Not selected"
    And the following fields match these values:
      | option d     | 0 |
      | option e     | 0 |
      | option f     | 0 |
    And "Previous page" "button" should exist
    And "Submit your answers" "button" should not exist
    And I should not see "this is the first page of the feedback"
    And I should see "this is the second page of the feedback"
    And I should not see "this is the third page of the feedback"
    And I press "Next page"
    # We are still on the second page because the field is required
    And I should see "Required" in the "form" "css_element"
    And I should see "this is the second page of the feedback"
    And I set the field "option e" to "1"
    And I set the field "option f" to "1"
    And the following fields match these values:
      | option d     | 0 |
      | option e     | 1 |
      | option f     | 1 |
    And I press "Next page"
    # Now we are on the third page, element is not shown because of wrong dependency.
    And I should see "this is the third page of the feedback"
    And I should not see "this is a multiple choice 3"
    # Go back, check that values are preserved and change the option to enable dependency.
    And I press "Previous page"
    And the following fields match these values:
      | option d     | 0 |
      | option e     | 1 |
      | option f     | 1 |
    And I set the field "option d" to "1"
    And I set the field "option e" to "0"
    And I press "Next page"
    # Now third page contains all items.
    And I should see "this is a multiple choice 3"
    And I should see "this is the third page of the feedback"
    And the following fields match these values:
      | option g     | 0 |
      | option h     | 0 |
      | option i     | 0 |
    And "Previous page" "button" should exist
    And "Next page" "button" should not exist
    And "Submit your answers" "button" should exist
    And I set the field "option i" to "1"
    And I press "Submit your answers"
    And I log out
    # Student 2 tries to trick - he answers the third question and then
    # goes back and changes dependency question. Analysis should not show this answer!
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I follow "Answer the questions..."
    And I set the field "option a" to "1"
    And I set the field "option b" to "1"
    And I press "Next page"
    And I set the field "option d" to "1"
    And I press "Next page"
    And I set the field "option g" to "1"
    And I press "Previous page"
    And I set the field "option d" to "0"
    And I set the field "option f" to "1"
    And I press "Next page"
    And I press "Submit your answers"
    And I log out
    # Login as teacher and check analysis
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I navigate to "Analysis" in current page administration
    And I should see "Submitted answers: 2"
    And I should see "Questions: 3"
    And I show chart data for the "multichoice1" feedback
    And I should see "1 (50.00 %)" in the "option a" "table_row"
    And I should see "1 (50.00 %)" in the "option b" "table_row"
    And I should not see "%" in the "option c" "table_row"
    And I show chart data for the "multichoice2" feedback
    And I should see "1 (50.00 %)" in the "option d" "table_row"
    And I should not see "%" in the "option e" "table_row"
    And I should see "2 (100.00 %)" in the "option f" "table_row"
    And I show chart data for the "multichoice3" feedback
    And I should not see "%" in the "option g" "table_row"
    And I should not see "%" in the "option h" "table_row"
    And I should see "1 (100.00 %)" in the "option i" "table_row"
    # Change the settings so we don't analyse empty submits
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I open the action menu in "//div[contains(@class, 'feedback_itemlist') and contains(.,'multichoice1')]" "xpath_element"
    And I choose "Edit question" in the open action menu
    And I set the field "Do not analyse empty submits" to "Yes"
    And I press "Save changes to question"
    And I follow "Analysis"
    And I show chart data for the "multichoice1" feedback
    And I should see "1 (100.00 %)" in the "option a" "table_row"
    And I should see "1 (100.00 %)" in the "option b" "table_row"
    And I should not see "%" in the "option c" "table_row"
    And I log out

  @javascript
  Scenario: Non-rated single-answer dropdown multiple choice questions in feedback
    When I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 1 |
      | Label            | multichoice1                |
      | Multiple choice type | Multiple choice - single answer allowed (dropdownlist) |
      | Multiple choice values | option a\noption b\noption c  |
    And I add a "Label" question to the feedback with:
      | Contents | this is the first page of the feedback |
    And I select "Add a page break" from the "Add question" singleselect
    And I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 2 |
      | Label            | multichoice2                |
      | Multiple choice type | Multiple choice - single answer allowed (dropdownlist) |
      | Multiple choice values | option d\noption e\noption f  |
      | Required | 1 |
    And I add a "Label" question to the feedback with:
      | Contents | this is the second page of the feedback |
    And I select "Add a page break" from the "Add question" singleselect
    And I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 3 |
      | Label            | multichoice3                |
      | Multiple choice type | Multiple choice - single answer allowed (dropdownlist) |
      | Multiple choice values | option g\noption h\noption i  |
      | Dependence item                | multichoice2          |
      | Dependence value               | option d              |
    And I add a "Label" question to the feedback with:
      | Contents | this is the third page of the feedback |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I follow "Answer the questions..."
    # Examine the first page, select nothing, go to the next page
    Then the following fields match these values:
      | this is a multiple choice 1 | 0 |
    And "Previous page" "button" should not exist
    And "Submit your answers" "button" should not exist
    And I should see "this is the first page of the feedback"
    And I should not see "this is the second page of the feedback"
    And I should not see "this is the third page of the feedback"
    And I press "Next page"
    # Examine the second page, select nothing, try to go to the next page
    And the following fields match these values:
      | this is a multiple choice 2 | 0 |
    And "Previous page" "button" should exist
    And "Submit your answers" "button" should not exist
    And I should not see "this is the first page of the feedback"
    And I should see "this is the second page of the feedback"
    And I should not see "this is the third page of the feedback"
    And I press "Next page"
    # We are still on the second page because the field is required
    And I should see "Required" in the "form" "css_element"
    And I should see "this is the second page of the feedback"
    And I set the field "this is a multiple choice 2" to "option e"
    And I press "Next page"
    # Now we are on the third page, element is not shown because of wrong dependency.
    And I should see "this is the third page of the feedback"
    And I should not see "this is a multiple choice 3"
    # Go back, check that values are preserved and change the option to enable dependency.
    And I press "Previous page"
    And the following fields match these values:
      | this is a multiple choice 2 | option e |
    And I set the field "this is a multiple choice 2" to "option d"
    And I press "Next page"
    # Now third page contains all items.
    And I should see "this is the third page of the feedback"
    And the following fields match these values:
      | this is a multiple choice 3 | 0 |
    And "Previous page" "button" should exist
    And "Next page" "button" should not exist
    And "Submit your answers" "button" should exist
    And I set the field "this is a multiple choice 3" to "option i"
    And I press "Submit your answers"
    And I log out
    # Student 2 tries to trick - he answers the third question and then
    # goes back and changes dependency question. Analysis should not show this answer!
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I follow "Answer the questions..."
    And I set the field "this is a multiple choice 1" to "option a"
    And I press "Next page"
    And I set the field "this is a multiple choice 2" to "option d"
    And I press "Next page"
    And I set the field "this is a multiple choice 3" to "option g"
    And I press "Previous page"
    And I set the field "this is a multiple choice 2" to "option f"
    And I press "Next page"
    And I press "Submit your answers"
    And I log out
    # Login as teacher and check analysis
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Learning experience"
    And I navigate to "Analysis" in current page administration
    And I should see "Submitted answers: 2"
    And I should see "Questions: 3"
    And I show chart data for the "multichoice1" feedback
    And I should see "1 (50.00 %)" in the "option a" "table_row"
    And I should not see "%" in the "option b" "table_row"
    And I should not see "%" in the "option c" "table_row"
    And I show chart data for the "multichoice2" feedback
    And I should see "1 (50.00 %)" in the "option d" "table_row"
    And I should not see "%" in the "option e" "table_row"
    And I should see "1 (50.00 %)" in the "option f" "table_row"
    And I show chart data for the "multichoice3" feedback
    And I should not see "%" in the "option g" "table_row"
    And I should not see "%" in the "option h" "table_row"
    And I should see "1 (100.00 %)" in the "option i" "table_row"
    # Change the settings so we don't analyse empty submits
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I open the action menu in "//div[contains(@class, 'feedback_itemlist') and contains(.,'multichoice1')]" "xpath_element"
    And I choose "Edit question" in the open action menu
    And I set the field "Do not analyse empty submits" to "Yes"
    And I press "Save changes to question"
    And I follow "Analysis"
    And I show chart data for the "multichoice1" feedback
    And I should see "1 (100.00 %)" in the "option a" "table_row"
    And I should not see "%" in the "option b" "table_row"
    And I should not see "%" in the "option c" "table_row"
    And I log out
