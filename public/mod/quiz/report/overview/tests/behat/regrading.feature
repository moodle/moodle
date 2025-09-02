@mod @mod_quiz @quiz @quiz_overview @javascript
Feature: Regrading quiz attempts using the Grades report
    In order to be able to correct mistakes I made setting up my quiz
    As a teacher
    I need to be able to re-grade attempts after editing questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname  |
      | teacher  | Mark      | Allwright |
      | student1 | Student   | One       |
      | student2 | Student   | Two       |
      | student3 | Student   | Three     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "activities" exist:
      | activity | name                       | course | idnumber |
      | quiz     | Quiz for testing regrading | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name |
      | Test questions   | truefalse   | TF   |
      | Test questions   | shortanswer | SA   |
    And quiz "Quiz for testing regrading" contains the following questions:
      | question | page | maxmark |
      | TF       | 1    | 5.0     |
      | SA       | 1    | 5.0     |
    And user "student1" has attempted "Quiz for testing regrading" with responses:
      | slot | response |
      | 1    | True     |
      | 2    | frog     |
    And user "student2" has attempted "Quiz for testing regrading" with responses:
      | slot | response |
      | 1    | True     |
      | 2    | toad     |

  Scenario: Regrade all attempts and all questions.
    Given I am on the "Quiz for testing regrading" "quiz activity" page logged in as teacher
    And I navigate to "Results" in current page administration
    And I press "Regrade attempts..."
    And I click on "Regrade now" "button" in the "Regrade" "dialogue"
    # Note, the order is not defined, so we can only check part of the message.
    # Also, nothing has changed in the quiz, so the regrade won't alter any scores,
    # but this is still a useful test that the regrade process completes without errors.
    Then I should see "Quiz for testing regrading"
    And I should see "Finished regrading (2/2)"
    And I should see "Regrade completed"
    And I press "Continue"

    # These next tests just serve to check we got back to the report.
    And I should see "Quiz for testing regrading"
    And I should see "Overall number of students achieving grade ranges"

  Scenario: Regrade selected attempts and all questions.
    Given I am on the "Quiz for testing regrading" "quiz activity" page logged in as teacher
    And I navigate to "Results" in current page administration
    When I click on "Select attempt" "checkbox" in the "Student Two" "table_row"
    And I press "Regrade attempts..."
    And I click on "Regrade now" "button" in the "Regrade" "dialogue"
    Then I should see "Quiz for testing regrading"
    And I should see "Finished regrading (1/1)"
    And I should see "Regrade completed"
    And I press "Continue"

    # These next tests just serve to check we got back to the report.
    And I should see "Quiz for testing regrading"
    And I should see "Overall number of students achieving grade ranges"

  Scenario: Regrade all attempts and selected questions.
    Given I am on the "Quiz for testing regrading" "mod_quiz > edit" page logged in as teacher
    When I follow "Edit question SA"
    And I set the field "id_fraction_1" to "50%"
    And I press "id_submitbutton"
    And I follow "Edit question TF"
    And I set the field "Correct answer" to "False"
    And I press "id_submitbutton"
    And I follow "Attempts: 2"
    And I press "Regrade attempts..."
    And I click on "Selected questions" "radio"
    And I click on "Question 1" "checkbox"
    And I click on "Regrade now" "button" in the "Regrade" "dialogue"
    Then I should see "Quiz for testing regrading"
    And I should see "Finished regrading (2/2)"
    And I should see "Regrade completed"
    And I press "Continue"
    And "Student OneReview attempt" row "Q. 1/50.00" column of "attempts" table should contain "50.00/0.00"
    And "Student TwoReview attempt" row "Q. 1/50.00" column of "attempts" table should contain "50.00/0.00"
    And "Student OneReview attempt" row "Grade/100.00" column of "attempts" table should contain "100.00/50.00"
    And "Student TwoReview attempt" row "Grade/100.00" column of "attempts" table should contain "90.00/40.00"

  Scenario: Regrade selected attempts and selected questions.
    Given I am on the "Quiz for testing regrading" "mod_quiz > edit" page logged in as teacher
    When I follow "Edit question SA"
    And I set the field "id_fraction_1" to "50%"
    And I press "id_submitbutton"
    And I follow "Edit question TF"
    And I set the field "Correct answer" to "False"
    And I press "id_submitbutton"
    And I follow "Attempts: 2"
    And I click on "Select attempt" "checkbox" in the "Student Two" "table_row"
    And I press "Regrade attempts..."
    And the "Question 1" "checkbox" should be disabled
    And the "Question 2" "checkbox" should be disabled
    And I click on "Selected questions" "radio"
    And the "Question 1" "checkbox" should be enabled
    And the "Question 2" "checkbox" should be enabled
    And the "Regrade now" "button" should be disabled
    And the "Dry run" "button" should be disabled
    And I click on "Question 1" "checkbox"
    And the "Regrade now" "button" should be enabled
    And the "Dry run" "button" should be enabled
    And I click on "Regrade now" "button" in the "Regrade" "dialogue"
    Then I should see "Quiz for testing regrading"
    And I should see "Finished regrading (1/1)"
    And I should see "Regrade completed"
    And I press "Continue"
    And "Student TwoReview attempt" row "Q. 1/50.00" column of "attempts" table should contain "50.00/0.00"
    And "Student TwoReview attempt" row "Grade/100.00" column of "attempts" table should contain "90.00/40.00"

  Scenario: Dry-run all attempts, then regrade all attempts.
    Given I am on the "Quiz for testing regrading" "mod_quiz > edit" page logged in as teacher
    And I follow "Edit question SA"
    And I set the field "id_fraction_1" to "50%"
    And I press "id_submitbutton"
    And I set the field "version" in the "TF" "list_item" to "v1"
    And I set the field "version" in the "SA" "list_item" to "v2 (latest)"
    And I follow "Attempts: 2"
    And I press "Regrade attempts..."
    And I click on "Dry run" "button" in the "Regrade" "dialogue"
    # Note, the order is not defined, so we can only check part of the message.
    Then I should see "Quiz for testing regrading"
    And I should see "Finished regrading (2/2)"
    And I should see "Regrade completed"
    And I press "Continue"
    And "Student One" row "Regrade" column of "attempts" table should not contain "Needed"
    And "Student TwoReview attempt" row "Regrade" column of "attempts" table should contain "Needed"
    # In the following, the first number is strike-through, and the second is not, but Behat can't see that.
    # At this point, it is showing what would change.
    And "Student TwoReview attempt" row "Q. 2/50.00" column of "attempts" table should contain "40.00/25.00"
    And "Student TwoReview attempt" row "Grade/100.00" column of "attempts" table should contain "90.00/75.00"
    And I press "Regrade attempts..."
    And I click on "Regrade now" "button" in the "Regrade" "dialogue"
    And I should see "Quiz for testing regrading"
    And I should see "Finished regrading (2/2)"
    And I should see "Regrade completed"
    And I press "Continue"
    # These next tests just serve to check we got back to the report.
    And I should see "Quiz for testing regrading"
    And I should see "Overall number of students achieving grade ranges"
    # Now, both old-score strike-through and new score plain, are still shown, but now it indicates what did change.
    And "Student TwoReview attempt" row "Q. 2/50.00" column of "attempts" table should contain "40.00/25.00"
    And "Student TwoReview attempt" row "Grade/100.00" column of "attempts" table should contain "90.00/75.00"

  Scenario: Dry-run a full regrade, then regrade the commit regrade.
    Given I am on the "Quiz for testing regrading" "mod_quiz > edit" page logged in as teacher
    When I follow "Edit question SA"
    And I set the field "id_fraction_1" to "50%"
    And I press "id_submitbutton"
    And I set the field "version" in the "TF" "list_item" to "v1"
    And I set the field "version" in the "SA" "list_item" to "v2 (latest)"
    And I follow "Attempts: 2"
    And I press "Regrade attempts..."
    And I click on "Dry run" "button" in the "Regrade" "dialogue"
    # Note, the order is not defined, so we can only check part of the message.
    And I should see "Quiz for testing regrading"
    And I should see "Finished regrading (2/2)"
    And I should see "Regrade completed"
    And I press "Continue"
    And "Student One" row "Regrade" column of "attempts" table should not contain "Needed"
    And "Student TwoReview attempt" row "Regrade" column of "attempts" table should contain "Needed"
    # In the following, the first number is strike-through, and the second is not, but Behat can't see that.
    # At this point, it is showing what would change.
    And "Student TwoReview attempt" row "Q. 2/50.00" column of "attempts" table should contain "40.00/25.00"
    And "Student TwoReview attempt" row "Grade/100.00" column of "attempts" table should contain "90.00/75.00"
    Then I should see "The last dry run of a regrade found that the regrade would change the marks for 1 questions in 1 attempts."
    And I press "Commit regrade"
    And I should see "Quiz for testing regrading"
    And I should see "Finished regrading (1/1)"
    And I should see "Regrade completed"
    And I press "Continue"

    # These next tests just serve to check we got back to the report.
    And I should see "Quiz for testing regrading"
    And I should see "Overall number of students achieving grade ranges"
    # Now, both old-score strike-through and new score plain, are still shown, but now it indicates what did change.
    And "Student TwoReview attempt" row "Q. 2/50.00" column of "attempts" table should contain "40.00/25.00"
    And "Student TwoReview attempt" row "Grade/100.00" column of "attempts" table should contain "90.00/75.00"
    And I should not see "The last dry run of a regrade found that the regrade would change the marks for 1 questions in 1 attempts."

  Scenario: Regrade all attempts works against quiz selected question version
    Given I am on the "Quiz for testing regrading" "quiz activity" page logged in as teacher
    And I navigate to "Results" in current page administration
    And I press "Regrade attempts..."
    And I click on "Dry run" "button" in the "Regrade" "dialogue"
    Then I should see "Quiz for testing regrading"
    And I should see "Finished regrading (2/2)"
    And I should see "Regrade completed"
    And I press "Continue"
    And I should see "Quiz for testing regrading"
    And I should see "Overall number of students achieving grade ranges"
    And "Student One" row "Regrade" column of "attempts" table should not contain "Needed"
    And I am on the "Quiz for testing regrading" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions"
    And I choose "Edit question" action for "TF" in the question bank
    And I set the field "Correct answer" to "False"
    And I press "id_submitbutton"
    And I am on the "Quiz for testing regrading" "mod_quiz > edit" page
    And I set the field "version" in the "TF" "list_item" to "v2 (latest)"
    And I navigate to "Results" in current page administration
    And I press "Regrade attempts..."
    And I click on "Dry run" "button" in the "Regrade" "dialogue"
    And I should see "Regrade completed"
    And I press "Continue"
    And "student1@example.com" row "Regrade" column of "attempts" table should contain "Needed"
    And "Correct" "icon" should appear before "50.00/0.00" "text"
    And I press "Regrade attempts..."
    And I click on "Regrade now" "button" in the "Regrade" "dialogue"
    And I should see "Regrade completed"
    And I press "Continue"
    Then "student1@example.com" row "Regrade" column of "attempts" table should contain "Done"
    And "Student OneReview attempt" row "Q. 1/50.00" column of "attempts" table should contain "50.00/0.00"
    And "Incorrect" "icon" should appear before "50.00/0.00" "text"

  Scenario: Regrade all attempts works against quiz selected latest question version
    Given I am on the "Quiz for testing regrading" "quiz activity" page logged in as teacher
    And I navigate to "Results" in current page administration
    And I click on "mod-quiz-report-overview-report-selectall-attempts" "checkbox"
    And I click on "Delete selected attempts" "button"
    And I click on "Yes" "button"
    And I am on the "Quiz for testing regrading" "mod_quiz > edit" page
    And I should see "(latest)" in the "TF" "list_item"
    # Create multiple question versions.
    And I am on the "Quiz for testing regrading" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions"
    And I choose "Edit question" action for "TF" in the question bank
    And I set the field "Correct answer" to "True"
    And I press "id_submitbutton"
    And I choose "Edit question" action for "TF" in the question bank
    And I set the field "Question name" to "New version of TF"
    And I set the field "Correct answer" to "False"
    And I press "id_submitbutton"
    And I am on the "Quiz for testing regrading" "mod_quiz > edit" page
    And I should see "(latest)" in the "TF" "list_item"
    And I click on "version" "select" in the "TF" "list_item"
    And I should see "v1"
    And I should see "v2"
    And I should see "v3 (latest)"
    # Set version that is going to be attempted to an older one.
    And I set the field "version" in the "TF" "list_item" to "v1"
    And user "student3" has attempted "Quiz for testing regrading" with responses:
      | slot | response |
      | 1    | True     |
      | 2    | toad     |
    And I am on the "Quiz for testing regrading" "mod_quiz > edit" page
    And I set the field "version" in the "TF" "list_item" to "Always latest"
    And I navigate to "Results" in current page administration
    And I press "Regrade attempts..."
    And I click on "Regrade now" "button" in the "Regrade" "dialogue"
    And I should see "Finished regrading (1/1)"
    And I should see "Regrade completed"
    And I press "Continue"
    Then "student3@example.com" row "Q. 1/50.00" column of "attempts" table should contain "50.00/0.00"
    And "Incorrect" "icon" should appear before "50.00/0.00" "text"

  Scenario: Regrade attempts should always regrade against latest random question version
    Given I am on the "Quiz for testing regrading" "quiz activity" page logged in as teacher
    And I navigate to "Results" in current page administration
    And I click on "mod-quiz-report-overview-report-selectall-attempts" "checkbox"
    And I click on "Delete selected attempts" "button"
    And I click on "Yes" "button"
    # Create multiple question versions.
    And I am on the "Quiz for testing regrading" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions"
    And I choose "Delete" action for "SA" in the question bank
    And I press "Delete"
    And I am on the "Quiz for testing regrading" "mod_quiz > edit" page
    And I click on "Delete" "link" in the "TF" "list_item"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I click on "Delete" "link" in the "SA" "list_item"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I click on "Add" "link"
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Test questions"
    And I press "Add random question"
    And I am on the "Quiz for testing regrading" "quiz activity" page logged in as student3
    And I click on "Attempt quiz" "button"
    And I should see "The answer is true."
    And I set the field "True" to "1"
    And I click on "Finish attempt ..." "button"
    And I press "Submit all and finish"
    And I click on "Submit" "button" in the "Submit all your answers and finish?" "dialogue"
    And I am on the "Quiz for testing regrading" "mod_quiz > question bank" page logged in as teacher
    And I apply question bank filter "Category" with value "Test questions"
    And I choose "Edit question" action for "TF" in the question bank
    And I set the field "Correct answer" to "False"
    And I press "id_submitbutton"
    And I navigate to "Results" in current page administration
    And "student3@example.com" row "Q. 1/100.00" column of "attempts" table should contain "100.00"
    And "Correct" "icon" should be visible
    And I press "Regrade attempts..."
    And I click on "Regrade now" "button" in the "Regrade" "dialogue"
    And I should see "Finished regrading (1/1)"
    And I should see "Regrade completed"
    And I press "Continue"
    Then "student3@example.com" row "Q. 1/100.00" column of "attempts" table should contain "100.00/0.00"
    And "Incorrect" "icon" should be visible
