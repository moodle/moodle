@core @core_user
Feature: Manually create a user
  In order create a user properly
  As an admin
  I need to be able to add new users and edit their fields.

  Scenario: Change default language for a new user
    Given I log in as "admin"
    When I navigate to "Users > Accounts > Add a new user" in site administration
    Then I should see "Preferred language"

  Scenario: Language not displayed when editing an existing user
    Given the following "users" exist:
      | username  | firstname | lastname | email                 |
      | student1  | Student   | 1        | student1@example.com  |
    When I am on the "student1" "user > editing" page logged in as "admin"
    Then I should not see "Preferred language"
