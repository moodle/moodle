@core @core_blog @javascript
Feature: Comment on a blog entry
  In order to respond to a blog post
  As a user
  I need to be able to comment on a blog entry

  Background:
    Given the following config values are set as admin:
      | enablemyhome | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | testuser | Test | User | moodle@example.com |
      | testuser2 | Test2 | User2 | moodle2@example.com |
    And the following "core_blog > entries" exist:
      | subject               | body                     | user     |
      | Blog post from user 1 | User 1 blog post content | testuser |

  Scenario: Commenting on my own blog entry
    Given I log in as "testuser"
    And I follow "Profile" in the user menu
    And I follow "View site blog entries"
    And I follow "Blog post from user 1"
    And I should see "User 1 blog post content"
    And I follow "Comments (0)"
    When I set the field "content" to "$My own >nasty< \"string\"!"
    And I follow "Save comment"
    Then I should see "$My own >nasty< \"string\"!"
    And I set the field "content" to "Another $Nasty <string?>"
    And I follow "Save comment"
    And I should see "Comments (2)" in the ".comment-link" "css_element"

  Scenario: Deleting my own comment
    Given I log in as "testuser"
    And I follow "Profile" in the user menu
    And I follow "View site blog entries"
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

  Scenario: Commenting on someone's blog post
    Given I am on site homepage
    And I log in as "testuser2"
    And I am on site homepage
    And I follow "Profile" in the user menu
    And I follow "View site blog entries"
    And I follow "Blog post from user 1"
    When I follow "Comments (0)"
    And I set the field "content" to "$My own >nasty< \"string\"!"
    And I follow "Save comment"
    Then I should see "$My own >nasty< \"string\"!"
