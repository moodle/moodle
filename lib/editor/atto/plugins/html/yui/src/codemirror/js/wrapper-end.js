// Actually define CodeMirror in our namespace.
Y.namespace('M.atto_html').CodeMirror = CodeMirror;

// Restore the original CodeMirror in case one existed.
window.CodeMirror = _codeMirror;
