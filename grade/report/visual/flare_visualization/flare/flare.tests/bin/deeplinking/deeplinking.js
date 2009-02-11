DeepLinkingUtils = {
	addEvent: function(elm, evType, fn, useCapture) {
		useCapture = useCapture || false;
		if (elm.addEventListener) {
			elm.addEventListener(evType, fn, useCapture);
			return true;
		}
		else if (elm.attachEvent) {
			var r = elm.attachEvent('on' + evType, fn);
			return r;
		}
		else {
			elm['on' + evType] = fn;
		}
	}
}

DeepLinking = (function() {
	// type of browser
    var browser = {
        ie: false, 
        firefox: false, 
        safari: false, 
        opera: false, 
        version: -1
    };

    // Default app state URL to use when no fragment ID present
    var defaultHash = '';

    // Last-known app state URL
    var currentHref = document.location.href;

    // Initial URL (used only by IE)
    var initialHref = document.location.href;

    // Initial URL (used only by IE)
    var initialHash = document.location.hash;

    // History frame source URL prefix (used only by IE)
    var historyFrameSourcePrefix = 'deeplinking/historyFrame.html?';

    // History maintenance (used only by Safari)
    var currentHistoryLength = -1;

    var historyHash = [];

    var initialState = createState(initialHref, initialHref + '#' + initialHash, initialHash);

    var backStack = [];
    var forwardStack = [];

	//UserAgent detection
    var useragent = navigator.userAgent.toLowerCase();

    if (useragent.indexOf("opera") != -1) {
        browser.opera = true;
    } else if (useragent.indexOf("msie") != -1) {
        browser.ie = true;
        browser.version = parseFloat(useragent.substring(useragent.indexOf('msie') + 4));
    } else if (useragent.indexOf("safari") != -1) {
        browser.safari = true;
        browser.version = parseFloat(useragent.substring(useragent.indexOf('safari') + 7));
    } else if (useragent.indexOf("gecko") != -1) {
        browser.firefox = true;
    }

    // Accessor functions for obtaining specific elements of the page.
    function getHistoryFrame()
    {
        return document.getElementById('ie_historyFrame');
    }

    function getAnchorElement()
    {
        return document.getElementById('firefox_anchorDiv');
    }

    function getFormElement()
    {
        return document.getElementById('safari_formDiv');
    }

	function getRememberElement()
	{
		return document.getElementById("safari_remember_field");
	}

    /* Get the Flash player object for performing ExternalInterface callbacks. */
    function getPlayer() {
        var player = null; /* AJH, needed?  = document.getElementById(getPlayerId()); */
        
        if (player == null) {
            player = document.getElementsByTagName('object')[0];
        }
        
        if (player == null || player.object == null) {
            player = document.getElementsByTagName('embed')[0];
        }

        return player;
    }

    /* Get the current location hash excluding the '#' symbol. */
    function getHash() {
       // It would be nice if we could use document.location.hash here,
       // but it's faulty sometimes.
       var idx = document.location.href.indexOf('#');
       return (idx >= 0) ? document.location.href.substr(idx+1) : '';
    }

    /* Get the current location hash excluding the '#' symbol. */
    function setHash(hash) {
       // It would be nice if we could use document.location.hash here,
       // but it's faulty sometimes.
       if (hash == '') hash = '#'
       document.location.hash = hash;
    }

    function createState(baseUrl, newUrl, flexAppUrl) {
        return { 'baseUrl': baseUrl, 'newUrl': newUrl, 'flexAppUrl': flexAppUrl, 'title': null };
    }

    /* Add a history entry to the browser.
     *   baseUrl: the portion of the location prior to the '#'
     *   newUrl: the entire new URL, including '#' and following fragment
     *   flexAppUrl: the portion of the location following the '#' only
     */
    function addHistoryEntry(baseUrl, newUrl, flexAppUrl, copyToAddressBar) {

        //delete all the history entries
        forwardStack = [];

        if (browser.ie) {
            //Check to see if we are being asked to do a navigate for the first
            //history entry, and if so ignore, because it's coming from the creation
            //of the history iframe
            if (flexAppUrl == defaultHash && document.location.href == initialHref && _ie_firstload) {
                currentHref = initialHref;
                return;
            }
            if ((!flexAppUrl || flexAppUrl == defaultHash) && _ie_firstload) {
                newUrl = baseUrl + '#' + defaultHash;
                flexAppUrl = defaultHash;
            } else {
                // for IE, tell the history frame to go somewhere without a '#'
                // in order to get this entry into the browser history.
                getHistoryFrame().src = historyFrameSourcePrefix + flexAppUrl;
            }
            if (copyToAddressBar) {
                setHash(flexAppUrl);
                //document.location.href = newUrl;
            }
        } else {

            //ADR
            if (backStack.length == 0 && initialState.flexAppUrl == flexAppUrl) {
                initialState = createState(baseUrl, newUrl, flexAppUrl);
            } else if(backStack.length > 0 && backStack[backStack.length - 1].flexAppUrl == flexAppUrl) {
                backStack[backStack.length - 1] = createState(baseUrl, newUrl, flexAppUrl);
            }

            if (browser.safari) {
                // for Safari, submit a form whose action points to the desired URL
                if (browser.version <= 419.3) {
                    var file = window.location.pathname.toString();
                    file = file.substring(file.lastIndexOf("/")+1);
                    getFormElement().innerHTML = '<form name="historyForm" action="'+file+'#' + flexAppUrl + '" method="GET"></form>';
                    //get the current elements and add them to the form
                    var qs = window.location.search.substring(1);
                    var qs_arr = qs.split("&");
                    for (var i = 0; i < qs_arr.length; i++) {
                        var tmp = qs_arr[i].split("=");
                        var elem = document.createElement("input");
                        elem.type = "hidden";
                        elem.name = tmp[0];
                        elem.value = tmp[1];
                        document.forms.historyForm.appendChild(elem);
                    }
                    document.forms.historyForm.submit();
                } else {
                    top.location.hash = flexAppUrl;
                }
                // We also have to maintain the history by hand for Safari
                historyHash[history.length] = flexAppUrl;
                _storeStates();
            } else {
                // Otherwise, write an anchor into the page and tell the browser to go there
                addAnchor(flexAppUrl);
                if (copyToAddressBar) {
                    setHash(flexAppUrl);
                }
           }
        }
        backStack.push(createState(baseUrl, newUrl, flexAppUrl));
    }

    function _storeStates() {
        if (browser.safari) {
            getRememberElement().value = historyHash.join(",");
        }
    }

    function handleBackButton() {
        //The "current" page is always at the top of the history stack.
        var current = backStack.pop();
        if (!current) { return; }
        var last = backStack[backStack.length - 1];
        if (!last && backStack.length == 0){
            last = initialState;
        }
        forwardStack.push(current);
    }

    function handleForwardButton() {
        //summary: private method. Do not call this directly.

        var last = forwardStack.pop();
        if (!last) { return; }
        backStack.push(last);
    }

    function handleArbitraryUrl() {
        //delete all the history entries
        forwardStack = [];
    }

    /* Called periodically to poll to see if we need to detect navigation that has occurred */
    function checkForUrlChange() {

        if (browser.ie) {
            if (currentHref != document.location.href && currentHref + '#' != document.location.href) {
                //This occurs when the user has navigated to a specific URL
                //within the app, and didn't use browser back/forward
                //IE seems to have a bug where it stops updating the URL it
                //shows the end-user at this point, but programatically it
                //appears to be correct.  Do a full app reload to get around
                //this issue.
                if (browser.version < 7) {
                    currentHref = document.location.href;
                    document.location.reload();
                } else {
                    //getHistoryFrame().src = historyFrameSourcePrefix + getHash();
                }
            }
		}

		if (browser.safari) {
            // For Safari, we have to check to see if history.length changed.
            if (currentHistoryLength >= 0 && history.length != currentHistoryLength) {
				//alert("did change: " + history.length + ", " + historyHash.length + "|" + historyHash[history.length] + "|>" + historyHash.join("|"));
                // If it did change, then we have to look the old state up
                // in our hand-maintained array since document.location.hash
                // won't have changed, then call back into BrowserManager.
                currentHistoryLength = history.length;
                var flexAppUrl = historyHash[currentHistoryLength];
                if (flexAppUrl == '') {
                    //flexAppUrl = defaultHash;
                }
                getPlayer().browserURLChange(flexAppUrl);
                _storeStates();
            }
		}
		if (browser.firefox) {
            if (currentHref != document.location.href) {
                var bsl = backStack.length;

                var urlActions = {
                    back: false, 
                    forward: false, 
                    set: false
                }

                if ((window.location.hash == initialHash || window.location.href == initialHref) && (bsl == 1)) {
                    urlActions.back = true;
                    // FIXME: could this ever be a forward button?
                    // we can't clear it because we still need to check for forwards. Ugg.
                    // clearInterval(this.locationTimer);
                    handleBackButton();
                }
                
                // first check to see if we could have gone forward. We always halt on
                // a no-hash item.
                if (forwardStack.length > 0) {
                    if (forwardStack[forwardStack.length-1].flexAppUrl == getHash()) {
                        urlActions.forward = true;
                        handleForwardButton();
                    }
                }

                // ok, that didn't work, try someplace back in the history stack
                if ((bsl >= 2) && (backStack[bsl - 2])) {
                    if (backStack[bsl - 2].flexAppUrl == getHash()) {
                        urlActions.back = true;
                        handleBackButton();
                    }
                }
                
                if (!urlActions.back && !urlActions.forward) {
                    var foundInStacks = {
                        back: -1, 
                        forward: -1
                    }

                    for (var i = 0; i < backStack.length; i++) {
                        if (backStack[i].flexAppUrl == getHash() && i != (bsl - 2)) {
                            arbitraryUrl = true;
                            foundInStacks.back = i;
                        }
                    }
                    for (var i = 0; i < forwardStack.length; i++) {
                        if (forwardStack[i].flexAppUrl == getHash() && i != (bsl - 2)) {
                            arbitraryUrl = true;
                            foundInStacks.forward = i;
                        }
                    }
                    handleArbitraryUrl();
                }

                // Firefox changed; do a callback into BrowserManager to tell it.
                currentHref = document.location.href;
                var flexAppUrl = getHash();
                if (flexAppUrl == '') {
                    //flexAppUrl = defaultHash;
                }
                getPlayer().browserURLChange(flexAppUrl);
            }
        }
        //setTimeout(checkForUrlChange, 50);
    }

    /* Write an anchor into the page to legitimize it as a URL for Firefox et al. */
    function addAnchor(flexAppUrl)
    {
       if (document.getElementsByName(flexAppUrl).length == 0) {
           getAnchorElement().innerHTML += "<a name='" + flexAppUrl + "'>" + flexAppUrl + "</a>";
       }
    }

    var _initialize = function () {
		if (browser.ie)
		{
            var scripts = document.getElementsByTagName('script');
            for (var i = 0, s; s = scripts[i]; i++) {
                if (s.src.indexOf("deeplinking.js") > -1) {
                    var iframe_location = (new String(s.src)).replace("deeplinking.js", "historyFrame.html");
                }
            }
			historyFrameSourcePrefix = iframe_location + "?";
			var src = historyFrameSourcePrefix;

            var iframe = document.createElement("iframe");
            iframe.id = 'ie_historyFrame';
            iframe.name = 'ie_historyFrame';
            //iframe.src = historyFrameSourcePrefix;
            document.body.appendChild(iframe);
		}

		if (browser.safari)
		{
			var rememberDiv = document.createElement("div");
			rememberDiv.id = 'asafari_rememberDiv';
			document.body.appendChild(rememberDiv);
			rememberDiv.innerHTML = '<input type="text" id="safari_remember_field" style="width: 500px;">';

			var formDiv = document.createElement("div");
			formDiv.id = 'safari_formDiv';
			document.body.appendChild(formDiv);

            var reloader_content = document.createElement('div');
            reloader_content.id = 'safarireloader';
            var scripts = document.getElementsByTagName('script');
            for (var i = 0, s; s = scripts[i]; i++) {
                if (s.src.indexOf("deeplinking.js") > -1) {
                    html = (new String(s.src)).replace(".js", ".html");
                }
            }
            reloader_content.innerHTML = '<iframe id="safarireloader-iframe" src="about:blank" frameborder="no" scrolling="no"></iframe>';
            document.body.appendChild(reloader_content);
            reloader_content.style.position = 'absolute';
            reloader_content.style.left = reloader_content.style.top = '-9999px';
            iframe = reloader_content.getElementsByTagName('iframe')[0];

			if (document.getElementById("safari_remember_field").value != "" ) {
				historyHash = document.getElementById("safari_remember_field").value.split(",");
			}

		}

		if (browser.firefox)
		{
			var anchorDiv = document.createElement("div");
			anchorDiv.id = 'firefox_anchorDiv';
			document.body.appendChild(anchorDiv);
		}
        
        //setTimeout(checkForUrlChange, 50);
    }

    return {
		historyHash: historyHash, 
        backStack: function() { return backStack; }, 
        forwardStack: function() { return forwardStack }, 
        getPlayer: getPlayer, 
        initialize: function(src) {
            _initialize(src);
        }, 
        setURL: function(url) {
            document.location.href = url;
        }, 
        getURL: function() {
            return document.location.href;
        }, 
        getTitle: function() {
            return document.title;
        }, 
        setTitle: function(title) {
            try {
                backStack[backStack.length - 1].title = title;
            } catch(e) { }
            
            document.title = title;
        }, 
        setDefaultURL: function(def)
        {
            defaultHash = def;
            def = getHash();
            //trailing ? is important else an extra frame gets added to the history
            //when navigating back to the first page.  Alternatively could check
            //in history frame navigation to compare # and ?.
			if (browser.ie)
			{
                _ie_firstload = true;
                getHistoryFrame().src = historyFrameSourcePrefix + def;
                window.location.replace("#" + def);
                setInterval(checkForUrlChange, 50);
			}

			if (browser.safari)
			{
                currentHistoryLength = history.length;
                if (historyHash.length == 0) {
                    historyHash[currentHistoryLength] = defaultHash;
                    var newloc = "#" + def;
                    window.location.replace(newloc);
                } else {
                    //alert(historyHash[historyHash.length-1]);
                }
                //setHash(def);
                setInterval(checkForUrlChange, 50);
			}
			
			
			if (browser.firefox || browser.opera)
			{
                var reg = new RegExp("#" + def + "$");
                if (window.location.toString().match(reg)) {
                } else {
                    var newloc ="#" + def;
                    window.location.replace(newloc);
                }
                setInterval(checkForUrlChange, 50);
                //setHash(def);
            }
        }, 

        /* Set the current browser URL; called from inside BrowserManager to propagate
         * the application state out to the container.
         */
        setBrowserURL: function(flexAppUrl, copyToAddressBar) {
           //fromIframe = fromIframe || false;
           //fromFlex = fromFlex || false;
           //alert("setBrowserURL: " + flexAppUrl);
           //flexAppUrl = (flexAppUrl == "") ? defaultHash : flexAppUrl ;

           var pos = document.location.href.indexOf('#');
           var baseUrl = pos != -1 ? document.location.href.substr(0, pos) : document.location.href;
           var newUrl = baseUrl + '#' + flexAppUrl;

           if (document.location.href != newUrl && document.location.href + '#' != newUrl) {
               currentHref = newUrl;
               addHistoryEntry(baseUrl, newUrl, flexAppUrl, copyToAddressBar);
               currentHistoryLength = history.length;
           }

           return false;
        } 

    }

})();

// Initialization

// Automated unit testing and other diagnostics

function setURL(url)
{
    document.location.href = url;
}

function backButton()
{
    history.back();
}

function forwardButton()
{
    history.forward();
}

function goForwardOrBackInHistory(step)
{
    history.go(step);
}

DeepLinkingUtils.addEvent(window, "load", function() { DeepLinking.initialize(); });
