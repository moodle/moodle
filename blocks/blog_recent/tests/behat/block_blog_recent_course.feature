@block @block_blog_menu @block_blog_recent
Feature: Students can use the recent blog entries block to view recent entries on a course page
  In order to enable the recent blog entries block a course page
  As a teacher
  I can add the recent blog entries block to a course page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | student1 | Student | 1 | student1@example.com | S1 |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Blog menu" block
    And I add the "Recent blog entries" block
    And I log out

  Scenario: Students use the recent blog entries block to view blogs
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    When I set the following fields to these values:
      | Entry title | S1 First Blog <span lang="RU" class="multilang">RUSSIAN</span><span lang="EN" class="multilang">ENGLISH</span> |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    Then I should see "S1 First Blog ENGLISH"
    And I should see "This is my awesome blog!"
    And I am on "Course 1" course homepage
    And I should see "S1 First Blog ENGLISH"
    And I follow "S1 First Blog"
    And I should see "This is my awesome blog!"

  Scenario: Students only see a few entries in the recent blog entries block
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    # Blog 1 of 5
    And I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    And I wait "1" seconds
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    # Blog 2 of 5
    And I set the following fields to these values:
      | Entry title | S1 Second Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    And I wait "1" seconds
    And I should see "S1 Second Blog"
    And I should see "This is my awesome blog!"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    # Blog 3 of 5
    And I set the following fields to these values:
      | Entry title | S1 Third Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    And I wait "1" seconds
    And I should see "S1 Third Blog"
    And I should see "This is my awesome blog!"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    # Blog 4 of 5
    And I set the following fields to these values:
      | Entry title | S1 Fourth Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    And I wait "1" seconds
    And I should see "S1 Fourth Blog"
    And I should see "This is my awesome blog!"
    And I am on "Course 1" course homepage
    And I follow "Add an entry about this course"
    # Blog 5 of 5
    And I set the following fields to these values:
      | Entry title | S1 Fifth Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    And I should see "S1 Fifth Blog"
    And I should see "This is my awesome blog!"
    When I am on "Course 1" course homepage
    And I should not see "S1 First Blog"
    And I should see "S1 Second Blog"
    And I should see "S1 Third Blog"
    And I should see "S1 Fourth Blog"
    And I should see "S1 Fifth Blog"
    And I follow "S1 Fifth Blog"
    And I should see "This is my awesome blog!"
    Then I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I configure the "Recent blog entries" block
    And I set the following fields to these values:
      | config_numberofrecentblogentries | 2 |
    And I press "Save changes"
    And I should see "S1 Fourth Blog"
    And I should see "S1 Fifth Blog"
