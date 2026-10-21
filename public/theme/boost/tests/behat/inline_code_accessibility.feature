@theme_boost @accessibility
Feature: Inline code accessibility
  In order to read inline code
  As a user
  I need inline code to have enough colour contrast on tinted surfaces

  @javascript
  Scenario: Inline code meets colour contrast standards on tinted surfaces
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "activities" exist:
      | activity | name      | course | contentformat | content                                                                                                                                                                                                    |
      | page     | Code page | C1     | 1             | <div class="alert alert-info"><code>admin/cli/cron.php</code></div><div class="alert alert-light"><code>admin/cli/cron.php</code></div><div class="alert alert-dark"><code>admin/cli/cron.php</code></div> |
    When I am on the "Code page" "page activity" page logged in as student1
    # The old code colour failed on alert-info and alert-dark. Code in table rows is not checked here: axe cannot see
    # the shading of a striped or hovered row, which is painted as a box shadow.
    Then the "#region-main .generalbox" "css_element" should meet accessibility standards
