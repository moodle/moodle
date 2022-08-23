@mod @mod_forum
Feature: New discussions and discussions with recently added replies are displayed first
  In order to use forum as a discussion tool
  As a user
  I need to see currently active discussions first

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname  | email                 |
      | teacher1  | Teacher   | 1         | teacher1@example.com  |
      | student1  | Student   | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname  | shortname | category  |
      | Course 1  | C1        | 0         |
    And the following "course enrolments" exist:
      | user      | course    | role            |
      | teacher1  | C1        | editingteacher  |
      | student1  | C1        | student         |
    And the following "activity" exists:
      | course   | C1                   |
      | activity | forum                |
      | name     | Course general forum |
    #
    # Add three posts into the blog.
    #
    And the following forum discussions exist in course "Course 1":
      | user     | forum                | name         | message                 | timemodified      |
      | student1 | Course general forum | Forum post 1 | This is the first post  | ##now +1 second## |
      | student1 | Course general forum | Forum post 2 | This is the second post | ##now +2 second## |
      | student1 | Course general forum | Forum post 3 | This is the third post  | ##now +3 second## |

  #
  # We need javascript/wait to prevent creation of the posts in the same second. The threads
  # would then ignore each other in the prev/next navigation as the Forum is unable to compute
  # the correct order.
  #
  @javascript
  Scenario: Replying to a forum post or editing it puts the discussion to the front
    Given I am on the "Course general forum" "forum activity" page logged in as student1
    #
    # Edit one of the forum posts.
    #
    And I follow "Forum post 2"
    And I click on "Edit" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' forumpost ')][contains(., 'Forum post 2')]" "xpath_element"
    And I set the following fields to these values:
      | Subject | Edited forum post 2     |
    And I press "Save changes"
    And I wait to be redirected
    #
    # Reply to another forum post.
    #
    And I am on the "Course general forum" "forum activity" page logged in as teacher1
    And I follow "Forum post 1"
    And I reply "Forum post 1" post from "Course general forum" forum with:
      | Message | Reply to the first post |
    And I am on the "Course general forum" "forum activity" page
    #
    # Make sure the order of the forum posts is as expected, with most recent new participation first (ie excluding edits).
    #
    Then I should see "Forum post 1" in the "//tr[contains(concat(' ', normalize-space(@class), ' '), ' discussion ')][position()=1]" "xpath_element"
    And I should see "Forum post 3" in the "//tr[contains(concat(' ', normalize-space(@class), ' '), ' discussion ')][position()=2]" "xpath_element"
    And I should see "Edited forum post 2" in the "//tr[contains(concat(' ', normalize-space(@class), ' '), ' discussion ')][position()=3]" "xpath_element"
    #
    # Make sure the next/prev navigation uses the same order of the posts.
    #
    And I follow "Forum post 3"
    And "//a[@aria-label='Next discussion: Forum post 1']" "xpath_element" should exist
    And "//a[@aria-label='Previous discussion: Edited forum post 2']" "xpath_element" should exist
