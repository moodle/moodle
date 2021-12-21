@block @block_blog_menu
Feature: Enable Block blog menu in an activity
  In order to enable the blog menu in an activity
  As a teacher
  I can add blog menu block to a course

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
    Given the following "activity" exists:
      | activity                      | assign                          |
      | name                          | Test assignment 1               |
      | intro                         | Offline text                    |
      | course                        | C1                              |
      | idnumber                      | 0001                            |
      | section                       | 1                               |
      | assignsubmission_file_enabled | 0                               |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test assignment 1"
    And I add the "Blog menu" block
    And I log out

  Scenario: Students use the blog menu block to post blogs
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add a new entry"
    When I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    Then I should see "S1 First Blog"
    And I should see "This is my awesome blog!"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Blog entries"
    And I should see "S1 First Blog"
    And I should see "This is my awesome blog!"

  Scenario: Students use the blog menu block to view their blogs about the activity
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add an entry about this Assignment"
    And I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog about this Assignment! |
    And I press "Save changes"
    And I should see "S1 First Blog"
    And I should see "This is my awesome blog about this Assignment!"
    And I should see "Associated Assignment: Test assignment 1"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add a new entry"
    And I set the following fields to these values:
      | Entry title | S2 Second Blog |
      | Blog entry body | My unrelated blog! |
    And I press "Save changes"
    And I should see "S2 Second Blog"
    And I should see "My unrelated blog!"
    And I should not see "Associated Assignment: Test assignment 1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add an entry about this Assignment"
    And I set the following fields to these values:
      | Entry title | S2 First Blog |
      | Blog entry body | My course blog is better! |
    And I press "Save changes"
    And I should see "S2 First Blog"
    And I should see "My course blog is better!"
    And I should see "Associated Assignment: Test assignment 1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    When I follow "View my entries about this Assignment"
    Then I should see "S2 First Blog"
    And I should not see "S2 Second Blog"
    And I should not see "S1 First Blog"

  Scenario: Students use the blog menu block to view all blogs about the assignment
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add an entry about this Assignment"
    And I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog about this Assignment! |
    And I press "Save changes"
    And I should see "S1 First Blog"
    And I should see "This is my awesome blog about this Assignment!"
    And I should see "Associated Assignment: Test assignment 1"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add a new entry"
    And I set the following fields to these values:
      | Entry title | S2 Second Blog |
      | Blog entry body | My unrelated blog! |
    And I press "Save changes"
    And I should see "S2 Second Blog"
    And I should see "My unrelated blog!"
    And I should not see "Associated Assignment: Test assignment 1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add an entry about this Assignment"
    And I set the following fields to these values:
      | Entry title | S2 First Blog |
      | Blog entry body | My course blog is better! |
    And I press "Save changes"
    And I should see "S2 First Blog"
    And I should see "My course blog is better!"
    And I should see "Associated Assignment: Test assignment 1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    When I follow "View all entries about this Assignment"
    Then I should see "S1 First Blog"
    And I should see "S2 First Blog"
    And I should not see "S2 Second Blog"

  Scenario: Students use the blog menu block to view all their blog entries
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add an entry about this Assignment"
    And I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog about this Assignment! |
    And I press "Save changes"
    And I should see "S1 First Blog"
    And I should see "This is my awesome blog about this Assignment!"
    And I should see "Associated Assignment: Test assignment 1"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add a new entry"
    And I set the following fields to these values:
      | Entry title | S2 Second Blog |
      | Blog entry body | My unrelated blog! |
    And I press "Save changes"
    And I should see "S2 Second Blog"
    And I should see "My unrelated blog!"
    And I should not see "Associated Assignment: Test assignment 1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add an entry about this Assignment"
    And I set the following fields to these values:
      | Entry title | S2 First Blog |
      | Blog entry body | My course blog is better! |
    And I press "Save changes"
    And I should see "S2 First Blog"
    And I should see "My course blog is better!"
    And I should see "Associated Assignment: Test assignment 1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    When I follow "Blog entries"
    Then I should see "S2 First Blog"
    And I should see "S2 Second Blog"
    And I should not see "S1 First Blog"

  Scenario: Teacher searches for student blogs
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add an entry about this Assignment"
    And I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog about this Assignment! |
    And I press "Save changes"
    And I should see "S1 First Blog"
    And I should see "This is my awesome blog about this Assignment!"
    And I should see "Associated Assignment: Test assignment 1"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add a new entry"
    And I set the following fields to these values:
      | Entry title | S2 Second Blog |
      | Blog entry body | My unrelated blog! |
    And I press "Save changes"
    And I should see "S2 Second Blog"
    And I should see "My unrelated blog!"
    And I should not see "Associated Assignment: Test assignment 1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I follow "Add an entry about this Assignment"
    And I set the following fields to these values:
      | Entry title | S2 First Blog |
      | Blog entry body | My course blog is better! |
    And I press "Save changes"
    And I should see "S2 First Blog"
    And I should see "My course blog is better!"
    And I should see "Associated Assignment: Test assignment 1"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment 1"
    And I set the field "Search" to "First"
    And I press "Search"
    Then I should see "S1 First Blog"
    And I should see "S2 First Blog"
    And I should not see "S2 Second Blog"
