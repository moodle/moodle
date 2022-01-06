@javascript @mod_quiz @quizaccess @quizaccess_seb
Feature: Safe Exam Browser settings in quiz edit form

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I turn editing mode on

  Scenario: Quiz setting "Require the use of Safe Exam Browser" has all types, except "Use an existing template".
    When I add a "Quiz" to section "1"
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
    When I add a "Quiz" to section "1"
    And I expand all fieldsets
    And the "Require the use of Safe Exam Browser" select box should contain "Yes – Configure manually"
    And the "Require the use of Safe Exam Browser" select box should contain "Yes – Use an existing template"
    And the "Require the use of Safe Exam Browser" select box should contain "Yes – Upload my own config"
    And the "Require the use of Safe Exam Browser" select box should contain "Yes – Use SEB client config"
    And the field "Require the use of Safe Exam Browser" matches value "No"

  Scenario: SEB settings if using No SEB
    Given the following "quizaccess_seb > seb templates" exist:
      | name       |
      | Template 1 |
    When I add a "Quiz" to section "1"
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
    When I add a "Quiz" to section "1"
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
    When I add a "Quiz" to section "1"
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
    When I add a "Quiz" to section "1"
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
    When I add a "Quiz" to section "1"
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
