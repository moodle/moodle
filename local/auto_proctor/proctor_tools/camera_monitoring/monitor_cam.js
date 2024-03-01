$(document).ready(function () {
    /*
        We stream the chosen device camera and send its frames to the FaceMesh model for face detection.

        FACE DETECTION
            - Count detected faces; when multiple faces are detected, then probSusMovement('multiple_face'); if no faces are detected, then probSusMovement('no_face').
            - If a single face is detected, then get the return facelandmark and retrieve the landmark of noseTip, noseBridge, rightEar, leftEar, and chin.
            - Landmark consist of x, y, z coordinates.
            - Using the retrieved landmarks, compute the Euler angle, pitch, and yaw.
            - When pitch and yaw go out of range for a neutral head pose, then it is considered a suspicious movement.
            - When suspicious movement is detected then probSusMovement('suspicious_movement').
            - If movement is in a range for a neutral head pose, then sendDuration(), stop timer for the previous detected activity, reset probSusCounter and susCounter.

            probSusMovement(evidence_name_type)
                - Iterate probSusCounter
                - When probSusCounter is greater than 10 and susCounter is 0, then reset or set sendDurationCounter to 0,
                iterate susCounter, start timer for the duration, and capture camera captureEvidence(evidence_name_type).
                
                NOTE: The value of evidence_name_type in captureEvidence(evidence_name_type) came from the probSusMovement(evidence_name_type).

        UPDATING CAPTURED ACTIVITY DURATION

            sendDuration()
            - If sendDurationCounter is 0 then iterate sendDurationCounter and updateDuration() for the previous detected activity.

            updateDuration()
            - Sends the value of duration variable in server along with the userid, quizid, quizattempt, and filename to update the duration of the previous detected activity in activity_report_table.

            updateTimer(milliseconds)
            - This function sets the duration variable with the value of miliseconds variable.

            startTimer()
            - This function set or reset the miliseconds to 0.
            - Iterate miliseconds by 10 every 10 miliseconds, then updateTimer(milliseconds).

            stopTimer()
            - Clear intervalId.

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

    let videoElement;

        let susCounter = 0;
        let probSusCounter = 0;
        let sendDurationCounter = 0;
        let duration;
        let intervalId;
        let filename;

        // Camera constraints.
        const getUserMediaConstraints = (deviceId) => {
            return {
                video: {
                deviceId: deviceId ? { exact: deviceId } : undefined,
                facingMode: 'user', // Set facingMode if preferred
                },
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

        // When windows load
        window.onload = async function() {

            // Use the user's chosen camera device.
            const constraints = getUserMediaConstraints(deviceId);

            // Stream user's chosen camera with facemesh model.
            try {
                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                    
                // Create video element for camera capture.
                const video = document.createElement('video');
                video.className = 'input_video';
                video.srcObject = stream;
                video.autoplay = true;
                video.playsinline = true;
                video.style.display = 'none';
                document.body.appendChild(video);

                // Apply facemesh to the selected camera.
                const onFrame = async () => {
                    await faceMesh.send({ image: video });
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
                            //probSusMovement('sendTheActivty');
                            sendDuration();
                            stopTimer();
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

        // Function for updating the duration of the detected and captured activity.
        function sendDuration(){
            if (sendDurationCounter === 0){
                sendDurationCounter++;
                updateDuration();
            }
        }

        // Function for when suspicous movement is detected.
        function probSusMovement(evidence_name_type) {
            probSusCounter++;

            // When probSusCounter is greater than 10 and susCounter is 0, then reset or set sendDurationCounter to 0,
            // iterate susCounter, start timer for the duration, and capture camera captureEvidence(evidence_name_type).
            // The value of evidence_name_type in captureEvidence(evidence_name_type) came from the probSusMovement(evidence_name_type).
            if (probSusCounter > 10){
                if (susCounter === 0){
                    //updateDuration();
                    sendDurationCounter = 0;
                    susCounter++;
                    const intervalId = startTimer();
                    captureEvidence(evidence_name_type);
                }
            }
        }

        // Function to update the timer
        function updateTimer(milliseconds) {
            duration = milliseconds;
        }

        // Function to start the timer for the duration
        function startTimer() {
            let milliseconds = 0;
            updateTimer(milliseconds);

            // Update the timer every 10 milliseconds
            intervalId = setInterval(function () {
                milliseconds += 10;
            updateTimer(milliseconds);
            }, 10);

            return intervalId;
        }

        // Function to stop timer.
        function stopTimer() {
            clearInterval(intervalId);
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
            xhr.send('evidence_name_type=' + evidence_name_type + '&filename=' + filename + '&userid=' + jsdata.userid + '&quizid=' + jsdata.quizid + '&quizattempt=' + jsdata.quizattempt);
        }

        // Update the duration for the recent activity
        // Sends the value of duration variable in server along with the userid, quizid, quizattempt, and filename to update the duration of the previous detected activity in activity_report_table.
        function updateDuration(){
            console.log('duration: ', duration);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', jsdata.wwwroot + '/local/auto_proctor/proctor_tools/camera_monitoring/save_cam_activity_duration.php', true); // Replace with the actual path
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
            xhr.send('filename=' + filename + '&duration=' + duration + '&userid=' + jsdata.userid + '&quizid=' + jsdata.quizid + '&quizattempt=' + jsdata.quizattempt);
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

                // If camera permission is denied, record in database.
                if (this.state = 'denied'){
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
});