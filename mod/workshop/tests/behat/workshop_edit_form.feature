@mod @mod_workshop
Feature: Workshop assessment with grade to pass
  In order to use workshop activity
  As a teacher
  I need to be able to setup workshop with require assessment grade and grade to pass

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | student1 | Sam1      | Student1 | student1@example.com |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course1  | c1        | topics | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | c1     | student        |
      | teacher1 | c1     | editingteacher |

  @javascript
  Scenario: Setup workshop with assessment grade and pass grade set
    And I log in as "teacher1"
    And I am on "Course1" course homepage with editing mode on
    And I add a "Workshop" to section "0"
    And I expand all fieldsets
    When I set the following fields to these values:
      | Workshop name          | Test workshop                                     |
      | Completion tracking    | Show activity as complete when conditions are met |
      | Require grade          | Assessment                                        |
      | completionpassgrade    | 1                                                 |
    And I press "Save and display"
    And I should see "This activity does not have a valid grade to pass set. It may be set in the Grade section of the activity settings."
    And I set the field "Assessment grade to pass" to ""
    And I press "Save and display"
    And I should see "This activity does not have a valid grade to pass set. It may be set in the Grade section of the activity settings."
    And I set the field "Assessment grade to pass" to "81"
    And I press "Save and display"
    And I should see "The grade to pass can not be greater than the maximum possible grade 20"
    And I set the field "Assessment grade to pass" to "hello"
    And I press "Save and display"
    And I should see "You must enter a number here."
    And I set the field "Assessment grade to pass" to "12,34"
    And I press "Save and display"
    And I should see "You must enter a number here."
    And I set the field "Assessment grade to pass" to "10"
    And I press "Save and display"
    Then I should see "Setup phase" in the "h3#mod_workshop-userplanheading" "css_element"
