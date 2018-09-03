@core @core_user
Feature: Create manually an user.
  In order create a user properly
  As an admin
  I need to be able to add new users and edit their fields.

  @javascript
  Scenario: Change default language for a new user
    Given I log in as "admin"
    When I navigate to "Add a new user" node in "Site administration > Users > Accounts"
    Then I should see "Preferred language"

  @javascript
  Scenario: Language not displayed when editing an existing user
    Given the following "users" exist:
      | username  | firstname | lastname | email                 |
      | student1  | Student   | 1        | student1@example.com  |
    When I log in as "admin"
    And I navigate to "Browse list of users" node in "Site administration > Users > Accounts"
    And I follow "Student 1"
    And I follow "Edit profile"
    Then I should not see "Preferred language"
