@core @core_completion
Feature: Allow admins to edit the default activity completion rules at site level.
  In order to set the activity completion defaults for new activities
  As an admin
  I need to be able to edit the completion rules for a group of activities at site level.

  Scenario: Navigate to site default activity completion
    Given I navigate to "Courses > Default settings > Default activity completion" in site administration
    When I should see "Default activity completion"
    Then I should see "These are the default completion conditions for activities in all courses."
    And the following config values are set as admin:
      | enablecompletion  | 0 |
    And I navigate to "Courses > Default settings" in site administration
    And I should not see "Default activity completion"
