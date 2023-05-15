@core @core_tag
Feature: Users can flag tags and manager can reset flags
  In order to use tags
  As a user
  I need to be able to flag the tag as inappropriate

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | interests                 |
      | manager1 | Manager   | 1        | manager1@example.com |                           |
      | user1    | User      | 1        | user1@example.com    | Nicetag, Badtag, Sweartag |
      | user2    | User      | 2        | user2@example.com    |                           |
      | user3    | User      | 3        | user3@example.com    |                           |
    And the following "system role assigns" exist:
      | user     | course               | role    |
      | manager1 | Acceptance test site | manager |
    And the following "tags" exist:
      | name         | isstandard |
      | Neverusedtag | 1          |
    And the following "role capability" exists:
      | role                         | user  |
      | moodle/site:viewparticipants | allow |
      | moodle/user:viewdetails      | allow |
    And I log in as "user2"
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    # TODO MDL-57120 site "Tags" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Tags" "link" in the "Navigation" "block"
    And I follow "Nicetag"
    And I follow "User 1"
    And I follow "Badtag"
    And I follow "Flag as inappropriate"
    And I should see "The person responsible will be notified"
    And I am on homepage
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    # TODO MDL-57120 site "Tags" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Tags" "link" in the "Navigation" "block"
    And I follow "Nicetag"
    And I follow "User 1"
    And I follow "Sweartag"
    And I follow "Flag as inappropriate"
    And I should see "The person responsible will be notified"
    And I log out
    And I log in as "user3"
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    # TODO MDL-57120 site "Tags" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Tags" "link" in the "Navigation" "block"
    And I follow "Nicetag"
    And I follow "User 1"
    And I follow "Sweartag"
    And I follow "Flag as inappropriate"
    And I should see "The person responsible will be notified"
    And I log out

  @javascript
  Scenario: Managing tag flags
    When I log in as "manager1"
    And I navigate to "Appearance > Manage tags" in site administration
    And I follow "Default collection"
    Then "Sweartag" "link" should appear before "Badtag" "link"
    And "Badtag" "link" should appear before "Nicetag" "link"
    And "(2)" "text" should exist in the "//tr[contains(.,'Sweartag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And "(1)" "text" should exist in the "//tr[contains(.,'Badtag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And "(" "text" should not exist in the "//tr[contains(.,'Nicetag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And "(" "text" should not exist in the "//tr[contains(.,'Neverusedtag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And I click on "Reset flag" "link" in the "Sweartag" "table_row"
    And I click on "Reset flag" "link" in the "Badtag" "table_row"
    And I wait until "//tr[contains(.,'Sweartag')]//a[contains(@title,'Flag as inappropriate')]" "xpath_element" exists
    And I click on "Flag as inappropriate" "link" in the "Sweartag" "table_row"
    And I click on "Flag as inappropriate" "link" in the "Nicetag" "table_row"
    And "(1)" "text" should exist in the "//tr[contains(.,'Sweartag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And "(1)" "text" should exist in the "//tr[contains(.,'Nicetag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And "(" "text" should not exist in the "//tr[contains(.,'Badtag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And "(" "text" should not exist in the "//tr[contains(.,'Neverusedtag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And I navigate to "Appearance > Manage tags" in site administration
    And I follow "Default collection"
    And "Nicetag" "link" should appear before "Sweartag" "link"
    And "Sweartag" "link" should appear before "Badtag" "link"
    And "(1)" "text" should exist in the "//tr[contains(.,'Sweartag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And "(1)" "text" should exist in the "//tr[contains(.,'Nicetag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And "(" "text" should not exist in the "//tr[contains(.,'Badtag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And "(" "text" should not exist in the "//tr[contains(.,'Neverusedtag')]//td[contains(@class,'col-flag')]" "xpath_element"
    And I log out
