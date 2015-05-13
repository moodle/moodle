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
    And the following config values are set as admin:
      | enableavailability  | 1 |
      | enablecompletion | 1 |

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

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
    And I am on site homepage
    And I follow "Course 1"

    # Page 2 should not appear yet.
    Then I should not see "Page 2" in the "region-main" "region"

    # Mark page 1 complete
    When I click on ".togglecompletion input[type=image]" "css_element"
    Then I should see "Page 2" in the "region-main" "region"
