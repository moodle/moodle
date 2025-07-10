@mod @mod_book
Feature: Book chapter numbering should be consistent for users
  In order to correctly refer to book chapters
  As a teacher or student
  I should be able to see the same chapter numbering as other users

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activity" exists:
      | course   | C1        |
      | activity | book      |
      | name     | Test book |
    And the following "mod_book > chapters" exist:
      | book      | title          | content        | pagenum |subchapter | hidden |
      | Test book | Intro          | Intro chapter  | 1       | 0         | 0      |
      | Test book | First chapter  | First chapter  | 2       | 0         | 0      |
      | Test book | Sub chapter A  | Sub chapter A  | 3       | 1         | 0      |
      | Test book | Sub chapter B  | Sub chapter B  | 4       | 1         | 1      |
      | Test book | Sub chapter C  | Sub chapter C  | 5       | 1         | 0      |
      | Test book | Second chapter | Second chapter | 6       | 0         | 1      |
      | Test book | Sub chapter D  | Sub chapter C  | 7       | 1         | 1      |
      | Test book | Third chapter  | Third chapter  | 8       | 0         | 0      |

  Scenario Outline: Chapter numbering for teachers is consistent in editing and view mode
    Given I am on the "Course 1" course page logged in as teacher1
    And I turn editing mode <editmode>
    And I am on the "Test book" "book activity" page
    # Check chapter numbering
    When I follow "2.1. Sub chapter A"
    Then I should see "2.1. Sub chapter A" in the ".book_content" "css_element"
    And I follow "2.x. Sub chapter B"
    And I should see "2.x. Sub chapter B" in the ".book_content" "css_element"
    And I follow "2.2. Sub chapter C"
    And I should see "2.2. Sub chapter C" in the ".book_content" "css_element"
    And I follow "x. Second chapter"
    And I should see "x. Second chapter" in the ".book_content" "css_element"
    And I follow "x.x. Sub chapter D"
    And I should see "x.x. Sub chapter D" in the ".book_content" "css_element"
    And I follow "3. Third chapter"
    And I should see "3. Third chapter" in the ".book_content" "css_element"

    Examples:
      | editmode |
      | on       |
      | off      |

  Scenario: Chapter numbering for students is consistent with what teachers see
    Given I am on the "Course 1" course page logged in as student1
    And I am on the "Test book" "book activity" page
    # Check chapter numbering
    When I follow "2.1. Sub chapter A"
    Then I should see "2.1. Sub chapter A" in the ".book_content" "css_element"
    And I follow "2.2. Sub chapter C"
    And I should see "2.2. Sub chapter C" in the ".book_content" "css_element"
    And I follow "3. Third chapter"
    And I should see "3. Third chapter" in the ".book_content" "css_element"
