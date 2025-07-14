@core @core_notes
Feature: Add notes to site users
  In order to share information with other users
  As an admin
  I need to add notes to site user accounts

  Scenario: Add a user site note
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    When I am on the "student1" "user > profile" page logged in as "admin"
    And I follow "Notes"
    And I follow "Add a new note"
    And I set the field "Content" to "Student 1 needs to pick up his game"
    And I press "Save changes"
    Then I should see "Student 1 needs to pick up his game" in the ".notelist" "css_element"
