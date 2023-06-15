@core @core_course
Feature: Restricting access to course lists
  In order to provide more targeted content
  As a Moodle Administrator
  I need to be able to give/revoke capabilities to view list of courses

  Background:
    Given the following "categories" exist:
      | name | category | idnumber |
      | Science category | 0 | SCI |
      | English category | 0 | ENG |
      | Other category   | 0 | MISC |
    And the following "courses" exist:
      | fullname   | shortname | category |
      | Biology Y1 | BIO1      | SCI |
      | Biology Y2 | BI02      | SCI |
      | English Y1 | ENG1      | ENG |
      | English Y2 | ENG2      | ENG |
      | Humanities Y1 | HUM2   | MISC |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | user0 | User | Z | user0@example.com |
      | userb | User | B | userb@example.com |
      | usere | User | E | usere@example.com |
    And the following "roles" exist:
      | name            | shortname    | description      | archetype      |
      | Category viewer | coursebrowse | My custom role 1 |                |
    And the following "role capability" exist:
        | role         | moodle/category:viewcourselist |
        | user         | prevent                        |
        | guest        | prevent                        |
        | coursebrowse | allow                          |
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I log out
    And the following "role assigns" exist:
      | user  | role           | contextlevel | reference |
      | usere | coursebrowse   | Category     | ENG       |
      | userb | coursebrowse   | Category     | ENG       |
      | userb | coursebrowse   | Category     | SCI       |

  Scenario: Browse courses as a user without any browse capability
    When I log in as "user0"
    And I am on site homepage
    Then I should not see "Available courses"
    And "Courses" "link" should not exist in the "Navigation" "block"
    And I log out

  Scenario: Browse own courses as a user without any browse capability
    Given the following "course enrolments" exist:
      | user  | course | role |
      | user0 | BIO1   | student |
    When I log in as "user0"
    And I am on site homepage
    And I should see "Available courses"
    And I should see "Biology Y1"
    And "Courses" "link" should not exist in the "Navigation" "block"
    And I log out

  Scenario: Browse courses as a user who has access to only one category
    When I log in as "usere"
    And I am on site homepage
    Then I should see "Available courses"
    And I should see "English Y1"
    And I should see "English Y2"
    And I should not see "Biology"
    And I should not see "Humanities"
    And I click on "Courses" "link" in the "Navigation" "block"
    And "English category" "text" should exist in the ".breadcrumb" "css_element"
    And I should see "English Y1"
    And I should see "English Y2"
    And I should not see "Biology"
    And I should not see "Humanities"
    And I should not see "Other category"
    And I follow "English Y2"
    And I should see "You cannot enrol yourself in this course."
    And I log out

  Scenario: Browse courses as a user who has access to several but not all categories
    When I log in as "userb"
    And I am on site homepage
    Then I should see "Available courses"
    And I should see "English Y1"
    And I should see "English Y2"
    And I should see "Biology"
    And I should not see "Humanities"
    And I click on "Courses" "link" in the "Navigation" "block"
    # And "category" "text" should not exist in the ".breadcrumb" "css_element"
    And I should see "Science category"
    And I should see "English category"
    And I should not see "Other category"
    And I follow "Science category"
    And I should see "Biology Y2"
    And I should not see "English Y1"
    And the "Course categories" select box should contain "Science category"
    And the "Course categories" select box should contain "English category"
    And the "Course categories" select box should not contain "Other category"
    And I follow "Biology Y1"
    And I should see "You cannot enrol yourself in this course."
    And I log out
