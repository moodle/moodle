function scorm_openpopup(url,name,options,width,height) {
    if (width<=100) {
        width = Math.round(screen.availWidth * width / 100);
    }
    if (height<=100) {
        height = Math.round(screen.availHeight * height / 100);
    }
    options += ",width="+width+",height="+height;

    windowobj = window.open(url,name,options);
    if (!windowobj) {
        return;
    }
    if ((width==100) && (height==100)) {
        // Fullscreen
        windowobj.moveTo(0,0);
    }
    windowobj.focus();
    return windowobj;
}
