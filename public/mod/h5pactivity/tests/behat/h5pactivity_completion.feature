@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe @javascript @core_completion
Feature: View activity completion information in the h5p activity
  In order to have visibility of h5p completion requirements
  As a student
  I need to be able to view my h5p completion progress

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
      | activity           | h5pactivity                          |
      | course             | C1                                   |
      | name               | Music history                        |
      | completion         | 2                                    |
      | completionview     | 1                                    |
      | completionusegrade | 1                                    |
      | packagefilepath    | h5p/tests/fixtures/filltheblanks.h5p |

  Scenario: A student can manually mark the h5p activity as done but a teacher cannot
    Given I am on the "Music history" "h5pactivity activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Students must manually mark the activity as done" to "1"
    And I press "Save and display"
    # Teacher view.
    And I am on the "Music history" "h5pactivity activity" page
    And the manual completion button for "Music history" should be disabled
    # Student view.
    When I am on the "Music history" "h5pactivity activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
