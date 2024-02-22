<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     local_auto_proctor
 * @author      Angelica
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
*/

require_once(__DIR__ . '/../../../config.php'); // Setup moodle global variable also
require_login();
// Get the global $DB object
global $DB, $PAGE, $USER, $CFG;

require_once($CFG->libdir . '/outputrenderers.php');

// Get required parameters
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url(url:'/local/auto_proctor/prompts.php')); // Set url

// Retrieve the data from the URL parameter
$data_param = optional_param('data', '', PARAM_RAW);

// Decode the JSON data
$jsdata = json_decode(urldecode($data_param), true);

// Access the values
$wwwroot = $jsdata['wwwroot'];
$userid = $jsdata['userid'];
$quizid = $jsdata['quizid'];
$quizattempt = $jsdata['quizattempt'];
$quizattempturl = $jsdata['quizattempturl'];
$monitor_camera_activated = $jsdata['monitor_camera_activated'];
$monitor_microphone_activated = $jsdata['monitor_microphone_activated'];

// ====== DEBUUGING PURPOSE
// echo "<script>";
// echo
// "
//         console.log('wwwroot: ', ". json_encode($wwwroot) .");
//         console.log('userid', $userid);
//         console.log('quizid', $quizid);
//         console.log('quizattempt', $quizattempt);
//         console.log('quizattempturl: ', ". json_encode($quizattempturl) .");
// ";
// echo "</script>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css"  rel="stylesheet" />

    <title>Document</title>
</head>
<body class="overflow-hidden">
    
<!-- MODAL HERE YOU CAN COPY IT PASE IT TO THE MAIN CODE -->
<!-- <button id = "toggleButton" data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="button">
    Toggle modal
</button> -->

<div id="popup-modal" tabindex="-1" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full flex" aria-modal="true" role="dialog">
<!-- <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"> -->
    <div class="relative p-10 py-9 w-full max-w-3xl max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center " data-modal-hide="popup-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-6 md:p-8  text-center">
                <h1 class="mb-2 text-2xl font-semibold text-black ">Multiple Monitor Detected</h1>

                <p class="mb-4 text-base font-normal text-gray-700 text-start ">We have detected multiple monitors. Please disconnect the extra monitor (or devices like Chromecast).</p>
                <p class="mb-8 text-base font-normal text-gray-700 text-start ">IIf you continue without disconnecting, AutoProctor will store details of the device.</p>
                <button onclick = "haveNotConnMonitor()" id = "have-not-multiple-btn" data-modal-hide="popup-modal" type="button" class="text-white bg-[#6B7280] hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-red-300  font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                    Haven’t connected Multiple Monitors
                </button>
            <!-- Modal body -->
            <div class="p-4 md:p-5 text-center">
                <button onclick = "haveRemoveExtMonitor()" id = "have-multiple-btn" data-modal-hide="popup-modal" type="button" class="text-white bg-[#059669] hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-red-300  font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                    Have removed External Monitor
                </button>
                <button onclick = "continueWithMulMonitor()" id = "continue-multiple-btn" data-modal-hide="popup-modal" type="button" class="text-white  bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-200 focus:z-10 ">Continue with Multiple Monitors</button>
            </div>
            <!-- Modal footer -->


            </div>
        </div>
    </div>
</div>

<div id="cam-view-popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-10 py-9 w-full max-w-3xl max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center " data-modal-hide="popup-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-6 md:p-8  text-center">
                <h1 class="mb-2 text-2xl font-semibold text-black ">Camera View</h1>

                <p class="mb-2 text-md font-normal text-gray-700 ">This is what the selected camera is capturing. If you want to use a different camera, go back to previous step and select a different camera.</p>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4">
                <!-- ADD IMAGE HERE -->
                <div class="flex justify-center">
                    <video id="camera-preview" alt="Your Image" class="max-w-64 h-auto" autoplay style="display:none;"></video>
                </div>
                <p class="text-base leading-relaxed text-black ">
                    If you see a completely black screen, it is mostly a camera error. Check your device’s camera.
                </p>
            </div>
            <!-- Modal footer -->
<!-- Modal footer -->
<div class="flex justify-between items-center p-4 md:p-5  border-gray-200 rounded-b ">
    <button data-modal-hide="cam-view-popup-modal" data-modal-target="cam-select-popup-modal" data-modal-toggle="cam-select-popup-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-5 focus:outline-none  rounded-lg border border-gray-400 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 ">Previous</button>
    <!-- <button id="nextButton" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center hidden" onclick = "sendSetupData()">Next</button> -->
    <button onclick = "sendSessionSetupData()" data-modal-hide="cam-select-popup-modal" type="button" data-modal-target="cam-view-popup-modal" data-modal-toggle="cam-view-popup-modal" class=" text-gray-100 bg-blue-700 hover:bg-[#0061A8] focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-00 focus:z-10">Next</button>

</div>

            </div>
        </div>
    </div>
</div>

<div id="cam-select-popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-12 py-16 w-full max-w-3xl max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center " data-modal-hide="popup-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-6 md:p-8 mb-4 text-center">
                <h1 class="mb-2 text-2xl font-semibold text-black ">Multiple Cameras Detected</h1>

                <h3 class="mb-6 text-md font-normal text-gray-700 ">We detected 2 cameras. Please select one of them to continue.</h3>
                <!-- FOR DROPDOWN -->
                <div  class="inline-flex items-end">
                    <button id="dropdownDefault" data-dropdown-toggle="filter"
                    class="mb-4 sm:mb-0 mr-4 inline-flex items-end text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-4 py-2.5">
                    Select any camera
                    <svg class="w-4 h-4 ml-2 " aria-hidden="true" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                    <!-- Dropdown menu -->
                    <div id="filter" class="z-10 hidden w-56 p-3 bg-white rounded-lg shadow">
                        <ul class="space-y-2 text-sm" aria-labelledby="dropdownDefault">
                            <li class="flex items-center">            
                                <label for=""
                                    class="ml-2 text-sm font-medium text-gray-900 ">
                                    Select any camera
                                </label>
                            </li>
                            <!-- <li class="flex items-center"> -->
                                <!-- <input type="radio" id="camera_usb" name="camera" > -->
                                <!-- <label for=""
                                    class="ml-2 text-sm font-medium text-gray-900 ">
                                    USB 2.0 HD UVC WebCam
                                </label> -->
                            <!-- </li> -->

                            <!-- <li class="flex items-center"> -->
                                <!-- <input type="radio" id="camera_usb" name="camera"> -->
                                <!-- <label for=""
                                    class="ml-2 text-sm font-medium text-gray-900 ">
                                    OBS Virtual Camera
                                </label> -->
                            <!-- </li> -->

                        </ul>
                    </div>
                </div>
                <!-- END OF DROPDOWN -->
                <button onclick = "startStream()" data-modal-hide="cam-select-popup-modal" type="button" data-modal-target="cam-view-popup-modal" data-modal-toggle="cam-view-popup-modal" class=" text-gray-100 bg-blue-700 hover:bg-[#0061A8] focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-00 focus:z-10">Next</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
<div id = "backdrop" modal-backdrop class="bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40"></div>

<script>
    let chosen_camera_device = null;
    //let monitor_setup = null;
    let chosen_monitor_set_up = "single_monitor_detected";
    let monitor_camera_activated = <?php echo $monitor_camera_activated; ?>;
    let monitor_microphone_activated = <?php echo $monitor_microphone_activated; ?>;

    var popupModal = document.getElementById("popup-modal");
    var camSelectPopupModal = document.getElementById("cam-select-popup-modal");


    if (monitor_microphone_activated === 1){
        navigator.mediaDevices.getUserMedia({ audio: true })
        .then(function(stream) {
            // Your code to handle the audio stream
        })
        .catch(function(err) {
            // Your code to handle any errors
        });

    }

    if (monitor_camera_activated === 1){
        navigator.mediaDevices.getUserMedia({ audio: true })
        .then(function(stream) {
            // Your code to handle the audio stream
        })
        .catch(function(err) {
            // Your code to handle any errors
        });
    }

    if (!window.screen.isExtended){
        camSelectPopupModal.classList.remove("hidden");
        camSelectPopupModal.classList.add("flex");
        camSelectPopupModal.setAttribute("aria-modal", "true");
        camSelectPopupModal.setAttribute("role", "dialog");
        
        popupModal.classList.remove("flex");
        popupModal.classList.add("hidden");
    }

    // Check if window screen is extended
    var have_not_multiple_btn = document.getElementById('have-not-multiple-btn');
    var have_multiple_btn = document.getElementById('have-multiple-btn');
    var continue_multiple_btn = document.getElementById('continue-multiple-btn');

    // If monitoring camera is activated
    if (monitor_camera_activated === 1) {
        have_not_multiple_btn.setAttribute('data-modal-target', 'cam-select-popup-modal');
        have_not_multiple_btn.setAttribute('data-modal-toggle', 'cam-select-popup-modal');
        have_not_multiple_btn.setAttribute('data-modal-hide', 'popup-modal');

        have_multiple_btn.setAttribute('data-modal-target', 'cam-select-popup-modal');
        have_multiple_btn.setAttribute('data-modal-toggle', 'cam-select-popup-modal');

        continue_multiple_btn.setAttribute('data-modal-target', 'cam-select-popup-modal');
        continue_multiple_btn.setAttribute('data-modal-toggle', 'cam-select-popup-modal');

        navigator.mediaDevices.getUserMedia({ video: true })
            .then((stream) => {
                videoElement = document.createElement('video');
                const camera = new Camera(videoElement, {onFrame: async () => {
                    await faceMesh.send({ image: videoElement });
                },
                width: 1280,
                height: 720,
                });

                camera.start();
                videoElement.srcObject = stream;
            })
            .catch((error) => {
                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                // User denied camera access
                console.error('User denied camera access.');
                    
                } else {
                    // Other errors
                    console.error('Error accessing camera:', error.message);
                }
            }
        );
    }

    function startStream() {
        var selectedRadio = document.querySelector('input[name="camera"]:checked');
        
        if (!selectedRadio) {
            alert("Please select a camera");
            return;
        }
        var deviceId = selectedRadio.value;
            
        var constraints = {
            video: { deviceId: { exact: deviceId } }
        };

        // Chosen camera device
        chosen_camera_device = JSON.stringify(constraints);
        console.log('chosen cam: ', chosen_camera_device); 

        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(stream) {
                var videoElement = document.getElementById('camera-preview');
                videoElement.srcObject = stream;
                videoElement.style.display = "block";
            })
            .catch(function(err) {
                console.error('Error accessing camera:', err);
                alert('Error accessing camera: ' + err.message);
        });
    }

    function haveNotConnMonitor(){
        var multiple_modal = document.getElementById('popup-modal');
        monitor_setup = null;

        multiple_modal.setAttribute('class', 'hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full');
        multiple_modal.removeAttribute('aria-modal');
        multiple_modal.removeAttribute('role');

        chosen_monitor_set_up = "have_not_conn_multiple_monitor";
        console.log('sending this: ', chosen_monitor_set_up);
        console.log('redirecting to quiz');

        if (monitor_microphone_activated === 1 && monitor_camera_activated !== 1){
            sendSessionSetupData();
        }
    }

    function haveRemoveExtMonitor(){
        var multiple_modal = document.getElementById('popup-modal');
        monitor_setup = 

        multiple_modal.setAttribute('class', 'hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full');
        multiple_modal.removeAttribute('aria-modal');
        multiple_modal.removeAttribute('role');

        chosen_monitor_set_up = "have_remove_external_monitor";
        console.log('sending this: ', chosen_monitor_set_up);
        console.log('redirecting to quiz');

        if (monitor_microphone_activated === 1 && monitor_camera_activated !== 1){
            sendSessionSetupData();
        }
    }

    function continueWithMulMonitor(){
        var multiple_modal = document.getElementById('popup-modal');

        multiple_modal.setAttribute('class', 'hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full');
        multiple_modal.removeAttribute('aria-modal');
        multiple_modal.removeAttribute('role');

        chosen_monitor_set_up = "continue_with_multiple_monitor";
        console.log('sending this: ', chosen_monitor_set_up);
        console.log('redirecting to quiz');

        if (monitor_microphone_activated === 1 && monitor_camera_activated !== 1){
            sendSessionSetupData();
        }
    }

    function sendSessionSetupData(){
        var xhr = new XMLHttpRequest();
        xhr.open('POST', <?php echo json_encode($wwwroot . '/local/auto_proctor/proctor_tools/proctor_setup/save_proctor_session_setup.php'); ?>, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        // ==== DEBUGGING =====
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    console.log('POST request successful');
                    window.location.href = <?php echo json_encode($quizattempturl); ?>;
                    // You can add further actions if needed
                } else {
                    console.error('POST request failed with status: ' + xhr.status);
                    // Handle the error or provide feedback to the user
                }
            }
        };
        xhr.send('userid=' + <?php echo $userid; ?> + '&quizid=' + <?php echo $quizid; ?> + '&quizattempt=' + <?php echo $quizattempt; ?> + '&quizattempturl=' + <?php echo json_encode($quizattempturl); ?> + '&chosen_camera_device=' + chosen_camera_device + '&chosen_monitor_set_up=' + chosen_monitor_set_up);
    }

    window.onload = function() {
        var filterElement = document.getElementById('filter');

        navigator.mediaDevices.enumerateDevices()
        .then(function(devices) {
            devices.forEach(function(device) {
                if (device.kind === 'videoinput') {
                    var option = document.createElement('li');
                    option.className = "flex items-center";
                    var input = document.createElement('input');
                    input.type = "radio";
                    input.id = device.deviceId;
                    input.name = "camera";
                    input.value = device.deviceId;
                    var label = document.createElement('label');
                    label.htmlFor = device.deviceId;
                    label.className = "ml-2 text-sm font-medium text-gray-900";
                    label.textContent = device.label || 'Camera ' + (filterElement.options.length + 1);
                    option.appendChild(input);
                    option.appendChild(label);
                    filterElement.querySelector('ul').appendChild(option);
                }
            });
        })
        .catch(function(err) {
            console.error('Error enumerating devices:', err);
        });

        // // Prevent dev mode
        // document.addEventListener("contextmenu", function (e) {
        //     e.preventDefault();
        // }, false);

        // document.addEventListener("keydown", function (e) {
        //     //document.onkeydown = function(e) {
        //     // "I" key
        //     if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
        //         disabledEvent(e);
        //     }
        //     // "J" key
        //     if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
        //         disabledEvent(e);
        //     }
        //     // "S" key + macOS
        //     if (e.keyCode == 83 && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) {
        //         disabledEvent(e);
        //     }
        //     // "U" key
        //     if (e.ctrlKey && e.keyCode == 85) {
        //         disabledEvent(e);
        //     }
        //     // "F12" key
        //     if (event.keyCode == 123) {
        //         disabledEvent(e);
        //     }
        // }, false);

        // function disabledEvent(e) {
        //     if (e.stopPropagation) {
        //         e.stopPropagation();
        //     } else if (window.event) {
        //         window.event.cancelBubble = true;
        //     }
        //     e.preventDefault();
        //     return false;
        // }
        
    };
</script>
    


    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
</body>
</html>