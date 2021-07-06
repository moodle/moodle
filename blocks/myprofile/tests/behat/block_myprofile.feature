@block @block_myprofile
Feature: The logged in user block allows users to view their profile information
  In order to enable the logged in user block
  As a user
  I can add the logged in user block and configure it to show my information

  Scenario: Configure the logged in user block to show / hide the users country
    Given the following "users" exist:
      | username | firstname | lastname | email                | country   |
      | teacher1 | Teacher   | One      | teacher1@example.com | AU        |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display country       | No |
    And I press "Save changes"
    Then I should see "Teacher One" in the "Logged in user" "block"
    And I should not see "Australia" in the "Logged in user" "block"
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display country | Yes |
    And I press "Save changes"
    And I should see "Australia" in the "Logged in user" "block"

  Scenario: Configure the logged in user block to show / hide the users city
    Given the following "users" exist:
      | username | firstname | lastname | email                | city  |
      | teacher1 | Teacher   | One      | teacher1@example.com | Perth |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display city          | No |
    And I press "Save changes"
    Then I should see "Teacher One" in the "Logged in user" "block"
    And I should not see "Perth" in the "Logged in user" "block"
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display city | Yes |
    And I press "Save changes"
    And I should see "Perth" in the "Logged in user" "block"

  Scenario: Configure the logged in user block to show / hide the users email
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display email         | No |
    And I press "Save changes"
    Then I should see "Teacher One" in the "Logged in user" "block"
    And I should not see "teacher1@example.com" in the "Logged in user" "block"
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display email | Yes |
    And I press "Save changes"
    And I should see "teacher1@example.com" in the "Logged in user" "block"

  Scenario: Configure the logged in user block to show / hide the users phone
    Given the following "users" exist:
      | username | firstname | lastname | email                | phone1   |
      | teacher1 | Teacher   | One      | teacher1@example.com | 555-5555 |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display phone         | No |
    And I press "Save changes"
    Then I should see "Teacher One" in the "Logged in user" "block"
    And I should not see "555-5555" in the "Logged in user" "block"
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display phone | Yes |
    And I press "Save changes"
    And I should see "555-5555" in the "Logged in user" "block"

  Scenario: Configure the logged in user block to show / hide the users mobile phone
    Given the following "users" exist:
      | username | firstname | lastname | email                | phone2   |
      | teacher1 | Teacher   | One      | teacher1@example.com | 555-5555 |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display mobile phone | No |
    And I press "Save changes"
    Then I should see "Teacher One" in the "Logged in user" "block"
    And I should not see "555-5555" in the "Logged in user" "block"
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display mobile phone | Yes |
    And I press "Save changes"
    And I should see "555-5555" in the "Logged in user" "block"

  Scenario: Configure the logged in user block to show / hide the users Institution
    Given the following "users" exist:
      | username | firstname | lastname | email                | institution   |
      | teacher1 | Teacher   | One      | teacher1@example.com | myinstitution |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display institution | No |
    And I press "Save changes"
    Then I should see "Teacher One" in the "Logged in user" "block"
    And I should not see "myinstitution" in the "Logged in user" "block"
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display institution | Yes |
    And I press "Save changes"
    And I should see "myinstitution" in the "Logged in user" "block"

  Scenario: Configure the logged in user block to show / hide the users address
    Given the following "users" exist:
      | username | firstname | lastname | email                | address   |
      | teacher1 | Teacher   | One      | teacher1@example.com | myaddress |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display address | No |
    And I press "Save changes"
    Then I should see "Teacher One" in the "Logged in user" "block"
    And I should not see "myaddress" in the "Logged in user" "block"
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display address | Yes |
    And I press "Save changes"
    And I should see "myaddress" in the "Logged in user" "block"

  Scenario: Configure the logged in user block to show / hide the users first access
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display first access | No |
    And I press "Save changes"
    Then I should see "Teacher One" in the "Logged in user" "block"
    And I should not see "First access:" in the "Logged in user" "block"
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display first access | Yes |
    And I press "Save changes"
    And I should see "First access:" in the "Logged in user" "block"

  Scenario: Configure the logged in user block to show / hide the users last access
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display last access | No |
    And I press "Save changes"
    Then I should see "Teacher One" in the "Logged in user" "block"
    And I should not see "Last access:" in the "Logged in user" "block"
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display last access | Yes |
    And I press "Save changes"
    And I should see "Last access:" in the "Logged in user" "block"

  Scenario: Configure the logged in user block to show / hide the users current login
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display current login | No |
    And I press "Save changes"
    Then I should see "Teacher One" in the "Logged in user" "block"
    And I should not see "Log in:" in the "Logged in user" "block"
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display current login | Yes |
    And I press "Save changes"
    And I should see "Log in:" in the "Logged in user" "block"

  Scenario: Configure the logged in user block to show / hide the users last ip
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display last IP | No |
    And I press "Save changes"
    Then I should see "Teacher One" in the "Logged in user" "block"
    And I should not see "IP:" in the "Logged in user" "block"
    And I configure the "Logged in user" block
    And I set the following fields to these values:
      | Display last IP | Yes |
    And I press "Save changes"
    And I should see "IP:" in the "Logged in user" "block"
