@block @block_blog_menu
Feature: Students can use block blog menu in a course
  In order students to use the blog menu in a course
  As a student
  I view blog menu block in a course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
      | student2 | Student | 2 | student2@example.com | S2 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Blog menu" block
    And I log out

  Scenario: Students use the blog menu block to post blogs
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Add a new entry"
    When I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    Then I should see "S1 First Blog"
    And I should see "This is my awesome blog!"
    And I am on "Course 1" course homepage
    And I follow "Blog entries"
    And I should see "S1 First Blog"
    And I should see "This is my awesome blog!"

  Scenario: Students use the blog menu block to view their blogs about the course
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    And I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog about this course! |
    And I press "Save changes"
    And I should see "S1 First Blog"
    And I should see "This is my awesome blog about this course!"
    And I should see "Associated Course: C1"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Add a new entry"
    And I set the following fields to these values:
      | Entry title | S2 Second Blog |
      | Blog entry body | My unrelated blog! |
    And I press "Save changes"
    And I should see "S2 Second Blog"
    And I should see "My unrelated blog!"
    And I should not see "Associated Course: C1"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    And I set the following fields to these values:
      | Entry title | S2 First Blog |
      | Blog entry body | My course blog is better! |
    And I press "Save changes"
    And I should see "S2 First Blog"
    And I should see "My course blog is better!"
    And I should see "Associated Course: C1"
    And I am on "Course 1" course homepage
    When I follow "View my entries about this course"
    Then I should see "S2 First Blog"
    And I should not see "S2 Second Blog"
    And I should not see "S1 First Blog"

  Scenario: Students use the blog menu block to view all blogs about the course
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    And I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog about this course! |
    And I press "Save changes"
    And I should see "S1 First Blog"
    And I should see "This is my awesome blog about this course!"
    And I should see "Associated Course: C1"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Add a new entry"
    And I set the following fields to these values:
      | Entry title | S2 Second Blog |
      | Blog entry body | My unrelated blog! |
    And I press "Save changes"
    And I should see "S2 Second Blog"
    And I should see "My unrelated blog!"
    And I should not see "Associated Course: C1"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    And I set the following fields to these values:
      | Entry title | S2 First Blog |
      | Blog entry body | My course blog is better! |
    And I press "Save changes"
    And I should see "S2 First Blog"
    And I should see "My course blog is better!"
    And I should see "Associated Course: C1"
    And I am on "Course 1" course homepage
    When I follow "View all entries for this course"
    Then I should see "S1 First Blog"
    And I should see "S2 First Blog"
    And I should not see "S2 Second Blog"

  Scenario: Students use the blog menu block to view all their blog entries
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    And I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog about this course! |
    And I press "Save changes"
    And I should see "S1 First Blog"
    And I should see "This is my awesome blog about this course!"
    And I should see "Associated Course: C1"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Add a new entry"
    And I set the following fields to these values:
      | Entry title | S2 Second Blog |
      | Blog entry body | My unrelated blog! |
    And I press "Save changes"
    And I should see "S2 Second Blog"
    And I should see "My unrelated blog!"
    And I should not see "Associated Course: C1"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    And I set the following fields to these values:
      | Entry title | S2 First Blog |
      | Blog entry body | My course blog is better! |
    And I press "Save changes"
    And I should see "S2 First Blog"
    And I should see "My course blog is better!"
    And I should see "Associated Course: C1"
    And I am on "Course 1" course homepage
    When I follow "Blog entries"
    Then I should see "S2 First Blog"
    And I should see "S2 Second Blog"
    And I should not see "S1 First Blog"

  Scenario: Teacher searches for student blogs
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    And I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog about this course! |
    And I press "Save changes"
    And I should see "S1 First Blog"
    And I should see "This is my awesome blog about this course!"
    And I should see "Associated Course: C1"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Add a new entry"
    And I set the following fields to these values:
      | Entry title | S2 Second Blog |
      | Blog entry body | My unrelated blog! |
    And I press "Save changes"
    And I should see "S2 Second Blog"
    And I should see "My unrelated blog!"
    And I should not see "Associated Course: C1"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    And I set the following fields to these values:
      | Entry title | S2 First Blog |
      | Blog entry body | My course blog is better! |
    And I press "Save changes"
    And I should see "S2 First Blog"
    And I should see "My course blog is better!"
    And I should see "Associated Course: C1"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I set the field "Search" to "First"
    And I press "Search"
    Then I should see "S1 First Blog"
    And I should see "S2 First Blog"
    And I should not see "S2 Second Blog"
