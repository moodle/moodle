@core @core_blog
Feature: Delete a blog entry
  In order to manage my blog entries
  As a user
  I need to be able to delete entries I no longer wish to appear

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | testuser | Test | User | moodle@example.com |
    And the following "core_blog > entries" exist:
      | subject       | body                     | user     |
      | Blog post one | User 1 blog post content | testuser |
      | Blog post two | User 1 blog post content | testuser |
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
    And I log in as "testuser"
    And I am on site homepage
    And I click on "Site blogs" "link" in the "Navigation" "block"

  Scenario: Delete blog post results in post deleted
    Given I follow "Blog post one"
    And I follow "Delete"
    And I should see "Delete the blog entry 'Blog post one'?"
    When I press "Continue"
    Then I should not see "Blog post one"
    And I should see "Blog post two"

  Scenario: Delete confirmation screen works and allows cancel
    Given I follow "Blog post one"
    When I follow "Delete"
    Then I should see "Delete the blog entry 'Blog post one'?"
    And I press "Cancel"
    And I should see "Blog post one"
    And I should see "Blog post two"
