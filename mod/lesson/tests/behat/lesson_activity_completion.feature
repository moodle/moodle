@mod @mod_lesson @core_completion
Feature: View activity completion in the lesson activity
  In order to have visibility of lesson completion requirements
  As a student
  I need to be able to view my lesson completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | teacher1 | C1 | editingteacher |
    And the following "activity" exists:
      | activity                   | lesson        |
      | course                     | C1            |
      | idnumber                   | mh1           |
      | name                       | Music history |
      | section                    | 1             |
      | completion                 | 2             |
      | completionview             | 1             |
      | completionusegrade         | 1             |
      | completionendreached       | 1             |
      | completiontimespentenabled | 1             |
      | completiontimespent        | 1             |
    And I am on the "Music history" "lesson activity" page logged in as teacher1
    And I follow "Add a content page"
    And I set the following fields to these values:
    | Page title  | Music history part 1        |
    | Description | The history of music part 1 |
    | Jump        | Next page                   |
    And I click on "Save page" "button"
    And I select "Add a question page" from the "qtype" singleselect
    And I set the field "Select a question type" to "Essay"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title    | Music essay                            |
      | Page contents | Write a really interesting music essay |
      | Jump          | End of lesson                          |
      | Score         | 1                                      |
    And I press "Save page"
    And I log out

  Scenario: View automatic completion items as a teacher
    When I am on the "Music history" "lesson activity" page logged in as teacher1
    Then "Music history" should have the "View" completion condition
    And "Music history" should have the "Spend at least 1 sec on this activity" completion condition
    And "Music history" should have the "Go through the activity to the end" completion condition
    And "Music history" should have the "Receive a grade" completion condition

  Scenario: View automatic completion items as a student
    Given I am on the "Music history" "lesson activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Spend at least 1 sec on this activity" completion condition of "Music history" is displayed as "todo"
    And the "Go through the activity to the end" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    When I am on the "Music history" "lesson activity" page
    And I wait "2" seconds
    And I reload the page
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Spend at least 1 sec on this activity" completion condition of "Music history" is displayed as "done"
    And the "Go through the activity to the end" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And I press "The history of music part 1"
    And I set the field "Your answer" to "Some drummers play with their sticks flipped around"
    And I press "Submit"
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Spend at least 1 sec on this activity" completion condition of "Music history" is displayed as "done"
    And the "Go through the activity to the end" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: Use manual completion
    Given I am on the "Music history" "lesson activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Completion tracking" to "Students can manually mark the activity as completed"
    And I press "Save and display"
    # Teacher view.
    And the manual completion button for "Music history" should be disabled
    And I log out
    # Student view.
    When I am on the "Music history" "lesson activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
