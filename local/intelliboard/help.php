<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

require('../../config.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
require_once($CFG->dirroot .'/local/intelliboard/instructor/lib.php');

require_login();

require_capability('local/intelliboard:view', context_system::instance());

$PAGE->set_pagetype('help');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url("/local/intelliboard/help.php"));
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');

echo $OUTPUT->header();
?>

<div class="intelliboard-splash-page" id="getstartedform">
    <div class="intelliboard-splash-header intelliboard-splash-bg">
        <div class="left">
            <h1>A Powerful<br> Learning Analytics Platform<br>For <strong>Your Moodle</strong></h1>
            <p>IntelliBoard works with your existing learner<br> data to give you <strong>deep understanding and reveal<br> critical insights.</strong></p>
            <button class="btn btn-primary next-btn" data-form="getstartedform">Get Started</button>
        </div>
        <div class="right">
            <img src="assets/img/splash1.jpg" class="splash1" alt="IntelliBoard" />
            <img src="assets/img/splash2.jpg" class="splash2" alt="IntelliBoard" />
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="intelliboard-splash-logos">
        <p>Trusted by 400+ institutions worldwide</p>
        <img src="assets/img/logos.png" class="splash2" alt="IntelliBoard" />
    </div>

    <div class="intelliboard-splash-grid">
        <h3>Platform</h3>
        <h2>Let Your Data Tell a Story with In-Depth Analytics</h2>
        <ul class="clearfix">
            <li>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-database-fill-gear" viewBox="0 0 16 16">
                      <path d="M8 1c-1.573 0-3.022.289-4.096.777C2.875 2.245 2 2.993 2 4s.875 1.755 1.904 2.223C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777C13.125 5.755 14 5.007 14 4s-.875-1.755-1.904-2.223C11.022 1.289 9.573 1 8 1"/>
                      <path d="M2 7v-.839c.457.432 1.004.751 1.49.972C4.722 7.693 6.318 8 8 8s3.278-.307 4.51-.867c.486-.22 1.033-.54 1.49-.972V7c0 .424-.155.802-.411 1.133a4.51 4.51 0 0 0-4.815 1.843A12 12 0 0 1 8 10c-1.573 0-3.022-.289-4.096-.777C2.875 8.755 2 8.007 2 7m6.257 3.998L8 11c-1.682 0-3.278-.307-4.51-.867-.486-.22-1.033-.54-1.49-.972V10c0 1.007.875 1.755 1.904 2.223C4.978 12.711 6.427 13 8 13h.027a4.55 4.55 0 0 1 .23-2.002m-.002 3L8 14c-1.682 0-3.278-.307-4.51-.867-.486-.22-1.033-.54-1.49-.972V13c0 1.007.875 1.755 1.904 2.223C4.978 15.711 6.427 16 8 16c.536 0 1.058-.034 1.555-.097a4.5 4.5 0 0 1-1.3-1.905m3.631-4.538c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
                    </svg>
                </span>
                <strong>Integrate</strong>
                <p>Bring your data from LMS, SIS, collaboration, attendance, pus</p>
            </li>
            <li>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wrench" viewBox="0 0 16 16">
                      <path d="M.102 2.223A3.004 3.004 0 0 0 3.78 5.897l6.341 6.252A3.003 3.003 0 0 0 13 16a3 3 0 1 0-.851-5.878L5.897 3.781A3.004 3.004 0 0 0 2.223.1l2.141 2.142L4 4l-1.757.364zm13.37 9.019.528.026.287.445.445.287.026.529L15 13l-.242.471-.026.529-.445.287-.287.445-.529.026L13 15l-.471-.242-.529-.026-.287-.445-.445-.287-.026-.529L11 13l.242-.471.026-.529.445-.287.287-.445.529-.026L13 11z"/>
                    </svg>
                </span>
                <strong>Build</strong>
                <p>Build and customize your own reports and dashboards</p>
            </li>
            <li>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                      <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                    </svg>
                </span>
                <strong>Collaborate</strong>
                <p>Share your reports and dashboards with your peers</p>
            </li>

            <li>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pie-chart-fill" viewBox="0 0 16 16">
                      <path d="M15.985 8.5H8.207l-5.5 5.5a8 8 0 0 0 13.277-5.5zM2 13.292A8 8 0 0 1 7.5.015v7.778zM8.5.015V7.5h7.485A8 8 0 0 0 8.5.015"/>
                    </svg>
                </span>
                <strong>Analyze</strong>
                <p>Get insights with pre-built visualizations for each stakeholder</p>
            </li>
            <li>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                      <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                </span>
                <strong>Identify</strong>
                <p>Set your rules and identify at-risk learners and dollars</p>
            </li>
            <li>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-graph-up" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07"/>
                    </svg>
                </span>
                <strong>Predict</strong>
                <p>Intervene early with machine learning models based on your data</p>
            </li>

            <li>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-check" viewBox="0 0 16 16">
                      <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                      <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
                    </svg>
                </span>
                <strong>Communicate</strong>
                <p>Create your own notifications and task communications</p>
            </li>
            <li>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                      <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"/>
                      <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"/>
                    </svg>
                </span>
                <strong>Automate</strong>
                <p>Schedule processing updates from your data sources</p>
            </li>
            <li>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                      <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                    </svg>
                </span>
                <strong>Secure</strong>
                <p>Ensure privacy and compliance with role-based permissions</p>
            </li>
        </ul>
    </div>
</div>



<div class="intelliboard-splash-page intelliboard-hide" id="accountform">
    <div class="intelliboard-form-grid bg-light">
        <div class="intelliboard-proggress-bar">
            <ul>
                <li class="bg-primary"></li>
                <li class="bg-secondary"></li>
                <li class="bg-secondary"></li>
            </ul>
        </div>
        <div>
            <h5><span class="text-primary font-weight-bold"><img src="assets/img/ib-icon.png" class="intelliboard-ibicon"  />Request an</span> IntelliBoard account</h5>
        </div>
        <div class="align-middle">
            <img src="assets/img/check-green.png" class="intelliboard-icon" alt="IntelliBoard Check" >
            <span class="text-muted small">No credit card required</span>
        </div>
        <div class="intelliboard-form-block mb-4">
            <div class="form-help-text alert alert-warning small visible" role="alert">Please, fill out the details below.</div>
            <div class="form-group">
                <label for="email">Business Email</label>
                <input required type="email"  autocomplete="email" class="form-control" id="email" name="email" aria-describedby="emailHelp"  autofocus>
            </div>
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input required type="text" autocomplete="name" class="form-control" id="fullname" name="fullname" aria-describedby="fullnameHelp" >
            </div>
            <div class="form-group">
                <label for="oragnization">Organization</label>
                <input required type="text" autocomplete="organization" class="form-control"  name="oragnization" id="oragnization"  />
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input required class="form-control" autocomplete="tel" type="tel" name="phone" id="phone"  />
            </div>
            <div class="form-group">
                <label for="region">Region</label>
                <select required class="form-control"  id="region" name="region">
                    <option disabled selected value> -- Select Your Region -- </option>
                    <option value="us">North America</option>
                    <option value="eu">Europe</option>
                    <option value="au">Asia</option>
                    <option value="us">Africa</option>
                    <option value="us">South America</option>
                    <option value="au">Oceania</option>
                </select>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col"><button class="btn btn-secondary btn-block prev-btn" data-form="accountform">&larr; 	Previous</button></div>
            <div class="col"><button disabled class="btn btn-primary btn-block next-btn" data-form="accountform">Next 	&rarr;</button></div>
        </div>
        <div class="mt-5 mb-5 text-center text-muted small">
                By signing up for IntelliBoard, you agree to our <a href="https://intelliboard.net/legal/terms/">Terms of Use</a>
                and <a href="https://intelliboard.net/legal/privacy-policy/">Privacy Policy</a>.
        </div>
    </div>
</div>

<div class="intelliboard-splash-page intelliboard-hide" id="accounttypeform">
    <div class="intelliboard-form-grid bg-light">
        <div class="intelliboard-proggress-bar">
            <ul>
                <li class="bg-primary"></li>
                <li class="bg-primary"></li>
                <li class="bg-secondary"></li>
            </ul>
        </div>
        <div class="align-center">
            <h5>Which account type is best for you?</h5>
        </div>
        <div>
            <span class="text-muted small align-center">
                Whether you're tracking performance for you school, college, or organization, choose the account type fits best for you.
            </span>
            <div>
                <div class="form-help-text alert alert-warning small visible" role="alert">Please, select an account type.</div>
                <input required type="hidden" id="accounttype" name="accounttype" value="" />
                <button class="btn btn-outline-secondary  pl-5 pt-2 pb-2 mt-3 text-left accounttype"  data-accounttype="corporate" href="#" role="button" aria-pressed="false">
                    <h5>Corporate</h5>
                    <span>
                    I would like to utilize IntelliBoard to monitor and ensure the compilance of learner within my organization.
                </span>
                </button>
                <button class="btn btn-outline-secondary pl-5 pt-2 pb-2 mt-3 text-left accounttype" data-accounttype="highereducation" href="#" role="button" aria-pressed="false">
                    <h5>Higher Ed</h5>
                    <span>
                    I would like to use IntelliBoard to monitor and evaluate the success of students at my university or college.
                </span>
                </button>
                <button class="btn btn-outline-secondary pl-5 pt-2 pb-2 mt-3 text-left accounttype" data-accounttype="k12" href="#" role="button" aria-pressed="false">
                    <h5>K-12</h5>
                    <span>
                    I would like to use IntelliBoard to track progress of students in my school or district.
                </span>
                </button>
                <button class="btn btn-outline-secondary pl-5 pt-2 pb-2 mt-3 text-left accounttype" href="#" data-accounttype="goverment" role="button" aria-pressed="false">
                    <h5>Goverment</h5>
                    <span>
                    I would like to use IntelliBoard to track compliance, progress, and success of learners.
                </span>
                </button>

            </div>

        </div>
        <div class="row mt-4">
            <div class="col"><button class="btn btn-secondary btn-block prev-btn" data-form="accounttypeform">&larr; 	Previous</button></div>
            <div class="col"><button class="btn btn-primary btn-block next-btn" disabled="disabled" data-form="accounttypeform">Next 	&rarr;</button></div>
        </div>
        <br>
    </div>
</div>

<div class="intelliboard-splash-page intelliboard-hide" id="usertypeform">
    <div class="intelliboard-form-grid bg-light">
        <div class="intelliboard-proggress-bar">
            <ul>
                <li class="bg-primary"></li>
                <li class="bg-primary"></li>
                <li class="bg-primary"></li>
            </ul>
        </div>
        <div><h5 class="align-center">Who will be using IntelliBoard?</h5></div>
        <div>
            <div class="text-muted small align-center">
                Help us personalize your experience with IntelliBoard.
            </div>
            <br>
            <div class="form-help-text alert alert-warning small visible" role="alert">Please, select at least 1 role.</div>
            <div class="intelliboard-user-types intelliboard-hide" id="corporate">
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn-block btn btn-outline-secondary usertype" data-usertype="manager" aria-pressed="false">Manager</button></div>
                    <div class="col"><button class="user-type btn-block btn btn-outline-secondary usertype" data-usertype="instructional" href="#" role="button" aria-pressed="false">Instructional Desinger</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="executive" aria-pressed="false">Executive</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="hr" role="button" aria-pressed="false">HR</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="partners" role="button" aria-pressed="false">Partners</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="lmsadmin" role="button" aria-pressed="false">LMS Admin</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="clients" role="button" aria-pressed="false">Clients</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="programmanagers" role="button" aria-pressed="false">Program Managers</button></div>
                </div>
            </div>
            <div class="intelliboard-user-types intelliboard-hide" id="highereducation">
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="academicadvisor" role="button" aria-pressed="false">Academic Advisor</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="dean" role="button" aria-pressed="false">Dean</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="athlethiccoach" role="button" aria-pressed="false">Athlethic Coach</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="registrar" role="button" aria-pressed="false">Registrar</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="financialaid" role="button" aria-pressed="false">Financial Aid</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="lmsadmin" role="button" aria-pressed="false">LMS Admin</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="instructor" role="button" aria-pressed="false">Instructor</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="academicaffairs" role="button" aria-pressed="false">Academic Affairs</button></div>
                </div>
            </div>
            <div class="intelliboard-user-types intelliboard-hide" id="k12">
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="schoolcounselor" role="button" aria-pressed="false">School Counselor</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="principal" role="button" aria-pressed="false">Principal</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="athlethiccoach" role="button" aria-pressed="false">Athlethic Coach</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="registrar" role="button" aria-pressed="false">Registrar</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="teacher" role="button" aria-pressed="false">Teacher</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="lmsadmin" role="button" aria-pressed="false">LMS Admin</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="itadmin" role="button" aria-pressed="false">IT Admin</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="academicaffairs" role="button" aria-pressed="false">Academic Affairs</button></div>
                </div>
            </div>
            <div class="intelliboard-user-types intelliboard-hide" id="goverment">
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="programmanager" role="button" aria-pressed="false">Program Manager</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="director" role="button" aria-pressed="false">Director</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="instructionaldesigner" role="button" aria-pressed="false">Instructional Designer</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="personnelleader" role="button" aria-pressed="false">HR&sol;Personnel Leader</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="itadmin" role="button" aria-pressed="false">IT Admin</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="lmsadmin" role="button" aria-pressed="false">LMS Admin</button></div>
                </div>
                <div class="row mb-2">
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="instructor" role="button" aria-pressed="false">Instructor</button></div>
                    <div class="col"><button class="user-type btn btn-block btn-outline-secondary usertype" data-usertype="trainingmanager" role="button" aria-pressed="false">Training Manager</button></div>
                </div>
            </div>
            <div class="row mb-2 mt-4">
                <div class="col"><button class="btn btn-secondary btn-block prev-btn" data-form="usertypeform">&larr; 	Previous</button></div>
                <div class="col"><button class="btn btn-primary btn-block" disabled="disabled" data-form="submitdata" id="submitdata">Submit</button></div>
            </div>
            <br>
        </div>
    </div>
</div>

<div class="intelliboard-splash-page intelliboard-hide" id="thanksform">
    <div class="intelliboard-form-grid bg-light">
        <h4 class="text-center text-primary">Thank you! We have received your IntelliBoard account request.</h4>
        <h6 class="text-center">Here is what happens next:</h6>
        <div>
            <p>
                A member of our team will reach out to you shortly to discuss your needs and guide you through the next steps in the process.
                Here what you can expect:
            </p>
            <ol type="1">
                <li>Personalized follow-up: One of our team members will contact you via email or phone within the next 1-2 business days.
                    They will provide you with all the information need and answer any questions you might have.</li>
                <li>Tailored support: During the follow-up, we’ll assess your specific requirements and help you get started with IntelliBoard.
                    We will ensure you have everything to maximize the benefits of our platform.​</li>
                <li>Onboarding process: After our initial conversation, we will outline a clear onboarding processes to ensure a smooth and efficient setup.</li>
            </ol>
            <p>
                Once again, thank you for your interest in IntelliBoard. We look forward to helping you achieve your goals!
            </p>
            <a target="_blank" class="btn btn-primary btn-block text-center" href="https://intelliboard.net/events/">Meet us at one of our events</a>
        </div>
    </div>
</div>
<div class="intelliboard-support-terms">
    <div class="intelliboard-support-terms-footer">

    </div>
    <div class="intelliboard-support-terms-footer-content">
        <span>For Additional information, visit our website
            <a target="_blank" href="https://www.intelliboard.net/">www.intelliboard.net
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5"/>
                    <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z"/>
                </svg>
            </a>
        </span>
        <br>
        © 2015 - <?php echo date("Y") ?> IntelliBoard, Inc.
        <?php echo get_string('support_terms', 'local_intelliboard'); ?>
    </div>
</div>


<script>


</script>
<style>
    .intelliboard-splash-page span.align-center{
        display: block;
        margin-bottom: 5px;
    }
    .intelliboard-splash-page .invisible{
        display: none;
    }
    .intelliboard-support-terms{
        background-color: #E1EAF5;
        margin:0;
        padding-top: 0px;
        position: relative;
    }
    .intelliboard-support-terms-footer {
        height: 180px;
        background-color: #fff;
        text-align: center;
        padding: 0;
        position: relative;
        clip-path: ellipse(50% 100px at 50% 0);
    }

    .intelliboard-support-terms-footer-content svg{
        width: 12px;
        position: absolute;
        right: -14px;
    }
    .intelliboard-support-terms-footer-content span{
        color: #333;
        position: relative;
    }
    .intelliboard-support-terms-footer-content {
        padding: 20px;
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
    }

.page-header-headings{
    display: none;
}
.intelliboard-hide {
    display: none;
}
.intelliboard-splash-header {
    max-width: 1000px;
    margin: auto;
    display: flex;
    position: relative;
}
.intelliboard-splash-bg::before
{
    content: "";
    background-image: url("assets/img/splash-bg.png");
    background-repeat: no-repeat;
    background-size: contain;
    width: 80%;
    position: absolute;
    height: 100%;
    left: 40%;
    z-index: 10;

}
.intelliboard-splash-header .left{
    width: 55%;
    float: left;
    z-index: 50;
}
.intelliboard-splash-header .right{
    width: 45%;
    float: right;
    z-index: 50;
}
.intelliboard-splash-header img.splash1{
    max-width: 400px;
    margin-top: 10%;
    width: 80%;
}
.intelliboard-splash-header img.splash2{
    max-width: 500px;
    margin-top: -30%;
    margin-left: 20%;
    width: 90%;
}
.intelliboard-splash-header img{
    display: block;
}
.intelliboard-splash-header p{
    font-size: 19px;
    margin: 30px 0;
    max-width: 500px;
}
.intelliboard-splash-header h1 strong{
    color: #CF7139;
}
.intelliboard-splash-header h1{
    font-size: 34px;
    margin-top: 30px;
}
.intelliboard-splash-logos img{
    max-width: 600px;
    width: 100%;
}
.intelliboard-splash-logos p{
    margin: 0;
}
.intelliboard-splash-logos{
    text-align: center;
    margin: 20px auto;
}
.intelliboard-splash-grid{
    max-width: 900px;
    margin: 50px auto;
    padding:20px 50px;
    background-color: #F4F5F8;
    border-radius: 5px;
}
.intelliboard-splash-grid h2{
    max-width: 450px;
    text-align: center;
    margin:20px auto 40px;
    font-size: 24px;
    font-weight: 800;
}
.intelliboard-splash-grid h3{
    text-transform:uppercase;
    text-align: center;
    color: #3C64D5;
    font-size: 10px;
    font-weight: 800;
    margin: 0;
}

.intelliboard-splash-grid ul{
    padding: 0;
    margin: 0;
    list-style: none;
}

.intelliboard-splash-grid ul li{
    width: 33.3%;
    float: left;
    padding: 0;
    margin: 0;
    position: relative;
    padding-left: 55px;
    padding-bottom: 55px;
}
.intelliboard-splash-grid ul li span svg{
    display: block;
    width: 40px;
    height: 40px;
    padding: 10px;
}
.intelliboard-splash-grid ul li p{
    margin: 0;
    font-size: 90%;
    padding-right: 2px;
}
.intelliboard-splash-grid ul li:hover span {
    color: orange;
}
.intelliboard-splash-grid ul li span{
    position: absolute;
    top: 0;
    left: 0;
    background-color: #fff;
    border-radius: 10px;
    display: block;
    border: 1px solid #E4E4E4;
    margin-right: 10px;
    color: #3C64D5;
    text-align: center;
}
.intelliboard-support-terms{
    text-align: center;
    width: 100%;
}
.intelliboard-form-grid {
    max-width: 640px;
    margin: 50px auto;
    padding:20px 50px;
    border-radius: 5px;
}

.intelliboard-form-grid img.intelliboard-icon {
    display: inline-block;
    width: 1rem;
    height: auto;
}
.intelliboard-form-grid img.intelliboard-ibicon {
    display: inline-block;
    margin-right: 0.5rem;
    width: 1.5rem;
}

.intelliboard-form-grid .intelliboard-proggress-bar {
    display: flex;
    justify-content: center;
}
.intelliboard-form-grid .intelliboard-proggress-bar ul {
    list-style-type: none;
    padding: 20px;
    display: flex;
    flex-basis: 100%;
    justify-content: center;
}
.intelliboard-form-grid .intelliboard-proggress-bar ul li {
    width: 18%;
    border-radius: 3px;
    margin-left: 10px;
    margin-right: 10px;
    height: 0.5em;
}

.intelliboard-form-grid .intelliboard-form-block {
    margin: 20px auto;
}

.intelliboard-user-types {
    margin-top: 1em;
    margin-bottom: 1em;
}
.intelliboard-user-types a.user-type {
    display: inline-block;
    width: 80%;
    margin: 0.5rem;
    justify-content: center;
    font-weight: 800;
}
button.active, button.active h5, button.active span {
    font-weight: bold;
}
.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}
.col {
    flex-basis: 0%;
    flex-grow: 1;
    max-width: 100%;
    padding-right: 15px;
    padding-left: 15px;
}

    @media screen and (max-width: 900px) {
        body .intelliboard-support-terms{
            margin: 0;
        }
        .intelliboard-splash-grid ul li {
           width: 100%;
        }
    }

</style>

<?php
$setup = get_config("local_intelliboard", "account_setup");
$PAGE->requires->js_call_amd('local_intelliboard/account_setup', 'init', [$setup]);
echo $OUTPUT->footer();
