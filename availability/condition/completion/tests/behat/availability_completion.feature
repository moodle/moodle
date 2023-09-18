@availability @availability_completion
Feature: availability_completion
  In order to control student access to activities
  As a teacher
  I need to set completion conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name   | completion |
      | page     | C1     | Page 1 | 1          |
      | page     | C1     | Page 2 |            |

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I am on the "Page 2" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Activity or resource" to "Page 1"
    And I press "Save and return to course"

    # Log back in as student.
    When I am on the "Course 1" "course" page logged in as "student1"

    # Page 2 should not appear yet.
    Then I should not see "Page 2" in the "region-main" "region"

    # Mark page 1 complete
    When I toggle the manual completion state of "Page 1"
    Then I should see "Page 2" in the "region-main" "region"

  @javascript
  Scenario: Test completion and course cache rebuild
    Given the following "activities" exist:
      | activity | name    | course | idnumber | completion | completionview | completionpostsenabled | completionposts |
      | forum    | forum 1 | C1     | forum1   | 2          | 1              | 1                      | 2               |
    And the following "mod_forum > discussions" exist:
      | forum  | subject      | message          |
      | forum1 | Forum post 1 | This is the body |
    And I am on the "Page 2" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Required completion status | must be marked complete |
      | cm                         | forum 1                 |
    And I press "Save and return to course"
    When I am on the "Course 1" "course" page logged in as "student1"
    # Page 2 should not appear yet.
    Then I should not see "Page 2" in the "region-main" "region"
    And I click on "forum 1" "link" in the "region-main" "region"
    # Page 2 should not appear yet.
    And I should not see "Page 2" in the "region-main" "region"
    And I am on the "forum 1" "forum activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I set the following fields to these values:
      | completionpostsenabled | 0 |
    And I press "Save and display"
    And I am on the "Course 1" "course" page logged in as "student1"
    And I click on "forum 1" "link" in the "region-main" "region"
    And I am on "Course 1" course homepage
    And I should see "Page 2" in the "region-main" "region"
