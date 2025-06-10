@core @core_calendar
Feature: Verify that the day and month names are displayed using UTF-8
  In order to be able to use the calendar using different languages
  As a user
  I need to see the day and month names displayed properly

  Background:
    Given remote langimport tests are enabled

  Scenario Outline: View the calendar for December 2012 with correct UTF-8
    Given the following "language pack" exists:
      | language | <lang> |
    And I log in as "admin"
    And I follow "Preferences" in the user menu
    And I follow "Preferred language"
    And I set the field "Preferred language" to "<lang>"
    And I press "Save changes"
    When I view the calendar for "12" "2022"
    Then I should see "<month>"
    And I should see "<day>"

    Examples:
      | lang  | month     | day |
      | en_ar | December  | Sat |
      | es    | diciembre | Sáb |
      | fr    | décembre  | Sa  |
      | ru    | декабря   | Сб  |
