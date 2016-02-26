@core @core_tag @javascript
Feature: Managers can create and manage tag collections
  In order to use tags effectively
  As a manager
  I need to be able to manage tag collections

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
      | Tag0 | 1          |
      | Tag1 | 1          |
      | Tag2 | 1          |
      | Tag3 | 1          |
    And I log in as "manager1"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    And I follow "Add tag collection"
    And I set the following fields to these values:
      | Name | Hobbies |
    And I press "Create"

  Scenario: Adding tag collections
    When I follow "Hobbies"
    Then I should see "Nothing to display"
    And I log out

  Scenario: Editing tag collections
    When I click on "Edit tag collection name" "link" in the "//table[contains(@class,'tag-collections-table')]//tr[contains(.,'Hobbies')]" "xpath_element"
    And I set the field "New name for tag collection Hobbies" to "Newname"
    And I press key "13" in the field "New name for tag collection Hobbies"
    Then I should not see "Hobbies"
    And I should see "Newname"
    And I log out

  Scenario: Resorting tag collections
    When I follow "Add tag collection"
    And I set the following fields to these values:
      | Name | Blogging |
    And I press "Create"
    Then "Blogging" "link" should appear after "Hobbies" "link"
    And I click on "Move up" "link" in the "Blogging" "table_row"
    And "Blogging" "link" should appear before "Hobbies" "link"
    And I click on "Move down" "link" in the "Blogging" "table_row"
    And "Blogging" "link" should appear after "Hobbies" "link"
    And I log out

  Scenario: Deleting tag collections
    When I click on "Delete" "link" in the "Hobbies" "table_row"
    Then I should see "Are you sure you want to delete tag collection \"Hobbies\"?"
    And I press "Yes"
    And I should not see "Hobbies"
    And I log out

  Scenario: Assigning tag area to tag collection
    And I should see "User interests" in the "//table[contains(@class,'tag-collections-table')]//tr[contains(.,'Default collection')]" "xpath_element"
    And I should not see "User interests" in the "//table[contains(@class,'tag-collections-table')]//tr[contains(.,'Hobbies')]" "xpath_element"
    When I click on "Change tag collection" "link" in the "//table[contains(@class,'tag-areas-table')]//tr[contains(.,'User interests')]" "xpath_element"
    And I set the field "Change tag collection of area User interests" to "Hobbies"
    Then I should not see "User interests" in the "//table[contains(@class,'tag-collections-table')]//tr[contains(.,'Default collection')]" "xpath_element"
    And I should see "User interests" in the "//table[contains(@class,'tag-collections-table')]//tr[contains(.,'Hobbies')]" "xpath_element"
    And I should see "Hobbies" in the "//table[contains(@class,'tag-areas-table')]//tr[contains(.,'User interests')]" "xpath_element"
    And I log out

  Scenario: Disabling tag areas
    When I click on "Disable" "link" in the "//table[contains(@class,'tag-areas-table')]//tr[contains(.,'User interests')]" "xpath_element"
    And I should not see "User interests" in the "table.tag-collections-table" "css_element"
    And I click on "Enable" "link" in the "//table[contains(@class,'tag-areas-table')]//tr[contains(.,'User interests')]" "xpath_element"
    And I should see "User interests" in the "//table[contains(@class,'tag-collections-table')]//tr[contains(.,'Default collection')]" "xpath_element"
    And I log out

  Scenario: Deleting non-empty tag collections
    When I click on "Change tag collection" "link" in the "//table[contains(@class,'tag-areas-table')]//tr[contains(.,'User interests')]" "xpath_element"
    And I set the field "Change tag collection of area User interests" to "Hobbies"
    And I click on "Delete" "link" in the "Hobbies" "table_row"
    Then I should see "Are you sure you want to delete tag collection \"Hobbies\"?"
    And I press "Yes"
    And I should not see "Hobbies"
    And I should see "User interests" in the "//table[contains(@class,'tag-collections-table')]//tr[contains(.,'Default collection')]" "xpath_element"
    And I log out

  Scenario: Moving tags when changing tag collections
    And I follow "Preferences" in the user menu
    And I follow "Edit profile"
    And I expand all fieldsets
    And I set the field "List of interests" to "Swimming, Tag0, Tag3"
    And I press "Update profile"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    When I click on "Change tag collection" "link" in the "//table[contains(@class,'tag-areas-table')]//tr[contains(.,'User interests')]" "xpath_element"
    And I set the field "Change tag collection of area User interests" to "Hobbies"
    And I follow "Hobbies"
    Then I should see "Swimming"
    And I should see "Tag0"
    And I should see "Tag3"
    And I should not see "Tag1"
    And I should not see "Tag2"
    And I follow "Manage tags"
    And I follow "Default collection"
    # Tag "Swimming" was not standard and was moved completely.
    And I should not see "Swimming"
    # Standard tag was not removed.
    And I should see "Tag0"
    And I should see "Tag3"
    And I should see "Tag1"
    And I should see "Tag2"
    And I log out

  Scenario: Creating searchable and non-searchable tag collections
    And I follow "Add tag collection"
    And I set the following fields to these values:
      | Name | Hiddencoll |
      | Searchable | 0 |
    And I press "Create"
    And "Yes" "text" should not exist in the "//table[contains(@class,'tag-collections-table')]//tr[contains(.,'Hiddencoll')]" "xpath_element"
    And I navigate to "Tags" node in "Site pages"
    Then the "Select tag collection" select box should contain "Default collection"
    And the "Select tag collection" select box should contain "Hobbies"
    And the "Select tag collection" select box should not contain "Hiddencoll"
    And I navigate to "Manage tags" node in "Site administration > Appearance"
    And I click on "Change searchable" "link" in the "Hobbies" "table_row"
    And I navigate to "Tags" node in "Site pages"
    And "Select tag collection" "select" should not exist
    And I log out
