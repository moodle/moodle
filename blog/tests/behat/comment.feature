@core @core_blog
Feature: Comment on a blog entry
  In order to respond to a blog post
  As a user
  I need to be able to comment on a blog entry

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | testuser | Test | User | moodle@example.com |
      | testuser2 | Test2 | User2 | moodle2@example.com |
    And the following "core_blog > entries" exist:
      | subject               | body                     | user     |
      | Blog post from user 1 | User 1 blog post content | testuser |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    # TODO MDL-57120 "Site blogs" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I configure the "Navigation" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Commenting on my own blog entry
    Given I am on site homepage
    And I log in as "testuser"
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Site blogs" "link" in the "Navigation" "block"
    And I follow "Blog post from user 1"
    And I should see "User 1 blog post content"
    And I follow "Comments (0)"
    When I set the field "content" to "$My own >nasty< \"string\"!"
    And I follow "Save comment"
    Then I should see "$My own >nasty< \"string\"!"
    And I set the field "content" to "Another $Nasty <string?>"
    And I follow "Save comment"
    And I should see "Comments (2)" in the ".comment-link" "css_element"

  @javascript
  Scenario: Deleting my own comment
    Given I am on site homepage
    And I log in as "testuser"
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Site blogs" "link" in the "Navigation" "block"
    And I follow "Blog post from user 1"
    And I should see "User 1 blog post content"
    And I follow "Comments (0)"
    And I set the field "content" to "$My own >nasty< \"string\"!"
    And I follow "Save comment"
    When I click on ".comment-delete a" "css_element"
    # Waiting for the animation to finish.
    And I wait "4" seconds
    Then I should not see "$My own >nasty< \"string\"!"
    And I follow "Blog post from user 1"
    And I click on ".comment-link" "css_element"
    And I should not see "$My own >nasty< \"string\"!"
    And I should see "Comments (0)" in the ".comment-link" "css_element"

  @javascript
  Scenario: Commenting on someone's blog post
    Given I am on site homepage
    And I log in as "testuser2"
    And I am on site homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Site blogs" "link" in the "Navigation" "block"
    And I follow "Blog post from user 1"
    When I follow "Comments (0)"
    And I set the field "content" to "$My own >nasty< \"string\"!"
    And I follow "Save comment"
    Then I should see "$My own >nasty< \"string\"!"
