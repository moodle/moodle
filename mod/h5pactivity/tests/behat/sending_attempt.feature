@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe
Feature: Do a H5P attempt
  In order to let students do a H5P attempt
  As a teacher
  I need to list students attempts on various reports

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/h5p:updatelibraries | Allow      | editingteacher | System       |           |
    And the following "activity" exists:
      | activity        | h5pactivity                                |
      | course          | C1                                         |
      | name            | Awesome H5P package                        |
      | packagefilepath | h5p/tests/fixtures/multiple-choice-2-6.h5p |
      | grademethod     | 2                                          |

  Scenario: View an H5P as a teacher
    When I am on the "Awesome H5P package" "h5pactivity activity" page logged in as teacher1
    And I wait until the page is ready
    Then I should see "This content is displayed in preview mode"

  @javascript
  Scenario: Do an attempt and check on course log report
    When I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I wait until the page is ready
    And I should not see "This content is displayed in preview mode"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I click on "Correct one" "text" in the ".h5p-question-content" "css_element"
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I switch to the main frame
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to course participants
    And I follow "Student 1"
    Then I follow "Today's logs"
    And I should see "xAPI statement received"

  @javascript
  Scenario: Do various attempts and check them with the attempts and user grades reports
    Given I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I wait until the page is ready
    And I should not see "This content is displayed in preview mode"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I click on "Wrong one" "text" in the ".h5p-question-content" "css_element"
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I click on "Retry" "button" in the ".h5p-question-buttons" "css_element"
    # We need to wait 1 second here because, in very quick environments, the 2nd
    # attempts happen too close to the 1st one and it's not sent properly. See MDL-76010.
    And I wait "1" seconds
    And I click on "Correct one" "text" in the ".h5p-question-content" "css_element"
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    # H5P does not allow to Retry if the user checks the correct answer, we need to refresh the page.
    And I switch to the main frame
    And I reload the page
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    # Because of the steps above, the 2nd and 3rd attempts are enough "separated" and we don't
    # need to add any wait here.
    And I click on "Wrong one" "text" in the ".h5p-question-content" "css_element"
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I click on "Retry" "button" in the ".h5p-question-buttons" "css_element"
    # Again, the wait between 3rd and 4th attempt, to separate them a little bit.
    And I wait "1" seconds
    And I click on "Correct one" "text" in the ".h5p-question-content" "css_element"
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I switch to the main frame
    When I navigate to "Attempts report" in current page administration
    And "1" row "Score" column of "table" table should contain "0"
    And "2" row "Score" column of "table" table should contain "1"
    And "3" row "Score" column of "table" table should contain "0"
    And "4" row "Score" column of "table" table should contain "1"
    And I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item          | Grade | Percentage  |
      | Awesome H5P package | 50.00 | 50.00 %     |
