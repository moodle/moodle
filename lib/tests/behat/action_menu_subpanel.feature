@core @javascript
Feature: Navigate action menu subpanels
  In order to navigate an action menu subpanel
  As a user
  I need to be able to use both keyboard and mouse to open the subpanel

  Background:
    Given I log in as "admin"
    And I am on fixture page "/lib/tests/behat/fixtures/action_menu_subpanel_output_testpage.php"

  Scenario: Navigate several action menus subpanels with mouse
    Given I click on "Actions menu" "button" in the "regularscenario" "region"
    And I click on "Subpanel example" "menuitem" in the "regularscenario" "region"
    And I should see "Status A" in the "regularscenario" "region"
    And I should see "Status B" in the "regularscenario" "region"
    And I should not see "Status C" in the "regularscenario" "region"
    And I should not see "Status D" in the "regularscenario" "region"
    When I click on "Another subpanel" "menuitem" in the "regularscenario" "region"
    Then I should not see "Status A" in the "regularscenario" "region"
    And I should not see "Status B" in the "regularscenario" "region"
    And I should see "Status C" in the "regularscenario" "region"
    And I should see "Status D" in the "regularscenario" "region"
    And I click on "Status D" "link" in the "regularscenario" "region"
    And I should see "Foo param value: Donkey" in the "paramcheck" "region"

  Scenario: Check extra data in subpanel action menu items
    When I should see "Adding data attributes to menu item" in the "dataattributes" "region"
    # the page have a javascript script to check that for us.
    Then "[data-extra='some other value']" "css_element" should exist in the "dataattributes" "region"
    And "[data-extra='some other value']" "css_element" should exist in the "dataattributes" "region"
    And I should see "Extra data attribute detected: some extra value" in the "datachecks" "region"
    And I should see "Extra data attribute detected: some other value" in the "datachecks" "region"

  Scenario: User can navigate left menus subpanels
    Given I click on "Actions menu" "button" in the "menuleft" "region"
    And I click on "Subpanel example" "menuitem" in the "menuleft" "region"
    And I should see "Status A" in the "menuleft" "region"
    And I should see "Status B" in the "menuleft" "region"
    And I should not see "Status C" in the "menuleft" "region"
    And I should not see "Status D" in the "menuleft" "region"
    When I click on "Another subpanel" "menuitem" in the "menuleft" "region"
    Then I should not see "Status A" in the "menuleft" "region"
    And I should not see "Status B" in the "menuleft" "region"
    And I should see "Status C" in the "menuleft" "region"
    And I should see "Status D" in the "menuleft" "region"
    And I click on "Status D" "link" in the "menuleft" "region"
    And I should see "Foo param value: Donkey" in the "paramcheck" "region"

  Scenario: User can show the subpanels content using keyboard
    Given I click on "Actions menu" "button" in the "regularscenario" "region"
    # Move to the first subpanel element.
    And I press the down key
    And I press the down key
    And I press the down key
    And I should see "Status A" in the "regularscenario" "region"
    And I should see "Status B" in the "regularscenario" "region"
    And I should not see "Status C" in the "regularscenario" "region"
    And I should not see "Status D" in the "regularscenario" "region"
    # Move to the next subpanel.
    When I press the down key
    Then I should not see "Status A" in the "regularscenario" "region"
    And I should not see "Status B" in the "regularscenario" "region"
    And I should see "Status C" in the "regularscenario" "region"
    And I should see "Status D" in the "regularscenario" "region"

  Scenario: User can browse the subpanel content using the arrow keys
    Given I click on "Actions menu" "button" in the "regularscenario" "region"
    # Move to the first subpanel element.
    And I press the down key
    And I press the down key
    And I press the down key
    # Move in the subpanel with arrow keys and loop the links with up and down.
    When I press the right key
    And the focused element is "Status A" "link" in the "regularscenario" "region"
    And I press the down key
    And the focused element is "Status B" "link" in the "regularscenario" "region"
    And I press the down key
    And the focused element is "Status A" "link" in the "regularscenario" "region"
    And I press the up key
    And the focused element is "Status B" "link" in the "regularscenario" "region"
    # Leave the subpanel with right and left key.
    Then I press the right key
    And the focused element is "Subpanel example" "menuitem" in the "regularscenario" "region"
    And I press the right key
    And the focused element is "Status A" "link" in the "regularscenario" "region"
    And I press the left key
    And the focused element is "Subpanel example" "menuitem" in the "regularscenario" "region"
    And I press the left key
    And the focused element is "Status A" "link" in the "regularscenario" "region"
    And I press the left key
    And the focused element is "Subpanel example" "menuitem" in the "regularscenario" "region"
    # Move to the next subpanel with enter.
    And I press the down key
    And I press the right key
    And the focused element is "Status C" "link" in the "regularscenario" "region"
    And I press the down key
    And the focused element is "Status D" "link" in the "regularscenario" "region"
    # Select the current link of the panel with enter.
    And I press the enter key
    And I should see "Foo param value: Donkey" in the "paramcheck" "region"

  Scenario: User can open and close subpanels in mobile
    Given I change the viewport size to "mobile"
    And I click on "Actions menu" "button" in the "regularscenario" "region"
    And I should not see "Status A" in the "regularscenario" "region"
    And I should not see "Status B" in the "regularscenario" "region"
    And I should not see "Status C" in the "regularscenario" "region"
    And I should not see "Status D" in the "regularscenario" "region"
    When I click on "Subpanel example" "menuitem" in the "regularscenario" "region"
    And I should see "Status A" in the "regularscenario" "region"
    And I should see "Status B" in the "regularscenario" "region"
    And I should not see "Status C" in the "regularscenario" "region"
    And I should not see "Status D" in the "regularscenario" "region"
    # In mobile click the menu item toggles the subpanel.
    Then I click on "Subpanel example" "menuitem" in the "regularscenario" "region"
    And I should not see "Status A" in the "regularscenario" "region"
    And I should not see "Status B" in the "regularscenario" "region"
    And I should not see "Status C" in the "regularscenario" "region"
    And I should not see "Status D" in the "regularscenario" "region"
    And I click on "Another subpanel" "menuitem" in the "regularscenario" "region"
    And I should not see "Status A" in the "regularscenario" "region"
    And I should not see "Status B" in the "regularscenario" "region"
    And I should see "Status C" in the "regularscenario" "region"
    And I should see "Status D" in the "regularscenario" "region"
    And I click on "Status D" "link" in the "regularscenario" "region"
    And I should see "Foo param value: Donkey" in the "paramcheck" "region"

  Scenario: User can browse the subpanels using keys in extra small windows
    Given I change the viewport size to "mobile"
    And I click on "Actions menu" "button" in the "regularscenario" "region"
    # Go to the seconds subpanel and open it with enter.
    And I press the down key
    And I press the down key
    And I press the down key
    And I press the down key
    And the focused element is "Another subpanel" "menuitem" in the "regularscenario" "region"
    And I press the enter key
    When I should not see "Status A" in the "regularscenario" "region"
    And I should not see "Status B" in the "regularscenario" "region"
    And I should see "Status C" in the "regularscenario" "region"
    And I should see "Status D" in the "regularscenario" "region"
    And the focused element is "Status C" "link" in the "regularscenario" "region"
    # Loop the subpanel links wand the menu item with up and down.
    Then I press the down key
    And the focused element is "Status D" "link" in the "regularscenario" "region"
    And I press the down key
    And the focused element is "Another subpanel" "menuitem" in the "regularscenario" "region"
    And I press the down key
    And the focused element is "Status C" "link" in the "regularscenario" "region"
    And I press the down key
    And the focused element is "Status D" "link" in the "regularscenario" "region"
    And I press the up key
    And the focused element is "Status C" "link" in the "regularscenario" "region"
    And I press the up key
    And the focused element is "Another subpanel" "menuitem" in the "regularscenario" "region"
    # Use up in the item to close the panel.
    And I press the up key
    And I should not see "Status A" in the "regularscenario" "region"
    And I should not see "Status B" in the "regularscenario" "region"
    And I should not see "Status C" in the "regularscenario" "region"
    And I should not see "Status D" in the "regularscenario" "region"
    And the focused element is "Subpanel example" "menuitem" in the "regularscenario" "region"
    # Enter the panel and select the second link.
    And I press the enter key
    And I press the down key
    And the focused element is "Status B" "link" in the "regularscenario" "region"
    And I press the enter key
    And I should see "Foo param value: Beetle" in the "paramcheck" "region"

  Scenario: action menu subpanels can display optional icons in the menu item
    Given I click on "Actions menu" "button" in the "regularscenario" "region"
    And "Locked icon" "icon" should not exist in the "regularscenario" "region"
    And "Message icon" "icon" should not exist in the "regularscenario" "region"
    And I click on "Actions menu" "button" in the "menuleft" "region"
    And "Locked icon" "icon" should not exist in the "menuleft" "region"
    And "Message icon" "icon" should not exist in the "menuleft" "region"
    When I click on "Actions menu" "button" in the "itemicon" "region"
    Then "Locked icon" "icon" should exist in the "itemicon" "region"
    And "Message icon" "icon" should exist in the "itemicon" "region"
    And I click on "Actions menu" "button" in the "itemiconleft" "region"
    And "Locked icon" "icon" should exist in the "itemiconleft" "region"
    And "Message icon" "icon" should exist in the "itemiconleft" "region"

  @accessibility
  Scenario: User can browse the subpanels using keys in a drawer action menu
    Given I click on "Actions menu" "button" in the "drawersimulation" "region"
    # Go to the seconds subpanel and open it with enter.
    And I press the down key
    And I press the down key
    And I press the down key
    And I press the down key
    And the focused element is "Another subpanel" "menuitem" in the "drawersimulation" "region"
    And I press the enter key
    When I should not see "Status A" in the "drawersimulation" "region"
    And I should not see "Status B" in the "drawersimulation" "region"
    And I should see "Status C" in the "drawersimulation" "region"
    And I should see "Status D" in the "drawersimulation" "region"
    And the focused element is "Status C" "link" in the "drawersimulation" "region"
    # Loop the subpanel links wand the menu item with up and down.
    Then I press the down key
    And the focused element is "Status D" "link" in the "drawersimulation" "region"
    And I press the down key
    And the focused element is "Another subpanel" "menuitem" in the "drawersimulation" "region"
    And I press the down key
    And the focused element is "Status C" "link" in the "drawersimulation" "region"
    And I press the down key
    And the focused element is "Status D" "link" in the "drawersimulation" "region"
    And I press the up key
    And the focused element is "Status C" "link" in the "drawersimulation" "region"
    And I press the up key
    And the focused element is "Another subpanel" "menuitem" in the "drawersimulation" "region"
    # Use up in the item to close the panel.
    And I press the up key
    And I should not see "Status A" in the "drawersimulation" "region"
    And I should not see "Status B" in the "drawersimulation" "region"
    And I should not see "Status C" in the "drawersimulation" "region"
    And I should not see "Status D" in the "drawersimulation" "region"
    And the focused element is "Subpanel example" "menuitem" in the "drawersimulation" "region"
    And the page should meet accessibility standards with "wcag143" extra tests
    # Enter the panel and select the second link.
    And I press the enter key
    And I press the down key
    And the focused element is "Status B" "link" in the "drawersimulation" "region"
    And I press the enter key
    And I should see "Foo param value: Beetle" in the "paramcheck" "region"

  Scenario: User can browse the menu using the WCAG recommended compount keyboard navigation
    Given I click on "Actions menu" "button" in the "regularscenario" "region"
    And I press the down key
    And I press the down key
    And I press the down key
    And the focused element is "Subpanel example" "menuitem" in the "regularscenario" "region"
    When I press the tab key
    And the focused element is "Status A" "link" in the "regularscenario" "region"
    And I should see "Status A" in the "regularscenario" "region"
    And I should see "Status B" in the "regularscenario" "region"
    And I press the down key
    Then I press the shift tab key
    And the focused element is "Subpanel example" "menuitem" in the "regularscenario" "region"
    And I should not see "Status A" in the "regularscenario" "region"
    And I should not see "Status B" in the "regularscenario" "region"
    And I press the enter key
    And the focused element is "Status A" "link" in the "regularscenario" "region"
    And I should see "Status A" in the "regularscenario" "region"
    And I should see "Status B" in the "regularscenario" "region"
    And I press the escape key
    And the focused element is "Subpanel example" "menuitem" in the "regularscenario" "region"
    And I should not see "Status A" in the "regularscenario" "region"
    And I should not see "Status B" in the "regularscenario" "region"
