$(document).ready(function () {
    /*
        We stream the chosen device camera and send its frames to the FaceMesh model for face detection.
        When streaming with muted audio, there is a conflict with FaceMesh. Therefore, we have two camera streams: one for the FaceMesh model, which has no audio, and the other for the recording, which has audio.

        CAMERA RECORDING
            - When camera stream then startRecording().
            - When file is being unloaded (exiting file, reloading the file) or when camera permission is denied during quiz then stopRecording().

            startRecording()
                - Set isRecording to true


        FACE DETECTION
            - Count detected faces; when multiple faces are detected, then probSusMovement('multiple_face'); if no faces are detected, then probSusMovement('no_face').
            - If a single face is detected, then get the return facelandmark and retrieve the landmark of noseTip, noseBridge, rightEar, leftEar, and chin.
            - Landmark consist of x, y, z coordinates.
            - Using the retrieved landmarks, compute the Euler angle, pitch, and yaw.
            - When pitch and yaw go out of range for a neutral head pose, then it is considered a suspicious movement.
            - When suspicious movement is detected then probSusMovement('suspicious_movement').
            - If movement is in a range for a neutral head pose, reset probSusCounter and susCounter.

            probSusMovement(evidence_name_type)
                - Iterate probSusCounter
                - When probSusCounter is greater than 10 and susCounter is 0, then iterate susCounter, start timer for the duration, and capture camera captureEvidence(evidence_name_type).
                
                NOTE: The value of evidence_name_type in captureEvidence(evidence_name_type) came from the probSusMovement(evidence_name_type).


        CAMERA CAPTURE

            captureEvidence(evidence_name_type)

                CAPTURING:
                    - Generate timestamp by generateTimestamp(), destruct the return value of the function into two constant variables: timestamp and milliseconds.
                    - Generate unique filename compose of userid, quizid, quizattempt, with timestamp and miliseconds.
                    - Create canvas element for capturing the video element.
                    - Draw image in canvas from the video.
                    - Convert the contents of a canvas element to a data URL, which represents the canvas image as a base64-encoded string.

                SENDING:
                    - Sending the data URL and the generated filename to the server for saving (save_cam_capture.php).
                    - After successfully sending the image url call sendActivityRecord(evidence_name_type) for saving the activity in the activity_report_table.

            sendActivityRecord(evidence_name_type)

                SENDING:
                    - Send the evidence_name_type, filename, userid, quizid, and quizattempt to server for saving the activity in the activity_report_table (save_cam_activity.php).
    */

    let isRecording = false;
    let isSending = true;
    let video_proctoring;
    let video_recording;
    let timestamp_captured;
    let preventedAction;
    let quittingQuiz;
    let newSubmitButton;

        let susCounter = 0;
        let probSusCounter = 0;
        let filename;

        let mediaRecorder;
        let recordedChunks = [];
        let startTime;
        let timestampInterval;
        let activity_timestamp;

        var last_clicked;

        // Camera constraints for proctoring
        const getUserMediaConstraintsProctoring = (deviceId) => {
            return {
                video: {
                deviceId: deviceId ? { exact: deviceId } : undefined,
                facingMode: 'user', // Set facingMode if preferred
                },
            };
        };

        // Camera constraints for recording
        const getUserMediaConstraintsRecording = (deviceId) => {
            return {
                video: {
                deviceId: deviceId ? { exact: deviceId } : undefined,
                facingMode: 'user', // Set facingMode if preferred
                frameRate: { max: 7 },
                },
                audio: true,
            };
        };

        // Get all available camera device.
        navigator.mediaDevices.enumerateDevices()
        .then(function(devices) {
            devices.forEach(function(device) {
                if (device.kind === 'videoinput') {
                    console.log('avail cam: ', device.deviceId);
                }
            });
        })

        // Initialize the user's chosen camera from the proctoring session setup.
        const chosenCameraDevice = JSON.parse(jsdata.chosen_camera_device);
        var deviceId = chosenCameraDevice.video.deviceId.exact;

        // When windows loada
        window.onload = async function() {

            // Use the user's chosen camera device.
            const constraints_proctoring = getUserMediaConstraintsProctoring(deviceId);
            const constraints_recording= getUserMediaConstraintsRecording(deviceId);

            // Stream user's chosen camera with facemesh model.
            try {
                const stream_proctoring = await navigator.mediaDevices.getUserMedia(constraints_proctoring);
                const stream_recording = await navigator.mediaDevices.getUserMedia(constraints_recording);

                    
                // Create video element for the proctoring.
                video_proctoring = document.createElement('video');
                video_proctoring.className = 'input_video';
                video_proctoring.srcObject = stream_proctoring;
                video_proctoring.autoplay = true;
                video_proctoring.playsinline = true;
                video_proctoring.style.display = 'none';
                document.body.appendChild(video_proctoring);

                // Create video element for camera recording.
                video_recording = document.createElement('video');
                video_recording.className = 'input_video';
                video_recording.srcObject = stream_recording;
                video_recording.autoplay = true;
                video_recording.muted = true;
                video_recording.playsinline = true;
                video_recording.style.display = 'none';
                document.body.appendChild(video_recording);

                // Start the camera recording
                startRecording();

                // Apply facemesh to the selected camera.
                const onFrame = async () => {
                    await faceMesh.send({ image: video_proctoring });
                    requestAnimationFrame(onFrame);
                };

                // Start sending frames to FaceMesh
                onFrame();

            }
            
            // If error accessing camera.
            catch (error) {

                // If camera permission is denied, record in database.
                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                    // User denied camera access
                        sendActivityRecord('camera_permission_denied');
                        // If strict mode was activated then forcefully exit quiz.
                        if (jsdata.strict_mode_activated == 1){
                            console.log('camera denied must redirect to review attempt quiz page');
                            window.location.href = jsdata.wwwroot + '/mod/quiz/view.php?id=' + jsdata.cmid;
                        }
                } 
                
                else {
                        // Other errors
                        console.error('Error accessing camera:', error.message);
                        sendActivityRecord('camera_permission_denied');

                        // If strict mode was activated then forcefully exit quiz.
                        if (jsdata.strict_mode_activated == 1){
                        console.log('camera denied must redirect to review attempt quiz page');
                        window.location.href = jsdata.wwwroot + '/mod/quiz/view.php?id=' + jsdata.cmid;
                    }
                }
            }
        }

        // Starting the camera recording
        function startRecording() {
            
            if (!video_recording.srcObject) {
                video_recording.srcObject = window.stream_recording;
            }

            console.log('recording')

            // Set this to true for the purpose of saving the video.
            isRecording = true;

            // For the raw data
            recordedChunks = [];

            // Start the timestamp
            startTime = Date.now();

            // Set and start the recording of the video
            mediaRecorder = new MediaRecorder(video_recording.srcObject, { mimeType: 'video/webm' });
            mediaRecorder.start();
            mediaRecorder.ondataavailable = handleDataAvailable;
            mediaRecorder.onstop = handleStop;
            updateTimestamp();
            // Update timestamp every second
            timestampInterval = setInterval(updateTimestamp, 1000);
        }

        function handleDataAvailable(event) {
            recordedChunks.push(event.data);
        }

        function handleStop(event) {
            clearInterval(timestampInterval); // Stop updating timestamp
            const blob = new Blob(recordedChunks, { type: 'video/webm' });
            const videoUrl = URL.createObjectURL(blob);
            video_recording.src = videoUrl;
            video_recording.style.display = 'none'; // Hide the video element
            downloadVideo(blob);
        }

        function downloadVideo(blob) {
            const { timestamp, milliseconds } = generateTimestamp();
            const recording_filename = 'EVD_USER_' + jsdata.userid + '_QUIZ_' + jsdata.quizid + '_ATTEMPT_' + jsdata.quizattempt + '_' +timestamp.replace(/[/:, ]/g, '') + '_' + milliseconds + '_RECORDING';

            // ==== USING XHR ====
            // const xhr = new XMLHttpRequest();
            // xhr.open('POST', jsdata.wwwroot + '/local/auto_proctor/proctor_tools/camera_monitoring/save_cam_recording.php', true);
            // xhr.setRequestHeader('Content-Type', 'application/octet-stream');
            // xhr.setRequestHeader('X-Recording-Filename', recording_filename); // Send recording filename as a header
            // xhr.responseType = 'blob';
            // xhr.onload = function () {
            //     if (xhr.status === 200) {
            //         console.log('Recording saved successfully.');
            //         const blob = new Blob([xhr.response], { type: 'video/webm' });
            //         isSending = false;
            //     }
            // };
            // xhr.send(blob);

            fetch(jsdata.wwwroot + '/local/auto_proctor/proctor_tools/camera_monitoring/save_cam_recording.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/octet-stream',
                    'X-Recording-Filename': recording_filename
                },
                body: blob
            })
            .then(response => {
                if (response.ok) {
                    console.log('Recording saved successfully.');
                    //const blob = new Blob([xhr.response], { type: 'video/webm' });
                    isSending = false;
                } else {
                    console.error('Failed to save recording:', response.statusText);
                }
            })
            .catch(error => {
                console.error('An error occurred while saving the recording:', error);
            });
        }

        function stopRecording() {
            isRecording = false;
            mediaRecorder.stop();
        }

        function updateTimestamp() {
            // Get the current time in milliseconds
            const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);

            // Calculate hours, minutes, and seconds
            const hours = Math.floor(elapsedSeconds / 3600).toString().padStart(2, '0');
            const minutes = Math.floor((elapsedSeconds % 3600) / 60).toString().padStart(2, '0');
            const seconds = (elapsedSeconds % 60).toString().padStart(2, '0');

            activity_timestamp = hours + ':' + minutes + ':' + seconds;
        }

        // Results of facemesh
        function onResults(results) {    

            // If facemesh return landmarks
            if (results.multiFaceLandmarks) {

                // Count face
                const faceCount = results.multiFaceLandmarks.length;

                // If face is only one.
                if (faceCount > 0 && faceCount < 2){

                    // Loop through the result of facemesh.
                    for (const landmarks of results.multiFaceLandmarks) {

                        // Initialize the necessary landmark for computation of euler for headpose estimation.
                        const noseTip = landmarks[4];
                        const noseBridge = landmarks[6];
                        const rightEar = landmarks[137];
                        const leftEar = landmarks[366];
                        const chin = landmarks[152];
                        
                        // Calculate the roll angle (z-axis) between noseTip, noseBridge, and chin
                        const rollAngle = Math.atan2(chin.y - noseTip.y, chin.x - noseTip.x) * 180 / Math.PI;

                        // Calculate the pitch angle (x-axis) between noseTip, noseBridge, and chin
                        const rawPitchAngle = Math.atan2(chin.z - noseTip.z, chin.y - noseTip.y) * 180 / Math.PI;

                        // Calculate the yaw angle (y-axis) between noseTip, noseBridge, and chin
                        const rawYawAngle = Math.atan2(chin.z - noseTip.z, chin.x - noseTip.x) * 180 / Math.PI;

                        // Furnish pitch and yaw angle
                        const pitchAngle = rawPitchAngle - 15;
                        const yawAngle = rawYawAngle - 90;
                        

                        // Headpose default is neutral.
                        let gazeDirection = "neutral";

                        // If the yaw and pitch goes above the neutral angle then headpose is a suspicious movement.
                        // Call probable suspicious movement function with evidence name type as 'suspicious_movement'.
                        if (yawAngle > 15 || yawAngle < -10 || pitchAngle > 10 || pitchAngle < -10) {
                            gazeDirection = "sus";
                            probSusMovement('suspicious_movement');
                        }

                        // If the headpose returns to neutral
                        // then stop the timer for the previous suspicious movement,
                        // reset the suspicous counters.
                        if (gazeDirection === "neutral"){
                            susCounter = 0;
                            probSusCounter = 0;
                        }
                    }
                }
                // If multiple face detected,
                // call probable suspicious movement function with evidence name type as 'multiple_face'.
                else if (faceCount > 1) {
                    probSusMovement('multiple_face');

                }
                // If no face detected
                // call probable suspicious movement function with evidence name type as 'no_face'.
                else {
                    //gazeDirectionElement.innerHTML = `Gaze Direction: No face detected`;
                    probSusMovement('no_face');
                }
                
            }
        }

        // Function for when suspicous movement is detected.
        function probSusMovement(evidence_name_type) {
            probSusCounter++;

            // When probSusCounter is greater than 10 and susCounter is 0, then iterate susCounter, and capture camera captureEvidence(evidence_name_type).
            // The value of evidence_name_type in captureEvidence(evidence_name_type) came from the probSusMovement(evidence_name_type).
            if (probSusCounter > 10){
                if (susCounter === 0){
                    susCounter++;
                    captureEvidence(evidence_name_type);
                    timestamp_captured = activity_timestamp;
                }
            }
        }

        function captureEvidence(evidence_name_type) {

            // Retrieve the video element containing the camera feed.
            var video = document.querySelector('.input_video');

            setTimeout(() => {
                // Create canvas for the capturing the video element.
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Draw image in the canvas from the video
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Generate unique filename compose of userid, quizid, quizattempt, with timestamp and miliseconds.
                const { timestamp, milliseconds } = generateTimestamp();
                filename = 'EVD_USER_' + jsdata.userid + '_QUIZ_' + jsdata.quizid + '_ATTEMPT_' + jsdata.quizattempt + '_' +timestamp.replace(/[/:, ]/g, '') + '_' + milliseconds + '_' + evidence_name_type +'.png'; // Custom filename with evidenceType
                
                // Convert the contents of a canvas element to a data URL, which represents the canvas image as a base64-encoded string.
                const dataUrl = canvas.toDataURL('image/png');
                
                // Sending the data URL and the generated filename to the server for saving (save_cam_capture.php).
                fetch(jsdata.wwwroot +'/local/auto_proctor/proctor_tools/camera_monitoring/save_cam_capture.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'dataUri=' + encodeURIComponent(dataUrl) + '&filename=' + encodeURIComponent(filename),
                })

                // After successfully sending the image url call sendActivityRecord(evidence_name_type) for saving the activity in the activity_report_table.
                .then(response => response.json())
                    .then(data => {
                        console.log('Screen captured and saved as: ' + data.filename);

                        sendActivityRecord(evidence_name_type);
                    })
                    .catch(error => {
                        console.error('Error saving screen capture:', error);
                    });
            }, 200);

        }

        // Function to generate timestamp
        function generateTimestamp() {
            const now = new Date();
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
                timeZoneName: 'short',
            };

            const formatter = new Intl.DateTimeFormat('en-US', options);
            const timestamp = formatter.format(now);

            return { timestamp, milliseconds: now.getMilliseconds() };
        }

        // Send the evidence_name_type, filename, userid, quizid, and quizattempt to server for saving the activity in the activity_report_table (save_cam_activity.php).
        function sendActivityRecord(evidence_name_type) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', jsdata.wwwroot + '/local/auto_proctor/proctor_tools/camera_monitoring/save_cam_activity.php', true); // Replace with the actual path
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log('POST request successful');
                    } else {
                        console.error('POST request failed with status: ' + xhr.status);
                        // Handle the error or provide feedback to the user
                    }
                }
            };
            xhr.send('evidence_name_type=' + evidence_name_type + '&filename=' + filename + '&activity_timestamp=' + timestamp_captured + '&userid=' + jsdata.userid + '&quizid=' + jsdata.quizid + '&quizattempt=' + jsdata.quizattempt);
        }
        
        const faceMesh = new FaceMesh({locateFile: (file) => {
            return `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`;
        }});

        faceMesh.setOptions({
            maxNumFaces: 2,
            refineLandmarks: true,
            minDetectionConfidence: 0.5,
            minTrackingConfidence: 0.5
        });
        faceMesh.onResults(onResults);

        // Check camera permission
        navigator.permissions.query({name: 'camera'}).then(function(permissionStatus) {
            console.log('camera permission state is ', permissionStatus.state);
            permissionStatus.onchange = function() {
                console.log('camera permission state has changed to ', this.state);

                // If camera permission is denied, record in database, stop the recording to save.
                if (this.state = 'denied'){
                    stopRecording();
                    sendActivityRecord('camera_permission_denied_during_quiz');

                    // Check if strict mode was activated
                    // If strict mode was activated then forcefully exit quiz.
                    if (jsdata.strict_mode_activated == 1){
                        console.log('camera denied must redirect to review attempt quiz page');
                        window.location.href = jsdata.wwwroot + '/mod/quiz/view.php?id=' + jsdata.cmid;
                    }
                }
            };
        });

        // Function to create an loading overlay
        function createOverlay() {
            // Check if overlay already exists
            if (!document.getElementById('overlay')) {
                // Create a div element for the overlay
                var overlay = document.createElement('div');
                
                // Set attributes for the overlay
                overlay.id = 'overlay';
                overlay.style.position = 'fixed';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.width = '100%';
                overlay.style.height = '100%';
                overlay.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                overlay.style.zIndex = '9999'; 
                
                // Append the loading animation HTML to the overlay
                overlay.innerHTML = `
                <style>
                    body {
                        font-family: 'Titillium Web', sans-serif;
                        font-size: 18px;
                        font-weight: bold;
                    }
                    .loading {
                        position: absolute;
                        left: 0;
                        right: 0;
                        top: 50%;
                        width: 100px;
                        color: #000;
                        margin: auto;
                        -webkit-transform: translateY(-50%);
                        -moz-transform: translateY(-50%);
                        -o-transform: translateY(-50%);
                        transform: translateY(-50%);
                    }
                    .loading span {
                        position: absolute;
                        height: 10px;
                        width: 84px;
                        top: 50px;
                        overflow: hidden;
                    }
                    .loading span > i {
                        position: absolute;
                        height: 10px;
                        width: 10px;
                        border-radius: 50%;
                        -webkit-animation: wait 4s infinite;
                        -moz-animation: wait 4s infinite;
                        -o-animation: wait 4s infinite;
                        animation: wait 4s infinite;
                    }
                    .loading span > i:nth-of-type(1) {
                        left: -28px;
                        background: black;
                    }
                    .loading span > i:nth-of-type(2) {
                        left: -21px;
                        -webkit-animation-delay: 0.8s;
                        animation-delay: 0.8s;
                        background: black;
                    }
                    @keyframes wait {
                        0%   { left: -7px  }
                        30%  { left: 52px  }
                        60%  { left: 22px  }
                        100% { left: 100px }
                    }
                </style>
                <div class="loading">
                    <p>Please wait</p>
                    <span><i></i><i></i></span>
                </div>`;

                // Append the overlay to the body
                document.body.appendChild(overlay);
            }
        }

        // Function to remove overlay
        function removeOverlay() {
            var overlay = document.getElementById('overlay');
            if (overlay) {
                overlay.parentNode.removeChild(overlay);
            }
        }

        window.onclick = function(e) {
            last_clicked = e.target;

            // Check if the last clicked element is an anchor element and has an href attribute
            if (last_clicked.tagName.toLowerCase() === 'a' && last_clicked.getAttribute('href') !== null) {
                quittingQuiz = true;
                console.log('Target is an anchor element with href:', last_clicked.getAttribute('href'));

                preventedAction = last_clicked;
                return true;
            }
            else if (last_clicked.tagName.toLowerCase() === 'input' && last_clicked.getAttribute('type') === 'submit'){
                quittingQuiz = false;
            }
            else{
                quittingQuiz = true;
                preventedAction = jsdata.wwwroot + '/mod/quiz/view.php?id=' + jsdata.cmid;
                console.log('Target is an anchor element with href:', last_clicked.getAttribute('href'));
                return true;
            }
        }

        // Function to continuously check isSending flag and update overlay
        function checkSendingStatus() {
            console.log('isSending: ', isSending);
            console.log('quittingQuiz: ', quittingQuiz);

            if (isSending) {
                createOverlay();
            } else if (!isSending){
                removeOverlay();
                if (quittingQuiz){
                    console.log('redirectingggggggggggggggg');
                    window.location.href = preventedAction;
                }
                else{
                    removeOverlay();
                    var button = document.getElementById('mod_quiz-next-nav');
                    if (button) {
                        console.log('clicking');
                        button.click();
                    }
                }
                
            }
        }

        document.getElementById('mod_quiz-next-nav').addEventListener('click', function(event) {
            console.log('isSending: ', isSending);
            // Prevent default action only on the first click
            if (!this.clickedOnce) {
                createOverlay();
                isSending = true;
                stopRecording();

                console.log('once');

                event.preventDefault();
                event.returnValue = "Your changes may not be saved. Are you sure you want to leave?";
                this.clickedOnce = true;
            }

            if (isSending){
                var intervalId = setInterval(checkSendingStatus, 1000);
            }
        });

        window.addEventListener('beforeunload', function (event) {
        console.log('isSending: ', isSending);
        console.log('RECORDING:', isRecording);
            if (isRecording){
                createOverlay();
                isSending = true;
                stopRecording();
                
                if (isSending){
                    console.log('onload');
                    event.preventDefault();
                    event.returnValue = "Your changes may not be saved. Are you sure you want to leave?";
                }
                
            }
            if (isSending){
                var intervalId = setInterval(checkSendingStatus, 1000);
            }

        });
        
});