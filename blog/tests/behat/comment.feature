@core @core_blog
Feature: Comment on a blog entry
  In order to respond to a blog post
  As a user
  I need to be able to comment on a blog entry

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | testuser | Test | User | moodle@moodlemoodle.com |
      | testuser2 | Test2 | User2 | moodle2@moodlemoodle.com |
    And I log in as "testuser"
    And I am on homepage
    And I expand "My profile" node
    And I expand "Blogs" node
    And I follow "Add a new entry"
    And I fill the moodle form with:
      | Entry title | Blog post from user 1 |
      | Blog entry body | User 1 blog post content |
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Commenting on my own blog entry
    Given I am on homepage
    And I log in as "testuser"
    And I am on homepage
    And I expand "My profile" node
    And I expand "Blogs" node
    And I follow "View all of my entries"
    And I follow "Blog post from user 1"
    And I should see "User 1 blog post content"
    And I follow "Comments (0)"
    When I fill in "content" with "$My own >nasty< \"string\"!"
    And I follow "Save comment"
    And I wait "4" seconds
    Then I should see "$My own >nasty< \"string\"!"
    And I fill in "content" with "Another $Nasty <string?>"
    And I follow "Save comment"
    And I wait "4" seconds
    And I should see "Comments (2)" in the ".comment-link" "css_element"

  @javascript
  Scenario: Deleting my own comment
    Given I am on homepage
    And I log in as "testuser"
    And I am on homepage
    And I expand "My profile" node
    And I expand "Blogs" node
    And I follow "View all of my entries"
    And I follow "Blog post from user 1"
    And I should see "User 1 blog post content"
    And I follow "Comments (0)"
    And I fill in "content" with "$My own >nasty< \"string\"!"
    And I follow "Save comment"
    And I wait "4" seconds
    When I click on ".comment-delete a" "css_element"
    And I wait "4" seconds
    Then I should not see "$My own >nasty< \"string\"!"
    And I follow "Blog post from user 1"
    And I click on ".comment-link" "css_element"
    And I should not see "$My own >nasty< \"string\"!"
    And I should see "Comments (0)" in the ".comment-link" "css_element"

  @javascript
  Scenario: Commenting on someone's blog post
    Given I am on homepage
    And I log in as "testuser2"
    And I am on homepage
    And I expand "Site pages" node
    And I follow "Site blogs"
    And I follow "Blog post from user 1"
    When I follow "Comments (0)"
    And I fill in "content" with "$My own >nasty< \"string\"!"
    And I follow "Save comment"
    And I wait "4" seconds
    Then I should see "$My own >nasty< \"string\"!"
