const injectSdkScript = async() => {
    // Inject CDN SDK Moodle Script
    let scriptLoaded = false;
    let sdkScript = document.createElement('script');
    sdkScript.type = "module";
    sdkScript.onload = () => {
        // eslint-disable-next-line no-console
        console.log('moodle script loaded!');
        scriptLoaded = true;
    };
    sdkScript.src = "https://unpkg.com/@honorlock/integration-sdk-js-moodle";
    document.getElementsByTagName('head')[0].appendChild(sdkScript);

    const injectedAt = new Date();
    return new Promise(function(resolve, reject) {
        const poll = ()=> {
            // 10s timeout
            if (new Date().getTime() - injectedAt.getTime() > 10000) {
                return reject('injectSdkScript timed out');
            }

            if (scriptLoaded) {
                // eslint-disable-next-line no-console
                console.log("honorlockproctoring::injectSdkScript sdk injected");
                return resolve();
            }

            return setTimeout(poll, 100);
        };

        poll();
    });
};

export const logDebug = (args) => {
    // eslint-disable-next-line no-console
    console.log('logDebug:', args);
};

export const viewQuiz = async() => {
    // eslint-disable-next-line no-console
    console.log("honorlockproctoring::viewQuiz plugin func");
    await injectSdkScript();
    window.MoodleFunctions.viewQuiz();
};

export const examAuth = async(args) => {
    // eslint-disable-next-line no-console
    console.log("honorlockproctoring::examAuth plugin func");
    await injectSdkScript();
    window.MoodleFunctions.examAuth(args);
};

export const takeQuiz = async() => {
    // eslint-disable-next-line no-console
    console.log("honorlockproctoring::takeQuiz plugin func");
    await injectSdkScript();
    window.MoodleFunctions.takeQuiz();
};

export const quizSummary = async() => {
    // eslint-disable-next-line no-console
    console.log("honorlockproctoring::quizSummary plugin func");
    await injectSdkScript();
    window.MoodleFunctions.quizSummary();
};
