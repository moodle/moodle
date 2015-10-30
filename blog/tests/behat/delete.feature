@core @core_blog
Feature: Delete a blog entry
  In order to manage my blog entries
  As a user
  I need to be able to delete entries I no longer wish to appear

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | testuser | Test | User | moodle@example.com |
    And I log in as "testuser"
    And I expand "Site pages" node
    And I follow "Site blogs"
    And I follow "Add a new entry"
    And I set the following fields to these values:
      | Entry title | Blog post one |
      | Blog entry body | User 1 blog post content |
    And I press "Save changes"
    And I follow "Add a new entry"
    And I set the following fields to these values:
      | Entry title | Blog post two |
      | Blog entry body | User 1 blog post content |
    And I press "Save changes"
    And I am on site homepage
    And I expand "Site pages" node
    And I follow "Site blogs"

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

