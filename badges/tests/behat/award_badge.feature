@core @core_badges @_only_local
Feature: Award badges
  In order to award badges to users for their achievements
  As an admin
  I need to add criteria to badges in the system

  Background:
    Given I am on homepage
    And I log in as "admin"

  @javascript
  Scenario: Award profile badge
    Given I expand "Site administration" node
    And I expand "Badges" node
    And I follow "Add a new badge"
    And I fill the moodle form with:
      | Name | Profile Badge |
      | Description | Test badge description |
      | issuername | Test Badge Site |
      | issuercontact | testuser@test-badge-site.com |
    And I upload "badges/tests/behat/badge.png" file to "Image" filepicker
    And I press "Create badge"
    And I select "Profile completion" from "type"
    And I check "First name"
    And I check "Email address"
    And I check "Phone"
    When I press "Save"
    Then I should see "Profile completion"
    And I should see "First name"
    And I should see "Email address"
    And I should not see "Criteria for this badge have not been set up yet."
    And I press "Enable access"
    And I press "Continue"
    And I expand "My profile settings" node
    And I follow "Edit profile"
    And I expand all fieldsets
    And I fill in "Phone" with "123456789"
    And I press "Update profile"
    And I follow "My badges"
    Then I should see "Profile Badge"
    And I should not see "There are no badges available."

  @javascript
  Scenario: Award site badge
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher | teacher | 1 | teacher1@asd.com |
      | student | student | 1 | student1@asd.com |
    And I expand "Site administration" node
    And I expand "Badges" node
    And I follow "Add a new badge"
    And I fill the moodle form with:
      | Name | Site Badge |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filepicker
    And I press "Create badge"
    And I select "Manual issue by role" from "type"
    And I check "Teacher"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I select "teacher 1 (teacher1@asd.com)" from "potentialrecipients[]"
    And I press "Award badge"
    And I select "student 1 (student1@asd.com)" from "potentialrecipients[]"
    And I press "Award badge"
    When I follow "Site Badge"
    Then I should see "Recipients (2)"
    And I log out
    And I log in as "student"
    And I expand "My profile" node
    And I follow "My badges"
    Then I should see "Site Badge"

  @javascript
  Scenario: Award course badge
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "courses" exists:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I click on "//span[text()='Badges']" "xpath_element" in the "Administration" "block"
    And I follow "Add a new badge"
    And I fill the moodle form with:
      | Name | Course Badge |
      | Description | Course badge description |
      | issuername | Tester of course badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filepicker
    And I press "Create badge"
    And I select "Manual issue by role" from "type"
    And I check "Teacher"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I select "Student 2 (student2@asd.com)" from "potentialrecipients[]"
    And I press "Award badge"
    And I select "Student 1 (student1@asd.com)" from "potentialrecipients[]"
    When I press "Award badge"
    And I follow "Course Badge"
    Then I should see "Recipients (2)"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I expand "My profile" node
    And I follow "My badges"
    Then I should see "Course Badge"