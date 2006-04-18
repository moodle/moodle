/*
* Dummy SCORM API
*/
 
function GenericAPIAdaptor(){
        this.LMSInitialize = LMSInitializeMethod;
        this.LMSGetValue = LMSGetValueMethod;
        this.LMSSetValue = LMSSetValueMethod;
        this.LMSCommit = LMSCommitMethod;
        this.LMSFinish = LMSFinishMethod;
        this.LMSGetLastError = LMSGetLastErrorMethod;
        this.LMSGetErrorString = LMSGetErrorStringMethod;
        this.LMSGetDiagnostic = LMSGetDiagnosticMethod;
}
/*
* LMSInitialize.
*/
function LMSInitializeMethod(parameter){return "true";}
/*
* LMSFinish.
*/
function LMSFinishMethod(parameter){return "true";}
/*
* LMSCommit.
*/
function LMSCommitMethod(parameter){return "true";}
/*
* LMSGetValue.
*/
function LMSGetValueMethod(element){return "";}
/*
* LMSSetValue.
*/
function LMSSetValueMethod(element, value){return "true";}
/*
* LMSGetLastErrorString
*/
function LMSGetErrorStringMethod(errorCode){return "No error";}
/*
* LMSGetLastError
*/
function LMSGetLastErrorMethod(){return "0";}
/*
* LMSGetDiagnostic
*/
function LMSGetDiagnosticMethod(errorCode){return "No error. No errors were encountered. Successful API call.";}
 
var API = new GenericAPIAdaptor;