@format @format_social
Feature: Change number of discussions displayed
  In order to change the number of discussions displayed
  As a teacher
  I need to edit the course and change the number of sections displayed.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | 0 | social |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Add a new discussion topic"
    And I set the following fields to these values:
      | Subject | Forum Post 10 |
      | Message | This is forum post ten |
    And I press "Post to forum"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I wait "1" seconds
    And I follow "Add a new discussion topic"
    And I set the following fields to these values:
      | Subject | Forum Post 9 |
      | Message | This is forum post nine |
    And I press "Post to forum"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I wait "1" seconds
    And I follow "Add a new discussion topic"
    And I set the following fields to these values:
      | Subject | Forum Post 8 |
      | Message | This is forum post eight |
    And I press "Post to forum"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I wait "1" seconds
    And I follow "Add a new discussion topic"
    And I set the following fields to these values:
      | Subject | Forum Post 7 |
      | Message | This is forum post seven |
    And I press "Post to forum"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I wait "1" seconds
    And I follow "Add a new discussion topic"
    And I set the following fields to these values:
      | Subject | Forum Post 6 |
      | Message | This is forum post six |
    And I press "Post to forum"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I wait "1" seconds
    And I follow "Add a new discussion topic"
    And I set the following fields to these values:
      | Subject | Forum Post 5 |
      | Message | This is forum post five |
    And I press "Post to forum"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I wait "1" seconds
    And I follow "Add a new discussion topic"
    And I set the following fields to these values:
      | Subject | Forum Post 4 |
      | Message | This is forum post four |
    And I press "Post to forum"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I wait "1" seconds
    And I follow "Add a new discussion topic"
    And I set the following fields to these values:
      | Subject | Forum Post 3 |
      | Message | This is forum post three |
    And I press "Post to forum"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I wait "1" seconds
    And I follow "Add a new discussion topic"
    And I set the following fields to these values:
      | Subject | Forum Post 2 |
      | Message | This is forum post two |
    And I press "Post to forum"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I wait "1" seconds
    And I follow "Add a new discussion topic"
    And I set the following fields to these values:
      | Subject | Forum Post 1 |
      | Message | This is forum post one |
    And I press "Post to forum"
    And I wait to be redirected
    And I am on "Course 1" course homepage

  Scenario: When number of discussions is decreased fewer discussions appear
    Given I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | numdiscussions | 5 |
    When I press "Save and display"
    Then I should see "This is forum post one"
    And I should see "This is forum post five"
    And I should not see "This is forum post six"

  Scenario: When number of discussions is decreased to less than 1 only 1 discussion should appear
    Given I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | numdiscussions | -1 |
    When I press "Save and display"
    Then I should see "This is forum post one"
    And I should not see "This is forum post two"
    And I should not see "This is forum post ten"

  Scenario: When number of discussions is increased more discussions appear
    Given I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | numdiscussions | 9 |
    When I press "Save and display"
    Then I should see "This is forum post one"
    And I should see "This is forum post five"
    And I should see "This is forum post nine"
    And I should not see "This is forum post ten"
