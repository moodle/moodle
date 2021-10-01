@core @core_user
Feature: Filter users by time created
  In order to find users created relative to a date
  As an admin
  I need to be able to filter users by their time created date

  Background:
    Given the following "users" exist:
     | username | firstname | lastname | email              |
     | user01   | User      | One      | user01@example.com |

  @javascript
  Scenario Outline: Matching user filter by time created
    Given I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "Show more..."
    # Set the filter fields, excluding admin.
    When I set the following fields to these values:
      | <enablefield>[enabled] | 1               |
      | <enablefield>[year]    | <year>          |
      | Username field limiter | doesn't contain |
      | Username value         | admin           |
    And I press "Add filter"
    Then I should see "User One" in the "users" "table"
    And I should see "1 / 2 Users"
    Examples:
      | enablefield     | year                |
      # Time created, is after.
      | timecreated_sdt | ## -1 year ## %Y ## |
      # Time created, is before.
      | timecreated_edt | ## +1 year ## %Y ## |

  @javascript
  Scenario Outline: Non-matching user filter by time created
    Given I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "Show more..."
    # Set the filter fields, excluding admin.
    When I set the following fields to these values:
      | <enablefield>[enabled] | 1               |
      | <enablefield>[year]    | <year>          |
      | Username field limiter | doesn't contain |
      | Username value         | admin           |
    And I press "Add filter"
    Then "Users" "table" should not exist
    And I should see "0 / 2 Users"
    Examples:
      | enablefield     | year                |
      # Time created, is after.
      | timecreated_sdt | ## +1 year ## %Y ## |
      # Time created, is before.
      | timecreated_edt | ## -1 year ## %Y ## |
