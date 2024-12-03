#@block @login @javascript
#Feature: User login
#  In order to access the system
#  As a Moodle user
#  I need to log in successfully

#  Background:
#    Given the following "users" exist:
#      | username | firstname | lastname | email | password |
#      | admin    | Admin     | User     |       |          |

#  @login
#  Scenario: Successful login
#    Given I am on site homepage
#    And I log in as "admin"
#    And I set the field "Username" to "admin"
#    And I set the field "Password" to ""
#    And I press "Log in"
#    Then I should see "Dashboard"


@block @javascript
Feature: User login
  In order to access the system
  As a Moodle user
  I need to log in successfully

  # Feature: Progress Bar
  #   In order to see Progress Bar
  #   As a moodle user
  #   I need to be enrolled in a course with assigned work

  Scenario: Checking to see if there is no progress bar
    #Scenario Successful login and Course creation and not seeing a Progress Bar
    Given I log in as "admin"
    And I am on site homepage
    Then I should see "Dashboard"
    And I should see "My courses"
    Then I click on "My courses" "link"
    Then I should see "My courses"
    And I click on "Create new course" "link"
    And I set the field "Course full name" to "Course 1"
    And I set the field "Course short name" to "C1"
    Then I press "Save and display"
    Then I click on "My courses" "link"
    Then I should see "Course 1"
    And I should not see "Progress Bar"

  Scenario: Successful login
    #Scenario Successful login and Course creation and not seeing a Progress Bar
    Given I log in as "admin"
    And I am on site homepage
    Then I should see "Dashboard"
    And I should see "My courses"
    Then I click on "My courses" "link"
    Then I should see "My courses"
    And I click on "Create new course" "link"
    And I set the field "Course full name" to "Course 1"
    And I set the field "Course short name" to "C1"
    Then I press "Save and display"
    And edit mode should be available on the current page
    Then I open my profile in edit mode
    #Then I click on "My courses" "link"
    #Then I should see "Course 1"
