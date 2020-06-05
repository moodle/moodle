@core
Feature: Gathering user feedback
  In order to facilitate data collection from as broad a sample of Moodle users as possible
  As Moodle HQ
  We should add a link within Moodle to a permanent URL on which surveys will be placed

  Scenario: Users should see a feedback link on footer when the feature is enabled
    Given the following config values are set as admin:
      | enableuserfeedback  | 1  |
    When I log in as "admin"
    Then I should see "Give feedback" in the "page-footer" "region"

  Scenario: Users should not see a feedback link on footer when the feature is disabled
    Given the following config values are set as admin:
      | enableuserfeedback  | 0  |
    When I log in as "admin"
    Then I should not see "Give feedback" in the "page-footer" "region"

  Scenario: Visitors should not see a feedback link on footer when they are not logged in
    Given the following config values are set as admin:
      | enableuserfeedback  | 1  |
    When I am on site homepage
    Then I should not see "Give feedback" in the "page-footer" "region"

  @javascript
  Scenario: Users should not see the notification after they click on the remind me later link
    Given the following config values are set as admin:
      | enableuserfeedback        | 1   |
      | userfeedback_nextreminder | 2   |
      | userfeedback_remindafter  | 90  |
    When I log in as "admin"
    And I follow "Dashboard" in the user menu
    And I click on "Remind me later" "link"
    And I reload the page
    Then I should not see "Give feedback" in the "region-main" "region"
    And I should not see "Remind me later" in the "region-main" "region"
