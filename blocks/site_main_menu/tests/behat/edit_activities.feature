@block @block_main_menu
Feature: Edit activities in main menu block
  In order to use main menu block
  As an admin
  I need to add and edit activities there

  @javascript
  Scenario: Edit name of acitivity in-place in site main menu block
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" node in "Front page settings"
    When I add a "Forum" to section "0" and I fill the form with:
      | Forum name | My forum name |
    And I click on "Edit title" "link" in the "//div[contains(@class,'block_site_main_menu')]//li[contains(.,'My forum name')]" "xpath_element"
    And I set the field "New name for activity My forum name" to "New forum name"
    And I press key "13" in the field "New name for activity My forum name"
    Then I should not see "My forum name"
    And I should see "New forum name"
    And I follow "New forum name"
    And I should not see "My forum name"
    And I should see "New forum name"
