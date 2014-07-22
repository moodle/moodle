@core @core_cohort
Feature: View cohort list
  In order to operate with cohorts
  As an admin or manager
  I need to be able to view the list of cohorts in the system

  Background:
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
      | Cat 2 | 0        | CAT2     |
      | Cat 3 | CAT1     | CAT3     |
    And the following "cohorts" exist:
      | name          | idnumber |
      | System cohort | CH0      |
    And the following "cohorts" exist:
      | name                 | idnumber | contextlevel | reference |
      | Cohort in category 1 | CH1      | Category     | CAT1      |
      | Cohort in category 2 | CH2      | Category     | CAT2      |
      | Cohort in category 3 | CH3      | Category     | CAT3      |
    Given the following "users" exist:
      | username | firstname | lastname | email           |
      | user1    | First     | User     | first@user.com  |
      | user2    | Second    | User     | second@user.com |
    And the following "role assigns" exist:
      | user  | role    | contextlevel | reference |
      | user1 | manager | System       |           |
      | user2 | manager | Category     | CAT1      |

  @javascript
  Scenario: Admin can see system cohorts and all cohorts
    When I log in as "admin"
    And I navigate to "Cohorts" node in "Site administration > Users > Accounts"
    Then I should see "System cohort"
    And I should not see "Cohort in category"
    And I follow "All cohorts"
    And I should see "System cohort"
    And I should see "Cohort in category 1"
    And I should see "Cohort in category 2"
    And I should see "Cohort in category 3"
    And I log out

  @javascript
  Scenario: Manager can see system cohorts and all cohorts
    When I log in as "user1"
    And I navigate to "Cohorts" node in "Site administration > Users > Accounts"
    Then I should see "System cohort"
    And I should not see "Cohort in category"
    And I follow "All cohorts"
    And I should see "System cohort"
    And I should see "Cohort in category 1"
    And I should see "Cohort in category 2"
    And I should see "Cohort in category 3"
    And I log out

  @javascript
  Scenario: Manager in category can see cohorts in the category
    When I log in as "user2"
    And I follow "Courses"
    And I follow "Cat 1"
    And I follow "Cohorts"
    And I should not see "All cohorts"
    And I should not see "System cohort"
    And I should see "Cohort in category 1"
    And I should not see "Cohort in category 2"
    And I should not see "Cohort in category 3"
    And I log out
