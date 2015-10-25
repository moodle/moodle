@block @block_navigation
Feature: Expand the courses nodes within the navigation block
  In order to navigate the site
  As an anonymous user, a guest, a student, and an admin
  I need to expand the courses node in the navigation block and check the display of courses and categories.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "categories" exist:
      | name   | category | idnumber | visible |
      | cat1   | 0        | cat1     | 1       |
      | cat2   | 0        | cat2     | 1       |
      | cat21  | cat2     | cat21    | 1       |
      | cat211 | cat21    | cat211   | 1       |
      | cat3   | 0        | cat3     | 0       |
    And the following "courses" exist:
      | fullname  | shortname | category | visible |
      | Course 1  | c1        | cat1     | 1       |
      | Course 2  | c2        | cat2     | 1       |
      | Course 3  | c3        | cat21    | 1       |
      | Course 4  | c4        | cat211   | 1       |
      | Course 5  | c5        | cat211   | 0       |
      | Course 6  | c6        | cat211   | 0       |
      | Course 7  | c7        | cat3     | 1       |
      | Course 8  | c8        | cat3     | 0       |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher1 | c1     | teacher |
      | teacher1 | c3     | teacher |
      | teacher1 | c5     | teacher |
      | student1 | c1     | student |
      | student1 | c2     | student |
      | student1 | c4     | student |
    And the following config values are set as admin:
      | navshowallcourses | 1 |
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 2"
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And I set the following fields to these values:
      | Allow guest access | Yes |
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: As an anonymous user I expand the courses node to see courses.
    When I should see "You are not logged in." in the ".logininfo" "css_element"
    And I should see "Home" in the "Navigation" "block"
    And I should see "Courses" in the "Navigation" "block"
    And I expand "Courses" node
    And I should see "cat1" in the "Navigation" "block"
    And I should see "cat2" in the "Navigation" "block"
    And I should not see "cat3" in the "Navigation" "block"
    And I expand "cat1" node
    And I expand "cat2" node
    And I should see "cat21" in the "Navigation" "block"
    And I expand "cat21" node
    And I should see "cat211" in the "Navigation" "block"
    And I expand "cat211" node
    Then I should see "c1" in the "Navigation" "block"
    And I should see "c2" in the "Navigation" "block"
    And I should see "c3" in the "Navigation" "block"
    And I should see "c4" in the "Navigation" "block"
    And I should not see "c5" in the "Navigation" "block"
    And I should not see "c6" in the "Navigation" "block"
    And navigation node "c1" should not be expandable
    And navigation node "c2" should not be expandable
    And navigation node "c3" should not be expandable
    And navigation node "c4" should not be expandable

  @javascript
  Scenario: As the admin user I expand the courses and category nodes to see courses.
    When I log in as "admin"
    And I am on site homepage
    And I should see "Site home" in the "Navigation" "block"
    And I should see "Courses" in the "Navigation" "block"
    And I expand "Courses" node
    And I should see "cat1" in the "Navigation" "block"
    And I should see "cat2" in the "Navigation" "block"
    And I should see "cat3" in the "Navigation" "block"
    And I expand "cat1" node
    And I expand "cat2" node
    And I expand "cat3" node
    And I should see "cat21" in the "Navigation" "block"
    And I expand "cat21" node
    And I should see "cat211" in the "Navigation" "block"
    And I expand "cat211" node
    Then I should see "c1" in the "Navigation" "block"
    And I should see "c2" in the "Navigation" "block"
    And I should see "c3" in the "Navigation" "block"
    And I should see "c4" in the "Navigation" "block"
    And I should see "c5" in the "Navigation" "block"
    And I should see "c6" in the "Navigation" "block"
    And I should see "c7" in the "Navigation" "block"
    And I should see "c8" in the "Navigation" "block"
    And navigation node "c1" should be expandable
    And navigation node "c2" should be expandable
    And navigation node "c3" should be expandable
    And navigation node "c4" should be expandable
    And navigation node "c5" should be expandable
    And navigation node "c6" should be expandable
    And navigation node "c7" should be expandable
    And navigation node "c8" should be expandable

  @javascript
  Scenario: As teacher1 I expand the courses and category nodes to see courses.
    When I log in as "teacher1"
    And I am on site homepage
    And I should see "Site home" in the "Navigation" "block"
    And I should see "Courses" in the "Navigation" "block"
    And I expand "Courses" node
    And I should see "cat1" in the "Navigation" "block"
    And I should see "cat2" in the "Navigation" "block"
    And I should not see "cat3" in the "Navigation" "block"
    And I expand "cat1" node
    And I expand "cat2" node
    And I should see "cat21" in the "Navigation" "block"
    And I expand "cat21" node
    And I should see "cat211" in the "Navigation" "block"
    And I expand "cat211" node
    Then I should see "c1" in the "Navigation" "block"
    And I should see "c2" in the "Navigation" "block"
    And I should see "c3" in the "Navigation" "block"
    And I should see "c4" in the "Navigation" "block"
    And I should see "c5" in the "Navigation" "block"
    And I should not see "c6" in the "Navigation" "block"
    And I should not see "c7" in the "Navigation" "block"
    And I should not see "c8" in the "Navigation" "block"
    And navigation node "c1" should be expandable
    And navigation node "c2" should be expandable
    And navigation node "c3" should be expandable
    And navigation node "c4" should not be expandable
    And navigation node "c5" should be expandable

  @javascript
  Scenario: As student1 I expand the courses and category nodes to see courses.
    When I log in as "student1"
    And I am on site homepage
    And I should see "Site home" in the "Navigation" "block"
    And I should see "Courses" in the "Navigation" "block"
    And I expand "Courses" node
    And I should see "cat1" in the "Navigation" "block"
    And I should see "cat2" in the "Navigation" "block"
    And I should not see "cat3" in the "Navigation" "block"
    And I expand "cat1" node
    And I expand "cat2" node
    And I should see "cat21" in the "Navigation" "block"
    And I expand "cat21" node
    And I should see "cat211" in the "Navigation" "block"
    And I expand "cat211" node
    Then I should see "c1" in the "Navigation" "block"
    And I should see "c2" in the "Navigation" "block"
    And I should see "c3" in the "Navigation" "block"
    And I should see "c4" in the "Navigation" "block"
    And I should not see "c5" in the "Navigation" "block"
    And I should not see "c6" in the "Navigation" "block"
    And I should not see "c7" in the "Navigation" "block"
    And I should not see "c8" in the "Navigation" "block"
    And navigation node "c1" should be expandable
    And navigation node "c2" should be expandable
    And navigation node "c3" should not be expandable
    And navigation node "c4" should be expandable

  @javascript
  Scenario: As guest I expand the courses and category nodes to see courses.
    When I log in as "guest"
    And I am on site homepage
    And I should see "Home" in the "Navigation" "block"
    And I should see "Courses" in the "Navigation" "block"
    And I expand "Courses" node
    And I should see "cat1" in the "Navigation" "block"
    And I should see "cat2" in the "Navigation" "block"
    And I should not see "cat3" in the "Navigation" "block"
    And I expand "cat1" node
    And I expand "cat2" node
    And I should see "cat21" in the "Navigation" "block"
    And I expand "cat21" node
    And I should see "cat211" in the "Navigation" "block"
    And I expand "cat211" node
    Then I should see "c1" in the "Navigation" "block"
    And I should see "c2" in the "Navigation" "block"
    And I should see "c3" in the "Navigation" "block"
    And I should see "c4" in the "Navigation" "block"
    And I should not see "c5" in the "Navigation" "block"
    And I should not see "c6" in the "Navigation" "block"
    And I should not see "c7" in the "Navigation" "block"
    And I should not see "c8" in the "Navigation" "block"
    And navigation node "c1" should not be expandable
    And navigation node "c2" should be expandable
    And navigation node "c3" should not be expandable
    And navigation node "c4" should not be expandable
