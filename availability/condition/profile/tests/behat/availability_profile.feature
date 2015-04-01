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
      | teacher1 | t@example.org |
      | student1 | s@example.org |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      | enableavailability  | 1 |

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on

    # Add
    And I add a "Page" to section "1"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "User profile" "button"
    And I set the field "User profile field" to "Email address"
    And I set the field "Value to compare against" to "s@example.org"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Add
    And I add a "Page" to section "2"
    And I set the following fields to these values:
      | Name         | P2 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "User profile" "button"
    And I set the field "User profile field" to "Email address"
    And I set the field "Value to compare against" to "q@example.org"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I follow "Course 1"

    # I see P1 but not P2.
    Then I should see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"

  @javascript
  Scenario: Test with custom user profile field
    # Add custom field.
    Given I log in as "admin"
    And I navigate to "User profile fields" node in "Site administration > Users > Accounts"
    And I set the field "datatype" to "Text input"
    And I set the following fields to these values:
      | Short name | superfield  |
      | Name       | Super field |
    And I click on "Save changes" "button"

    # Set field value for user.
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I click on "a[title=Edit]" "css_element" in the "s@example.org" "table_row"
    And I expand all fieldsets
    And I set the field "Super field" to "Bananaman"
    And I click on "Update profile" "button"

    # Set Page activity which has requirement on this field.
    And I am on homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Page" to section "1"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "User profile" "button"
    And I set the following fields to these values:
      | User profile field       | Super field |
      | Value to compare against | Bananaman   |
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Edit it again and check the setting still works.
    When I follow "P1"
    And I navigate to "Edit settings" node in "Page module administration"
    And I expand all fieldsets
    Then the field "User profile field" matches value "Super field"
    And the field "Value to compare against" matches value "Bananaman"

    # Log out and back in as student. Should be able to see activity.
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then I should see "P1" in the "region-main" "region"
