@availability @availability_profile
Feature: availability_profile
  In order to control student access to activities
  As a teacher
  I need to set profile conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username | email         |
      | teacher1 | t@example.com |
      | student1 | s@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name |
      | page     | C1     | P1   |
      | page     | C1     | P2   |

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I am on the "P1" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "User profile" "button"
    And I set the field "User profile field" to "Email address"
    And I set the field "Value to compare against" to "s@example.com"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Add
    And I am on the "P2" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "User profile" "button"
    And I set the field "User profile field" to "Email address"
    And I set the field "Value to compare against" to "q@example.com"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Log back in as student.
    When I am on the "Course 1" "course" page logged in as "student1"

    # I see P1 but not P2.
    Then I should see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"

  @javascript
  Scenario: Test with custom user profile field
    # Add custom field.
    Given I log in as "admin"
    And I navigate to "Users > Accounts > User profile fields" in site administration
    And I set the field "datatype" to "Text input"
    And I set the following fields to these values:
      | Short name | superfield  |
      | Name       | Super field |
    And I click on "Save changes" "button"

    # Set field value for user.
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I click on ".icon[title=Edit]" "css_element" in the "s@example.com" "table_row"
    And I expand all fieldsets
    And I set the field "Super field" to "Bananaman"
    And I click on "Update profile" "button"

    # Set Page activity which has requirement on this field.
    And I am on the "P1" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "User profile" "button"
    And I set the following fields to these values:
      | User profile field       | Super field |
      | Value to compare against | Bananaman   |
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Edit it again and check the setting still works.
    When I am on the P1 "page activity editing" page
    And I expand all fieldsets
    Then the field "User profile field" matches value "Super field"
    And the field "Value to compare against" matches value "Bananaman"

    # Log out and back in as student. Should be able to see activity.
    And I am on the "Course 1" "course" page logged in as "student1"
    Then I should see "P1" in the "region-main" "region"
