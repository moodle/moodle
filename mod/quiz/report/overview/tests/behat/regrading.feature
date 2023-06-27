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
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name                       | course | idnumber |
      | quiz       | Quiz for testing regrading | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name  |
      | Test questions   | truefalse   | TF    |
      | Test questions   | shortanswer | SA    |
    And quiz "Quiz for testing regrading" contains the following questions:
      | question | page | maxmark |
      | TF       | 1    | 5.0     |
      | SA       | 1    | 5.0     |
    And user "student1" has attempted "Quiz for testing regrading" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | frog     |
    And user "student2" has attempted "Quiz for testing regrading" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | toad     |

  Scenario: Regrade all attempts
    Given I am on the "Quiz for testing regrading" "quiz activity" page logged in as teacher
    And I navigate to "Results > Grades" in current page administration
    When I press "Regrade all"

    # Note, the order is not defined, so we can only check part of the message.
    # Also, nothing has changed in the quiz, so the regrade won't alter any scores,
    # but this is still a useful test that the regrade process completes without errors.
    Then I should see "Quiz for testing regrading"
    And I should see "Successfully regraded (2/2)"
    And I should see "Regrade completed successfully"
    And I press "Continue"

    # These next tests just serve to check we got back to the report.
    And I should see "Quiz for testing regrading"
    And I should see "Overall number of students achieving grade ranges"

  Scenario: Regrade selected attempts
    Given I am on the "Quiz for testing regrading" "quiz activity" page logged in as teacher
    And I navigate to "Results > Grades" in current page administration
    When I click on "Select attempt" "checkbox" in the "Student Two" "table_row"

    And I press "Regrade selected attempts"
    Then I should see "Quiz for testing regrading"
    And I should see "Successfully regraded (1/1)"
    And I should see "Regrade completed successfully"
    And I press "Continue"

    # These next tests just serve to check we got back to the report.
    And I should see "Quiz for testing regrading"
    And I should see "Overall number of students achieving grade ranges"

  Scenario: Dry-run a full regrade, then regrade the attempts that will need it.
    Given I am on the "Quiz for testing regrading" "quiz activity" page logged in as teacher
    When I navigate to "Edit quiz" in current page administration
    And I follow "Edit question SA"
    And I set the field "id_fraction_1" to "50%"
    And I press "id_submitbutton"
    And I follow "Attempts: 2"
    And I press "Dry run a full regrade"

    # Note, the order is not defined, so we can only check part of the message.
    Then I should see "Quiz for testing regrading"
    And I should see "Successfully regraded (2/2)"
    And I should see "Regrade completed successfully"
    And I press "Continue"

    And "Student One" row "Regrade" column of "attempts" table should not contain "Needed"
    And "Student TwoReview attempt" row "Regrade" column of "attempts" table should contain "Needed"
    # In the following, the first number is strike-through, and the second is not, but Behat can't see that.
    # At this point, it is showing what would change.
    And "Student TwoReview attempt" row "Q. 2/50.00Sort by Q. 2/50.00 Ascending" column of "attempts" table should contain "40.00/25.00"
    And "Student TwoReview attempt" row "Grade/100.00Sort by Grade/100.00 Ascending" column of "attempts" table should contain "90.00/75.00"
    And I press "Regrade attempts marked as needing regrading (1)"
    And I should see "Quiz for testing regrading"
    And I should see "Successfully regraded (1/1)"
    And I should see "Regrade completed successfully"
    And I press "Continue"

    # These next tests just serve to check we got back to the report.
    And I should see "Quiz for testing regrading"
    And I should see "Overall number of students achieving grade ranges"
    # Now, both old-score strike-through and new score plain, are still shown, but now it indicates what did change.
    And "Student TwoReview attempt" row "Q. 2/50.00Sort by Q. 2/50.00 Ascending" column of "attempts" table should contain "40.00/25.00"
    And "Student TwoReview attempt" row "Grade/100.00Sort by Grade/100.00 Ascending" column of "attempts" table should contain "90.00/75.00"
    And "Regrade attempts marked as needing regrading" "button" should not exist
