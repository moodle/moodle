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

  Scenario: Navigate submenu items with mouse
    Given I click on "Actions menu" "button" in the "basicsubmenuexample" "region"
    When I click on "Item 2 with submenu" "menuitem" in the "basicsubmenuexample" "region"
    Then I should see "Alpha" in the "basicsubmenuexample" "region"
    And I should see "Beta" in the "basicsubmenuexample" "region"
    And I should see "Gamma" in the "basicsubmenuexample" "region"

  Scenario: Navigate left-aligned submenu with mouse
    Given I click on "Actions menu" "button" in the "leftsubmenuexample" "region"
    When I click on "Item 2 with submenu" "menuitem" in the "leftsubmenuexample" "region"
    Then I should see "Alpha" in the "leftsubmenuexample" "region"
    And I should see "Beta" in the "leftsubmenuexample" "region"
    And I should see "Gamma" in the "leftsubmenuexample" "region"

  Scenario: Navigate submenu items with keyboard arrow keys
    Given I click on "Actions menu" "button" in the "basicsubmenuexample" "region"
    # Navigate down to "Item 2 with submenu".
    And I press the down key
    And I press the down key
    And the focused element is "Item 2 with submenu" "menuitem" in the "basicsubmenuexample" "region"
    And I should see "Alpha" in the "basicsubmenuexample" "region"
    And I should see "Beta" in the "basicsubmenuexample" "region"
    And I should see "Gamma" in the "basicsubmenuexample" "region"
    # Enter the submenu content.
    When I press the right key
    Then the focused element is "Alpha" "link" in the "basicsubmenuexample" "region"
    And I press the down key
    And the focused element is "Beta" "link" in the "basicsubmenuexample" "region"
    And I press the down key
    And the focused element is "Gamma" "link" in the "basicsubmenuexample" "region"
    # Loop back to the first item.
    And I press the down key
    And the focused element is "Alpha" "link" in the "basicsubmenuexample" "region"
    # Navigate backwards.
    And I press the up key
    And the focused element is "Gamma" "link" in the "basicsubmenuexample" "region"
    # Exit submenu back to the parent menu item.
    And I press the left key
    And the focused element is "Item 2 with submenu" "menuitem" in the "basicsubmenuexample" "region"

  Scenario: Navigate nested submenu containing a subpanel with mouse
    Given I click on "Actions menu" "button" in the "nestedsubmenuexample" "region"
    When I click on "Item 2 with submenu" "menuitem" in the "nestedsubmenuexample" "region"
    Then I should see "Alpha" in the "nestedsubmenuexample" "region"
    And I should see "Beta" in the "nestedsubmenuexample" "region"
    And I should see "More options" in the "nestedsubmenuexample" "region"
    # Open the nested subpanel within the submenu (subpanel without link).
    And I click on "More options" "menuitem" in the "nestedsubmenuexample" "region"
    And I should see "Option 1" in the "nestedsubmenuexample" "region"
    And I should see "Option 2" in the "nestedsubmenuexample" "region"
    And I click on "Actions menu" "button" in the "nestedsubmenuwithlinkexample" "region"
    And I click on "Item 2 with submenu" "menuitem" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Alpha" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Beta" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Gamma" in the "nestedsubmenuwithlinkexample" "region"
    # Subpanel with link just follows the URL.
    And I click on "Gamma" "menuitem" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Dashboard" in the "page-header" "region"

  Scenario: Navigate nested submenu containing a choice list with keyboard
    Given I click on "Actions menu" "button" in the "nestedsubmenuexample" "region"
    # Navigate to "Item 2 with submenu".
    And I press the down key
    And I press the down key
    And the focused element is "Item 2 with submenu" "menuitem" in the "nestedsubmenuexample" "region"
    # Enter the submenu.
    When I press the right key
    Then the focused element is "Alpha" "link" in the "nestedsubmenuexample" "region"
    And I press the down key
    And the focused element is "Beta" "link" in the "nestedsubmenuexample" "region"
    And I press the down key
    And the focused element is "More options" "menuitem" in the "nestedsubmenuexample" "region"
    # Enter the nested choicelist.
    And I press the left key
    And the focused element is "Option 1" "link" in the "nestedsubmenuexample" "region"
    And I press the down key
    And the focused element is "Option 2" "link" in the "nestedsubmenuexample" "region"
    And I press the right key
    And the focused element is "More options" "menuitem" in the "nestedsubmenuexample" "region"
    And I press the up key
    And the focused element is "Beta" "link" in the "nestedsubmenuexample" "region"
    And I press the right key
    And the focused element is "Item 2 with submenu" "menuitem" in the "nestedsubmenuexample" "region"
    And I press the right key
    # Exit the submenu back to the parent menu item.
    And I press the left key
    And the focused element is "Item 2 with submenu" "menuitem" in the "nestedsubmenuexample" "region"

  Scenario: Navigate nested submenu containing a second level submenu with keyboard
    Given I click on "Actions menu" "button" in the "nestedsubmenuwithlinkexample" "region"
    # Navigate to "Item 2 with submenu".
    And I press the down key
    And I press the down key
    And the focused element is "Item 2 with submenu" "menuitem" in the "nestedsubmenuwithlinkexample" "region"
    # Enter the submenu using enter key (subpanel without link).
    When I press the enter key
    Then the focused element is "Alpha" "link" in the "nestedsubmenuwithlinkexample" "region"
    And I press the down key
    And the focused element is "Beta" "link" in the "nestedsubmenuwithlinkexample" "region"
    And I press the down key
    And the focused element is "Gamma" "menuitem" in the "nestedsubmenuwithlinkexample" "region"
    # Enter the nested submenu using directional arrow key.
    And I press the right key
    And the focused element is "Yota" "link" in the "nestedsubmenuwithlinkexample" "region"
    And I press the down key
    And the focused element is "Zeta" "link" in the "nestedsubmenuwithlinkexample" "region"
    And I press the down key
    And the focused element is "Omega" "link" in the "nestedsubmenuwithlinkexample" "region"
    And I press the left key
    And the focused element is "Gamma" "menuitem" in the "nestedsubmenuwithlinkexample" "region"
    # Enter the nested submenu using space key.
    And I press the space key
    And the focused element is "Yota" "link" in the "nestedsubmenuwithlinkexample" "region"
    And I press the escape key
    And the focused element is "Gamma" "link" in the "nestedsubmenuwithlinkexample" "region"
    # Subpanel with link just follows the URL on enter.
    And I press the enter key
    And I should see "Dashboard" in the "page-header" "region"

  Scenario: Submenu items display icons and data attributes when configured
    Given I click on "Actions menu" "button" in the "submenuicons" "region"
    When I click on "Item 2 with submenu" "menuitem" in the "submenuicons" "region"
    Then I should see "Edit" in the "submenuicons" "region"
    And "Edit icon" "icon" should exist in the "submenuicons" "region"
    And I should see "Hide" in the "submenuicons" "region"
    And "Hide icon" "icon" should exist in the "submenuicons" "region"
    And I should see "Delete" in the "submenuicons" "region"
    And "Delete icon" "icon" should exist in the "submenuicons" "region"
    And "[data-foo='bar']" "css_element" should exist in the "submenuicons" "region"

  Scenario: Navigate submenu using WCAG compound keyboard navigation
    Given I click on "Actions menu" "button" in the "nestedsubmenuwithlinkexample" "region"
    And I press the down key
    And I press the down key
    And the focused element is "Item 2 with submenu" "menuitem" in the "nestedsubmenuwithlinkexample" "region"
    # Tab enters the subpanel content.
    When I press the tab key
    Then the focused element is "Alpha" "link" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Alpha" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Beta" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Gamma" in the "nestedsubmenuwithlinkexample" "region"
    And I press the down key
    And I press the down key
    # Tab enters the sub-subpanel content.
    And I press the tab key
    And the focused element is "Yota" "link" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Yota" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Zeta" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Omega" in the "nestedsubmenuwithlinkexample" "region"
    # Shift-Tab returns to the main subpanel item and closes the sub-subpanel.
    And I press the shift tab key
    And the focused element is "Gamma" "menuitem" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Alpha" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Beta" in the "nestedsubmenuwithlinkexample" "region"
    And I should see "Gamma" in the "nestedsubmenuwithlinkexample" "region"
    And I should not see "Yota" in the "nestedsubmenuwithlinkexample" "region"
    And I should not see "Zeta" in the "nestedsubmenuwithlinkexample" "region"
    And I should not see "Omega" in the "nestedsubmenuwithlinkexample" "region"
    # Shift-Tab again returns to the main menu item and closes the subpanel.
    And I press the shift tab key
    And the focused element is "Item 2 with submenu" "menuitem" in the "nestedsubmenuwithlinkexample" "region"
    And I should not see "Alpha" in the "nestedsubmenuwithlinkexample" "region"
    And I should not see "Beta" in the "nestedsubmenuwithlinkexample" "region"
    And I should not see "Gamma" in the "nestedsubmenuwithlinkexample" "region"

  @accessibility
  Scenario: Submenu meets accessibility standards
    Given I click on "Actions menu" "button" in the "basicsubmenuexample" "region"
    # Navigate to the submenu and open it.
    And I press the down key
    And I press the down key
    And the focused element is "Item 2 with submenu" "menuitem" in the "basicsubmenuexample" "region"
    And I should see "Alpha" in the "basicsubmenuexample" "region"
    Then the page should meet accessibility standards with "wcag143" extra tests

  Scenario: Navigate submenu in mobile viewport
    Given I change the viewport size to "mobile"
    And I click on "Actions menu" "button" in the "basicsubmenuexample" "region"
    And I should not see "Alpha" in the "basicsubmenuexample" "region"
    And I should not see "Beta" in the "basicsubmenuexample" "region"
    And I should not see "Gamma" in the "basicsubmenuexample" "region"
    When I click on "Item 2 with submenu" "menuitem" in the "basicsubmenuexample" "region"
    Then I should see "Alpha" in the "basicsubmenuexample" "region"
    And I should see "Beta" in the "basicsubmenuexample" "region"
    And I should see "Gamma" in the "basicsubmenuexample" "region"
    # In mobile, clicking the menu item again toggles the subpanel closed.
    When I click on "Item 2 with submenu" "menuitem" in the "basicsubmenuexample" "region"
    Then I should not see "Alpha" in the "basicsubmenuexample" "region"
    And I should not see "Beta" in the "basicsubmenuexample" "region"
    And I should not see "Gamma" in the "basicsubmenuexample" "region"
