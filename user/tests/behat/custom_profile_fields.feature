@core @core_user
Feature: Custom profile fields should be visible and editable by those with the correct permissions.

  Background:
    Given the following "users" exist:
      | username            | firstname           | lastname | email                           |
      | userwithinformation | userwithinformation | 1        | userwithinformation@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "course enrolments" exist:
      | user                | course | role    |
      | userwithinformation | C1     | student |

    And the following config values are set as admin:
      | registerauth    | email |

    And I log in as "admin"
    And I navigate to "Users > Accounts > User profile fields" in site administration
    And I click on "Create a new profile field" "link"
    And I click on "Text input" "link"
    And I set the following fields to these values:
      | Short name                    | notvisible_field |
      | Name                          | notvisible_field |
      | Display on signup page?       | Yes              |
      | Who is this field visible to? | Not visible      |
    And I click on "Save changes" "button"

    And I click on "Create a new profile field" "link"
    And I click on "Text input" "link"
    And I set the following fields to these values:
      | Short name                    | uservisible_field |
      | Name                          | uservisible_field |
      | Display on signup page?       | Yes               |
      | Who is this field visible to? | Visible to user   |
    And I click on "Save changes" "button"

    And I click on "Create a new profile field" "link"
    And I click on "Text input" "link"
    And I set the following fields to these values:
      | Short name                    | everyonevisible_field |
      | Name                          | everyonevisible_field |
      | Display on signup page?       | No                    |
      | Who is this field visible to? | Visible to everyone   |
    And I click on "Save changes" "button"

    And I click on "Create a new profile field" "link"
    And I click on "Text input" "link"
    And I set the following fields to these values:
      | Short name                    | teachervisible_field                |
      | Name                          | teachervisible_field                |
      | Display on signup page?       | Yes                                 |
      | Who is this field visible to? | Visible to user, teachers and admins |
    And I click on "Save changes" "button"

    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I click on ".icon[title=Edit]" "css_element" in the "userwithinformation@example.com" "table_row"
    And I expand all fieldsets
    And I set the field "notvisible_field" to "notvisible_field_information"
    And I set the field "uservisible_field" to "uservisible_field_information"
    And I set the field "everyonevisible_field" to "everyonevisible_field_information"
    And I set the field "teachervisible_field" to "teachervisible_field_information"
    And I click on "Update profile" "button"
    And I log out

  @javascript
  Scenario: Visible custom profile fields can be part of the sign up form for anonymous users.
    Given I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    And I expand all fieldsets
    Then I should not see "notvisible_field"
    And I should see "uservisible_field"
    And I should not see "everyonevisible_field"
    And I should see "teachervisible_field"

  @javascript
  Scenario: Visible custom profile fields can be part of the sign up form for guest users.
    Given I log in as "guest"
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    And I expand all fieldsets
    Then I should not see "notvisible_field"
    And I should see "uservisible_field"
    And I should not see "everyonevisible_field"
    And I should see "teachervisible_field"

  @javascript
  Scenario: User with moodle/user:update but without moodle/user:viewalldetails or moodle/site:viewuseridentity can only update visible profile fields.
    Given the following "roles" exist:
      | name         | shortname   | description | archetype |
      | Update Users | updateusers | updateusers |           |
    And the following "permission overrides" exist:
      | capability                   | permission | role        | contextlevel | reference |
      | moodle/user:update           | Allow      | updateusers | System       |           |
      | moodle/site:viewuseridentity | Prohibit   | updateusers | System       |           |
    And the following "users" exist:
      | username         | firstname   | lastname | email                   |
      | user_updateusers | updateusers | 1        | updateusers@example.com |
    And the following "role assigns" exist:
      | user             | role        | contextlevel | reference |
      | user_updateusers | updateusers | System       |           |
    And the following "course enrolments" exist:
      | user             | course | role           |
      | user_updateusers | C1     | editingteacher |
    And I log in as "user_updateusers"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "userwithinformation 1"

    Then I should see "everyonevisible_field"
    And I should see "everyonevisible_field_information"
    And I should not see "uservisible_field"
    And I should not see "uservisible_field_information"
    And I should not see "notvisible_field"
    And I should not see "notvisible_field_information"
    And I should not see "teachervisible_field"
    And I should not see "teachervisible_field_information"
    And I follow "Edit profile"
    And the following fields match these values:
      | everyonevisible_field | everyonevisible_field_information |
    And I should not see "uservisible_field"
    And I should not see "notvisible_field"
    And I should not see "teachervisible_field"

  @javascript
  Scenario: User with moodle/user:viewalldetails and moodle/site:viewuseridentity but without moodle/user:update can view all profile fields.
    Given the following "roles" exist:
      | name             | shortname      | description    | archetype |
      | View All Details | viewalldetails | viewalldetails |           |
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/user:viewalldetails | Allow      | viewalldetails | System       |           |
    And the following "users" exist:
      | username            | firstname      | lastname | email                      |
      | user_viewalldetails | viewalldetails | 1        | viewalldetails@example.com |
    And the following "role assigns" exist:
      | user                | role           | contextlevel | reference |
      | user_viewalldetails | viewalldetails | System       |           |
    And the following "course enrolments" exist:
      | user                | course | role           |
      | user_viewalldetails | C1     | editingteacher |
    And I log in as "user_viewalldetails"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "userwithinformation 1"

    Then I should see "everyonevisible_field"
    And I should see "everyonevisible_field_information"
    And I should see "uservisible_field"
    And I should see "uservisible_field_information"
    And I should see "notvisible_field"
    And I should see "notvisible_field_information"
    And I should see "teachervisible_field"
    And I should see "teachervisible_field_information"
    And I should not see "Edit profile"

  @javascript
  Scenario: User with moodle/user:viewalldetails and moodle/user:update and moodle/site:viewuseridentity capabilities can view and edit all profile fields.
    Given the following "roles" exist:
      | name                              | shortname                    | description                  | archetype |
      | View All Details and Update Users | viewalldetailsandupdateusers | viewalldetailsandupdateusers |           |
    And the following "permission overrides" exist:
      | capability                 | permission | role                         | contextlevel | reference |
      | moodle/user:viewalldetails | Allow      | viewalldetailsandupdateusers | System       |           |
      | moodle/user:update         | Allow      | viewalldetailsandupdateusers | System       |           |
    And the following "users" exist:
      | username                          | firstname                    | lastname | email                                    |
      | user_viewalldetailsandupdateusers | viewalldetailsandupdateusers | 1        | viewalldetailsandupdateusers@example.com |
    And the following "role assigns" exist:
      | user                              | role                         | contextlevel | reference |
      | user_viewalldetailsandupdateusers | viewalldetailsandupdateusers | System       |           |
    And the following "course enrolments" exist:
      | user                              | course | role           |
      | user_viewalldetailsandupdateusers | C1     | editingteacher |
    And I log in as "user_viewalldetailsandupdateusers"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "userwithinformation 1"

    Then I should see "everyonevisible_field"
    And I should see "everyonevisible_field_information"
    And I should see "uservisible_field"
    And I should see "uservisible_field_information"
    And I should see "notvisible_field"
    And I should see "notvisible_field_information"
    And I should see "teachervisible_field"
    And I should see "teachervisible_field_information"
    And I follow "Edit profile"
    And the following fields match these values:
      | everyonevisible_field | everyonevisible_field_information |
      | uservisible_field     | uservisible_field_information     |
      | notvisible_field      | notvisible_field_information      |
      | teachervisible_field  | teachervisible_field_information  |

  @javascript
  Scenario: Users can view and edit custom profile fields except those marked as not visible.
    Given I log in as "userwithinformation"
    And I follow "Profile" in the user menu

    Then I should see "everyonevisible_field"
    And I should see "everyonevisible_field_information"
    And I should see "uservisible_field"
    And I should see "uservisible_field_information"
    And I should see "teachervisible_field"
    And I should see "teachervisible_field_information"
    And I should not see "notvisible_field"
    And I should not see "notvisible_field_information"

    And I click on "Edit profile" "link" in the "region-main" "region"
    Then the following fields match these values:
      | everyonevisible_field | everyonevisible_field_information |
      | uservisible_field     | uservisible_field_information     |
    And I should not see "notvisible_field"
    And I should not see "notvisible_field_information"

  @javascript
  Scenario: Users can view but not edit custom profile fields when denied the edit own profile capability.
    Given the following "roles" exist:
      | name                | shortname          | description        | archetype |
      | Deny editownprofile | denyeditownprofile | denyeditownprofile |           |

    And the following "permission overrides" exist:
      | capability                 | permission | role               | contextlevel | reference |
      | moodle/user:editownprofile | Prohibit   | denyeditownprofile | System       |           |
    And the following "role assigns" exist:
      | user                | role               | contextlevel | reference |
      | userwithinformation | denyeditownprofile | System       |           |

    And I log in as "userwithinformation"
    And I follow "Profile" in the user menu

    Then I should see "everyonevisible_field"
    And I should see "everyonevisible_field_information"
    And I should see "uservisible_field"
    And I should see "uservisible_field_information"
    And I should see "teachervisible_field"
    And I should see "teachervisible_field_information"
    And I should not see "notvisible_field"
    And I should not see "notvisible_field_information"

    And I should not see "Edit profile"

  @javascript
  Scenario: User with parent permissions on other user context can view and edit all profile fields.
    Given the following "roles" exist:
      | name   | shortname  | description | archetype |
      | Parent | parent     | parent      |           |
    And the following "users" exist:
      | username  | firstname | lastname | email              |
      | parent    | Parent    | user     | parent@example.com |
    And the following "role assigns" exist:
      | user   | role   | contextlevel | reference            |
      | parent | parent | User         | userwithinformation  |
    And the following "permission overrides" exist:
      | capability                  | permission | role   | contextlevel | reference           |
      | moodle/user:viewalldetails  | Allow      | parent | User         | userwithinformation |
      | moodle/user:viewdetails     | Allow      | parent | User         | userwithinformation |
      | moodle/user:editprofile     | Allow      | parent | User         | userwithinformation |
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Mentees" block
    And I log out
    And I log in as "parent"
    And I am on site homepage
    When I follow "userwithinformation"
    Then I should see "everyonevisible_field"
    And I should see "everyonevisible_field_information"
    And I should see "uservisible_field"
    And I should see "uservisible_field_information"
    And I should see "teachervisible_field"
    And I should see "teachervisible_field_information"
    And I should not see "notvisible_field"
    And I should not see "notvisible_field_information"
    And I follow "Edit profile"
    And the following fields match these values:
      | everyonevisible_field | everyonevisible_field_information |
      | uservisible_field     | uservisible_field_information     |
      | teachervisible_field  | teachervisible_field_information  |
