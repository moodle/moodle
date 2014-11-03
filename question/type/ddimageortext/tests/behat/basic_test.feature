@ou @ou_vle @qtype @qtype_ddimageortext
Feature: Test all the basic functionality of this question type
  In order to evaluate students responses, As a teacher I need to
  create and preview ddimageortext (Drag and drop onto image) questions.

    # Due to complexity and since the javascript code needs to be converted at some stage,
    # we are not going to test attempting this qtype. However, we will do all other
    # possible testings, such as creating the question preview it and seeing the
    # correct information on the preview string as well as backing-up and restoring
    # the course containing this qtype.

    # Another way to test attempting this qtype while previewing it, it write a
    # customised step for tabbing through place-holders and another customised
    # step for making use of arrow keys in order to go through the list of choices.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Create, edit then preview a ddimageortext question.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"
    Then I should see "Question bank"

    # Create a new question
    # We have to set the fields individually, because of the step for uploading the background image.
    Given I press "Create a new question ..."
    And I set the field "Drag and drop onto image" to "1"
    Then I press "Add"
    And I should see "Adding drag and drop onto image"
    Then I set the field "Question name" to "Drag and drop onto image 001"
    And I set the field "Question text" to "Identify the features in this cross-section."
    And I set the field "General feedback" to "The locations are now labelled on the diagram below."
    Given I upload "question/type/ddimageortext/tests/fixtures/oceanfloorbase.jpg" file to "Background image" filemanager

    # Draggable items
    Given I follow "Draggable items"
    Then I press "Blanks for 3 more draggable items"

    And I set the field "id_drags_0_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_0" to "island<br/>arc"

    And I set the field "id_drags_1_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_1" to "mid-ocean<br/>ridge"

    And I set the field "id_drags_2_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_2" to "abyssal<br/>plain"

    And I set the field "id_drags_3_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_3" to "continental<br/>rise"

    And I set the field "id_drags_4_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_4" to "ocean<br/>trench"

    And I set the field "id_drags_5_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_5" to "continental<br/>slope"

    And I set the field "id_drags_6_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_6" to "mountain<br/>belt"

    And I set the field "id_drags_7_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_7" to "continental<br/>shelf"

    # Drop zones
    Given I follow "Drop zones"
    Then I press "Blanks for 3 more drop zones"

    And I set the field "id_drops_0_xleft" to "53"
    And I set the field "id_drops_0_ytop" to "17"
    And I set the field "id_drops_0_choice" to "7"

    And I set the field "id_drops_1_xleft" to "172"
    And I set the field "id_drops_1_ytop" to "2"
    And I set the field "id_drops_1_choice" to "8"

    And I set the field "id_drops_2_xleft" to "363"
    And I set the field "id_drops_2_ytop" to "31"
    And I set the field "id_drops_2_choice" to "5"

    And I set the field "id_drops_3_xleft" to "440"
    And I set the field "id_drops_3_ytop" to "13"
    And I set the field "id_drops_3_choice" to "3"

    And I set the field "id_drops_4_xleft" to "115"
    And I set the field "id_drops_4_ytop" to "74"
    And I set the field "id_drops_4_choice" to "6"

    And I set the field "id_drops_5_xleft" to "210"
    And I set the field "id_drops_5_ytop" to "94"
    And I set the field "id_drops_5_choice" to "4"

    And I set the field "id_drops_6_xleft" to "310"
    And I set the field "id_drops_6_ytop" to "87"
    And I set the field "id_drops_6_choice" to "1"

    And I set the field "id_drops_7_xleft" to "479"
    And I set the field "id_drops_7_ytop" to "84"
    And I set the field "id_drops_7_choice" to "2"


    And I press "id_submitbutton"
    Then I should see "Drag and drop onto image 001"

    # Preview it.
    When I click on "Preview" "link" in the "Drag and drop onto image 001" "table_row"
    And I switch to "questionpreview" window
    Then I should see "Preview question: Drag and drop onto image 001"
    And I switch to the main window

    # Backup the course and restore it.
    When I log out
    And I log in as "admin"
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    Then I should see "Course 2"
    When I navigate to "Question bank" node in "Course administration"
    Then I should see "Drag and drop onto image 001"

    # Edit the copy and verify the form field contents.
    When I click on "Edit" "link" in the "Drag and drop onto image 001" "table_row"
    Then the following fields match these values:
      | Question name             | Drag and drop onto image 001                         |
      | Question text             | Identify the features in this cross-section.         |
      | General feedback          | The locations are now labelled on the diagram below. |

     # Draggable items
      | id_drags_0_dragitemtype | Draggable text        |
      | id_draglabel_0          | island<br/>arc        |

      | id_drags_1_dragitemtype | Draggable text        |
      | id_draglabel_1          | mid-ocean<br/>ridge   |

      | id_drags_2_dragitemtype | Draggable text        |
      | id_draglabel_2          | abyssal<br/>plain     |

      | id_drags_3_dragitemtype | Draggable text        |
      | id_draglabel_3          | continental<br/>rise  |

      | id_drags_4_dragitemtype | Draggable text        |
      | id_draglabel_4          | ocean<br/>trench      |

      | id_drags_5_dragitemtype | Draggable text        |
      | id_draglabel_5          | continental<br/>slope |

      | id_drags_6_dragitemtype | Draggable text        |
      | id_draglabel_6          | mountain<br/>belt     |

      | id_drags_7_dragitemtype | Draggable text        |
      | id_draglabel_7          | continental<br/>shelf |

     # Drop zones
      | id_drops_0_xleft  | 53  |
      | id_drops_0_ytop   | 17  |
      | id_drops_0_choice | 7   |

      | id_drops_1_xleft  | 172 |
      | id_drops_1_ytop   | 2   |
      | id_drops_1_choice | 8   |

      | id_drops_2_xleft  | 363 |
      | id_drops_2_ytop   | 31  |
      | id_drops_2_choice | 5   |

      | id_drops_3_xleft  | 440 |
      | id_drops_3_ytop   | 13  |
      | id_drops_3_choice | 3   |

      | id_drops_4_xleft  | 115 |
      | id_drops_4_ytop   | 74  |
      | id_drops_4_choice | 6   |

      | id_drops_5_xleft  | 210 |
      | id_drops_5_ytop   | 94  |
      | id_drops_5_choice | 4   |

      | id_drops_6_xleft  | 310 |
      | id_drops_6_ytop   | 87  |
      | id_drops_6_choice | 1   |

      | id_drops_7_xleft  | 479 |
      | id_drops_7_ytop   | 84  |
      | id_drops_7_choice | 2   |

    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
