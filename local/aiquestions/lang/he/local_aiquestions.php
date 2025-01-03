<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_aiquestions
 * @category    string
 * @copyright   2023 Ruthy Salomon <ruthy.salomon@gmail.com> , Yedidia Klein <yedidia@openapp.co.il>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'מחולל שאלות טקסט לשאלות באמצעות AI';
$string['pluginname_desc'] = 'תוסף זה מאפשר לך ליצור שאלות מתוך טקסט.';
$string['pluginname_help'] = 'השתמש בתוסף זה מתפריט הניהול של הקורס.';
$string['privacy:metadata'] = 'מחולל שאלות טקסט לשאלות אינו מאחסן נתונים אישיים.';
$string['openaikey'] = 'מפתח API של OpenAI';
$string['openaikeydesc'] = 'נא להקליד כאן את מפתח ה- API של OpenAI שלך.<br>
ניתן לקבל את מפתח ה- API שלך מ- <a href="https://platform.openai.com/account/api-keys">https://platform.openai.com/account/api-keys</a><br>
יש לבחור בכפתור "+ Create New Secret Key" ולהעתיק את המפתח לשדה זה.<br>
יש לציין שנדרש חשבון של OpenAI שכולל הגדרות חיוב כדי לקבל מפתח API.';
$string['story'] = 'מלל לשאלות';
$string['storydesc'] = 'נא להקליד כאן את המלל שלך.';
$string['numofquestions'] = 'מספר השאלות';
$string['numofquestionsdesc'] = 'נא לבחור כאן את מספר השאלות שברצונך ליצור.';
$string['generate'] = 'יצירת שאלות';
$string['aiquestions'] = 'שאלות AI';
$string['backtocourse'] = 'חזרה לקורס';
$string['gotoquestionbank'] = 'עברו לבנק השאלות';
$string['generatemore'] = 'יצירת שאלות נוספות';
$string['createdquestionwithid'] = 'נוצרה שאלה עם מזהה ';
$string['language'] = 'שפה';
$string['languagedesc'] = 'נא לבחור כאן את השפה שבה ברצונך להשתמש ביצירת השאלות.<br>
יש לשים לב שישנן שפות שנתמכות פחות מאחרות על ידי ChatGPT.';
$string['usepersonalprompt'] = 'שימוש בהתג מותאם אישית';
$string['usepersonalpromptdesc'] = 'נא לבחור כאן אם ברצונך להשתמש בהתג מותאם אישית.';
$string['personalprompt'] = 'prompt מותאם אישית';
$string['personalpromptdesc'] = 'נא להקליד כאן את prompt האישי שלך.
ה-prompt הוא ההסבר ל- ChatGPT כיצד ליצור את השאלות.<br>
יש לכלול ב-prompt את שני מחזיקי המקום הבאים: {{numofquestions}} ו-{{language}}.';
$string['tasksuccess'] = 'משימת יצירת השאלות נוצרה בהצלחה';
$string['generating'] = 'יוצר את השאלות שלך... (ניתן לעזוב דף זה, ולבדוק מאוחר יותר בבנק השאלות)';
$string['generationfailed'] = 'נכשל ביצירת השאלות לאחר {$a} ניסיונות';
$string['generationtries'] = 'מספר הניסיונות שנשלחו ל- OpenAI: <b>{$a}</b>';
$string['outof'] = 'מתוך';
$string['numoftries'] = '<b>{$a}</b> ניסיונות';
$string['numoftriesset'] = 'מספר הניסיונות';
$string['numoftriesdesc'] = 'נא לכתוב כאן את מספר הניסיונות שברצונך לשלוח ל- OpenAI';
$string['preview'] = 'תצוגה מקדימה של השאלה בטאב חדש';
$string['cronoverdue'] = 'נראה שהמשימה cron אינה פועלת,
יצירת השאלות תלויה במשימות AdHoc שנוצרות על ידי המשימה cron, נא לבדוק את הגדרות ה-cron שלך.
ראה <a href="https://docs.moodle.org/en/Cron#Setting_up_cron_on_your_system">
https://docs.moodle.org/en/Cron#Setting_up_cron_on_your_system
</a> לקבלת מידע נוסף.';
$string['createdquestionsuccess'] = 'השאלה נוצרה בהצלחה';
$string['createdquestionssuccess'] = 'השאלות נוצרו בהצלחה';
$string['errornotcreated'] = 'שגיאה : השאלות לא נוצרו';
