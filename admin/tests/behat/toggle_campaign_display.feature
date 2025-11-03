@core @core_admin
Feature: Toggle campaign banner visibility
  In order to control the visibility of the campaign banner content
  As an admin
  I need to be able to disable the campaign banner display

  Scenario Outline: Admin can disable the campaign banner display
    Given the following config values are set as admin:
      | showcampaigncontent | <showcampaigncontent> |
    And I log in as "admin"
    When I navigate to "Notifications" in site administration
    Then "//iframe[@id='campaign-content']" "xpath_element" <display> exist

    Examples:
      | showcampaigncontent | display    |
      | true                | should     |
      | false               | should not |
