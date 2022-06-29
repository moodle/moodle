@core @core_tag
Feature: Browsing tagged items
  In order to search by tag
  As a user
  I need to be able to browse tagged items

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             | interests |
      | user1    | User      | 1        | user1@example.com | Cat       |
      | user2    | User      | 2        | user1@example.com | Cat, Dog  |
      | user3    | User      | 3        | user1@example.com | Dog       |
    And the following "courses" exist:
      | fullname  | shortname | tags     |
      | Course 1  | c1        | Cat, Dog |
      | Course 2  | c2        | Cat      |
      | Course 3  | c3        | Cat      |
      | Course 4  | c4        | Cat      |
      | Course 5  | c5        | Cat      |
      | Course 6  | c6        | Cat      |
      | Course 7  | c7        | Cat      |

  Scenario: Browse tag index with javascript disabled
    When I log in as "user1"
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    # TODO MDL-57120 "Tags" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I click on "Tags" "link" in the "Navigation" "block"
    And I follow "Cat"
    Then I should see "Courses" in the ".tag-index-items h3" "css_element"
    And I should see "User 1" in the "#tagarea-core-user" "css_element"
    And I should see "Course 7"
    And I should see "Course 3"
    And I should not see "Course 2"
    And I should not see "Course 1"
    And I should see "User 1" in the "#tagarea-core-user" "css_element"
    And I should see "User 2"
    And I should not see "User 3"
    And I click on "More" "link" in the "#tagarea-core-course" "css_element"
    And I should see "Courses" in the "#tagarea-core-course" "css_element"
    And "#tagarea-core-user" "css_element" should not exist
    And I should not see "Course 7"
    And I should not see "Course 3"
    And I should see "Course 2"
    And I should see "Course 1"
    And I click on "Back" "link" in the "#tagarea-core-course" "css_element"
    And I should see "Courses" in the ".tag-index-items h3" "css_element"
    And "#tagarea-core-user" "css_element" should not exist
    And I should see "Course 7"
    And I should see "Course 3"
    And I should not see "Course 2"
    And I should not see "Course 1"
    And I follow "Show only tagged Courses"
    And I should see "Courses tagged with \"Cat\""
    And "#tagarea-core-user" "css_element" should not exist
    And I should see "Course 7"
    And I should see "Course 3"
    And I should see "Course 2"
    And I should see "Course 1"
    And I follow "Back to all items tagged with \"Cat\""
    And I should see "Courses" in the "#tagarea-core-course" "css_element"
    And I should see "User interests" in the "#tagarea-core-user" "css_element"
    And I should see "Course 7"
    And I should see "Course 3"
    And I should not see "Course2"
    And I should not see "Course1"
    And I log out

  @javascript
  Scenario: Browse tag index with javascript enabled
    When I log in as "user1"
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    # TODO MDL-57120 "Tags" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Tags" "link" in the "Navigation" "block"
    And I follow "Cat"
    Then I should see "Courses" in the "#tagarea-core-course" "css_element"
    And I should see "User interests" in the "#tagarea-core-user" "css_element"
    And I should see "Course 7"
    And I should see "Course 3"
    And I should not see "Course 2"
    And I should not see "Course 1"
    And I click on "More" "link" in the "#tagarea-core-course" "css_element"
    And I should see "Courses" in the "#tagarea-core-course" "css_element"
    And I should see "User interests" in the "#tagarea-core-user" "css_element"
    And I should not see "Course 7"
    And I should not see "Course 3"
    And I should see "Course 2"
    And I should see "Course 1"
    And I click on "Back" "link" in the "#tagarea-core-course" "css_element"
    And I should see "Courses" in the "#tagarea-core-course" "css_element"
    And I should see "User interests" in the "#tagarea-core-user" "css_element"
    And I should see "Course 7"
    And I should see "Course 3"
    And I should not see "Course 2"
    And I should not see "Course 1"
    And I follow "Show only tagged Courses"
    And I should see "Courses" in the "#tagarea-core-course" "css_element"
    And "#tagarea-core-user" "css_element" should not exist
    And I should see "Course 7"
    And I should see "Course 3"
    And I should see "Course 2"
    And I should see "Course 1"
    And I follow "Back to all items tagged with \"Cat\""
    And I should see "Courses" in the "#tagarea-core-course" "css_element"
    And I should see "User interests" in the "#tagarea-core-user" "css_element"
    And I should see "Course 7"
    And I should see "Course 3"
    And I should not see "Course2"
    And I should not see "Course1"
    And I log out
