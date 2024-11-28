@mod @mod_forum @core_completion
Feature: View activity completion in the forum activity
  In order to have visibility of forum completion requirements
  As a student
  I need to be able to view my forum completion progress

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
      | activity | forum         |
      | course   | C1            |
      | idnumber | mh1           |
      | name     | Music history |
    And I am on the "Music history" "forum activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | Whole forum grading > Type   | Point        |
      | Add requirements             | 1            |
      | View the activity            | 1            |
      | Receive a grade              | 1            |
      | completiongradeitemnumber    | Whole forum |
      | Any grade                    | 1            |
      | completionpostsenabled       | 1            |
      | completionposts              | 2            |
      | completiondiscussionsenabled | 1            |
      | completiondiscussions        | 1            |
      | completionrepliesenabled     | 1            |
      | completionreplies            | 1            |
    And I press "Save and display"

  @javascript
  Scenario: Forum module displays automatic completion conditions to teachers
    When I am on the "Music history" "forum activity" page logged in as teacher1
    Then "Music history" should have the "View" completion condition
    And "Music history" should have the "Start discussions: 1" completion condition
    And "Music history" should have the "Make forum posts: 2" completion condition
    And "Music history" should have the "Post replies: 1" completion condition
    And "Music history" should have the "Receive a grade" completion condition

  @javascript
  Scenario: A student can complete a forum activity by meeting the completion conditions
    Given I am on the "Music history" "forum activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Start discussions: 1" completion condition of "Music history" is displayed as "todo"
    And the "Make forum posts: 2" completion condition of "Music history" is displayed as "todo"
    And the "Post replies: 1" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And I add a new discussion to "Music history" forum with:
       | Subject | Fun instruments |
       | Message | I like drums    |
    And I am on the "Music history" "forum activity" page
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Start discussions: 1" completion condition of "Music history" is displayed as "done"
    And the "Make forum posts: 2" completion condition of "Music history" is displayed as "todo"
    And the "Post replies: 1" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And I reply "Fun instruments" post from "Music history" forum with:
      | Subject | Reply 1 to Fun instruments |
      | Message | Guitar is also Fun         |
    And I am on the "Music history" "forum activity" page
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Start discussions: 1" completion condition of "Music history" is displayed as "done"
    And the "Make forum posts: 2" completion condition of "Music history" is displayed as "done"
    And the "Post replies: 1" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    # Grade the student
    And I am on the "Music history" "forum activity" page logged in as teacher1
    And I press "Grade users"
    And I set the field "grade" to "33"
    And I press "Save"
    And I press "Close grader"
    # All conditions should now be completed.
    When I am on the "Music history" "forum activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Start discussions: 1" completion condition of "Music history" is displayed as "done"
    And the "Make forum posts: 2" completion condition of "Music history" is displayed as "done"
    And the "Post replies: 1" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: A student can manually mark the forum activity as done but a teacher cannot
    Given I am on the "Music history" "forum activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Students must manually mark the activity as done" to "1"
    And I press "Save and display"
    # Teacher view.
    And the manual completion button for "Music history" should be disabled
    # Student view.
    When I am on the "Music history" "forum activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
