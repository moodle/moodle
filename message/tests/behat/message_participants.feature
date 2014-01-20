@core @core_message
Feature: An user can message course participants
  In order to communicate efficiently with my students
  As a teacher
  I need to message them all

  @javascript
  Scenario: An user can message multiple course participants including him/her self
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
      | student3 | Student | 3 | student3@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Participants"
    When I click on "input[type='checkbox']" "css_element" in the "Teacher 1" "table_row"
    And I click on "input[type='checkbox']" "css_element" in the "Student 1" "table_row"
    And I select "Send a message" from "With selected users..."
    And I fill the moodle form with:
      | messagebody | Here it is, the message content |
    And I press "Preview"
    And I press "Send message"
    And I follow "Home"
    And I expand "My profile" node
    And I follow "Messages"
    And I select "Recent conversations" from "Message navigation:"
    Then I should see "Here it is, the message content"
    And I should see "Student 1"
    And I click on "this conversation" "link" in the "//div[@class='singlemessage'][contains(., 'Teacher 1')]" "xpath_element"
    And I should see "Here it is, the message content"
