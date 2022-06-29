@javascript @theme_boost
Feature: Course reuse navigation
  As a teacher
  I can navigate to course reuse pages

  Background:
    Given the following "courses" exist:
      | fullname | shortname | newsitems |
      | Course 1 | C1        | 5 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: A Teacher can navigate to the course Import page.
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I navigate to "Course reuse" in current page administration
    Then I should see "Find a course to import data from:"

  Scenario Outline: A Teacher can navigate to other Course reuse pages.
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I navigate to "Course reuse" in current page administration
    And I select "<adminpage>" from the "jump" singleselect
    Then I should see "<title>"

    Examples:
      | adminpage     | title                              |
      |   Backup      | Backup settings                    |
      |   Restore     | Import a backup file               |
      |   Import      | Find a course to import data from: |
      |   Reset       | Reset course                       |

  Scenario: An Administrator can view the course copy page.
    Given I log in as "admin"
    When I am on "Course 1" course homepage
    And I navigate to "Course reuse" in current page administration
    And I select "Copy course" from the "jump" singleselect
    Then I should see "This course will be duplicated and put into the selected course category"
