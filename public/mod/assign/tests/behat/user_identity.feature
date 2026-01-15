@mod @mod_assign @javascript
Feature: An appropriate authorised user can see custom user identity fields on assignment areas.

  Background:
    Given the following "custom profile fields" exist:
      | datatype  | shortname           | name                | param1 | param2 | visible |
      | text      | registrationnumber  | Registration Number | 30     | 30     | 3       |
    And the following "users" exist:
      | username | firstname | lastname    | email                | department | profile_field_registrationnumber | firstnamephonetic |
      | teacher1 | Teacher   | 1           | teacher1@example.com |            |                                  |                   |
      | user1    | User      | One         | one@example.com      | Attack     | 12345678901                      | Yewzer            |
      | user2    | User      | Two         | two@example.com      | Defence    | 12345678902                      | Yoozare           |
      | user3    | User      | Indigo Blue | two@example.com      | Defence    | 12345678903                      | Yoozare           |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | user3    | C1     | student        |
    And the following "activity" exists:
      | activity                                      | assign                  |
      | course                                        | C1                      |
      | name                                          | Test assignment name    |
      | intro                                         | Submit your online text |
      | submissiondrafts                              | 0                       |
      | assignsubmission_onlinetext_enabled           | 1                       |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | user3     | I'm the user3 submission     |
    And the following config values are set as admin:
      | showuseridentity | email,profile_field_registrationnumber |

  Scenario: Assignment grading table displays custom identity fields
    When I am on the "Test assignment name" Activity page logged in as teacher1
    And I follow "Submissions"
    Then I should see "Registration Number" in the "table" "css_element"
    And I should see "12345678903"
