@core @core_badges @_file_upload
Feature: Award badges based on cohort
  In order to award badges to users based on their cohort membership
  As an admin
  I need to add cohort criteria to badges in the system

  @javascript
  Scenario: Award cohort membership badge for a member of a single cohort.
    Given the following "cohorts" exist:
      | name       | idnumber |
      | One Cohort | CH1      |
      | Two Cohort | CH2      |
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | user1    | First     | User     | first@example.com  |
      | user2    | Second    | User     | second@example.com |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | CH1   |
      | user2 | CH2   |
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Cohort membership"
    And I set the field "id_cohort_cohorts" to "One Cohort"
    And I press "Save"
    And I press "Enable access"
    When I press "Continue"
    Then I should see "Recipients (1)"
    And I log out
    And I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"

  @javascript
  Scenario: Award cohort membership badge for a member of all required cohorts.
    Given the following "cohorts" exist:
      | name         | idnumber |
      | One Cohort   | CH1      |
      | Two Cohort   | CH2      |
      | Three Cohort | CH3      |
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | user1    | First     | User     | first@example.com  |
      | user2    | Second    | User     | second@example.com |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | CH1   |
      | user1 | CH2   |
      | user2 | CH1   |
      | user2 | CH3   |
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Cohort membership"
    And I expand all fieldsets
    And I set the field "id_cohort_cohorts" to "One Cohort,Two Cohort"
    And I set the field "id_agg_1" to "1"
    And I press "Save"
    When I press "Enable access"
    And I press "Continue"
    Then I should see "Recipients (1)"
    And I log out
    And I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"

  @javascript
  Scenario: Award cohort membership badge for a member of any required cohorts.
    Given the following "cohorts" exist:
      | name         | idnumber |
      | One Cohort   | CH1      |
      | Two Cohort   | CH2      |
      | Three Cohort | CH3      |
    And the following "users" exist:
      | username | firstname | lastname | email              |
      | user1    | First     | User     | first@example.com  |
      | user2    | Second    | User     | second@example.com |
      | user3    | third     | User     | third@example.com  |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | CH1   |
      | user1 | CH2   |
      | user2 | CH1   |
      | user2 | CH3   |
      | user3 | CH2   |
      | user3 | CH3   |
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Cohort membership"
    And I set the field "id_cohort_cohorts" to "One Cohort"
    And I press "Save"
    When I press "Enable access"
    And I press "Continue"
    Then I should see "Recipients (2)"
    And I log out
    And I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"
    And I log out
    And I log in as "user2"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"

  @javascript
  Scenario: Award badge based on a single cohort membership and other criteria.
    Given the following "cohorts" exist:
      | name       | idnumber |
      | One Cohort | CH1      |
      | Two Cohort | CH2      |
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | user1    | First     | User     | first@example.com  |
      | user2    | Second    | User     | second@example.com |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | CH1   |
      | user2 | CH2   |
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Cohort membership"
    And I set the field "id_cohort_cohorts" to "One Cohort"
    And I press "Save"
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    And I set the field "Manager" to "1"
    And I set the field "Any of the selected roles awards the badge" to "1"
    And I press "Save"
    When I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "First User (first@example.com)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Second User (second@example.com)"
    And I press "Award badge"
    And I follow "Site Badge"
    Then I should see "Recipients (1)"
    And I log out
    And I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"
    And I log out
    And I log in as "user2"
    And I follow "Profile" in the user menu
    And I should not see "Site Badge"

  @javascript
  Scenario: Award badge based on a single cohort membership or other criteria.
    Given the following "cohorts" exist:
      | name       | idnumber |
      | One Cohort | CH1      |
      | Two Cohort | CH2      |
    And the following "users" exist:
      | username | firstname | lastname | email              |
      | user1    | First     | User     | first@example.com  |
      | user2    | Second    | User     | second@example.com |
      | user3    | Third     | User     | third@example.com  |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | CH1   |
      | user2 | CH2   |
      | user3 | CH2   |
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Cohort membership"
    And I set the field "id_cohort_cohorts" to "One Cohort"
    And I press "Save"
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    And I set the field "Manager" to "1"
    And I set the field "Any of the selected roles awards the badge" to "1"
    And I press "Save"
    And I set the field "update" to "Any"
    When I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (1)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "First User (first@example.com)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Second User (second@example.com)"
    And I press "Award badge"
    And I follow "Site Badge"
    Then I should see "Recipients (2)"
    And I log out
    And I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"
    And I log out
    And I log in as "user2"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"
    And I log out
    And I log in as "user3"
    And I follow "Profile" in the user menu
    And I should not see "Site Badge"

  @javascript
  Scenario: Award badge based on a multiple cohort membership or other criteria.
    Given the following "cohorts" exist:
      | name       | idnumber |
      | One Cohort | CH1      |
      | Two Cohort | CH2      |
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | user1    | First     | User     | first@example.com  |
      | user2    | Second    | User     | second@example.com |
      | user3    | third     | User     | third@example.com  |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | CH1   |
      | user1 | CH2   |
      | user2 | CH2   |
      | user2 | CH2   |
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Cohort membership"
    And I set the field "id_cohort_cohorts" to "One Cohort"
    And I press "Save"
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    And I set the field "Manager" to "1"
    And I set the field "Any of the selected roles awards the badge" to "1"
    And I press "Save"
    And I set the field "update" to "Any"
    When I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (1)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "First User (first@example.com)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Second User (second@example.com)"
    And I press "Award badge"
    And I follow "Site Badge"
    Then I should see "Recipients (2)"
    And I log out
    And I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"
    And I log out
    And I log in as "user2"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"
    And I log out
    And I log in as "user3"
    And I follow "Profile" in the user menu
    And I should not see "Site Badge"

  @javascript
  Scenario: Award badge based on a multiple cohort membership and other criteria.
    Given the following "cohorts" exist:
      | name       | idnumber |
      | One Cohort | CH1      |
      | Two Cohort | CH2      |
    And the following "users" exist:
      | username | firstname | lastname | email              |
      | user1    | First     | User     | first@example.com  |
      | user2    | Second    | User     | second@example.com |
      | user3    | Third     | User     | third@example.com  |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | CH1   |
      | user1 | CH2   |
      | user2 | CH1   |
      | user3 | CH2   |
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Cohort membership"
    And I expand all fieldsets
    And I set the field "id_cohort_cohorts" to "One Cohort,Two Cohort"
    And I set the field "id_agg_1" to "1"
    And I press "Save"
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    And I set the field "Manager" to "1"
    And I set the field "Any of the selected roles awards the badge" to "1"
    And I press "Save"
    And I set the field "update" to "All"
    When I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "First User (first@example.com)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Second User (second@example.com)"
    And I press "Award badge"
    And I follow "Site Badge"
    Then I should see "Recipients (1)"
    And I log out
    And I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge"
    And I log out
    And I log in as "user2"
    And I follow "Profile" in the user menu
    And I should not see "Site Badge"
    And I log out
    And I log in as "user3"
    And I follow "Profile" in the user menu
    And I should not see "Site Badge"

  @javascript
  Scenario: Award multiple badges based on single cohort membership
    Given the following "cohorts" exist:
      | name       | idnumber |
      | One Cohort | CH1      |
      | Two Cohort | CH2      |
    And the following "users" exist:
      | username | firstname | lastname | email              |
      | user1    | First     | User     | first@example.com  |
      | user2    | Second    | User     | second@example.com |
      | user3    | Third     | User     | third@example.com  |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | CH1   |
      | user1 | CH2   |
      | user2 | CH2   |
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge 1 |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Cohort membership"
    And I set the field "id_cohort_cohorts" to "One Cohort"
    And I press "Save"
    And I press "Enable access"
    When I press "Continue"
    And I should see "Recipients (1)"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge 2 |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Cohort membership"
    And I set the field "id_cohort_cohorts" to "Two Cohort"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    Then I should see "Recipients (2)"
    And I log out
    And I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge 1"
    And I should see "Site Badge 2"
    And I log out
    And I log in as "user2"
    And I follow "Profile" in the user menu
    And I should not see "Site Badge 1"
    And I should see "Site Badge 2"
    And I log out
    And I log in as "user3"
    And I follow "Profile" in the user menu
    And I should not see "Site Badge 1"
    And I should not see "Site Badge 2"

  @javascript
  Scenario: Award multiple badges based on multiple cohort memberships
    Given the following "cohorts" exist:
      | name         | idnumber |
      | One Cohort   | CH1      |
      | Two Cohort   | CH2      |
      | Three Cohort | CH3      |
    And the following "users" exist:
      | username | firstname | lastname | email              |
      | user1    | First     | User     | first@example.com  |
      | user2    | Second    | User     | second@example.com |
      | user3    | Third     | User     | third@example.com  |
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge 1 |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Cohort membership"
    And I expand all fieldsets
    And I set the field "id_cohort_cohorts" to "One Cohort,Two Cohort"
    And I set the field "id_agg_1" to "1"
    And I press "Save"
    And I press "Enable access"
    When I press "Continue"
    And I should see "Recipients (0)"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Site Badge 2 |
      | Description | Site badge description |
      | issuername | Tester of site badge |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Cohort membership"
    And I expand all fieldsets
    And I set the field "id_cohort_cohorts" to "Three Cohort,Two Cohort"
    And I set the field "id_agg_1" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    Then I navigate to "Users > Accounts >Cohorts" in site administration
    And I add "First User (first@example.com)" user to "CH1" cohort members
    And I add "First User (first@example.com)" user to "CH2" cohort members
    And I add "Second User (second@example.com)" user to "CH2" cohort members
    And I add "Second User (second@example.com)" user to "CH3" cohort members
    And I log out
    And I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site Badge 1"
    And I should not see "Site Badge 2"
    And I log out
    And I log in as "user2"
    And I follow "Profile" in the user menu
    And I should not see "Site Badge 1"
    And I should see "Site Badge 2"
    And I log out
    And I log in as "user3"
    And I follow "Profile" in the user menu
    And I should not see "Site Badge 1"
    And I should not see "Site Badge 2"
