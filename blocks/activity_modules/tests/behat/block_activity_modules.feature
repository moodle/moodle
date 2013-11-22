@block @block_activity_modules @_only_local
Feature: Block activity modules
  In order to overview activity modules in a course
  As a manager
  I can add activities block in a course or on the frontpage

  Background:
    Given I log in as "admin"
    And I expand "Site administration" node
    And I expand "Plugins" node
    And I expand "Activity modules" node
    And I follow "Manage activities"
    And I click on "//a[@title=\"Show\"]" "xpath_element" in the "Feedback" "table_row"
    And I click on "//a[@title=\"Show\"]" "xpath_element" in the "Assignment (2.2)" "table_row"

  @javascript
  Scenario: Add activities block on the frontpage
    And the following "activities" exists:
      | activity   | name                        | intro                              | course               | idnumber    |
      | assign     | Frontpage assignment name   | Frontpage assignment description   | Acceptance test site | assign0     |
      | assignment | Frontpage assignment22 name | Frontpage assignment22 description | Acceptance test site | assignment0 |
      | data       | Frontpage database name     | Frontpage database description     | Acceptance test site | data0       |
      | forum      | Frontpage forum name        | Frontpage forum description        | Acceptance test site | forum0      |
      | label      | Frontpage label name        | Frontpage label description        | Acceptance test site | label0      |
      | lti        | Frontpage lti name          | Frontpage lti description          | Acceptance test site | lti0        |
      | page       | Frontpage page name         | Frontpage page description         | Acceptance test site | page0       |
      | quiz       | Frontpage quiz name         | Frontpage quiz description         | Acceptance test site | quiz0       |
      | resource   | Frontpage resource name     | Frontpage resource description     | Acceptance test site | resource0   |
    # Add activities with missing generators: book, chat, choice, feedback, folder, glossary, imscp, lesson, scorm, survey, url, wiki, workshop
    And I am on homepage

    When I follow "Turn editing on"

    And I add a "IMS content package" to section "1"
    And I fill the moodle form with:
      | Name        | Frontpage imscp name        |
      | Description | Frontpage imscp description |
    And I upload "mod/scorm/tests/packages/singlescobasic.zip" file to "Package file" filepicker
    And I press "Save and return to course"

    And I add a "Book" to section "1" and I fill the form with:
      | Name        | Frontpage book name        |
      | Description | Frontpage book description |

    And I add a "Chat" to section "1" and I fill the form with:
      | Name        | Frontpage chat name        |
      | Description | Frontpage chat description |

    And I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Frontpage choice name        |
      | Description | Frontpage choice description |
      | Option 1    | Test choice option      |

    And I add a "Feedback" to section "0" and I fill the form with:
      | Name        | Frontpage feedback name        |
      | Description | Frontpage feedback description |

    And I add a "Folder" to section "0" and I fill the form with:
      | Name        | Frontpage folder name        |
      | Description | Frontpage folder description |

    And I add a "Glossary" to section "1" and I fill the form with:
      | Name        | Frontpage glossary name        |
      | Description | Frontpage glossary description |

    And I add a "SCORM package" to section "1"
    And I fill the moodle form with:
      | Name        | Frontpage scorm name        |
      | Description | Frontpage scorm description |
    And I upload "mod/scorm/tests/packages/singlescobasic.zip" file to "Package file" filepicker
    And I press "Save and return to course"

    And I add a "Lesson" to section "1" and I fill the form with:
      | Name | Frontpage lesson name |

    And I add a "Survey" to section "1" and I fill the form with:
      | Name        | Frontpage survey name   |
      | Survey type | ATTLS (20 item version) |

    And I add a "URL" to section "1" and I fill the form with:
      | Name         | Frontpage url name        |
      | Description  | Frontpage url description |
      | External URL | http://moodle.org         |

    And I add a "Wiki" to section "1" and I fill the form with:
      | Wiki name       | Frontpage wiki name        |
      | Description     | Frontpage wiki description |
      | First page name | first page name            |

    And I add a "Workshop" to section "1" and I fill the form with:
      | Workshop name | Frontpage workshop name        |
      | Description   | Frontpage workshop description |

    And I add the "Activities" block
    And I click on "Assignments" "link" in the "Activities" "block"
    Then I should see "Frontpage assignment name"
    And I am on homepage
    And I click on "Assignments (2.2)" "link" in the "Activities" "block"
    And I should see "Frontpage assignment22 name"
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
    And I click on "basicltis" "link" in the "Activities" "block"
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

  @javascript
  Scenario: Add activities block in a course
    Given the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exists:
      | activity   | name                   | intro                         | course | idnumber    |
      | assign     | Test assignment name   | Test assignment description   | C1     | assign1     |
      | assignment | Test assignment22 name | Test assignment22 description | C1     | assignment1 |
      | data       | Test database name     | Test database description     | C1     | data1       |
      | forum      | Test forum name        | Test forum description        | C1     | forum1      |
      | label      | Test label name        | Test label description        | C1     | label1      |
      | lti        | Test lti name          | Test lti description          | C1     | lti1        |
      | page       | Test page name         | Test page description         | C1     | page1       |
      | quiz       | Test quiz name         | Test quiz description         | C1     | quiz1       |
      | resource   | Test resource name     | Test resource description     | C1     | resource1   |
    # Add activities with missing generators: book, chat, choice, feedback, folder, glossary, imscp, lesson, scorm, survey, url, wiki, workshop
    And I am on homepage
    When I follow "Courses"
    And I follow "Course 1"
    And I turn editing mode on

    And I add a "IMS content package" to section "0"
    And I fill the moodle form with:
      | Name        | Test imscp name        |
      | Description | Test imscp description |
    And I upload "mod/scorm/tests/packages/singlescobasic.zip" file to "Package file" filepicker
    And I press "Save and return to course"

    And I add a "Book" to section "1" and I fill the form with:
      | Name        | Test book name        |
      | Description | Test book description |

    And I add a "Chat" to section "1" and I fill the form with:
      | Name        | Test chat name        |
      | Description | Test chat description |

    And I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Test choice name        |
      | Description | Test choice description |
      | Option 1    | Test choice option      |

    And I add a "Feedback" to section "1" and I fill the form with:
      | Name        | Test feedback name        |
      | Description | Test feedback description |

    And I add a "Folder" to section "1" and I fill the form with:
      | Name        | Test folder name        |
      | Description | Test folder description |

    And I add a "Glossary" to section "2" and I fill the form with:
      | Name        | Test glossary name        |
      | Description | Test glossary description |

    And I add a "SCORM package" to section "0"
    And I fill the moodle form with:
      | Name        | Test scorm name        |
      | Description | Test scorm description |
    And I upload "mod/scorm/tests/packages/singlescobasic.zip" file to "Package file" filepicker
    And I press "Save and return to course"

    And I add a "Lesson" to section "0" and I fill the form with:
      | Name        | Test lesson name        |

    And I add a "Survey" to section "0" and I fill the form with:
      | Name        | Test survey name        |
      | Survey type | ATTLS (20 item version) |

    And I add a "URL" to section "0" and I fill the form with:
      | Name         | Test url name        |
      | Description  | Test url description |
      | External URL | http://moodle.org    |

    And I add a "Wiki" to section "0" and I fill the form with:
      | Wiki name       | Test wiki name        |
      | Description     | Test wiki description |
      | First page name | first page name       |

    And I add a "Workshop" to section "0" and I fill the form with:
      | Workshop name | Test workshop name        |
      | Description   | Test workshop description |

    And I add the "Activities" block
    And I click on "Assignments" "link" in the "Activities" "block"
    Then I should see "Test assignment name"
    And I follow "Course 1"
    And I click on "Assignments (2.2)" "link" in the "Activities" "block"
    And I should see "Test assignment22 name"
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
    And I click on "basicltis" "link" in the "Activities" "block"
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
