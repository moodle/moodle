@tool @tool_httpsreplace
Feature: View the httpsreplace report
  In order to switch to https
  As an admin
  I need to be able to automatically replace http links

  Background: Create some http links
    # This is a slow running feature (more than the default 30 seconds on slow environments)
    # so we are allowing up to 120 (factor = 4) seconds to the replacement to complete.
    Given I mark this test as slow setting a timeout factor of 4

    And I am on site homepage
    And the following "courses" exist:
      | fullname | shortname | category | summary                                                                                                     |
      | Course 1 | C1        | 0        | <img src="http://intentionally.unavailable/test.png"> <img src="http://download.moodle.org/unittest/test.jpg"> |
    And I log in as "admin"

  @javascript
  Scenario: Go to the HTTPS replace report screen. Make sure broken domains are reported.
    When I navigate to "Security > HTTP security" in site administration
    And I follow "HTTPS conversion tool"
    And I press "Continue"
    Then I should see "intentionally.unavailable"

  @javascript
  Scenario: Use the find and replace tool.
    When I navigate to "Security > HTTP security" in site administration
    And I follow "HTTPS conversion tool"
    And I press "Continue"
    And I set the field "I understand the risks of this operation" to "1"
    And I press "Perform conversion"
    Then I should see "intentionally.unavailable"
    And I should see "download.moodle.org"
