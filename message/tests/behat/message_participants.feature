@core @core_message
Feature: An user can message course participants
  In order to communicate efficiently with my students
  As a teacher
  I need to message them all

  @javascript
  Scenario: An user can message multiple course participants including him/her self
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Participants"
    When I click on "input[type='checkbox']" "css_element" in the "Teacher 1" "table_row"
    And I click on "input[type='checkbox']" "css_element" in the "Student 1" "table_row"
    And I set the field "With selected users..." to "Send a message"
    And I set the following fields to these values:
      | messagebody | Here it is, the message content |
    And I press "Preview"
    And I press "Send message"
    And I follow "Messages" in the user menu
    And I set the field "Message navigation:" to "Recent conversations"
    Then I should see "Here it is, the message content"
    And I should see "Student 1"
    And I click on "this conversation" "link" in the "//div[@class='singlemessage'][contains(., 'Teacher 1')]" "xpath_element"
    And I should see "Here it is, the message content"
