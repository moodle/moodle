@block @block_activity_modules
Feature: Block activity modules
  In order to overview activity modules in a course
  As a manager
  I can add activities block in a course or on the frontpage

  Background:
    Given I log in as "admin"
    And I navigate to "Manage activities" node in "Site administration > Plugins > Activity modules"
    And I click on "//a[@title=\"Show\"]" "xpath_element" in the "Feedback" "table_row"

  Scenario: Add activities block on the frontpage
    Given the following "activities" exist:
      | activity   | name                        | intro                              | course               | idnumber    |
      | assign     | Frontpage assignment name   | Frontpage assignment description   | Acceptance test site | assign0     |
      | book       | Frontpage book name         | Frontpage book description         | Acceptance test site | book0       |
      | chat       | Frontpage chat name         | Frontpage chat description         | Acceptance test site | chat0       |
      | choice     | Frontpage choice name       | Frontpage choice description       | Acceptance test site | choice0     |
      | data       | Frontpage database name     | Frontpage database description     | Acceptance test site | data0       |
      | feedback   | Frontpage feedback name     | Frontpage feedback description     | Acceptance test site | feedback0   |
      | forum      | Frontpage forum name        | Frontpage forum description        | Acceptance test site | forum0      |
      | label      | Frontpage label name        | Frontpage label description        | Acceptance test site | label0      |
      | lti        | Frontpage lti name          | Frontpage lti description          | Acceptance test site | lti0        |
      | page       | Frontpage page name         | Frontpage page description         | Acceptance test site | page0       |
      | quiz       | Frontpage quiz name         | Frontpage quiz description         | Acceptance test site | quiz0       |
      | resource   | Frontpage resource name     | Frontpage resource description     | Acceptance test site | resource0   |
      | imscp      | Frontpage imscp name        | Frontpage imscp description        | Acceptance test site | imscp0      |
      | folder     | Frontpage folder name       | Frontpage folder description       | Acceptance test site | folder0     |
      | glossary   | Frontpage glossary name     | Frontpage glossary description     | Acceptance test site | glossary0   |
      | scorm      | Frontpage scorm name        | Frontpage scorm description        | Acceptance test site | scorm0      |
      | lesson     | Frontpage lesson name       | Frontpage lesson description       | Acceptance test site | lesson0     |
      | survey     | Frontpage survey name       | Frontpage survey description       | Acceptance test site | survey0     |
      | url        | Frontpage url name          | Frontpage url description          | Acceptance test site | url0        |
      | wiki       | Frontpage wiki name         | Frontpage wiki description         | Acceptance test site | wiki0       |
      | workshop   | Frontpage workshop name     | Frontpage workshop description     | Acceptance test site | workshop0   |

    And I am on homepage
    When I follow "Turn editing on"
    And I add the "Activities" block
    And I click on "Assignments" "link" in the "Activities" "block"
    Then I should see "Frontpage assignment name"
    And I am on homepage
    And I click on "Chats" "link" in the "Activities" "block"
    And I should see "Frontpage chat name"
    And I am on homepage
    And I click on "Choices" "link" in the "Activities" "block"
    And I should see "Frontpage choice name"
    And I am on homepage
    And I click on "Databases" "link" in the "Activities" "block"
    And I should see "Frontpage database name"
    And I am on homepage
    And I click on "Feedback" "link" in the "Activities" "block"
    And I should see "Frontpage feedback name"
    And I am on homepage
    And I click on "Forums" "link" in the "Activities" "block"
    And I should see "Frontpage forum name"
    And I am on homepage
    And I click on "External Tools" "link" in the "Activities" "block"
    And I should see "Frontpage lti name"
    And I am on homepage
    And I click on "Quizzes" "link" in the "Activities" "block"
    And I should see "Frontpage quiz name"
    And I am on homepage
    And I click on "Glossaries" "link" in the "Activities" "block"
    And I should see "Frontpage glossary name"
    And I am on homepage
    And I click on "SCORM packages" "link" in the "Activities" "block"
    And I should see "Frontpage scorm name"
    And I am on homepage
    And I click on "Lessons" "link" in the "Activities" "block"
    And I should see "Frontpage lesson name"
    And I am on homepage
    And I click on "Wikis" "link" in the "Activities" "block"
    And I should see "Frontpage wiki name"
    And I am on homepage
    And I click on "Workshop" "link" in the "Activities" "block"
    And I should see "Frontpage workshop name"
    And I am on homepage
    And I click on "Resources" "link" in the "Activities" "block"
    And I should see "Frontpage book name"
    And I should see "Frontpage page name"
    And I should see "Frontpage resource name"
    And I should see "Frontpage imscp name"
    And I should see "Frontpage folder name"
    And I should see "Frontpage url name"

  Scenario: Add activities block in a course
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    |
      | assign     | Test assignment name   | Test assignment description   | C1     | assign1     |
      | book       | Test book name         | Test book description         | C1     | book1       |
      | chat       | Test chat name         | Test chat description         | C1     | chat1       |
      | choice     | Test choice name       | Test choice description       | C1     | choice1     |
      | data       | Test database name     | Test database description     | C1     | data1       |
      | feedback   | Test feedback name     | Test feedback description     | C1     | feedback1   |
      | folder     | Test folder name       | Test folder description       | C1     | folder1     |
      | forum      | Test forum name        | Test forum description        | C1     | forum1      |
      | glossary   | Test glossary name     | Test glossary description     | C1     | glossary1   |
      | imscp      | Test imscp name        | Test imscp description        | C1     | imscp1      |
      | label      | Test label name        | Test label description        | C1     | label1      |
      | lesson     | Test lesson name       | Test lesson description       | C1     | lesson1     |
      | lti        | Test lti name          | Test lti description          | C1     | lti1        |
      | page       | Test page name         | Test page description         | C1     | page1       |
      | quiz       | Test quiz name         | Test quiz description         | C1     | quiz1       |
      | resource   | Test resource name     | Test resource description     | C1     | resource1   |
      | scorm      | Test scorm name        | Test scorm description        | C1     | scorm1      |
      | survey     | Test survey name       | Test survey description       | C1     | survey1     |
      | url        | Test url name          | Test url description          | C1     | url1        |
      | wiki       | Test wiki name         | Test wiki description         | C1     | wiki1       |
      | workshop   | Test workshop name     | Test workshop description     | C1     | workshop1   |

    When I follow "Courses"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Activities" block
    And I click on "Assignments" "link" in the "Activities" "block"
    Then I should see "Test assignment name"
    And I follow "Course 1"
    And I click on "Chats" "link" in the "Activities" "block"
    And I should see "Test chat name"
    And I follow "Course 1"
    And I click on "Choices" "link" in the "Activities" "block"
    And I should see "Test choice name"
    And I follow "Course 1"
    And I click on "Databases" "link" in the "Activities" "block"
    And I should see "Test database name"
    And I follow "Course 1"
    And I click on "Feedback" "link" in the "Activities" "block"
    And I should see "Test feedback name"
    And I follow "Course 1"
    And I click on "Forums" "link" in the "Activities" "block"
    And I should see "Test forum name"
    And I follow "Course 1"
    And I click on "External Tools" "link" in the "Activities" "block"
    And I should see "Test lti name"
    And I follow "Course 1"
    And I click on "Quizzes" "link" in the "Activities" "block"
    And I should see "Test quiz name"
    And I follow "Course 1"
    And I click on "Glossaries" "link" in the "Activities" "block"
    And I should see "Test glossary name"
    And I follow "Course 1"
    And I click on "SCORM packages" "link" in the "Activities" "block"
    And I should see "Test scorm name"
    And I follow "Course 1"
    And I click on "Lessons" "link" in the "Activities" "block"
    And I should see "Test lesson name"
    And I follow "Course 1"
    And I click on "Wikis" "link" in the "Activities" "block"
    And I should see "Test wiki name"
    And I follow "Course 1"
    And I click on "Workshop" "link" in the "Activities" "block"
    And I should see "Test workshop name"
    And I follow "Course 1"
    And I click on "Resources" "link" in the "Activities" "block"
    And I should see "Test book name"
    And I should see "Test page name"
    And I should see "Test resource name"
    And I should see "Test imscp name"
    And I should see "Test folder name"
    And I should see "Test url name"
