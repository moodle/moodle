# moodle-quiz_downloadsubmissions

#### Moodle Quiz Report Plugin for downloading essay submissions. 

The ‘Download essay submission’ plugin offers users a convenient way by which teachers can download quiz essay attachments submitted by students in response to quiz essay questions.  

#### Installation
* The plugin folder ‘downloadsubmissions’ is to be added under ‘moodle/mod/quiz/report’ directory.

#### How to use?
 * Go to a particular quiz.

 * Click on 'Settings' icon.

 * The plugin ‘Download essay submissions’ link will appear under ‘Results’ section. Click on it.

 * The teacher needs to click on the button ‘Download essay submissions’.

 * On clicking this button, the teacher will get a zip file consisting of attachments/files submitted by students in response to the quiz essay questions.
 
 * The hierarchy of folders present in the downloaded zip file is explained through an example.
 
 <b> Example: Quiz Scenario </b>
 
 A Quiz (Programming Tutorial) has an essay question (Question No.: 3, Question name: OOP Concept) requiring attachments/files to be submitted in response by students.
 
 A student (Student name: Anisha Patki, Username: anisha) attempts the quiz twice, each time attaching a response file to that particular essay question.
 
 The files submitted by the student as responses are:
  - First Attempt - Answer.odt
  - Second attempt - New_answer.pdf
 
 Now, in the downloaded zip file, the folder hierarchy for this particular student's response files is as follows: 
 - Q8 - OOP Concept / anisha - Anisha Patki / Attempt1_Answer.odt
 - Q8 - OOP Concept / anisha - Anisha Patki / Attempt2_New_answer.pdf
 
 (<b>Note:</b> Here, in 'Q8', '8' is the database question id for that particular question and may not match the question no. as it appeared in the quiz as shown in the example above.)
 
 
#### Usage

Through this feature, now teachers will be able to download/save all attachments of all attempts submitted by students in response to the quiz essay questions at one time.