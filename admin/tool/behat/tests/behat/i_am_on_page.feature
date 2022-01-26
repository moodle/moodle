@tool @tool_behat
Feature: Use core page resolvers for the I am on the page steps
  In order to write tests correctly
  As a developer
  I need to have steps which take me straight to a page

  Scenario Outline: When I am on an instance
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
    And the following "course" exists:
      | fullname  | Economics 101 |
      | shortname | ECON101       |
      | idnumber  | 2021-econ101  |
    And the following "activity" exists:
      | course   | ECON101                   |
      | activity | forum                     |
      | name     | Fundamentals of Economics |
      | idnumber | fundamentalsofeconomics   |
    And I log in as "admin"
    When I am on the <identifier> <type> page
    Then I should see "<shouldsee>"

    Examples:
      | description              | identifier                  | type                         | shouldsee                   |
      | Course Category idnumber | CAT1                        | category                     | Add a new course            |
      | Course Category name     | "Cat 1"                     | Category                     | Add a new course            |
      | Course Full name         | "Economics 101"             | course                       | Fundamentals of Economics   |
      | Course Short name        | ECON101                     | COURSE                       | Fundamentals of Economics   |
      | Course idnumber          | "2021-econ101"              | Course                       | Fundamentals of Economics   |
      | Forum idnumber           | fundamentalsofeconomics     | Activity                     | Add discussion topic        |
      | Generic activity editing | fundamentalsofeconomics     | "Activity editing"           | Updating: Forum             |
      | Forum name               | "Fundamentals of Economics" | "Forum activity"             | Add discussion topic        |
      | Forum name editing       | "Fundamentals of Economics" | "Forum activity editing"     | Updating: Forum             |
      | Forum name permissions   | "Fundamentals of Economics" | "Forum activity permissions" | Permissions in Forum: Fun   |
      | Forum name roles         | "Fundamentals of Economics" | "Forum activity roles"       | Assign roles in Forum: Fun  |

  Scenario Outline: When I am on an instance logged in as
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
    And the following "course" exists:
      | fullname  | Economics 101 |
      | shortname | ECON101       |
      | idnumber  | 2021-econ101  |
    And the following "activity" exists:
      | course   | ECON101                   |
      | activity | forum                     |
      | name     | Fundamentals of Economics |
      | idnumber | fundamentalsofeconomics   |
    When I am on the <identifier> <type> page logged in as admin
    Then I should see "<shouldsee>"

    Examples:
      | description              | identifier                  | type                         | shouldsee                  |
      | Course Category idnumber | CAT1                        | category                     | Add a new course           |
      | Course Category name     | "Cat 1"                     | Category                     | Add a new course           |
      | Course Full name         | "Economics 101"             | course                       | Fundamentals of Economics  |
      | Course Short name        | ECON101                     | COURSE                       | Fundamentals of Economics  |
      | Course idnumber          | "2021-econ101"              | Course                       | Fundamentals of Economics  |
      | Forum idnumber           | fundamentalsofeconomics     | Activity                     | Add discussion topic       |
      | Generic activity editing | fundamentalsofeconomics     | "Activity editing"           | Updating: Forum            |
      | Forum name               | "Fundamentals of Economics" | "Forum activity"             | Add discussion topic       |
      | Forum name editing       | "Fundamentals of Economics" | "Forum activity editing"     | Updating: Forum            |
      | Forum name permissions   | "Fundamentals of Economics" | "Forum activity permissions" | Permissions in Forum: Fun  |
      | Forum name roles         | "Fundamentals of Economics" | "Forum activity roles"       | Assign roles in Forum: Fun |

  Scenario Outline: When I am on a named page
    Given I log in as "admin"
    When I am on the <identifier> page
    Then I should see "<shouldsee>"

    Examples:
      | description | identifier              | shouldsee                   |
      | Admin page  | "Admin notifications"   | Check for available updates |
      | Home page   | Homepage                | Calendar                    |

  Scenario Outline: When I am on a named page logged in as
    When I am on the <identifier> page logged in as admin
    Then I should see "<shouldsee>"

    Examples:
      | description | identifier            | shouldsee                   |
      | Admin page  | "Admin notifications" | Check for available updates |
      | Home page   | Homepage              | Calendar                    |
