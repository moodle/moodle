@core @core_tag
Feature: Manager can add standard tags and change the tag type of existing tags
  In order to use tags
  As a manage
  I need to be able to change tag type

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | Manager   | 1        | manager1@example.com |
      | user1    | User      | 1        | user1@example.com    |
    And the following "system role assigns" exist:
      | user     | course               | role    |
      | manager1 | Acceptance test site | manager |
    And the following "tags" exist:
      | name | isstandard |
      | Tag0 | 0          |
      | Tag1 | 0          |
      | Tag2 | 0          |
      | Tag3 | 1          |

  @javascript
  Scenario: Adding standard tags
    When I log in as "manager1"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    And I follow "Default collection"
    Then "Make standard" "link" should exist in the "Tag0" "table_row"
    And "Make standard" "link" should exist in the "Tag1" "table_row"
    And "Make standard" "link" should exist in the "Tag2" "table_row"
    And "Remove from standard tags" "link" should exist in the "Tag3" "table_row"
    And I follow "Add standard tags"
    And I set the field "Enter comma-separated list of new tags" to "Tag1,TAG2,Tag3,Tag4,Tag5"
    And I press "Continue"
    And I should see "Standard tag(s) added"
    # No changes to Tag0
    And "Make standard" "link" should exist in the "Tag0" "table_row"
    # Tag1 was already present, now it is standard
    And "Remove from standard" "link" should exist in the "Tag1" "table_row"
    # Tag2 was already present, now it is standard. It was not renamed to TAG2
    And "Remove from standard" "link" should exist in the "Tag2" "table_row"
    And I should not see "TAG2"
    # Tag3 was already present and it already was standard
    And "Remove from standard tags" "link" should exist in the "Tag3" "table_row"
    # Tag4 and Tag5 were added as standard
    And "Remove from standard tags" "link" should exist in the "Tag4" "table_row"
    And "Remove from standard tags" "link" should exist in the "Tag5" "table_row"
    And I log out

  @javascript
  Scenario: Changing tag isstandard
    When I log in as "manager1"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    And I follow "Default collection"
    And I click on "Make standard" "link" in the "Tag0" "table_row"
    And I click on "Make standard" "link" in the "Tag1" "table_row"
    And I wait until "//tr[contains(.,'Tag0')]//a[contains(@title,'Remove from standard tags')]" "xpath_element" exists
    And I wait until "//tr[contains(.,'Tag1')]//a[contains(@title,'Remove from standard tags')]" "xpath_element" exists
    And I click on "Remove from standard tags" "link" in the "Tag0" "table_row"
    And I click on "Remove from standard tags" "link" in the "Tag3" "table_row"
    And I wait until "//tr[contains(.,'Tag0')]//a[contains(@title,'Make standard')]" "xpath_element" exists
    And I wait until "//tr[contains(.,'Tag3')]//a[contains(@title,'Make standard')]" "xpath_element" exists
    Then "Make standard" "link" should exist in the "Tag0" "table_row"
    And "Remove from standard tags" "link" should exist in the "Tag1" "table_row"
    And "Make standard" "link" should exist in the "Tag2" "table_row"
    And "Make standard" "link" should exist in the "Tag3" "table_row"
    And I follow "Default collection"
    And "Make standard" "link" should exist in the "Tag0" "table_row"
    And "Remove from standard tags" "link" should exist in the "Tag1" "table_row"
    And "Make standard" "link" should exist in the "Tag2" "table_row"
    And "Make standard" "link" should exist in the "Tag3" "table_row"
    And I log out

  Scenario: Changing tag isstandard in edit form
    When I log in as "manager1"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    And I follow "Default collection"
    And I click on "Edit this tag" "link" in the "Tag1" "table_row"
    And I set the following fields to these values:
      | Standard | 1 |
    And I press "Update"
    Then "Remove from standard tags" "link" should exist in the "Tag1" "table_row"
    And I click on "Edit this tag" "link" in the "Tag1" "table_row"
    And I set the following fields to these values:
      | Standard | 0 |
    And I press "Update"
    And "Make standard" "link" should exist in the "Tag1" "table_row"
    And I log out

  @javascript
  Scenario: Changing standard tags property of tag area
    When I log in as "manager1"
    And I open my profile in edit mode
    And I expand all fieldsets
    And I should not see "Manage standard tags"
    And I set the following fields to these values:
      | List of interests | Tag3 , Tag2 |
    And I press "Update profile"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    And I click on "Change standard tag usage" "link" in the "//table[contains(@class,'tag-areas-table')]//tr[contains(.,'User interests')]" "xpath_element"
    And the field "Change showing standard tags in area User interests" matches value "Don't suggest"
    And I set the field "Change showing standard tags in area User interests" to "Suggest"
    And I follow "Profile" in the user menu
    And I follow "Edit profile"
    And I expand all fieldsets
    And I should see "Manage standard tags"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    And I click on "Change standard tag usage" "link" in the "//table[contains(@class,'tag-areas-table')]//tr[contains(.,'User interests')]" "xpath_element"
    And the field "Change showing standard tags in area User interests" matches value "Suggest"
    And I set the field "Change showing standard tags in area User interests" to "Force"
    And I follow "Profile" in the user menu
    And I should see "Tag3"
    And I should see "Tag2"
    And I follow "Edit profile"
    And I expand all fieldsets
    And I should see "Manage standard tags"
    And I press "Update profile"
    # Non-standard tags were automatically removed on form save.
    And I should see "Tag3"
    And I should not see "Tag2"
    And I log out
