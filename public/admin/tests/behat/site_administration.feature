@core @core_admin
Feature: Navigate site administration pages
  In order to configure my site
  As an admin
  I need to be able to navigate the site administration pages

  @javascript
  Scenario: Navigate to an admin category page
    Given I log in as "admin"
    When I navigate to "Plugins > Activity modules > Assignment" in site administration
    # Ensure secondary navigation is still present and "Plugins" is selected.
    Then "//a[@aria-current = 'true' and normalize-space() = 'Plugins']" "xpath" should exist in the ".secondary-navigation" "css_element"
    And I should see "Category: Assignment"

  @javascript
  Scenario: Navigate to an admin settings page
    Given I log in as "admin"
    When I navigate to "Plugins > Activity modules > Forum" in site administration
    # Ensure secondary navigation is still present and "Plugins" is selected.
    Then "//a[@aria-current = 'true' and normalize-space() = 'Plugins']" "xpath" should exist in the ".secondary-navigation" "css_element"
    And I should see "Forum"
