@mod @mod_choice
Feature: A student can see how the results of the choice activity will be published
  In order to put my mind at ease when it comes to answering a choice
  As a student
  I need to learn how my choice will be handled and published to the other course participants.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"

  Scenario: Results will not be published to the students
    Given the following "activities" exist:
      | activity | name        | intro              | course | idnumber | option |
      | choice   | Choice 1 | Choice description | C1     | choice1  | Option 1, Option 2 |
    And I am on "Course 1" course homepage
    And I follow "Choice 1"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Publish results | Do not publish results to students |
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Choice 1"
    Then I should see "The results of this activity will not be published after you answer."

  Scenario: Full results will be shown to the students after they answer
    Given the following "activities" exist:
      | activity | name     | intro              | course | idnumber | option             |
      | choice   | Choice 1 | Choice description | C1     | choice1  | Option 1, Option 2 |
    And I am on "Course 1" course homepage
    When I follow "Choice 1"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Publish results    | Show results to students after they answer            |
      | Privacy of results | Publish full results, showing names and their choices |
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Choice 1"
    Then I should see "Full results, showing everyone's choices, will be published after you answer."

  Scenario: Anonymous results will be shown to students after they answer
    Given the following "activities" exist:
      | activity | name     | intro              | course | idnumber | option             |
      | choice   | Choice 1 | Choice description | C1     | choice1  | Option 1, Option 2 |
    And I am on "Course 1" course homepage
    When I follow "Choice 1"
    And I navigate to "Settings" in current page administration
    And I set the field "Publish results" to "Show results to students after they answer"
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Choice 1"
    Then I should see "Anonymous results will be published after you answer."

  Scenario: Full results will be shown to students only after the choice is closed
    Given the following "activities" exist:
      | activity | name     | intro              | course | idnumber | option             |
      | choice   | Choice 1 | Choice description | C1     | choice1  | Option 1, Option 2 |
    And I am on "Course 1" course homepage
    When I follow "Choice 1"
    And I navigate to "Settings" in current page administration
    And I set the field "Publish results" to "Show results to students only after the choice is closed"
    And I set the field "Privacy of results" to "Publish full results, showing names and their choices"
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Choice 1"
    Then I should see "Full results, showing everyone's choices, will be published after the activity is closed."

  Scenario: Anonymous results will be shown to students only after the choice is closed
    Given the following "activities" exist:
      | activity | name     | intro              | course | idnumber | option             |
      | choice   | Choice 1 | Choice description | C1     | choice1  | Option 1, Option 2 |
    And I am on "Course 1" course homepage
    When I follow "Choice 1"
    And I navigate to "Settings" in current page administration
    And I set the field "Publish results" to "Show results to students only after the choice is closed"
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Choice 1"
    Then I should see "Anonymous results will be published after the activity is closed."

  Scenario: Results will always be shown to students
    And the following "activities" exist:
      | activity | name     | intro              | course | idnumber | option             |
      | choice   | Choice 1 | Choice description | C1     | choice1  | Option 1, Option 2 |
    And I am on "Course 1" course homepage
    When I follow "Choice 1"
    And I navigate to "Settings" in current page administration
    And I set the field "Publish results" to "Always show results to students"
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Choice 1"
    Then I should not see "Full results, showing everyone's choices, will be published after you answer."
    And I should not see "Full results, showing everyone's choices, will be published after the activity is closed."
    And I should not see "Anonymous results will be published after you answer."
    And I should not see "Anonymous results will be published after the activity is closed."
    And I should not see "The results of this activity will not be published after you answer."
