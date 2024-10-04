@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe @javascript
Feature: View essay attempt report
  In order to let users to review an essay attempt
  As a user
  I need to view long fill in interactions in the report

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      # No HTML should appear even with formatstringstriptags disabled.
      | formatstringstriptags | 0 |
    And the following "activity" exists:
      | activity        | h5pactivity                        |
      | course          | C1                                 |
      | name            | Awesome H5P package                |
      | grademethod     | 2                                  |
      | packagefilepath | h5p/tests/fixtures/basic_essay.h5p |

  Scenario: View attempt essay content
    # Do an attempt.
    Given I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I change window size to "large"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I set the field with xpath "//textarea" to "This is a smurfing smurf"
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I switch to the main frame
    And I reload the page
    # Check attempt.
    When I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "This is a smurfing smurf"
    And I should not see "<strong>smurf</strong>"
