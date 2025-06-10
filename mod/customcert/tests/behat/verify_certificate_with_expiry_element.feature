@mod @mod_customcert
Feature: Being able to verify a certificate with an expiry element
  In order to ensure expiry elements are working as expected
  As a teacher
  I need to verify a certificate with an expiry element

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity   | name                 | intro                      | course | idnumber    | verifyany |
      | customcert | Custom certificate 1 | Custom certificate 1 intro | C1     | customcert1 | 0         |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I navigate to "Edit certificate" in current page administration
    And I add the element "Code" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Font                     | Helvetica |
      | Size                     | 20        |
      | Colour                   | #045ECD   |
      | Width                    | 20        |
      | Reference point location | Top left  |
    And I press "Save changes"
    And I add the element "Expiry" to page "1" of the "Custom certificate 1" certificate template
    And I set the following fields to these values:
      | Date item                | Expiry date (1 year) |
      | Date format              | 2                    |
      | Start date               | award                |
      | Font                     | Helvetica            |
      | Size                     | 20                   |
      | Colour                   | #045ECD              |
      | Width                    | 20                   |
      | Reference point location | Top left             |
    And I press "Save changes"

  Scenario: Verify a certificate with an expiry element
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Custom certificate 1"
    And I press "View certificate"
    And I log out
    And I log in as "teacher1"
    And I visit the verification url for the "Custom certificate 1" certificate
    Then I verify the "Custom certificate 1" certificate for the user "student1"
