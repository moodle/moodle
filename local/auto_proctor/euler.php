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
<div id="camera-selection">
  <select id="camera-selector"></select>
  <video id="camera-preview" autoplay playsinline></video>
  <button id="confirm-camera">Select Camera</button>
  </div>


    <div class="container">
        <!-- <video class="input_video"></video> -->
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

        navigator.mediaDevices.enumerateDevices()
        .then(function(devices) {
            devices.forEach(function(device) {
                if (device.kind === 'videoinput') {

                    console.log('avail cam: ', device.deviceId);
                    //d89b7d58a7f1e6abeec4eb35a2ced3563f221dc27e5fe8043485ab8b5c2101e4
                    

                }
            });
        })


        const getUserMediaConstraints = (deviceId) => {
            return {
                video: {
                deviceId: deviceId ? { exact: deviceId } : undefined,
                facingMode: 'user', // Set facingMode if preferred
                },
            };
        };

        const cameraSelector = document.getElementById('camera-selector');
        const cameraPreview = document.getElementById('camera-preview');
        const confirmButton = document.getElementById('confirm-camera');


            navigator.mediaDevices.enumerateDevices()
            .then(devices => {
                const videoDevices = devices.filter(device => device.kind === 'videoinput');

                videoDevices.forEach(device => {
                const option = document.createElement('option');
                option.value = device.deviceId;
                option.textContent = device.label || `Camera ${videoDevices.indexOf(device) + 1}`;
                cameraSelector.appendChild(option);
                });

                // // Set initial camera (optional)
                // cameraSelector.value = '657b358df7650e0d57bb4a73e9f2b1b7a4f1b17c9c7ff0d9c83d6a04981626ac';
            })
            .catch(error => {
                console.error('Error listing devices:', error);
            });

            var deviceId = 'd89b7d58a7f1e6abeec4eb35a2ced3563f221dc27e5fe8043485ab8b5c2101e4';

            window.onload = async function() {
                const constraints = getUserMediaConstraints(deviceId);

                try {
                    const stream = await navigator.mediaDevices.getUserMedia(constraints);
                    
                    // Create video element dynamically
                    const inputVideoElement = document.createElement('video');
                    inputVideoElement.className = 'input_video'; // Add class name
                    inputVideoElement.srcObject = stream;
                    inputVideoElement.autoplay = true; // Autoplay
                    inputVideoElement.playsinline = true; // Ensure playsinline for mobile browsers
                    document.body.appendChild(inputVideoElement); // Append to the document body

                    // When the video stream is loaded, dynamically set the canvas size to match the video stream
                    inputVideoElement.addEventListener('loadedmetadata', () => {
                        canvasElement.width = inputVideoElement.videoWidth;
                        canvasElement.height = inputVideoElement.videoHeight;
                    });

                    // Apply FaceMesh to the selected camera
                    const onFrame = async () => {
                        await faceMesh.send({ image: inputVideoElement });
                        requestAnimationFrame(onFrame);
                    };
                    // Start sending frames to FaceMesh
                    onFrame();
                } catch (error) {
                    console.error('Error accessing camera:', error);
                }
            }

        // FaceMesh setup (same as before)
        const faceMesh = new FaceMesh({ locateFile: (file) => {
            return `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`;
        } });
        faceMesh.setOptions({
                maxNumFaces: 1,
                refineLandmarks: true,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5
        });
        faceMesh.onResults(onResults);

    </script>
</body>
</html>