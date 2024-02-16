$(document).ready(function () {
    let susCounter = 0;
    let duration = 0;
    let intervalId;
    let speech_detected = false;
    let evidence_name_type;
    let allowedMic;
    let filename;
    let mediaRecorder;
    let chunks = [];
  
    navigator.mediaDevices.getUserMedia({ audio: true })
      .then(function(stream) {
        allowedMic = true;
        mediaRecorder = new MediaRecorder(stream);
        const audioContext = new AudioContext();
        const analyser = audioContext.createAnalyser();
        const microphone = audioContext.createMediaStreamSource(stream);
  
        analyser.fftSize = 256;
  
        microphone.connect(analyser);
  
        const feedbackGain = audioContext.createGain();
        feedbackGain.gain.value = 0; // Initially muted
  
        analyser.connect(feedbackGain);
        feedbackGain.connect(audioContext.destination);

        // Speech Recognition
            window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            recognition.interimResults = true;

            let transcript = '';

            recognition.addEventListener('result', e => {
                noiseDetected('speech_detected');
                speech_detected = true;
                console.log('Speech detected: ', speech_detected);
                const interimTranscript = Array.from(e.results)
                    .map(result => result[0].transcript)
                    .join('');

                if (e.results[0].isFinal) {
                    transcript += interimTranscript;
                    console.log('Final Transcript:', transcript);
                    // Reset transcript for new speech recognition
                    transcript = '';
                } else {
                    console.log('Interim Transcript:', interimTranscript);
                }
            });

            recognition.addEventListener('end', () => {
                noiseDetected('send_the_activity');
                speech_detected = false;
                console.log('Speech detected: ', speech_detected);
                recognition.start();
            });

            recognition.start();
  
        // Loud noise
            setInterval(() => {
                const dataArray = new Uint8Array(analyser.frequencyBinCount);
                analyser.getByteFrequencyData(dataArray);
        
                const average = dataArray.reduce((a, b) => a + b, 0) / dataArray.length;
                const volume = Math.round(average);
        
                if (!speech_detected){
                    if (volume > 80){
                        noiseDetected('loud_noise');
                    }
                    else{
                        feedbackGain.gain.value = 0;
                        noiseDetected('send_the_activity');
                    }
                }
            }, 100); // Update display every 100 milliseconds

        // Collect recorded data
        mediaRecorder.ondataavailable = function(e) {
            chunks.push(e.data);
        };

        // Save recorded audio
        mediaRecorder.onstop = function(e) {
            if (duration >= 1000){
                // Create a Blob object from e.data
                const blob = new Blob(chunks, { 'type' : 'audio/wav' }); // Adjust the MIME type as necessary
                // Generate a unique filename (you can customize this logic)
                const { timestamp, milliseconds } = generateTimestamp();
                filename = 'EVD_USER_' + jsdata.userid + '_QUIZ_' + jsdata.quizid +'_ATTEMPT_' + jsdata.quizattempt + '_' + timestamp.replace(/[/:, ]/g, '') + '_' + milliseconds + '_' + evidence_name_type +'_.wav'; // Custom filename for audio

                // Send blob and filename to server
                const formData = new FormData();
                formData.append('audio', blob, filename);

                fetch(jsdata.wwwroot + '/local/auto_proctor/proctor_tools/microphone_monitoring/save_mic_capture.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Audio saved successfully:', response);
                    sendActivityRecord();
                })
                .catch(error => {
                    console.error('Error saving audio:', error);
                });
            }
            chunks = [];
        };

      })
      .catch(function(err) {
        allowedMic = false;
        sendActivityRecord();
        console.error('Error capturing audio:', err);

        evidence_name_type = 'microphone_permission_denied';
        sendActivityRecord();
        if (jsdata.strict_mode_activated == 1){
            console.log('microphone denied must redirect to review attempt quiz page');
                window.location.href = jsdata.wwwroot + '/mod/quiz/view.php?id=' + jsdata.cmid;
        }
    });

    function noiseDetected(activity_type){
        if (susCounter === 0){
            if (activity_type === "speech_detected" || activity_type === "loud_noise"){
                // Play the feedback in here
                mediaRecorder.start();
                evidence_name_type = activity_type;
                console.log('start recording');
                const intervalId = startTimer();
                //feedbackGain.gain.value = 1;

                susCounter++;
            }
        }
        else if (susCounter === 1 && activity_type === "send_the_activity"){
            probSusCounter = 0;
            susCounter = 0;
            stopTimer();
            mediaRecorder.stop();
            console.log('stop recording');
        }
    }


    // Function to update the timer display
    function updateTimer(milliseconds) {
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

    // Save in database
    function sendActivityRecord() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', jsdata.wwwroot + '/local/auto_proctor/proctor_tools/microphone_monitoring/save_mic_activity.php', true); // Replace with the actual path
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        // if (!allowedMic){
        //     evidence_name_type = 'permission_denied';
        // }

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

    navigator.permissions.query({name: 'microphone'}).then(function(permissionStatus) {
        console.log('microphone permission state is ', permissionStatus.state);
        permissionStatus.onchange = function() {
            console.log('microphone permission state has changed to ', this.state);
            if (this.state = 'denied'){
                evidence_name_type = 'microphone_permission_denied_during_quiz';
                sendActivityRecord();

                // Check if strict mode was activated
                if (jsdata.strict_mode_activated == 1){
                    console.log('microphone denied must redirect to review attempt quiz page');
                    window.location.href = jsdata.wwwroot + '/mod/quiz/view.php?id=' + jsdata.cmid;
                }
            }
        };
    });
});