@mod @mod_forum
Feature: Blog posts are always displayed in reverse chronological order
  In order to use forum as a blog
  As a user
  I need to see most recent blog posts first

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
      | course   | C1                |
      | activity | forum             |
      | name     | Course blog forum |
      | type     | blog              |

  #
  # We need javascript/wait to prevent creation of the posts in the same second. The threads
  # would then ignore each other in the prev/next navigation as the Forum is unable to compute
  # the correct order.
  #
  @javascript
  Scenario: Replying to a blog post or editing it does not affect its display order
    Given I am on the "Course blog forum" "forum activity" page logged in as student1
    #
    # Add three posts into the blog.
    #
    When I add a new topic to "Course blog forum" forum with:
      | Subject | Blog post 1             |
      | Message | This is the first post  |
    And I add a new topic to "Course blog forum" forum with:
      | Subject | Blog post 2             |
      | Message | This is the second post |
    And I add a new topic to "Course blog forum" forum with:
      | Subject | Blog post 3             |
      | Message | This is the third post  |
    #
    # Edit one of the blog posts.
    #
    And I click on "Edit" "link" in the "//div[@aria-label='Blog post 2 by Student 1']" "xpath_element"
    And I set the following fields to these values:
      | Subject | Edited blog post 2      |
    And I press "Save changes"
    And I wait to be redirected
    And I log out
    #
    # Reply to another blog post.
    #
    And I am on the "Course blog forum" "forum activity" page logged in as teacher1
    And I click on "Discuss this topic" "link" in the "//div[@aria-label='Blog post 1 by Student 1']" "xpath_element"
    And I reply "Blog post 1" post from "Course blog forum" forum with:
      | Message | Reply to the first post |
    And I am on the "Course blog forum" "forum activity" page
    #
    # Make sure the order of the blog posts is still reverse chronological.
    #
    Then I should see "This is the third post" in the "//article[position()=1]" "xpath_element"
    And I should see "This is the second post" in the "//article[position()=2]" "xpath_element"
    And I should see "This is the first post" in the "//article[position()=3]" "xpath_element"
    #
    # Make sure the next/prev navigation uses the same order of the posts.
    #
    And I click on "Discuss this topic" "link" in the "//div[@aria-label='Edited blog post 2 by Student 1']" "xpath_element"
    And "//a[@aria-label='Next discussion: Blog post 3']" "xpath_element" should exist
    And "//a[@aria-label='Previous discussion: Blog post 1']" "xpath_element" should exist
