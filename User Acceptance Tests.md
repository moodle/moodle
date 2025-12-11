# ![AuST Group Logo](AuST-Group_Logo_MAIN.png) User Acceptance Tests (UAT) - AuST Moodle LMS
**Q4 2025 Validation Cycle**

| Document Details | |
| :--- | :--- |
| **Project** | AuST Internal Moodle LMS |
| **Version** | 1.0 |
| **Date** | 2025-12-11 |
| **Status** | **Release Candidate** |
| **Tester** | [Tester Name] |

---

## 1. Introduction
This document outlines the comprehensive User Acceptance Testing (UAT) protocols for the AuST Moodle Learning Management System. The purpose is to validate that the core system, custom themes, and proprietary plugins function according to requirements and provide a stable learning environment.

### 1.1 Scope
The scope of this testing cycle includes:
*   **Core Moodle 4.x Functionality**: Deep dive into Users, Courses, Activities, and Grades.
*   **Custom Theme (`theme_aust`)**: Branding, Dark Mode, Navigation.
*   **Custom Plugins**:
    *   **Course Matrix (`local_coursematrix`)**: Automated rule-based enrolment.
    *   **Master Builder (`local_masterbuilder`)**: API endpoints.
    *   **Quiz Password Verify (`local_quiz_password_verify`)**: Identity verification.
*   **Infrastructure**: SMTP Email Delivery.

---

## 2. Environment Setup
| Parameter | Value |
| :--- | :--- |
| **URL** | [Insert Stage/Prod URL] |
| **Test Account (Admin)** | `admin` / [Password] |
| **Test Account (Manager)** | `mang_user` / [Password] |
| **Test Account (Teacher)** | `teach_user` / [Password] |
| **Test Account (Student)** | `stud_user` / [Password] |
| **Browser Support** | Chrome (Latest), Firefox (Latest), Edge (Chromium) |

---

## 3. Test Cases

### Section A: Authentication & Security

| ID | Feature | Test Steps | Expected Result | Pass/Fail |
| :--- | :--- | :--- | :--- | :--- |
| **A-1** | **User Login (Manual)** | 1. Navigate to Login Page.<br>2. Enter valid username/password.<br>3. Click "Log in". | User is redirected to Dashboard. Session active. | |
| **A-2** | **Invalid Login** | 1. Navigate to Login Page.<br>2. Enter invalid credentials.<br>3. Click "Log in". | Error "Invalid login, please try again". Access denied. | |
| **A-3** | **Password Reset (SMTP)** | 1. Click "Forgotten your username or password?".<br>2. Enter valid email.<br>3. Check email inbox. | Reset email received via **SMTP Server**. Sender is correct. Link works. | |
| **A-4** | **Force Password Change** | 1. Admin sets "Force password change" for user.<br>2. User Logs in. | User is immediately prompted to change password before accessing site. | |
| **A-5** | **Session Timeout** | 1. Log in.<br>2. Leave idle for [Timeout Setting] mins.<br>3. Attempt action. | User redirected to login screen. | |
| **A-6** | **Guest Access** | 1. Enable Guest access on a course.<br>2. Access course as Guest (logged out). | Content visible (read-only). No ability to submit. | |

### Section B: Detailed User Management

| ID | Feature | Test Steps | Expected Result | Pass/Fail |
| :--- | :--- | :--- | :--- | :--- |
| **B-1** | **Create User Manually** | 1. Site Admin > Users > Add a new user.<br>2. Fill required fields (Username, Password, Name, Email).<br>3. Click "Create user". | User created successfully. Profile visible in User list. | |
| **B-2** | **Bulk Upload Users** | 1. Site Admin > Users > Upload users.<br>2. Upload CSV with 5+ test users (fields: username, password, firstname, lastname, email).<br>3. Process upload. | All users created. No errors. Confirmation screen shows stats. | |
| **B-3** | **Suspend User Account** | 1. Site Admin > Users > Browse list of users.<br>2. Click "Suspend" icon (eye) for a user.<br>3. User attempts to log in. | Login denied. "Your account has been suspended". | |
| **B-4** | **Delete User Account** | 1. Site Admin > Users > Browse list of users.<br>2. delete a test user. | User removed from system. Verify they no longer appear in search. | |
| **B-5** | **Assign System Role** | 1. Site Admin > Users > Permissions > Assign system roles.<br>2. Assign "Course Creator" to a user. | User can now see "Add a new course" options in Course Management. | |
| **B-6** | **Cohorts - Manual Creation** | 1. Site Admin > Users > Accounts > Cohorts.<br>2. Create "Test Cohort Team A". | Cohort created. | |
| **B-7** | **Cohorts - Add Members** | 1. Open "Test Cohort Team A".<br>2. Add 3 users. | Users successfully added to the cohort. | |
| **B-8** | **Check User Profile Fields** | 1. View User Profile.<br>2. edit "Department" or "Institution". | Fields save correctly. Used by `local_coursematrix`. | |

### Section C: Course Management & Enrolment

| ID | Feature | Test Steps | Expected Result | Pass/Fail |
| :--- | :--- | :--- | :--- | :--- |
| **C-1** | **Create New Course** | 1. Site Admin > Courses > Add a new course.<br>2. Details: Full Name, Short Name, Category, Start Date.<br>3. Save. | Course created. Redirects to Content/Participants page. | |
| **C-2** | **Manual Enrolment (Student)** | 1. Course > Participants > Enrol users.<br>2. Search for User A.<br>3. Assign role "Student". | User A listed as Student. Can access course content. | |
| **C-3** | **Manual Enrolment (Teacher)** | 1. Course > Participants > Enrol users.<br>2. Search for User B.<br>3. Assign role "Teacher". | User B listed as Teacher. Can turn on "Edit Mode". | |
| **C-4** | **Remove User from Course** | 1. Course > Participants.<br>2. Click "Trash" icon next to User A enrol method.<br>3. Confirm. | User A removed from list. Formerly enrolled user can no longer access. | |
| **C-5** | **Self Enrolment (Key)** | 1. Course > Participants > Enrolment methods.<br>2. Enable "Self enrolment".<br>3. Set Enrolment Key.<br>4. Student attempts to join. | Student prompted for key. Correct key grants access. | |
| **C-6** | **Cohort Sync** | 1. Course > Enrolment methods > Add "Cohort sync".<br>2. Select "Test Cohort Team A". | All members of Cohort Team A automatically enrolled. | |
| **C-7** | **Groups Creation** | 1. Course > Participants > Groups.<br>2. Create "Group Alpha".<br>3. Add users. | Group exists. Users are members. | |
| **C-8** | **Groupings** | 1. Create "Grouping 1".<br>2. Add "Group Alpha" to it. | Grouping structure valid. Can be used for activity restrictions. | |
| **C-9** | **Backup Course** | 1. Course > More > Course Reuse > Backup.<br>2. Select default settings (Activities, Users).<br>3. Perform Backup. | `.mbz` file generated. "Backup completed successfully". File available for download. | |
| **C-10** | **Restore Course** | 1. Site Admin > Courses > Restore.<br>2. and upload the `.mbz` file.<br>3. Restore as new course. | Course duplicated with all content/users (depending on settings). | |
| **C-11** | **Reset Course** | 1. Course > More > Course Reuse > Reset.<br>2. Select "Delete all submissions" and "Unenrol students".<br>3. Execute. | Course content remains. User data/grades wiped. Roster empty. | |

### Section D: AuST Theme & UI (`theme_aust`)

| ID | Feature | Test Steps | Expected Result | Pass/Fail |
| :--- | :--- | :--- | :--- | :--- |
| **D-1** | **Global Branding** | 1. View all pages.<br>2. Verify **AuST Logo** in header.<br>3. Verify Footer copyright/links. | Logo is crisp. Colors are AuST Blue (#004A91) and Gold (#FDB913). | |
| **D-2** | **Top Navigation (Desktop)** | 1. Hover main menu items.<br>2. Click "My Courses". | Dropdowns function smoothly. "Moodle" text is AuST Gold. | |
| **D-3** | **Dark Mode Toggle** | 1. User User Menu > Dark Mode toggle.<br>2. Switch ON. | UI background becomes Dark Grey (#121212/similar). Text #E0E0E0. | |
| **D-4** | **Dark Mode Specifics** | 1. Check Course Cards on Dashboard.<br>2. Check "Edit Mode" toggle.<br>3. Check Activity Icons. | No white backgrounds on transparent images. Text legible. Form inputs visible. | |
| **D-5** | **Login Page (Dark Mode)** | 1. Logout.<br>2. Enable Dark Mode (if OS pref or persistent cookie). | Login Card `.loginform` has **AuST Orange** border/highlight. Background is dark. | |
| **D-6** | **Accessibility Check** | 1. Use keyboard (Tab) to navigate menus.<br>2. Check contrast. | Focus rings visible. Key elements reachable via keyboard. | |

### Section E: Activities & Assessment

| ID | Feature | Test Steps | Expected Result | Pass/Fail |
| :--- | :--- | :--- | :--- | :--- |
| **E-1** | **Assignment - Submission** | 1. Student opens Assignment.<br>2. Uploads File (PDF/Doc).<br>3. Clicks "Save changes" -> "Submit assignment". | Status changes to "Submitted for grading". File is stored. | |
| **E-2** | **Assignment - Grading** | 1. Teacher opens Assignment.<br>2. Clicks "Grade".<br>3. Enters Grade (85/100) and Feedback comments. | Grade saved. Student notified (if notifications on). | |
| **E-3** | **Quiz - Setup** | 1. Teacher adds Quiz.<br>2. Timing: Enable Open/Close dates.<br>3. Layout: 1 question per page. | Quiz created. | |
| **E-4** | **Quiz - Attempt** | 1. Student takes Quiz.<br>2. Times out or submits manually. | Quiz submits. Auto-graded questions calculated immediately. | |
| **E-5** | **Quiz - Manual Grading** | 1. Attempt with Essay question.<br>2. Teacher goes to Results > Manual grading. | Teacher can input score for essay. Total grade updates. | |
| **E-6** | **Forum - Q&A** | 1. Create "Q&A Forum".<br>2. Teacher posts Q.<br>3. Student tries to view other replies before posting. | Student cannot see other replies until they post their own. | |
| **E-7** | **Forum - Subscription** | 1. Subscribe to Forum (Forced or Optional).<br>2. Post new topic. | Cron runs -> **SMTP** sends email copy of post to subscribers. | |
| **E-8** | **H5P Content** | 1. Add H5P activity (Interactive Video).<br>2. Student interacts with content. | Content loads. Score pushed to Gradebook upon completion. | |
| **E-9** | **Restrict Access** | 1. Set Quiz to open only after "Lesson 1" is marked complete. | Quiz is greyed out/unavailable until condition met. | |
| **E-10**| **Activity Completion** | 1. Set File resource to "Student must view to complete".<br>2. Student clicks file. | Checkbox turns green (Completed) on course page. | |

### Section F: Custom Plugins

#### 1. Course Matrix (`local_coursematrix`)
| ID | Feature | Test Steps | Expected Result | Pass/Fail |
| :--- | :--- | :--- | :--- | :--- |
| **F-1** | **View Matrix** | 1. Admin > Plugins > Course Matrix. | Table shows Dept/Job combinations from User DB. | |
| **F-2** | **Rules Engine** | 1. Assign Course X to "Engineering | Manager".<br>2. Run Cron (or wait). | All users with Dept=Engineering/Title=Manager enrolled in Course X. | |

#### 2. Master Builder (`local_masterbuilder`)
| ID | Feature | Test Steps | Expected Result | Pass/Fail |
| :--- | :--- | :--- | :--- | :--- |
| **F-3** | **Sync API** | 1. (Via Postman/Script) Call Master Builder endpoint. | Returns 200 OK. Confirms connectivity. | |
| **F-4** | **True/False Question** | 1. Verify custom question created by builder. | Question text is bilingual (Eng/Esp). Correct answer = 1. | |

#### 3. Quiz Password Verify (`local_quiz_password_verify`)
| ID | Feature | Test Steps | Expected Result | Pass/Fail |
| :--- | :--- | :--- | :--- | :--- |
| **F-5** | **Trigger** | 1. Start protected Quiz. | Modal appears demanding **User Login Password**. | |
| **F-6** | **Verification** | 1. Enter correct password. 2. Verify. | Access Granted. Audit log updated in DB. | |

### Section G: Gradebook & Reporting

| ID | Feature | Test Steps | Expected Result | Pass/Fail |
| :--- | :--- | :--- | :--- | :--- |
| **G-1** | **Grader Report** | 1. Course > Grades.<br>2. Enable "Edit mode".<br>3. Override a grade manually. | Grade cell turns orange (overridden). Total recalculates. | |
| **G-2** | **User Report** | 1. View "User Report" as Student. | Student sees their own grades and feedback only. | |
| **G-3** | **Export Grades** | 1. Grades > Export > Excel Spreadsheet. | File downloads. Contains all columns and student data. | |
| **G-4** | **Logs** | 1. Course > Reports > Live Logs.<br>2. Perform actions in another tab. | Actions appear in Live Logs in real-time. | |
| **G-5** | **Course Completion** | 1. Course > Reports > Course Completion.<br>2. Verify aggregation (e.g., "All activities completed"). | Report shows status per student (In Progress/Complete). | |

### Section H: System Admin & Infrastructure

| ID | Feature | Test Steps | Expected Result | Pass/Fail |
| :--- | :--- | :--- | :--- | :--- |
| **H-1** | **Cron Job** | 1. Admin > Task processing > Ad hoc tasks.<br>2. Verify Cron is running regularly (every 1 min). | "Cron running correctly" message in status or recent log runtimes. | |
| **H-2** | **SMTP Email Test** | 1. Admin > Server > Email > Outgoing mail configuration.<br>2. "Test outgoing mail configuration". | Test email sent successfully via configured SMTP server. | |
| **H-3** | **Cache Purge** | 1. Admin > Development > Purge caches.<br>2. "Purge all caches". | System refreshes. No broken assets/styles on reload. | |
| **H-4** | **Plugin Overview** | 1. Admin > Plugins > Plugins overview. | All Custom Plugins listed. No "Missing from disk" errors. | |

---

## 4. Sign-off

**Tester Signature:** _____________________  **Date:** ___________

**Stakeholder Signature:** _________________  **Date:** ___________

> **Note:** Any failures must be logged in the Issue Tracker with High Priority if they affect core learning capability (Login, Quiz, Course Access).
