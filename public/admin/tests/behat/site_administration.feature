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
    Then "//a[(@aria-current = 'true' or @aria-current = 'page') and normalize-space() = 'Plugins']" "xpath" should exist in the ".secondary-navigation" "css_element"
    And I should see "Category: Assignment"

  @javascript
  Scenario: Navigate to an admin settings page
    Given I log in as "admin"
    When I navigate to "Plugins > Activity modules > Forum" in site administration
    # Ensure secondary navigation is still present and "Plugins" is selected.
    Then "//a[(@aria-current = 'true' or @aria-current = 'page') and normalize-space() = 'Plugins']" "xpath" should exist in the ".secondary-navigation" "css_element"
    And I should see "Forum"

  @javascript
  Scenario: Switching site administration tabs updates the URL anchor and survives a reload
    Given I log in as "admin"
    And I visit "/admin/search.php"
    When I select "Users" from secondary navigation
    Then the url should match "#linkusers$"
    And I reload the page
    And I should see "Browse list of users"
    And "//a[@aria-selected = 'true' and normalize-space() = 'Users']" "xpath" should exist in the ".secondary-navigation" "css_element"

  @javascript
  Scenario: Selecting a site administration tab from the "More" overflow menu updates the URL anchor
    Given I log in as "admin"
    And I visit "/admin/search.php"
    # Force the "Server" category tab to collapse into the "More" overflow menu.
    And I change viewport size to "500x800"
    When I click on "More" "link" in the ".secondary-navigation .navigation.secondarynav-tabbar" "css_element"
    And I click on "Server" "link" in the ".secondary-navigation [data-region='moredropdown']" "css_element"
    Then the url should match "#linkserver$"
    And I reload the page
    And I should see "PHP info"
    And "//a[@aria-selected = 'true' and normalize-space() = 'Server']" "xpath" should exist in the ".secondary-navigation [data-region='moredropdown']" "css_element"

  @javascript
  Scenario: Switching tabs on an admin settings page updates the URL anchor and survives a reload
    Given I log in as "admin"
    And I visit "/admin/settings.php?section=themesettingboost"
    When I click on "Advanced settings" "link" in the "#adminsettings" "css_element"
    Then the url should match "#theme_boost_advanced$"
    And I reload the page
    And the url should match "#theme_boost_advanced$"
    And "//a[@role = 'tab' and contains(@class, 'active') and normalize-space() = 'Advanced settings']" "xpath" should exist
