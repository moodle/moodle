@local @local_adminer
Feature: Start the adminer modal iframe
  In order to use adminer
  As an admin
  I need to be able to see the adminer frame

  @javascript
  Scenario: Start without the current database
    Given the following config values are set as admin:
      | startwithdb  | 0 | local_adminer |
    And I log in as "admin"
    And I click on "Site administration" "link"
    And I click on "Server" "link"
    And I should see "Moodle Adminer"
    And I click on "Moodle Adminer" "link" in the "#linkserver" "css_element"
    And I wait "2" seconds
    And I switch to "adminer-frame" iframe
    Then I should see "Adminer started without database"

  @javascript
  Scenario: Start with the current database
    Given the following config values are set as admin:
      | startwithdb  | 1 | local_adminer |
    And I log in as "admin"
    And I click on "Site administration" "link"
    And I click on "Server" "link"
    And I should see "Moodle Adminer"
    And I click on "Moodle Adminer" "link" in the "#linkserver" "css_element"
    And I wait "2" seconds
    And I switch to "adminer-frame" iframe
    Then I should see "Adminer started with database"

  @javascript
  Scenario: Prevent starting with wrong secret
    Given the following config values are set as admin:
      | local_adminer_secret  | mysecret |
    And I log in as "admin"
    And I click on "Site administration" "link"
    And I click on "Server" "link"
    And I should see "Moodle Adminer"
    And I click on "Moodle Adminer" "link" in the "#linkserver" "css_element"
    And I wait "2" seconds
    And I switch to "adminer-frame" iframe
    And I should see "Adminer secret"
    And I set the field "adminersecret" to "abc"
    And I click on "input#id_submitbutton" "css_element"
    Then I should see "Wrong Adminer secret!"

  @javascript
  Scenario: Start using a secret
    Given the following config values are set as admin:
      | local_adminer_secret  | mysecret |
    And I log in as "admin"
    And I click on "Site administration" "link"
    And I click on "Server" "link"
    And I should see "Moodle Adminer"
    And I click on "Moodle Adminer" "link" in the "#linkserver" "css_element"
    And I wait "2" seconds
    And I switch to "adminer-frame" iframe
    And I should see "Adminer secret"
    And I set the field "adminersecret" to "mysecret"
    And I click on "input#id_submitbutton" "css_element"
    Then I should see "Adminer started"
