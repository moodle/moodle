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

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

    # Add a Page with a completion tickbox.
    And I add a "Page" to section "1" and I fill the form with:
      | Name                | Page 1 |
      | Description         | Test   |
      | Page content        | Test   |
      | Completion tracking | 1      |

    # And another one that depends on it (hidden otherwise).
    And I add a "Page" to section "2"
    And I set the following fields to these values:
      | Name         | Page 2 |
      | Description  | Test   |
      | Page content | Test   |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Activity or resource" to "Page 1"
    And I press "Save and return to course"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage

    # Page 2 should not appear yet.
    Then I should not see "Page 2" in the "region-main" "region"

    # Mark page 1 complete
    When I toggle the manual completion state of "Page 1"
    Then I should see "Page 2" in the "region-main" "region"

  @javascript
  Scenario: Test completion and course cache rebuild
    Given the following "activities" exist:
      | activity | name    | intro   | course | idnumber |
      | forum    | forum 1 | forum 1 | C1     | forum1   |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I open "forum 1" actions menu
    And I click on "Edit settings" "link" in the "forum 1" activity
    And I set the following fields to these values:
      | Completion tracking    | Show activity as complete when conditions are met |
      | completionview         | 1                                                 |
      | completionpostsenabled | 1                                                 |
      | completionposts        | 2                                                 |
    And I press "Save and return to course"
    And I add a new discussion to "forum 1" forum with:
      | Subject | Forum post 1 |
      | Message | This is the body |
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "2"
    And I set the following fields to these values:
      | Name         | Page 2 |
      | Description  | Test   |
      | Page content | Test   |
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Required completion status | must be marked complete |
      | cm                         | forum 1                 |
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    # Page 2 should not appear yet.
    Then I should not see "Page 2" in the "region-main" "region"
    And I click on "forum 1" "link" in the "region-main" "region"
    # Page 2 should not appear yet.
    And I should not see "Page 2" in the "region-main" "region"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I am on the "forum 1" "forum activity editing" page
    And I expand all fieldsets
    And I set the following fields to these values:
      | completionpostsenabled | 0 |
    And I press "Save and display"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on "forum 1" "link" in the "region-main" "region"
    And I am on "Course 1" course homepage
    And I should see "Page 2" in the "region-main" "region"
