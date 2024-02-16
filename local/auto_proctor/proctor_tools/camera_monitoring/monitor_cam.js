$(document).ready(function () {
    let videoElement;
        //const canvasElement = document.getElementsByClassName('output_canvas')[0];

        //const promptMessageElement = document.getElementById('promptMessage');

        // const pitchAngleElement = document.getElementById('pitchAngle');
        // const yawAngleElement = document.getElementById('yawAngle');
        // const rollAngleElement = document.getElementById('rollAngle');
        // const gazeDirectionElement = document.getElementById('gazeDirection');

        let susCounter = 0;
        let probSusCounter = 0;
        let duration;
        let intervalId;
        let filename;

        // const camera = new Camera(videoElement, {onFrame: async () => {
        //     await faceMesh.send({image: videoElement});
        //     },
        //     width: 1280,
        //     height: 720
        // });

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
                sendActivityRecord('camera_permission_denied');
                // Check if strict mode was activated
                if (jsdata.strict_mode_activated == 1){
                    console.log('camera denied must redirect to review attempt quiz page');
                    window.location.href = jsdata.wwwroot + '/mod/quiz/view.php?id=' + jsdata.cmid;
                }
            } else {
                // Other errors
                console.error('Error accessing camera:', error.message);
                sendActivityRecord('camera_permission_denied');
                // Check if strict mode was activated
                if (jsdata.strict_mode_activated == 1){
                    console.log('camera denied must redirect to review attempt quiz page');
                    window.location.href = jsdata.wwwroot + '/mod/quiz/view.php?id=' + jsdata.cmid;
                }
            }
        });



        function onResults(results) {
            
            if (results.multiFaceLandmarks) {
                const faceCount = results.multiFaceLandmarks.length;
                if (faceCount > 0 && faceCount < 2){

                    for (const landmarks of results.multiFaceLandmarks) {
                        const noseTip = landmarks[4];
                        const noseBridge = landmarks[6];
                        const rightEar = landmarks[137];  // Adjust the index based on your model
                        const leftEar = landmarks[366];   // Adjust the index based on your model
                        const chin = landmarks[152];
                        
                        // Calculate the roll angle (z-axis) between noseTip, noseBridge, and chin
                        const rollAngle = Math.atan2(chin.y - noseTip.y, chin.x - noseTip.x) * 180 / Math.PI;

                        // Calculate the pitch angle (x-axis) between noseTip, noseBridge, and chin
                        const rawPitchAngle = Math.atan2(chin.z - noseTip.z, chin.y - noseTip.y) * 180 / Math.PI;

                        // Calculate the yaw angle (y-axis) between noseTip, noseBridge, and chin
                        const rawYawAngle = Math.atan2(chin.z - noseTip.z, chin.x - noseTip.x) * 180 / Math.PI;
                        const pitchAngle = rawPitchAngle - 15;
                        const yawAngle = rawYawAngle - 90;
                        

                        let gazeDirection = "neutral";
                        let promptMessage = "";

                        if (yawAngle > 15 || yawAngle < -10 || pitchAngle > 10 || pitchAngle < -10) {
                            gazeDirection = "sus";
                            promptMessage = "Please position yourself at the center and face forward towards the camera.";
                            probSusMovement('suspicious_movement');
                        }

                        if (gazeDirection === "neutral"){
                            probSusMovement('sendTheActivty');
                            stopTimer();
                            susCounter = 0;
                            probSusCounter = 0;
                        }

                        // Display the angles
                        // promptMessageElement.innerHTML = `Prompt Message: ${promptMessage}`;
                        // pitchAngleElement.innerHTML = `Pitch Angle: ${pitchAngle.toFixed(2)}`;
                        // yawAngleElement.innerHTML = `Yaw Angle: ${yawAngle.toFixed(2)}`;
                        // rollAngleElement.innerHTML = `Roll Angle: ${rollAngle.toFixed(2)}`;
                        // gazeDirectionElement.innerHTML = `Gaze Direction: ${gazeDirection}`;
                    }
                }
                else if (faceCount > 1) {
                    //gazeDirectionElement.innerHTML = `Gaze Direction: Multiple face detected`;
                    probSusMovement('multiple_face');

                }
                else {
                    //gazeDirectionElement.innerHTML = `Gaze Direction: No face detected`;
                    probSusMovement('no_face');
                }
                
            }
        }

        function probSusMovement(evidence_name_type) {
            probSusCounter++;
            console.log('sussss', probSusCounter);
            if (probSusCounter > 10 && evidence_name_type !== 'sendTheActivty'){
                if (susCounter === 0 && evidence_name_type !== 'sendTheActivty'){
                    updateDuration();
                    susCounter++;
                    console.log('Counter: ', susCounter);
                    const intervalId = startTimer();
                    captureEvidence(evidence_name_type);
                    console.log('captured');
                }
            }
        }

        // Function to update the timer display
        function updateTimer(milliseconds) {
            //document.getElementById('timer').textContent = seconds + '.' +milliseconds;
            duration = milliseconds;
        }

        // Function to start the timer
        function startTimer() {
            //let seconds = 0;
            let milliseconds = 0;
            updateTimer(milliseconds);

            // Update the timer every 10 milliseconds
            intervalId = setInterval(function () {
                milliseconds += 10;
                // if (milliseconds >= 1000) {
                //     seconds++;
                //     milliseconds = 0;
                // }
            updateTimer(milliseconds);
            }, 10);
        }
        function stopTimer() {
            clearInterval(intervalId);
        }

        function captureEvidence(evidence_name_type) {
            // Get the video element
            var video = document.querySelector('.input_video');

            setTimeout(() => {
            const canvas = document.createElement('canvas');
            canvas.width = videoElement.videoWidth;
            canvas.height = videoElement.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

            // var capturedContainer = document.getElementById('capturedContainer');
            // capturedContainer.innerHTML = ''; // Clear previous content
            // capturedContainer.appendChild(canvas);
                                
            const { timestamp, milliseconds } = generateTimestamp();
            filename = 'EVD_USER_' + jsdata.userid + '_QUIZ_' + jsdata.quizid + '_ATTEMPT_' + jsdata.quizattempt + '_' +timestamp.replace(/[/:, ]/g, '') + '_' + milliseconds + '_' + evidence_name_type +'.png'; // Custom filename with evidenceType
                                
            const dataUrl = canvas.toDataURL('image/png');
            
            fetch(jsdata.wwwroot +'/local/auto_proctor/proctor_tools/camera_monitoring/save_cam_capture.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'dataUri=' + encodeURIComponent(dataUrl) + '&filename=' + encodeURIComponent(filename),
            })
            .then(response => response.json())
                .then(data => {
                    console.log('Screen captured and saved as: ' + data.filename);
                    // Send to function that saves in database
                    sendActivityRecord(evidence_name_type);
                })
                .catch(error => {
                    console.error('Error saving screen capture:', error);
                });
            }, 200);

        }

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

        // Save in database
        function sendActivityRecord(evidence_name_type) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', jsdata.wwwroot + '/local/auto_proctor/proctor_tools/camera_monitoring/save_cam_activity.php', true); // Replace with the actual path
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            // ==== DEBUGGING =====
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
        // Update the filename for the recent activity
        function updateDuration(){
            console.log('duration: ', duration);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', jsdata.wwwroot + '/local/auto_proctor/proctor_tools/camera_monitoring/save_cam_activity_duration.php', true); // Replace with the actual path
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            // ==== DEBUGGING =====
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

        
        function updateNoseAngle(angleNoseTipBridge, angleRightEarLeftEar) {
            noseAngleDisplay.innerHTML = `Nose Tip to Nose Bridge Angle: ${angleNoseTipBridge.toFixed(2)} degrees<br>`;
            noseAngleDisplay.innerHTML += `Right Ear to Left Ear Angle: ${angleRightEarLeftEar.toFixed(2)} degrees`;
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

        navigator.permissions.query({name: 'camera'}).then(function(permissionStatus) {
            console.log('camera permission state is ', permissionStatus.state);
            permissionStatus.onchange = function() {
                console.log('camera permission state has changed to ', this.state);
                if (this.state = 'denied'){
                    sendActivityRecord('camera_permission_denied_during_quiz');
                    if (jsdata.strict_mode_activated == 1){
                        console.log('camera denied must redirect to review attempt quiz page');
                        window.location.href = jsdata.wwwroot + '/mod/quiz/view.php?id=' + jsdata.cmid;
                    }
                }
            };
        });
});