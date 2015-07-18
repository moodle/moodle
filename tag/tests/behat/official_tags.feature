@core @core_tag
Feature: Manager can add official tags and change the tag type of existing tags
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
      | name | tagtype  |
      | Tag0 | default  |
      | Tag1 | default  |
      | Tag2 | default  |
      | Tag3 | official |

  Scenario: Adding official tags
    When I log in as "manager1"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    Then "Make official" "link" should exist in the "Tag0" "table_row"
    And "Make official" "link" should exist in the "Tag1" "table_row"
    And "Make official" "link" should exist in the "Tag2" "table_row"
    And "Remove from official tags" "link" should exist in the "Tag3" "table_row"
    And I set the following fields to these values:
      | Add official tags | Tag1,TAG2,Tag3,Tag4,Tag5 |
    And I press "Add official tags"
    And I should see "Official tag(s) added"
    # No changes to Tag0
    And "Make official" "link" should exist in the "Tag0" "table_row"
    # Tag1 was already present, now it is official
    And "Remove from official" "link" should exist in the "Tag1" "table_row"
    # Tag2 was already present, now it is official. It was not renamed to TAG2
    And "Remove from official" "link" should exist in the "Tag2" "table_row"
    And I should not see "TAG2"
    # Tag3 was already present and it already was official
    And "Remove from official tags" "link" should exist in the "Tag3" "table_row"
    # Tag4 and Tag5 were added as official
    And "Remove from official tags" "link" should exist in the "Tag4" "table_row"
    And "Remove from official tags" "link" should exist in the "Tag5" "table_row"
    And I log out

  Scenario: Changing tag type with javascript disabled
    When I log in as "manager1"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    And I click on "Make official" "link" in the "Tag0" "table_row"
    And I should see "Tag type changed"
    And I click on "Make official" "link" in the "Tag1" "table_row"
    And I should see "Tag type changed"
    And I click on "Remove from official tags" "link" in the "Tag0" "table_row"
    And I should see "Tag type changed"
    And I click on "Remove from official tags" "link" in the "Tag3" "table_row"
    And I should see "Tag type changed"
    Then "Make official" "link" should exist in the "Tag0" "table_row"
    And "Remove from official tags" "link" should exist in the "Tag1" "table_row"
    And "Make official" "link" should exist in the "Tag2" "table_row"
    And "Make official" "link" should exist in the "Tag3" "table_row"
    And I log out

  @javascript
  Scenario: Changing tag type with javascript enabled
    When I log in as "manager1"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    And I click on "Make official" "link" in the "Tag0" "table_row"
    And I click on "Make official" "link" in the "Tag1" "table_row"
    And I wait until "//tr[contains(.,'Tag0')]//a[contains(@title,'Remove from official tags')]" "xpath_element" exists
    And I wait until "//tr[contains(.,'Tag1')]//a[contains(@title,'Remove from official tags')]" "xpath_element" exists
    And I click on "Remove from official tags" "link" in the "Tag0" "table_row"
    And I click on "Remove from official tags" "link" in the "Tag3" "table_row"
    And I wait until "//tr[contains(.,'Tag0')]//a[contains(@title,'Make official')]" "xpath_element" exists
    And I wait until "//tr[contains(.,'Tag3')]//a[contains(@title,'Make official')]" "xpath_element" exists
    Then "Make official" "link" should exist in the "Tag0" "table_row"
    And "Remove from official tags" "link" should exist in the "Tag1" "table_row"
    And "Make official" "link" should exist in the "Tag2" "table_row"
    And "Make official" "link" should exist in the "Tag3" "table_row"
    And I follow "Manage tags"
    And "Make official" "link" should exist in the "Tag0" "table_row"
    And "Remove from official tags" "link" should exist in the "Tag1" "table_row"
    And "Make official" "link" should exist in the "Tag2" "table_row"
    And "Make official" "link" should exist in the "Tag3" "table_row"
    And I log out

  Scenario: Changing tag type in edit form
    When I log in as "manager1"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    And I click on "Edit this tag" "link" in the "Tag1" "table_row"
    And I set the following fields to these values:
      | Official | 1 |
    And I press "Update"
    Then "Remove from official tags" "link" should exist in the "Tag1" "table_row"
    And I click on "Edit this tag" "link" in the "Tag1" "table_row"
    And I set the following fields to these values:
      | Official | 0 |
    And I press "Update"
    And "Make official" "link" should exist in the "Tag1" "table_row"
    And I log out
