<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils/control_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <video class="input_video"></video>
        <canvas class="output_canvas" width="1280px" height="720px"></canvas>
        <div id="promptMessage"></div>

        <div id="pitchAngle"></div> 
        <div id="yawAngle"></div> 
        <div id="rollAngle"></div>
        <div id="gazeDirection"></div> 
    </div>

    <script type="module">
        const videoElement = document.getElementsByClassName('input_video')[0];
        const canvasElement = document.getElementsByClassName('output_canvas')[0];
        const canvasCtx = canvasElement.getContext('2d');

        const promptMessageElement = document.getElementById('promptMessage');

        const pitchAngleElement = document.getElementById('pitchAngle');
        const yawAngleElement = document.getElementById('yawAngle');
        const rollAngleElement = document.getElementById('rollAngle');
        const gazeDirectionElement = document.getElementById('gazeDirection');


        function onResults(results) {
            //console.log(results);
            canvasCtx.save();
            canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            canvasCtx.drawImage(
                results.image, 0, 0, canvasElement.width, canvasElement.height);
            if (results.multiFaceLandmarks) {
                for (const landmarks of results.multiFaceLandmarks) {
                    drawConnectors(canvasCtx, landmarks, FACEMESH_TESSELATION,
                                    {color: '#C0C0C070', lineWidth: 1});
                    drawConnectors(canvasCtx, landmarks, FACEMESH_RIGHT_EYE, {color: '#FF3030'});
                    drawConnectors(canvasCtx, landmarks, FACEMESH_RIGHT_EYEBROW, {color: '#FF3030'});
                    drawConnectors(canvasCtx, landmarks, FACEMESH_RIGHT_IRIS, {color: '#FF3030'});
                    drawConnectors(canvasCtx, landmarks, FACEMESH_LEFT_EYE, {color: '#30FF30'});
                    drawConnectors(canvasCtx, landmarks, FACEMESH_LEFT_EYEBROW, {color: '#30FF30'});
                    drawConnectors(canvasCtx, landmarks, FACEMESH_LEFT_IRIS, {color: '#30FF30'});
                    drawConnectors(canvasCtx, landmarks, FACEMESH_FACE_OVAL, {color: '#E0E0E0'});
                    drawConnectors(canvasCtx, landmarks, FACEMESH_LIPS, {color: '#E0E0E0'});

                    //const landmarks = landmarksArray[0];
                    const noseTip = landmarks[4];
                    const noseBridge = landmarks[6];
                    const rightEar = landmarks[137];  // Adjust the index based on your model
                    const leftEar = landmarks[366];   // Adjust the index based on your model
                    const chin = landmarks[152];
                    
                    if (noseTip) {
                        canvasCtx.fillStyle = 'red';
                        canvasCtx.beginPath();
                        canvasCtx.arc(noseTip.x * canvasElement.width, noseTip.y * canvasElement.height, 5, 0, 2 * Math.PI);
                        canvasCtx.fill();
                    }

                    if (noseTip) {
                        canvasCtx.fillStyle = 'red';
                        canvasCtx.beginPath();
                        canvasCtx.arc(noseTip.x * canvasElement.width, noseTip.y * canvasElement.height, 5, 0, 2 * Math.PI);
                        canvasCtx.fill();
                    }

                    if (noseBridge) {
                        canvasCtx.fillStyle = 'green';
                        canvasCtx.beginPath();
                        canvasCtx.arc(noseBridge.x * canvasElement.width, noseBridge.y * canvasElement.height, 5, 0, 2 * Math.PI);
                        canvasCtx.fill();
                    }
                    if (rightEar) {
                        canvasCtx.fillStyle = 'red';
                        canvasCtx.beginPath();
                        canvasCtx.arc(rightEar.x * canvasElement.width, rightEar.y * canvasElement.height, 5, 0, 2 * Math.PI);
                        canvasCtx.fill();
                    }

                    if (leftEar) {
                        canvasCtx.fillStyle = 'green';
                        canvasCtx.beginPath();
                        canvasCtx.arc(leftEar.x * canvasElement.width, leftEar.y * canvasElement.height, 5, 0, 2 * Math.PI);
                        canvasCtx.fill();
                    }

                    if (chin) {
                        canvasCtx.fillStyle = 'blue';
                        canvasCtx.beginPath();
                        canvasCtx.arc(chin.x * canvasElement.width, chin.y * canvasElement.height, 5, 0, 2 * Math.PI);
                        canvasCtx.fill();
                    }

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

                    if (yawAngle > 10) {
                        gazeDirection = "left";
                        promptMessage = "Please position yourself at the center and face forward towards the camera.";
                    } else if (yawAngle < -10) {
                        gazeDirection = "right";
                        promptMessage = "Please position yourself at the center and face forward towards the camera.";
                    }

                    if (pitchAngle > 10) {
                        gazeDirection = "downward";
                        promptMessage = "Please position yourself at the center and face forward towards the camera.";
                    } else if (pitchAngle < -10) {
                        gazeDirection = "upward";
                        promptMessage = "Please position yourself at the center and face forward towards the camera.";
                    }

                    // Display the angles
                    promptMessageElement.innerHTML = `Prompt Message: ${promptMessage}`;
                    pitchAngleElement.innerHTML = `Pitch Angle: ${pitchAngle.toFixed(2)}`;
                    yawAngleElement.innerHTML = `Yaw Angle: ${yawAngle.toFixed(2)}`;
                    rollAngleElement.innerHTML = `Roll Angle: ${rollAngle.toFixed(2)}`;
                    gazeDirectionElement.innerHTML = `Gaze Direction: ${gazeDirection}`;
                }
            }
            //console.log(results.multiFaceLandmarks[4]);

            canvasCtx.restore();
        }

        function updateNoseAngle(angleNoseTipBridge, angleRightEarLeftEar) {
            noseAngleDisplay.innerHTML = `Nose Tip to Nose Bridge Angle: ${angleNoseTipBridge.toFixed(2)} degrees<br>`;
            noseAngleDisplay.innerHTML += `Right Ear to Left Ear Angle: ${angleRightEarLeftEar.toFixed(2)} degrees`;
        }
        
        const faceMesh = new FaceMesh({locateFile: (file) => {
            return `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`;
        }});

        faceMesh.setOptions({
            maxNumFaces: 1,
            refineLandmarks: true,
            minDetectionConfidence: 0.5,
            minTrackingConfidence: 0.5
        });
        faceMesh.onResults(onResults);
        
        const camera = new Camera(videoElement, {onFrame: async () => {
            await faceMesh.send({image: videoElement});
            },
            width: 1280,
            height: 720
        });
        camera.start();
    </script>
</body>
</html>