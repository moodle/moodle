$(document).ready(function () {
    //src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"

    let screenShared = null;
    let screenStream = null;
    let videoElement;
    let stopsSharing = false;

                            //document.addEventListener('visibilitychange', handleVisibilityChange);

                            // function handleVisibilityChange() {
                            //     if (document.visibilityState === 'hidden' && !document.hasFocus()) {
                            //         if (screenShared && !stopsSharing) {
                            //             // Send an AJAX request to your server to indicate screen sharing                                     
                            //             captureAndSaveScreen();
                            //         }
                            //     }
                            // }
                            
    function startScreenSharing() {
        // Check if user device has mutiple monitor
        if (window.screen.isExtended){
            console.log('Multiple screen');
        }
        else{
            console.log('Single screen');
        }
        
        navigator.mediaDevices.getDisplayMedia({ video: true })
            .then(stream => {
            videoElement = document.createElement('video');
            videoElement.srcObject = stream;
            videoElement.autoplay = true;

            screenStream = stream;
            screenShared = true;

            screenStream.getVideoTracks()[0].onended = () => {
                stopsSharing = true;
                screenShared = false;
                console.log('Screen sharing stopped by the student.');
                // Send an AJAX request to your server to indicate screen sharing stopped
                //sendScreenSharingStatus(2); // stops sharing
                captureAndSaveScreen('stops_sharing_screen');
            };

            captureAndSaveScreen('shared_screen'); // Capture the shared screen
            //sendScreenSharingStatus(1); // shared screen
            console.log('Consent:', 1);
        })
        .catch(error => {
            console.error('Error starting screen sharing:', error);
            screenShared =  false;
            // Send an AJAX request to your server to indicate screen sharing error
            //sendScreenSharingStatus(0); // 0 indicates screen sharing stopped
            captureAndSaveScreen('did_not_share_screen');
        });
                                    
            //document.addEventListener('visibilitychange', handleVisibilityChange);
            window.addEventListener('focus', handleTabSwitch);
            window.addEventListener('blur', handleTabSwitch);
    }
                            
    function handleTabSwitch() {
        if (document.hasFocus()) {
            console.log('Tab switched back to focus');
        } 
        else {
            console.log('Tab switched');
            if (screenShared === true && stopsSharing === false) {
                // If user shared screen and continously sharing it
                // Capture and save the shared screen when the tab is switched
                captureAndSaveScreen('tab_switch');
            }
            else if(screenShared === false || stopsSharing === true){
                // If user did not share screen or when user shared screen but stop it
                // Will not capture but will still be reported in the acitivity table
                captureAndSaveScreen('tab_switch_screen_not_shared');
            }                                        
        }
    }

    function sendScreenSharingStatus(screen_activity, filename, activity_type) {
        // Send an AJAX request to your server to record screen sharing status
        console.log('Sending screen_activity:', screen_activity);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', jsdata.wwwroot + '/local/auto_proctor/proctor_tools/tab_monitoring/save_screen_activity.php', true); // Replace with the actual path
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        // ==== DEBUGGING =====
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    console.log('POST request successful');
                        // If strict mode was activated
                        if (jsdata.strict_mode_activated == 1){
                            // If student stops sharing screen or did not share screen then redirect to quiz attempt review page
                            if (activity_type == 'stops_sharing_screen' || activity_type == 'did_not_share_screen'){
                                console.log('stops sharing must redirect to review attempt quiz page');
                                window.location.href = jsdata.wwwroot + '/mod/quiz/view.php?id=' + jsdata.cmid;
                            }
                        }
                } else {
                    console.error('POST request failed with status: ' + xhr.status);
                    // Handle the error or provide feedback to the user
                }
            }
        };
        xhr.send('screen_activity=' + screen_activity + '&userid=' + jsdata.userid + '&quizid=' + jsdata.quizid + '&quizattempt=' + jsdata.quizattempt + '&filename=' + encodeURIComponent(filename) + '&activity_type=' + activity_type);
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
                            
    function captureAndSaveScreen(evidence_name_type) {
        if (evidence_name_type !== 'tab_switch_screen_not_shared' && evidence_name_type !== 'did_not_share_screen' && evidence_name_type !== 'stops_sharing_screen'){
            setTimeout(() => {
            const canvas = document.createElement('canvas');
            canvas.width = videoElement.videoWidth;
            canvas.height = videoElement.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
                                
            const { timestamp, milliseconds } = generateTimestamp();
            const filename = 'EVD_USER_' + jsdata.userid + '_' + timestamp.replace(/[/:, ]/g, '') + '_' + milliseconds + '_' + evidence_name_type + '.png'; // Custom filename with evidenceType
                                
            const dataUrl = canvas.toDataURL('image/png');
                                
            fetch(jsdata.wwwroot + '/local/auto_proctor/proctor_tools/tab_monitoring/save_screen_capture.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'dataUri=' + encodeURIComponent(dataUrl) + '&filename=' + encodeURIComponent(filename),
            })
            .then(response => response.json())
                .then(data => {
                    console.log('Screen captured and saved as: ' + data.filename);
                    sendScreenSharingStatus(4, filename, evidence_name_type);
                })
                .catch(error => {
                    console.error('Error saving screen capture:', error);
                });
            }, 500);
        }
        else{
            sendScreenSharingStatus(1, 0,evidence_name_type);
        }
    }
                                                                       
    // Start screen sharing when the script is loaded
    startScreenSharing();
});