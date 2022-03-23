@mod @mod_forum
Feature: An admin or teacher sets the post threshold for blocking and warning
  A student should not be able to post more than blocking value

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | teacher1 | Teacher   | 1        | teacher1@example.com  |
      | student1 | Student   | 1        | student1@example.com  |
      | student2 | Student   | 1        | student2@example.com  |
    And the following "courses" exist:
      | fullname | shortname  | category  |
      | Course 1 | C1         | 0         |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity   | name                   | intro             | course | idnumber     | groupmode |
      | forum      | Test forum name        | Test forum name   | C1     | forum        | 0         |

  @javascript
  Scenario: A student should not be able to post new discussion or reply to the existing discussion once the threshold block count is reached
    Given  I am on the "Test forum name" "forum activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Time period for blocking" to "2 days"
    And I set the field "Post threshold for blocking" to "3"
    And I set the field "Post threshold for warning" to "2"
    And I press "Save and display"
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student1
    When I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I should see "Add discussion topic"
    # Verify that when navigated to one of the topics and then click reply the warning notification is shown.
    And I click on "Test post subject two" "link"
    And I click on "Reply" "link"
    And I should see "You are approaching the posting threshold. You have posted 2 times in the last 2 days and the limit is 3 posts."
    And I click on "Test forum name" "link"
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject three |
      | Message | Test post message three |
    Then I should not see "Add discussion topic"
    # Verify that no reply link available in the posts.
    And I click on "Test post subject three" "link"
    And I should not see "Reply"
    And I am on the "Test forum name" "forum activity" page
    And I click on "Test post subject two" "link"
    And I should not see "Reply"
    And I log out
    # Verify that student2 is not affected by the posts made by student1
    And I am on the "Test forum name" "forum activity" page logged in as student2
    And I should see "Add discussion topic"

  @javascript
  Scenario: A student should see warning when the post is about to reach threshold when experimental nested discussion view is set
    Given  I am on the "Test forum name" "forum activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Time period for blocking" to "2 days"
    And I set the field "Post threshold for blocking" to "3"
    And I set the field "Post threshold for warning" to "2"
    And I press "Save and display"
    And I log out
    And I am on the "Test forum name" "forum activity" page logged in as student1
    When I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject two |
      | Message | Test post message two |
    And I should see "Add discussion topic"
    #Now verify that when "Use experimental nested discussion view" is set, the user should see same warning in the Reply.
    And I follow "Preferences" in the user menu
    And I click on "Forum preferences" "link"
    And I set the field "Use experimental nested discussion view" to "Yes"
    And I press "Save changes"
    And I am on the "Test forum name" "forum activity" page
    And I click on "Test post subject two" "link"
    When I press "Reply"
    Then I should see "You are approaching the posting threshold. You have posted 2 times in the last 2 days and the limit is 3 posts."
