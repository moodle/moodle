@core @core_backup @core_contentbank
Feature: Import course content bank content
  In order to import content from a course contentbank
  As a teacher
  I need to confirm that errors will not happen

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | editingteacher |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname |
      | Course       | C1        | contenttype_h5p | teacher1 | ipsums.h5p  |
    And I log in as "teacher1"

  Scenario: Import content bank content to another course
    Given I am on "Course 2" course homepage
    And I click on "Content bank" "link"
    And I should not see "ipsums.h5p"
    When I import "Course 1" course into "Course 2" course using this options:
    And I click on "Content bank" "link"
    Then I should see "ipsums.h5p"
    And I am on "Course 1" course homepage
    And I click on "Content bank" "link"
    And I should see "ipsums.h5p"

  Scenario: User could configure not to import content bank
    Given I am on "Course 2" course homepage
    And I click on "Content bank" "link"
    And I should not see "ipsums.h5p"
    When I import "Course 1" course into "Course 2" course using this options:
      | Initial | Include content bank content | 0 |
    And I click on "Content bank" "link"
    Then I should not see "ipsums.h5p"
    And I am on "Course 1" course homepage
    And I click on "Content bank" "link"
    And I should see "ipsums.h5p"
