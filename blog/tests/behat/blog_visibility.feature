@core @core_blog
Feature: Blogs can be set to be only visible by the author.
  In order to make blogs personal only
  As a user
  I need to set the blog level to Users can only see their own blogs.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | testuser | Test | User | moodle@example.com |
      | testuser2 | Test2 | User2 | moodle2@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1 |
    And the following "course enrolments" exist:
      | user      | course | role    |
      | testuser  | C1     | student |
      | testuser2 | C1     | student |
    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Blog" node in "Site administration > Appearance"
    And I set the following fields to these values:
      | Blog visibility | Users can only see their own blog |
    And I press "Save changes"
    And I log out

  Scenario: A student can not see another student's blog entries.
    Given I log in as "testuser"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Test2 User2"
    And I should see "Miscellaneous"
    Then I should not see "Blog entries"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I should see "User blog: Test User"
