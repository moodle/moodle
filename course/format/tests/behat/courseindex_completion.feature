@core @core_course @core_courseformat
Feature: Course index completion icons
  In order to quickly check my activities completions
  As a student
  I need to see the activity completion in the course index.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | enablecompletion | 1        |
      | numsections      | 4        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section | completion |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       | 1          |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    # The course index is hidden by default in small devices.
    And I change window size to "large"

  @javascript
  Scenario: Teacher does not see completion icons.
    When I am on the "C1" "Course" page logged in as "teacher1"
    Then I should see "New section" in the "courseindex-content" "region"
    And I should see "Activity sample 1" in the "courseindex-content" "region"
    And "To do" "icon" should not exist in the "courseindex-content" "region"

  @javascript
  Scenario: User should see the completion icons
    When I am on the "C1" "Course" page logged in as "student1"
    Then I should see "New section" in the "courseindex-content" "region"
    And I should see "Activity sample 1" in the "courseindex-content" "region"
    And "To do" "icon" should exist in the "courseindex-content" "region"

  @javascript
  Scenario: Manual completion should update the course index completion
    Given I am on the "C1" "Course" page logged in as "student1"
    And "To do" "icon" should exist in the "courseindex-content" "region"
    When I press "Mark as done"
    And I wait until "Done" "button" exists
    Then "Done" "icon" should exist in the "courseindex-content" "region"
    And I press "Done"
    And I wait until "Mark as done" "button" exists
    And "To do" "icon" should exist in the "courseindex-content" "region"

  @javascript
  Scenario: Manual completion in an activity page should update the course index
    Given I am on the "sample1" "Activity" page logged in as "student1"
    And "To do" "icon" should exist in the "courseindex-content" "region"
    When I press "Mark as done"
    And I wait until "Done" "button" exists
    Then "Done" "icon" should exist in the "courseindex-content" "region"
    And I press "Done"
    And I wait until "Mark as done" "button" exists
    And "To do" "icon" should exist in the "courseindex-content" "region"

  @javascript
  Scenario: Refresh the page should keep the completion consistent
    Given I am on the "C1" "Course" page logged in as "student1"
    And "To do" "icon" should exist in the "courseindex-content" "region"
    When I press "Mark as done"
    And I wait until "Done" "button" exists
    And I reload the page
    Then the manual completion button of "Activity sample 1" is displayed as "Done"

  @javascript
  Scenario: Auto completion should appear in the course index
    Given the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section | completion | completionview |
      | assign   | Activity sample 2 | Test assignment description | C1     | sample2  | 1       | 1          | 1              |
    When I am on the "sample2" "Activity" page logged in as "student1"
    And I am on the "C1" "Course" page
    Then "Done" "icon" should exist in the "courseindex-content" "region"

  @javascript
  Scenario: Completion failed should appear in the course index
    Given the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
    And the following "activities" exist:
      | activity | name           | course | idnumber | attempts | gradepass | completion | completionusegrade | completionpassgrade | completionattemptsexhausted |
      | quiz     | Test quiz name | C1     | quiz1    | 1        | 5.00      | 2          | 1                  | 1                   | 1                           |
    And quiz "Test quiz name" contains the following questions:
      | question       | page |
      | First question | 1    |
    And user "student1" has attempted "Test quiz name" with responses:
      | slot | response |
      | 1    | False    |
    When I am on the "C1" "Course" page logged in as "student1"
    And "Failed" "icon" should exist in the "courseindex-content" "region"

  @javascript
  Scenario: Completion passed should appear in the course index
    Given the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
    And the following "activities" exist:
      | activity | name           | course | idnumber | attempts | gradepass | completion | completionusegrade | completionpassgrade | completionattemptsexhausted |
      | quiz     | Test quiz name | C1     | quiz1    | 1        | 5.00      | 2          | 1                  | 1                   | 1                           |
    And quiz "Test quiz name" contains the following questions:
      | question       | page |
      | First question | 1    |
    And user "student1" has attempted "Test quiz name" with responses:
      | slot | response |
      | 1    | True    |
    When I am on the "C1" "Course" page logged in as "student1"
    And "Done" "icon" should exist in the "courseindex-content" "region"

  @javascript
  Scenario: Completion done should appear in the course index when the requirement is any grade
    Given the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
    And the following "activities" exist:
      | activity | name           | course | idnumber | attempts | gradepass | completion | completionusegrade | completionpassgrade |
      | quiz     | Test quiz name | C1     | quiz1    | 1        | 5.00      | 2          | 1                  | 0                   |
    And quiz "Test quiz name" contains the following questions:
      | question       | page |
      | First question | 1    |
    And user "student1" has attempted "Test quiz name" with responses:
      | slot | response |
      | 1    | False    |
    When I am on the "C1" "Course" page logged in as "student1"
    And "Done" "icon" should exist in the "courseindex-content" "region"

  @javascript
  Scenario: Activities with custom completion rules could fail
    Given the following "activity" exists:
      | activity                 | scorm                                                         |
      | course                   | C1                                                            |
      | name                     | Music history                                              |
      | packagefilepath          | mod/scorm/tests/packages/RuntimeMinimumCalls_SCORM12-mini.zip |
      | maxattempt               | 1                                                             |
      | latattemptlock           | 1                                                             |
      # Add requirements
      | completion               | 2                                                             |
      | completionscorerequired  | 90                                                            |
    Given I am on the "Music history" "scorm activity" page logged in as student1
    # We need a little taller window because Firefox is, apparently, unable to auto-scroll within
    # an iframe, so we need to ensure that the "Save changes" button is visible in the viewport.
    And I change window size to "large"
    And I press "Enter"
    And I switch to the main frame
    And I click on "Par?" "list_item"
    And I switch to "scorm_object" iframe
    And I wait until the page is ready
    And I switch to the main frame
    And I click on "Keeping Score" "list_item"
    And I switch to "scorm_object" iframe
    And I wait until the page is ready
    And I switch to the main frame
    And I click on "Other Scoring Systems" "list_item"
    And I switch to "scorm_object" iframe
    And I wait until the page is ready
    And I switch to the main frame
    And I click on "The Rules of Golf" "list_item"
    And I switch to "scorm_object" iframe
    And I wait until the page is ready
    And I switch to the main frame
    And I click on "Playing Golf Quiz" "list_item"
    And I switch to "scorm_object" iframe
    And I wait until the page is ready
    And I click on "[id='question_com.scorm.golfsamples.interactions.playing_1_1']" "css_element"
    And I press "Submit Answers"
    And I wait until "Score: 20" "text" exists
    And I switch to the main frame
    And I click on "Exit activity" "link"
    And "Failed" "icon" should exist in the "courseindex-content" "region"
