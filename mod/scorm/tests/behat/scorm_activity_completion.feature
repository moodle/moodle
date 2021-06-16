@mod @mod_scorm @core_completion @_file_upload @_switch_iframe
Feature: View activity completion in the SCORM activity
  In order to have visibility of scorm completion requirements
  As a student
  I need to be able to view my scorm completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
      | Show activity completion conditions | Yes |
    And I press "Save and display"
    And I turn editing mode on
    And I add a "SCORM package" to section "1"
    And I set the following fields to these values:
      | Name                        | Music history                                     |
      | Number of attempts          | 1 attempt                                         |
      | Completion tracking         | Show activity as complete when conditions are met |
      | Require view                | 1                                                 |
      | Require grade               | 1                                                 |
      | completionscoredisabled     | 0                                                 |
      | completionscorerequired     | 3                                                 |
      | completionstatusrequired[2] | 1                                                 |
      | completionstatusrequired[4] | 1                                                 |
      | completionstatusallscos     | 1                                                 |
    And I upload "mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12-mini.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    And I log out

  @javascript
  Scenario: View automatic completion items as a teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I follow "Music history"
    Then "Music history" should have the "View" completion condition
    And "Music history" should have the "Receive a score of 3 or more" completion condition
    And "Music history" should have the "Do all parts of this activity" completion condition
    And "Music history" should have the "Receive a grade" completion condition
    And "Music history" should have the "Complete and pass the activity" completion condition

  @javascript
  Scenario: View automatic completion items as a student
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    And the "View" completion condition of "Music history" is displayed as "todo"
    And the "Receive a score of 3 or more" completion condition of "Music history" is displayed as "todo"
    And the "Do all parts of this activity" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Complete and pass the activity" completion condition of "Music history" is displayed as "todo"
    And I press "Enter"
    And I switch to the main frame
    And I click on "Par?" "list_item"
    And I wait until the page is ready
    And I click on "Keeping Score" "list_item"
    And I wait until the page is ready
    And I click on "Other Scoring Systems" "list_item"
    And I wait until the page is ready
    And I click on "The Rules of Golf" "list_item"
    And I wait until the page is ready
    And I click on "Playing Golf Quiz" "list_item"
    And I switch to "scorm_object" iframe
    And I click on "[id='question_com.scorm.golfsamples.interactions.playing_1_1']" "css_element"
    And I press "Submit Answers"
    And I switch to the main frame
    And I click on "How to Have Fun Playing Golf" "list_item"
    And I wait until the page is ready
    And I click on "How to Make Friends Playing Golf" "list_item"
    And I wait until the page is ready
    And I click on "Having Fun Quiz" "list_item"
    And I switch to "scorm_object" iframe
    And I click on "[id='question_com.scorm.golfsamples.interactions.fun_1_False']" "css_element"
    And I press "Submit Answers"
    And I switch to the main frame
    And I follow "Exit activity"
    And I follow "Music history"
    And the "View" completion condition of "Music history" is displayed as "done"
    # Conditions that are not possible to achieve (eg score below requirement but all attempts used) are marked as failed.
    And the "Receive a score of 3 or more" completion condition of "Music history" is displayed as "failed"
    And the "Do all parts of this activity" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "Complete and pass the activity" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: Use manual completion
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Completion tracking" to "Students can manually mark the activity as completed"
    And I press "Save and display"
    # Teacher view.
    And the manual completion button for "Music history" should be disabled
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
