@javascript @mod_quiz @quizaccess @quizaccess_seb
Feature: Safe Exam Browser settings in quiz edit form

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "activities" exist:
      | activity | course | section | name   |
      | quiz     | C1     | 1       | Quiz 1 |
      | quiz     | C1     | 1       | Quiz 2 |

  Scenario: Quiz setting "Require the use of Safe Exam Browser" has all types, except "Use an existing template".
    When I am on the "Quiz 1" "quiz activity editing" page logged in as admin
    And I expand all fieldsets
    And the "Require the use of Safe Exam Browser" select box should contain "Yes – Configure manually"
    And the "Require the use of Safe Exam Browser" select box should not contain "Yes – Use an existing template"
    And the "Require the use of Safe Exam Browser" select box should contain "Yes – Upload my own config"
    And the "Require the use of Safe Exam Browser" select box should contain "Yes – Use SEB client config"
    And the field "Require the use of Safe Exam Browser" matches value "No"

  Scenario: Quiz setting "Require the use of Safe Exam Browser" has all types if at least one template has been added.
    Given the following "quizaccess_seb > seb templates" exist:
      | name       |
      | Template 1 |
    When I am on the "Quiz 1" "quiz activity editing" page logged in as admin
    And I expand all fieldsets
    And the "Require the use of Safe Exam Browser" select box should contain "Yes – Configure manually"
    And the "Require the use of Safe Exam Browser" select box should contain "Yes – Use an existing template"
    And the "Require the use of Safe Exam Browser" select box should contain "Yes – Upload my own config"
    And the "Require the use of Safe Exam Browser" select box should contain "Yes – Use SEB client config"
    And the field "Require the use of Safe Exam Browser" matches value "No"

  Scenario: Quiz can be edited without capability to select SEB template
    Given the following "permission override" exists:
      | role         | editingteacher                       |
      | capability   | quizaccess/seb:manage_seb_templateid |
      | permission   | Prevent                              |
      | contextlevel | System                               |
      | reference    |                                      |
    And the following "user" exists:
      | username     | teacher |
      | firstname    | Teacher |
      | lastname     | One     |
    And the following "course enrolment" exists:
      | user         | teacher        |
      | course       | C1             |
      | role         | editingteacher |
    And I log in as "teacher"
    # Create the quiz.
    When I add a quiz activity to course "Course 1" section "0" and I fill the form with:
      | Name | My quiz |
    Then I should not see "New Quiz"
    # Edit the quiz.
    And I am on the "My quiz" "quiz activity editing" page
    And I set the field "Name" to "My quiz edited"
    And I press "Save and return to course"
    And I should not see "Edit settings"

  Scenario: SEB settings if using No SEB
    Given the following "quizaccess_seb > seb templates" exist:
      | name       |
      | Template 1 |
    And I am on the "Quiz 1" "quiz activity editing" page logged in as admin
    And I expand all fieldsets
    And I set the field "Require the use of Safe Exam Browser" to "No"
    Then I should not see "Upload Safe Exam Browser config file"
    Then I should not see "Safe Exam Browser config template"
    Then I should not see "Template 1"
    Then I should not see "Show Safe Exam Browser download button"
    Then I should not see "Enable quitting of SEB"
    Then I should not see "Quit password"
    Then I should not see "Allowed browser exam keys"
    Then I should not see "Show Exit Safe Exam Browser button, configured with this quit link"
    Then I should not see "Ask user to confirm quitting"
    Then I should not see "Enable reload in exam"
    Then I should not see "Show SEB task bar"
    Then I should not see "Show reload button"
    Then I should not see "Show time"
    Then I should not see "Show keyboard layout"
    Then I should not see "Show Wi-Fi control"
    Then I should not see "Enable audio controls"
    Then I should not see "Mute on startup"
    Then I should not see "Enable spell checking"
    Then I should not see "Enable URL filtering"
    Then I should not see "Filter also embedded content"
    Then I should not see "Expressions allowed"
    Then I should not see "Regex allowed"
    Then I should not see "Expressions blocked"
    Then I should not see "Regex blocked"

  Scenario: SEB settings if using Use SEB client config
    Given the following "quizaccess_seb > seb templates" exist:
      | name       |
      | Template 1 |
    And I am on the "Quiz 1" "quiz activity editing" page logged in as admin
    And I expand all fieldsets
    And I set the field "Require the use of Safe Exam Browser" to "Yes – Use SEB client config"
    Then I should see "Show Safe Exam Browser download button"
    Then I should see "Allowed browser exam keys"
    Then I should not see "Upload Safe Exam Browser config file"
    Then I should not see "Safe Exam Browser config template"
    Then I should not see "Template 1"
    Then I should not see "Enable quitting of SEB"
    Then I should not see "Quit password"
    Then I should not see "Show Exit Safe Exam Browser button, configured with this quit link"
    Then I should not see "Ask user to confirm quitting"
    Then I should not see "Enable reload in exam"
    Then I should not see "Show SEB task bar"
    Then I should not see "Show reload button"
    Then I should not see "Show time"
    Then I should not see "Show keyboard layout"
    Then I should not see "Show Wi-Fi control"
    Then I should not see "Enable audio controls"
    Then I should not see "Mute on startup"
    Then I should not see "Enable spell checking"
    Then I should not see "Enable URL filtering"
    Then I should not see "Filter also embedded content"
    Then I should not see "Expressions allowed"
    Then I should not see "Regex allowed"
    Then I should not see "Expressions blocked"
    Then I should not see "Regex blocked"

  Scenario: SEB settings if using Upload my own config
    Given the following "quizaccess_seb > seb templates" exist:
      | name       |
      | Template 1 |
    And I am on the "Quiz 1" "quiz activity editing" page logged in as admin
    And I expand all fieldsets
    And I set the field "Require the use of Safe Exam Browser" to "Yes – Upload my own config"
    Then I should see "Upload Safe Exam Browser config file"
    Then I should see "Show Safe Exam Browser download button"
    Then I should not see "Enable quitting of SEB"
    Then I should not see "Quit password"
    Then I should see "Allowed browser exam keys"
    Then I should not see "Show Exit Safe Exam Browser button, configured with this quit link"
    Then I should not see "Ask user to confirm quitting"
    Then I should not see "Enable reload in exam"
    Then I should not see "Show SEB task bar"
    Then I should not see "Show reload button"
    Then I should not see "Show time"
    Then I should not see "Show keyboard layout"
    Then I should not see "Show Wi-Fi control"
    Then I should not see "Enable audio controls"
    Then I should not see "Mute on startup"
    Then I should not see "Enable spell checking"
    Then I should not see "Enable URL filtering"
    Then I should not see "Filter also embedded content"
    Then I should not see "Expressions allowed"
    Then I should not see "Regex allowed"
    Then I should not see "Expressions blocked"
    Then I should not see "Regex blocked"
    Then I should not see "Safe Exam Browser config template"
    Then I should not see "Template 1"

  Scenario: SEB settings if using Use an existing template
    Given the following "quizaccess_seb > seb templates" exist:
      | name       |
      | Template 1 |
    And I am on the "Quiz 1" "quiz activity editing" page logged in as admin
    And I expand all fieldsets
    And I set the field "Require the use of Safe Exam Browser" to "Yes – Use an existing template"
    Then I should see "Safe Exam Browser config template"
    Then I should see "Template 1"
    Then I should see "Show Safe Exam Browser download button"
    Then I should see "Enable quitting of SEB"
    Then I should see "Quit password"
    Then I should not see "Allowed browser exam keys"
    Then I should not see "Upload Safe Exam Browser config file"
    Then I should not see "Show Exit Safe Exam Browser button, configured with this quit link"
    Then I should not see "Ask user to confirm quitting"
    Then I should not see "Enable reload in exam"
    Then I should not see "Show SEB task bar"
    Then I should not see "Show reload button"
    Then I should not see "Show time"
    Then I should not see "Show keyboard layout"
    Then I should not see "Show Wi-Fi control"
    Then I should not see "Enable audio controls"
    Then I should not see "Mute on startup"
    Then I should not see "Enable spell checking"
    Then I should not see "Enable URL filtering"
    Then I should not see "Filter also embedded content"
    Then I should not see "Expressions allowed"
    Then I should not see "Regex allowed"
    Then I should not see "Expressions blocked"
    Then I should not see "Regex blocked"
    And I set the field "Enable quitting of SEB" to "No"
    Then I should not see "Quit password"

  Scenario: SEB settings if using Configure manually
    Given the following "quizaccess_seb > seb templates" exist:
      | name       |
      | Template 1 |
    And I am on the "Quiz 1" "quiz activity editing" page logged in as admin
    And I expand all fieldsets
    And I set the field "Require the use of Safe Exam Browser" to "Yes – Configure manually"
    Then I should see "Show Safe Exam Browser download button"
    Then I should see "Enable quitting of SEB"
    Then I should see "Quit password"
    Then I should see "Show Exit Safe Exam Browser button, configured with this quit link"
    Then I should see "Ask user to confirm quitting"
    Then I should see "Enable reload in exam"
    Then I should see "Show SEB task bar"
    Then I should see "Show reload button"
    Then I should see "Show time"
    Then I should see "Show keyboard layout"
    Then I should see "Show Wi-Fi control"
    Then I should see "Enable audio controls"
    Then I should not see "Mute on startup"
    Then I should see "Enable spell checking"
    Then I should see "Enable URL filtering"
    Then I should not see "Filter also embedded content"
    Then I should not see "Expressions allowed"
    Then I should not see "Regex allowed"
    Then I should not see "Expressions blocked"
    Then I should not see "Regex blocked"
    And I set the field "Enable quitting of SEB" to "No"
    Then I should not see "Quit password"
    And I set the field "Show SEB task bar" to "No"
    Then I should not see "Show reload button"
    Then I should not see "Show time"
    Then I should not see "Show keyboard layout"
    Then I should not see "Show Wi-Fi control"
    And I set the field "Enable audio controls" to "Yes"
    Then I should see "Mute on startup"
    And I set the field "Enable URL filtering" to "Yes"
    Then I should see "Filter also embedded content"
    Then I should see "Expressions allowed"
    Then I should see "Regex allowed"
    Then I should see "Expressions blocked"
    Then I should see "Regex blocked"
    Then I should not see "Upload Safe Exam Browser config file"
    Then I should not see "Allowed browser exam keys"
    Then I should not see "Safe Exam Browser config template"
    Then I should not see "Template 1"

  Scenario: Disable templates that are already in use and seeing their visibility in settings
    Given the following "quizaccess_seb > seb templates" exist:
      | name       | enabled |
      | Template 1 | 1       |
      | Template 2 | 0       |
    # Set Quiz 1 template to Template 1
    When I am on the "Quiz 1" "quiz activity editing" page logged in as admin
    And I expand all fieldsets
    And I set the field "Require the use of Safe Exam Browser" to "Yes – Use an existing template"
    Then I should see "Safe Exam Browser config template"
    And the "Safe Exam Browser config template" select box should contain "Template 1"
    And the "Safe Exam Browser config template" select box should not contain "Template 2"
    And I set the field "Safe Exam Browser config template" to "Template 1"
    And I press "Save and return to course"
    # Disable Template 1
    And I navigate to "Plugins > Activity modules > Quiz > Safe Exam Browser templates" in site administration
    And I click on "Edit" "link" in the "Template 1" "table_row"
    And I set the field "Enabled" to "No"
    And I press "Save changes"
    # Check Quiz 1 is still using Template 1
    When I am on the "Quiz 1" "quiz activity editing" page logged in as admin
    And I expand all fieldsets
    And the field "Require the use of Safe Exam Browser" matches value "Yes – Use an existing template"
    Then I should see "Template 1"
    And the "Safe Exam Browser config template" select box should contain "Template 1"
    And the "Safe Exam Browser config template" select box should not contain "Template 2"
    # Check Quiz 3 cannot use any templates as they're all disabled
    When I am on the "Quiz 2" "quiz activity editing" page logged in as admin
    And I expand all fieldsets
    And the "Require the use of Safe Exam Browser" select box should not contain "Yes – Use an existing template"
