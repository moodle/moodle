function scorm_openpopup(url,name,options,width,height) {
    windowobj = window.open(url,name,options);
    if (!windowobj) {
        return;
    }
    if ((width==100) && (height==100)) {
        // Fullscreen
        windowobj.moveTo(0,0);
    }
    if (width<=100) {
        width = Math.round(screen.availWidth * width / 100);
    }
    if (height<=100) {
        height = Math.round(screen.availHeight * height / 100);
    }
    windowobj.resizeTo(width,height);
    windowobj.focus();
    return windowobj;
}
