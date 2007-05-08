<?php

header("Content-type: text/plain; charset=utf-8");

$chameleon_theme_root = explode('/', $_SERVER['PHP_SELF']);
array_pop($chameleon_theme_root);
array_pop($chameleon_theme_root);
$chameleon_theme_root = implode('/', $chameleon_theme_root);

?>

if (!window.Node) {
     var Node = {
         ELEMENT_NODE: 1,
         ATTRIBUTE_NODE: 2,
         TEXT_NODE: 3,
         CDATA_SECTION_NODE: 4,
         ENTITY_REFERENCE_NODE: 5,
         ENTITY_NODE: 6,
         PROCESSING_INSTRUCTIONS_NODE: 7,
         COMMENT_NODE: 8,
         DOCUMENT_NODE: 9,
         DOCUMENT_TYPE_NODE: 10,
         DOCUMENT_FRAGMENT_NODE: 11,
         NOTATION_NODE: 12
    };
}



String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, '');
};


(function() {

    var struct = [];
    var hotspotMode = null;
        
    var Config = {
        THEME_ROOT: '<?php echo $chameleon_theme_root; ?>',
        REMOTE_URI: '<?php echo substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')); ?>/css.php<?php echo (isset($_GET['id'])) ? '?id='.(int) $_GET['id'] : '?dummy=1'; ?>',
        FONTS_LIST: ['verdana, arial, helvetica, sans-serif', '"trebuchet ms", verdana, sans-serif', 'georgia, "trebuchet ms", times, serif', 'Other'],
        FONT_WEIGHTS: ['normal', 'bold'],
        FONT_STYLES: ['normal', 'italic'],
        TEXT_DECORATION: ['none', 'underline', 'overline', 'line-through'],
        TEXT_ALIGN: ['left', 'right', 'center', 'justify'],
        REPEAT_LIST: ['repeat', 'repeat-x', 'repeat-y', 'no-repeat'],
        POSITION_LIST: ['left top', 'left center', 'left bottom', 'center top', 'center center', 'center bottom', 'right top', 'right center', 'right bottom'],
        BORDER_LIST: ['solid', 'dotted', 'dashed', 'none'],
        UNITS: ['px', 'pt', 'em', '%'],
        PROPS_LIST: ['color', 'background-color', 'background-image', 'background-attachment', 'background-position', 'font-family', 'font-size', 'font-weight', 'font-style', 'line-height', 'margin', 'padding', 'border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width', 'border-top-style', 'border-right-style', 'border-bottom-style', 'border-left-style', 'border-top-color', 'border-right-color', 'border-bottom-color', 'border-left-color']
    };
      


    var Util = {
        __registry: {},
        __uniqueId: 0,

        createElement: function(tag, id) {
            if (!id) var id = 'chameleon-element-' + ++Util.__uniqueId;
            var obj = document.createElement(tag);
            obj.setAttribute('id', id);
            return obj;
        },
    
        removeElement: function(obj) {
            if (!obj || !obj.parentNode) return false;

            var kids = obj.getElementsByTagName('*');
            if (!kids.length && typeof obj.all != 'undefined') {
                kids = obj.all;
            }
            
            var n = kids.length;
            while (n--) {
                if (kids[n].id && Util.__registry[kids[n].id]) {
                    Util.__removeAllEvents(kids[n]);
                }
            }
            if (Util.__registry[obj.id]) {
                Util.__removeAllEvents(obj);
            }
            obj.parentNode.removeChild(obj); 
        },
        
        clearElement: function(obj) {
            while (obj.hasChildNodes()) {
                obj.removeChild(obj.firstChild);
            }
        }, 

        addEvent: function(obj, ev, fn) {
            if (!Util.__addToRegistry(obj, ev, fn)) return;
  
            if (obj.addEventListener) {
                obj.addEventListener(ev, fn, false);
            } else if (obj.attachEvent) {
                obj['e' + ev + fn] = fn;
                obj[ev + fn] = function() { 
                    obj['e' + ev + fn](window.event);
                };
                obj.attachEvent('on' + ev, obj[ev + fn]);
            }
        },
        removeEvent: function(obj, ev, fn) {
            if (!Util.__removeFromRegistry(obj, ev, fn)) return;

            if (obj.removeEventListener) {
                obj.removeEventListener(ev, fn, false);
            } else if (obj.detachEvent) {
                obj.detachEvent('on' + ev, obj[ev + fn]);
                obj[ev + fn] = null;     
            }
        },

        __getEventId: function(obj) {
            if (obj == document)  return 'chameleon-doc';
            if (obj == window) return 'chameleon-win';
            if (obj.id) return obj.id;
            return false;
        },
        __findEvent: function(id, ev, fn) {
            var i = Util.__registry[id][ev].length;
            while (i--) {
                if (Util.__registry[id][ev][i] == fn) {
                    return i;
                }
            }
            return -1;
        },
        __addToRegistry: function(obj, ev, fn) {
            var id = Util.__getEventId(obj);

            if (!id) return false;

            if (!Util.__registry[id]) {
                Util.__registry[id] = {};
            }
            if (!Util.__registry[id][ev]) {
                Util.__registry[id][ev] = [];
            }
            if (Util.__findEvent(id, ev, fn) == -1) {
                Util.__registry[id][ev].push(fn);
                return true;
            }
            return false;
        },
        __removeFromRegistry: function(obj, ev, fn) {
            var id = Util.__getEventId(obj);
     
            if (!id) return false;
 
            var pos = Util.__findEvent(id, ev, fn);
            if (pos != -1) {
                Util.__registry[id][ev].splice(pos, 1);
                return true;
            }
            return false;
        },
        __removeAllEvents: function(obj) {
            for (var event in Util.__registry[obj.id]) {
                var n = Util.__registry[obj.id][event].length;
                while (n--) {
                    Util.removeEvent(obj, event, Util.__registry[obj.id][event][n]);
                }
            }
        },

        cleanUp: function() {
            struct = null;
            UI.closeAllBoxes();
        }
    };





    var Pos = {
        getElement: function(obj) {
            var x = 0; var y = 0;
            if (obj.offsetParent) {
                while (obj.offsetParent) {
                    x += obj.offsetLeft;
                    y += obj.offsetTop;
                    obj = obj.offsetParent;
                }
            }
            return {x: x, y: y};
        },
        getMouse: function(e) {
            var x = 0; var y = 0;
            if (e.pageX || e.pageY) {
                x = e.pageX;
                y = e.pageY;
            } else if (e.clientX || e.clientY) {
                x = e.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
                y = e.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
            }
            return {x: x, y: y};
        }
    };
    
    
    




    var CSS = {
        
        __localCSS: {},
        __remoteCSS: {},
        
        __localSaveRequired: false,
        __remoteSaveRequired: false,
        
        
        requireRemoteSave: function() {
            CSS.__remoteSaveRequired = true;            
        },
        
        clearTheme: function() {
            /*var links = document.getElementsByTagName('link');
            var n = links.length;
            while (n--) {
                if (links[n].href && links[n].href.indexOf('<?php echo $chameleon_theme_root . "/styles.php"; ?>') != -1) {
                    links[n].parentNode.removeChild(links[n]);
                    break;
                }
            }*/
        },
        

        loadRemote: function(doSetup) {
            if (!Sarissa.IS_ENABLED_XMLHTTP) {
                return false;
            }
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.responseText.indexOf('CHAMELEON_ERROR') != -1) {
                        alert('There was an error loading from the server:\n' + xmlhttp.responseText.replace(/CHAMELEON_ERROR /, '') + '.');
                        return;
                    }

                    CSS.__remoteCSS = CSS.toObject(xmlhttp.responseText);
                    CSS.__localCSS = CSS.__clone(CSS.__remoteCSS);
                    CSS.preview();
                    if (doSetup) {
                        setup();
                    }
                    xmlhttp = null;
                }
            };
            xmlhttp.open('GET', Config.REMOTE_URI  + '&nc=' + new Date().getTime(), true);
            xmlhttp.send(null);
            return true;
        },
        
        
        updateTemp: function(e, reset) {
            if (!CSS.__localSaveRequired && !reset) {
                UI.statusMsg('There are no changes that need saving!', 'chameleon-notice');
                return;
            }
            
            if (!reset) {
                UI.statusMsg('Updating temporary styles on the server...', 'chameleon-working');
            } else {
                UI.statusMsg('Deleting temporary styles from the server...', 'chameleon-working');
            }
            
            var css = CSS.toString();
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.responseText.indexOf('CHAMELEON_ERROR') != -1) {
                        UI.statusMsg('There was an error saving to the server:\n' + xmlhttp.responseText.replace(/CHAMELEON_ERROR /, '') + '.', 'chameleon-error');
                        
                    } else {
                        CSS.__localSaveRequired = false;
                        if (!reset) {
                            UI.statusMsg('Temporary styles have been updated.', 'chameleon-ok');
                        } else {
                            UI.statusMsg('Temporary styles have been cleared.', 'chameleon-ok');
                        }        
                    }
                    xmlhttp = null;
                }
            };
            xmlhttp.open('POST', Config.REMOTE_URI + '&temp=1', true);
            xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xmlhttp.send('css=' + css);
        },
        

        updateRemote: function() {
            if (!CSS.__remoteSaveRequired) {
                UI.statusMsg('There are no changes that need saving!', 'chameleon-notice');
                return;
            }
        
            var css = CSS.toString(CSS.__localCSS);

            UI.statusMsg('Updating styles on the server...', 'chameleon-working');
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.responseText.indexOf('CHAMELEON_ERROR') != -1) {
                        UI.statusMsg('There was an error saving to the server:\n' + xmlhttp.responseText.replace(/CHAMELEON_ERROR /, '') + '.', 'chameleon-error');
                    } else {
                        CSS.__remoteCSS = CSS.toObject(css);
                        CSS.__localSaveRequired = false;
                        CSS.__remoteSaveRequired = false;
                        UI.statusMsg('Styles have been saved to the server.', 'chameleon-ok');
                    }
                    xmlhttp = null;
                }
            };
            xmlhttp.open('POST', Config.REMOTE_URI, true);
            xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xmlhttp.send('css=' + css);
        },
        
    
        
        
      
      
        hardReset: function(e, noPrompt) {
            if (noPrompt || confirm('Are you sure? This will erase all styles that have not been permanently saved to the server.')) {
                CSS.__localCSS = {};
                CSS.updateTemp(null, true);
                
                CSS.__localCSS = CSS.__clone(CSS.__remoteCSS);
                CSS.__localSaveRequired = false;
                CSS.__remoteSaveRequired = false;
                CSS.preview();
            }
        },
        
        
        
        setPropValue: function(prop, value, selector) {
            if (!selector) var selector = CSS.Selector.get();

            if (!CSS.__localCSS[selector]) {
                CSS.__localCSS[selector] = {};
            }
            
            var matches = prop.match(/^border\-([^\-]+)$/);
            if (value) {
                var func = CSS.__requiresFunction(prop);
                if (func && value != 'none') {
                    CSS.__localCSS[selector][prop] = func + '(' + value + ')';
                } else if (matches) {
                    CSS.__localCSS[selector]['border-left-' + matches[1]] = value;
                    CSS.__localCSS[selector]['border-right-' + matches[1]] = value;
                    CSS.__localCSS[selector]['border-top-' + matches[1]] = value;
                    CSS.__localCSS[selector]['border-bottom-' + matches[1]] = value;
                } else {
                    CSS.__localCSS[selector][prop] = value;
                }
            } else {
                if (matches) {
                    CSS.unsetProp('border-left-' + matches[1], selector);
                    CSS.unsetProp('border-right-' + matches[1], selector);
                    CSS.unsetProp('border-top-' + matches[1], selector);
                    CSS.unsetProp('border-bottom-' + matches[1], selector);
                } else {
                    CSS.unsetProp(prop, selector);
                }
            }
            
            CSS.__localSaveRequired = true;
            CSS.__remoteSaveRequired = true;
            CSS.preview(selector);
        },
        
        getPropValue: function(prop, selector) {
            if (!selector) var selector = CSS.Selector.get();

            if (!CSS.__localCSS[selector] || !CSS.__localCSS[selector][prop]) {
                return '';
            }
            return CSS.__cleanFunctions(CSS.__localCSS[selector][prop]);
        },

        unsetProp: function(prop, selector) {
            if (!selector) var selector = CSS.Selector.get();

            if (!CSS.__localCSS[selector] || !CSS.__localCSS[selector][prop]) return;

            CSS.__localCSS[selector][prop] = null;
            delete CSS.__localCSS[selector][prop];

            if (!CSS.__hasProps(selector)) {
                CSS.__localCSS[selector] = null;
                delete CSS.__localCSS[selector];
            }
        },
        
        
        __hasProps: function(selector) {
            for (var prop in CSS.__localCSS[selector]) {
                if (prop) {
                    return true;
                }
            }
            return false;
        },
        
        


        __cleanFunctions: function(val) {
            var toClean = ['url'];
            for (var i = 0; i < toClean.length; ++i) {
                var start = val.indexOf(toClean[i] + '(');
                var end = val.indexOf(')', start);
                if (start == -1 || end == -1) {
                    continue;
                }
                val = val.slice(start + toClean[i].length + 1, end);
            }
            return val;
        },

        __requiresFunction: function(prop) {
            var fnProps = {};
            fnProps['background-image'] = 'url';
            if (fnProps[prop]) {
                return fnProps[prop];
            }
            return false;
        },




        fixPath: function(val) {
            if (val == 'none') return val;
            
            var tmp = val.split('(');
            if (tmp.length > 1) {
                tmp[1] = Config.THEME_ROOT + '/' + tmp[1];
                return tmp.join('(');
            }
            return Config.THEME_ROOT + '/' + val;
        },
        
        
        
        preview: function(sel) {
            var styleId = 'chameleon-preview-styles';

            var h = document.getElementsByTagName('head')[0];
            var s = document.getElementById(styleId);
            
            if (!s) {
                var s = Util.createElement('style', styleId);
                s.setAttribute('type', 'text/css');
                h.appendChild(s);
            }
            
            if (navigator.userAgent.toLowerCase().indexOf('msie') != -1  && !window.opera && document.styleSheets && document.styleSheets.length > 0) {
                var lastStyle = document.styleSheets[document.styleSheets.length - 1];
                
                var ieCrashProtector = /[^a-z0-9 #_:\.\-\*]/i; // some characters appearing in a selector can cause addRule to crash IE in spectacular style - if the selector contains any character outside this list don't try to add to the preview
                var ieWarning = false;
                
                if (sel) {
                    var matchedSelectors = [];
                    if (typeof sel == 'string') {
                        sel = [sel];
                    }
                    var n = lastStyle.rules.length;
                    while (n--) {
                        var ns = sel.length;
                        if (ns == 0) {
                            break;
                        }
                        while (ns--) {
                            if (sel[ns].match(ieCrashProtector)) {
                                ieWarning = true;
                                sel.splice(ns, 1);
                                break;
                            }
                            
                            if (lastStyle.rules[n].selectorText.toLowerCase() == sel[ns].toLowerCase()) {
                                matchedSelectors.push(sel[ns]);
                                sel.splice(ns, 1);
                                lastStyle.removeRule(n);
                                break;
                            }
                        }
                    }
                    matchedSelectors = matchedSelectors.concat(sel);
                    var sl = matchedSelectors.length;
                    while (sl--) {
                        lastStyle.addRule(matchedSelectors[sl], CSS.__propsToString(CSS.__localCSS[matchedSelectors[sl]], true));
                    }
                } else {
                    var n = lastStyle.rules.length;
                    while (n--) {
                        lastStyle.removeRule(n);
                    }
                   
                    for (var sel in CSS.__localCSS) {
                        if (sel.match(ieCrashProtector)) {
                            ieWarning = true;
                            continue;
                        }
                        var dec = CSS.__propsToString(CSS.__localCSS[sel], true);
                        lastStyle.addRule(sel, dec);
                    }
                }
                
                if (ieWarning) {
                    UI.statusMsg('The edited CSS contains content that can not be previewed by Internet Explorer', 'chameleon-notice');
                }
                
            } else {
                Util.clearElement(s);
                s.appendChild(document.createTextNode(CSS.toString(CSS.__localCSS, true))); // I think innerHTML would be faster here, but it doesn't work in KHTML browsers (Safari etc)
            }
        },
        
        

        __merge: function() {
            var merged = {};
            for (var i = 0; i < arguments.length; ++i) {
                for (var sel in arguments[i]) {
                    var newSelector = false;
                    if (!merged[sel]) {
                        merged[sel] = {};
                        newSelector = true;
                    }
                    for (var prop in arguments[i][sel]) {
                        merged[sel][prop] = arguments[i][sel][prop];
                    }

                    if (i > 0 && !newSelector) {
                        for (var prop in merged[sel]) {
                            if (!arguments[i][sel][prop]) {
                                 merged[sel][prop] = null;
                                 delete merged[sel][prop];
                            }
                        }
                    }
                }
                if (i > 0) {
                    for (var sel in merged) {
                        if (!arguments[i][sel]) {
                            merged[sel] = null;
                            delete merged[sel];
                        }
                    }
                }
            }
            return merged;
        },
        
        __clone: function(src) {
            var cloned = {};
            for (var sel in src) {
                if (!cloned[sel]) {
                    cloned[sel] = {};
                }
                for (var prop in src[sel]) {
                    cloned[sel][prop] = src[sel][prop];
                }
            }
            return cloned;
        },
        
        
        toString: function(css, fixpath) {
            if (!css) var css = CSS.__localCSS;
            
            var dec = '';
            for (var sel in css) {
                dec += sel + ' ' + CSS.__propsToString(css[sel], fixpath, sel);
            }
            return dec;
        },
        
        __propsToString: function(css, fixpath) {
            CSS.__Shorthand.border = {};
            
            var hasBorder = false;
            var col = false;
            var importantBorders = [];

            var dec = '{\n';
            for (var prop in css) {
                
                var includeProp = true;
                
                if (prop.indexOf('border') != -1 && prop.indexOf('spacing') == -1 && prop.indexOf('collapse') == -1) {
                    if (css[prop].indexOf('!important') == -1) {
                        CSS.__Shorthand.recordBorder(prop, css[prop]);
                    } else {
                        importantBorders.push({prop: prop, css: css[prop]});
                    }
                    includeProp = false;
                    hasBorder = true;
                }
                
                if (prop == 'color') {
                    col = css[prop];
                }

                if (includeProp) {
                    if (fixpath && (CSS.__requiresFunction(prop) == 'url') && css[prop] != 'none') {
                        dec += '  ' + prop + ': ' + CSS.fixPath(css[prop]) + ';\n';
                    } else {
                        dec += '  ' + prop + ': ' + css[prop] + ';\n';
                    }
                }
            }
            
            if (hasBorder) {
                dec += CSS.__Shorthand.getBorderString(col);
            }
            var n;
            if (n = importantBorders.length) {
                while (n--) {
                    dec += '  ' + importantBorders[n].prop + ': ' + importantBorders[n].css + ';\n';
                }
            }
            
            dec += '}\n';
            return dec;
        },
        
                
        
        
        toObject: function(css) {
            var cssObj = {};
            var end;

            while (end = css.indexOf('}'), end != -1) {
                var cssRule = css.substr(0, end);
                var parts = cssRule.split('{');
                var selector = parts.shift()
                if (selector.indexOf(',') != -1) {
                    var selectorArr = selector.split(',');
                } else {
                    var selectorArr = [selector];
                }
                
                var rules = parts.pop().trim();
                rules = rules.split(';');
                for (var i = 0; i < rules.length; ++i) {
                    if (rules[i].indexOf(':') == -1) {
                        break;
                    }
                    var rule = rules[i].split(':');
                    var prop = rule.shift().trim();
                    var val = rule.pop().trim();
                    
                    for (var j = 0; j < selectorArr.length; ++j) {
                        var noFontPropReset = {};
                        
                        selector = selectorArr[j].trim();
                        if (!cssObj[selector]) {
                            cssObj[selector] = {};
                        }
                    
                        if (prop != 'font' && (prop.indexOf('font') != -1 || prop == 'line-height')) {
                            noFontPropReset[prop] = true;
                        }
                    
                        if (prop == 'background') {
                            CSS.__Shorthand.setBackground(cssObj, selector, val);
                        } else if (prop == 'font') {    
                            CSS.__Shorthand.setFont(cssObj, selector, val, noFontPropReset);
                        } else if ((prop == 'border' || prop.match(/^border\-([^-]+)$/)) && prop.indexOf('spacing') == -1 && prop.indexOf('collapse') == -1) {
                            CSS.__Shorthand.setBorder(cssObj, selector, val, prop);
                        } else {
                            cssObj[selector][prop] = val;
                        }
                    }
                }
                css = css.substring(end + 1);
            }
            return cssObj;
        },
        
        
        
        
        
        getSelectorCSS: function(selector, asObject) {
            if (!selector) var selector = CSS.Selector.get();

            var css = (CSS.__localCSS[selector]) ? CSS.__localCSS[selector] : {};
            if (asObject) {
                return css;
            }
            return selector + ' ' + CSS.__propsToString(css);
        },
        
        
        
        saveRequired: function() {
            return CSS.__localSaveRequired || CSS.__serverSaveRequired;
        },
        
        
        checkSpec: function(e, selector) {
            if (!selector) var selector = CSS.Selector.get();
            if (selector == '') {
                UI.statusMsg('First you have to choose which item to style!', 'chameleon-notice');
                return;
            }
            
            var splitSelector = function(selector) {
                var selectorEnd = selector.split(' ').pop();
                selectorEnd = selectorEnd.replace(/([\.:#])/g, '|$1');
                return selectorEnd.split('|');
            };
            
            var similar = [];
        
            var selectorBits = splitSelector(selector);
        
            for (var sel in CSS.__localCSS) {
                var selBits = splitSelector(sel);
        
                var n = selectorBits.length;
        
                while (n--) {
                    var match = selectorBits[n];
                    var m = selBits.length;
                    while (m--) {
                        if (selBits[m] == match) {
                            var l = similar.length;
                            var add = true;
                            while (l--) {
                                if (similar[l] == sel) {
                                    add = false;
                                    break;
                                }
                            }
                            if (add) {
                                similar.push(sel);
                            }
                            break;
                        }
                    }
                }
            }
            
            if (similar.length) {
                UI.Selector.__displayOverview(null, similar, selector);
            } else {
                UI.statusMsg('Your file currently contains no selectors that appear similar to "' + selector + '"', 'chameleon-notice');
            }  
        },
        
        
        unloadPrompt: function() {
            if (CSS.__localSaveRequired) {
                if (confirm('You have made changes to the CSS on this page since the last time it was saved, these changes will be lost unless you save them now. Select OK to save a temporary copy or Cancel to continue and discard the unsaved CSS.')) {
                    CSS.updateTemp();
                }
            }
            var cookieVal = (CSS.__remoteSaveRequired) ? 1 : 0;
            var crumb = new cookie('chameleon_server_save_required', cookieVal, 30, '/', null, null);
            crumb.set();
        }

    };
    
    
    
    CSS.Selector = {
        
        trimmed: [],
        full: [],
        selector: '',
        
        create: function() {
            CSS.Selector.trimmed = [];
 
            var n = struct.length;
            while (n--) {
                if (CSS.Selector.full[n]) {
                    CSS.Selector.trimmed.push(CSS.Selector.full[n].val);
                }
            }
            CSS.Selector.set(CSS.Selector.trimmed.join(' '));
        },
        
        modify: function(e) {
            var target = e.target || e.srcElement;
            var p = target.position;
            
            var sel = CSS.Selector.full;

            if (!sel[p]) {
                UI.Selector.highlight(target);
                sel[p] = {val: target.selectorValue, id: target.id};
            } else if (sel[p].val != target.selectorValue) {
                UI.Selector.highlight(target);
                UI.Selector.unhighlight(document.getElementById(sel[p].id));
                sel[p] = {val: target.selectorValue, id: target.id};
            } else {
                UI.Selector.unhighlight(target);
                sel[p] = null;
            }

            CSS.Selector.create();
            UI.Selector.displaySelector(CSS.Selector.trimmed);
        },
        
        set: function(sel) {
            CSS.Selector.selector = sel;
        },
        
        get: function() {
            return CSS.Selector.selector;  
        },

        reset: function() {
            CSS.Selector.trimmed = [];
            CSS.Selector.full = [];
            CSS.Selector.set('');
        }               
    };
    
    
    
    CSS.__Shorthand = {
        border: {},
        
        recordBorder: function(prop, value) {
            var pr = prop.split('-')
            var p = pr.pop();
            var s = pr.pop();
            if (!CSS.__Shorthand.border[p]) {
                CSS.__Shorthand.border[p] = [];
            }
            if (!CSS.__Shorthand.border[s]) {
                CSS.__Shorthand.border[s] = {};
            }
            if (!CSS.__Shorthand.border[s][p]) {
                CSS.__Shorthand.border[s][p] = [];
            }
            CSS.__Shorthand.border[p].push({prop: prop, value: value});
            CSS.__Shorthand.border[s][p] = value;
        },
        
        getBorderString: function(col) {
            var cb = CSS.__Shorthand.border;
            
            var useHowManyProps = function(prop) {
                if (!cb['top'] || !cb['right'] || !cb['bottom'] || !cb['left']) {
                    return false;
                }
                
                if (!(cb['top'][prop] && cb['right'][prop] && cb['bottom'][prop] && cb['left'][prop])) {
                    return false;
                }
                
                if (cb['top'][prop] == cb['right'][prop] && cb['top'][prop] == cb['bottom'][prop] && cb['top'][prop] == cb['left'][prop]) {
                    return 1;
                }
                if (cb['top'][prop] == cb['bottom'][prop] && cb['right'][prop] == cb['left'][prop]) {
                    return 2;
                }
                if (cb['right'][prop] == cb['left'][prop]) {
                    return 3;
                }
                return 4;
            };
            
            var getPropShorthand = function(prop) {
                var num = useHowManyProps(prop);
                if (!num) {
                    return '';
                }
                
                if (prop.indexOf('color') != -1) {
                    var l = inheritColor(cb['left'][prop]);
                    var r = inheritColor(cb['right'][prop]);
                    var t = inheritColor(cb['top'][prop]);
                    var b = inheritColor(cb['bottom'][prop]);
                } else {
                    var l = cb['left'][prop];
                    var r = cb['right'][prop];
                    var t = cb['top'][prop];
                    var b = cb['bottom'][prop];
                }
                
                var propShorthand = '';
                if (num == 1) {
                    propShorthand += '  border-' + prop + ': ' + l;
                } else if (num == 2) {
                    propShorthand += '  border-' + prop + ': ' + t + ' ' + l;
                } else if (num == 3) {
                    propShorthand += '  border-' + prop + ': ' + t + ' ' + l + ' ' + b;
                } else {
                    propShorthand += '  border-' + prop + ': ' + t + ' ' + r + ' ' + b + ' ' + l;
                }
                return propShorthand + ';\n';
            };
            
            var propsStr = function(props) {
                var str = '';
                for (var i = 0; i < props.length; ++i) {
                    str += '  ' + props[i].prop + ': ' + ((props[i].prop.indexOf('color') != -1) ? inheritColor(props[i].value) : props[i].value) + ';\n';
                }
                return str;
            };
            
            var inheritColor = function(val) {
                if (!col || val != 'inherit') return val;               
                return col;
            };
            
            var setImportant = function(str) {
                if (!str) return '';
                if (str.indexOf('!important') == -1) return str;
                str = str.replace(/ *\!important */g, ' ');
                return str.substr(0, str.lastIndexOf(';')) + ' !important;\n';
            };
                      
            var widthEqual = (cb['width']) ? CSS.__Shorthand.__allPropsEqual(cb['width']) : false;
            var styleEqual = (cb['style']) ? CSS.__Shorthand.__allPropsEqual(cb['style']) : false;
            var colorEqual = (cb['color']) ? CSS.__Shorthand.__allPropsEqual(cb['color']) : false;
                        
            if (widthEqual && styleEqual && colorEqual) {
                var propStr = setImportant(cb['width'][0].value + ' ' + cb['style'][0].value + ' ' + inheritColor(cb['color'][0].value) + ';\n');              
                if (cb['left'] && cb['top'] && cb['right'] && cb['bottom']) {
                    return '  border: ' + propStr;
                }
                
                var sideShorthand = '';
                if (cb['top']) {
                    sideShorthand += '  border-top: ' + propStr;
                }
                if (cb['right']) {
                    sideShorthand += '  border-right: ' + propStr;
                }
                if (cb['bottom']) {
                    sideShorthand += '  border-bottom: ' + propStr;
                }
                if (cb['left']) {
                    sideShorthand += '  border-left: ' + propStr;
                }
                return sideShorthand;
            }
            
            var widthProps = getPropShorthand('width');
            if (!widthProps) {
                widthProps = (cb['width']) ? propsStr(cb['width']) : '';
            }
            var styleProps = getPropShorthand('style');
            if (!styleProps) {
                styleProps = (cb['style']) ? propsStr(cb['style']) : '';
            }
            var colorProps = getPropShorthand('color');
            if (!colorProps) {
                colorProps = (cb['color']) ? propsStr(cb['color']) : '';
            }
            
            return setImportant(widthProps) + setImportant(styleProps) + setImportant(colorProps);

        },
        
        
        
        
        
        setBorder: function(css, selector, value, prop) {
            var props = {};
            var p = '';

            props['width'] = {
                regexp: /^(thin|medium|thick|0|(\d+(([^%\d]+)|%)))$/,
                def: 'medium'
            };
            props['style'] = {
                regexp: /none|dotted|dashed|solid|double|groove|ridge|inset|outset/,
                def: 'none'
            };
            props['color'] = {
                regexp: /^((rgb\(\d{1,3} *, *\d{1,3} *, *\d{1,3} *\))|(#[A-F0-9]{3}([A-F0-9]{3})?)|([a-z]+))$/i,
                def: 'inherit'
            };
            
            var bits = value.split(' ');
            var imp = (bits[bits.length - 1] == '!important') ? ' ' + bits.pop() : '';
                        
            if (prop == 'border') {
                for (var i in props) {
                    css[selector]['border-top-' + i] = props[i].def;
                    css[selector]['border-right-' + i] = props[i].def;
                    css[selector]['border-bottom-' + i] = props[i].def;
                    css[selector]['border-left-' + i] = props[i].def;
                    var j = bits.length;
                    while (j--) {
                        if (bits[j].match(props[i].regexp)) {
                            css[selector]['border-top-' + i] = bits[j];
                            css[selector]['border-right-' + i] = bits[j];
                            css[selector]['border-bottom-' + i] = bits[j];
                            css[selector]['border-left-' + i] = bits[j];
                            bits.splice(j, 1);
                            break;
                        }
                    }
                }
            } else if (prop == 'border-left' || prop == 'border-right' || prop == 'border-top' || prop == 'border-bottom') {
                for (var i in props) {
                    css[selector][prop + '-' + i] = props[i].def;
                    var j = bits.length;
                    while (j--) {
                        if (bits[j].match(props[i].regexp)) {
                            css[selector][prop + '-' + i] = bits[j] + imp;
                            bits.splice(j, 1);
                            break;
                        }
                    }   
                }
                imp = '';

            } else if (prop == 'border-width' || prop == 'border-style' || prop == 'border-color') {
                var p = prop.split('-').pop();
                var num = bits.length;
                if (num == 1) {
                    css[selector]['border-top-' + p] = bits[0];
                    css[selector]['border-right-' + p] = bits[0];
                    css[selector]['border-bottom-' + p] = bits[0];
                    css[selector]['border-left-' + p] = bits[0];
                } else if (num == 2) {
                    css[selector]['border-top-' + p] = bits[0];
                    css[selector]['border-right-' + p] = bits[1];
                    css[selector]['border-bottom-' + p] = bits[0];
                    css[selector]['border-left-' + p] = bits[1];
                } else if (num == 3) {
                    css[selector]['border-top-' + p] = bits[0];
                    css[selector]['border-right-' + p] = bits[1];
                    css[selector]['border-bottom-' + p] = bits[2];
                    css[selector]['border-left-' + p] = bits[1];
                } else if (num == 4) {
                    css[selector]['border-top-' + p] = bits[0];
                    css[selector]['border-right-' + p] = bits[1];
                    css[selector]['border-bottom-' + p] = bits[2];
                    css[selector]['border-left-' + p] = bits[3];
                }
            }

            if (imp != '') {
                var sides = ['top', 'right', 'bottom', 'left'];
                for (var i = 0; i < 4; ++i) {
                    for (var j in props) {
                        if (p != '' && p != j) {
                            continue;
                        }

                        if (css[selector]['border-' + sides[i] + '-' + j]) {
                            css[selector]['border-' + sides[i] + '-' + j] += imp;
                        }
                    }
                }
            }
            
        },
        
        
        
        
        setBackground: function(css, selector, value) {
            var imp = (value.indexOf('!important') != -1) ? ' !important' : '';
            if (imp != '') {
                value = value.replace(/ *\!important */g, '');
            }
            
            var urlPos = value.indexOf('url(');
            if (urlPos == -1 && value.indexOf('none') == -1) {
                css[selector]['background-color'] = value + imp;
                return;
            } else if (urlPos == -1 && value.indexOf(' none') != -1) {
                var bits = value.split(' ');
                css[selector]['background-color'] = bits[0] + imp;
                css[selector]['background-image'] = bits[1] + imp;
                return;
            } else if (value == 'none') {
                css[selector]['background-image'] = value + imp;
                return;
            }
            var bits = value.split('url(');
            var endImg = bits[1].indexOf(')');
            if (endImg == -1) {
                return;
            }
            css[selector]['background-image'] = 'url(' + bits[1].substr(0, endImg).replace(/["']+/g, '') + ')' + imp; //"
            
            var pos = [];
            
            var bgOptions =  bits[1].substring(endImg + 1).split(' ');
            var n = bgOptions.length;
            
            for (var i = 0; i < n; ++i) {
                var opt = bgOptions[i].trim();
                if (opt.indexOf('repeat') != -1) {
                    css[selector]['background-repeat'] = opt + imp;
                } else if (opt == 'fixed' || opt == 'scroll') {
                    css[selector]['background-attachment'] = opt + imp;
                } else if (opt != '') {
                    pos.push(opt);
                }
            }
            if (pos.length == 2) {
                css[selector]['background-position'] = pos.join(' ') + imp;
            }
            var col = bits[0].trim();
            if (col != '') {
                css[selector]['background-color'] = col + imp;
            }
        },
        
        setFont: function(css, selector, value, noreset) {
            var imp = (value.indexOf('!important') != -1) ? ' !important' : '';
            if (imp != '') {
                value = value.replace(/ *\!important */g, '');
            }
            
            var order = ['font-style', 'font-variant', 'font-weight', 'font-size', 'font-family'];
            var numProps = order.length;
            var allowedVals = {};
            allowedVals['font-style'] = /(normal|italic|oblique|inherit)/;
            allowedVals['font-variant'] = /(normal|small\-caps|inherit)/;
            allowedVals['font-weight'] = /(normal|bold|bolder|lighter|100|200|300|400|500|600|700|800|900|inherit)/;
            allowedVals['font-size'] = /([^ ]+)/;
            allowedVals['font-family'] = /(.+$)/;
            
            if (!noreset['font-style']) css[selector]['font-style'] = 'normal';
            if (!noreset['font-variant']) css[selector]['font-variant'] = 'normal';
            if (!noreset['font-weight']) css[selector]['font-weight'] = 'normal';
            if (!noreset['font-size']) css[selector]['font-size'] = 'medium';
            if (!noreset['line-height']) css[selector]['line-height'] = 'normal';
            
            var expandShorthand = function(bits) {
                var numBits = bits.length;
                var startProp = 0;
                for (var i = 0; i < numBits; ++i) {
                    if (i > numProps - 1) {
                        return;
                    }
                    for (var j = startProp; j < numProps; ++j) {
                        if (bits[i].match(allowedVals[order[j]])) {
                            if (order[j] == 'font-size' && bits[i].indexOf('/') != -1) {
                                var fsLh = bits[i].split('/');
                                css[selector]['font-size'] = fsLh[0] + imp;
                                css[selector]['line-height'] = fsLh[1] + imp;
                            } else {
                                css[selector][order[j]] = bits[i] + imp;
                            }
                            startProp = j + 1;
                            break;
                        }
                    }
                }
            };
            
            var removeCommaListSpaces = function(str) {
                var comma = str.indexOf(',');
                if (comma != -1) {
                    return str.substr(0, comma) + str.substring(comma).replace(/ +/g, '');
                }
                return str;
            };
            
            var hasQuote = value.match(/(["'])/); //"
            if (hasQuote) {
                var tmp = value.split(hasQuote[1]);
                var bits = removeCommaListSpaces(tmp.shift()).split(' ');
                var startFont = bits.pop();
                
                expandShorthand(bits);
                
                css[selector]['font-family'] = startFont + hasQuote[1] + tmp.join(hasQuote[1]) + imp;           
            } else {
                value = removeCommaListSpaces(value);            
                expandShorthand(value.split(' '));
            }
        },
        
        
        

        __allPropsEqual: function(props) {
            var num = props.length - 1;
            if (num < 3) return false;
            
            for (var i = 0; i < num; ++i) {
                if (props[i].value != props[i + 1].value) {
                    return false;
                }
            }
            return true;
        }
    };
    
    
    
    CSS.FreeEdit = {
      
        __initial: {},
        
        setInitial: function(e) {
            var target = e.target || e.srcElement;

            CSS.FreeEdit.__initial = CSS.toObject(target.value);
        },
        
        saveComplete: function(e) {
            var target = e.target || e.srcElement;
            target.value = CSS.FreeEdit.__stripComments(target.value);

            CSS.__localCSS = CSS.__merge(CSS.__localCSS, CSS.toObject(target.value));

            CSS.__localSaveRequired = true;
            CSS.__remoteSaveRequired = true;

            CSS.preview();
        },
        
        saveSelector: function(e) {
            var target = e.target || e.srcElement;
            target.value = CSS.FreeEdit.__stripComments(target.value);
            
            var changedSelectors = [];
            var css = CSS.toObject(target.value);
            for (var sel in css) {
                changedSelectors.push(sel);
                if (!CSS.__localCSS[sel]) {
                    CSS.__localCSS[sel] = {};
                }
                for (var prop in css[sel]) {
                    CSS.__localCSS[sel][prop] = css[sel][prop];
                }
            }

            for (var sel in CSS.FreeEdit.__initial) {
                if (!css[sel] && CSS.__localCSS[sel]) {
                    changedSelectors.push(sel);
                    CSS.__localCSS[sel] = null;
                    delete CSS.__localCSS[sel];
                    continue;
                }
                for (var prop in CSS.FreeEdit.__initial[sel]) {
                    if (!css[sel][prop] && CSS.__localCSS[sel][prop]) {
                        CSS.__localCSS[sel][prop] = null;
                        delete CSS.__localCSS[sel][prop];
                    }
                }
            }
            
            CSS.__localSaveRequired = true;
            CSS.__remoteSaveRequired = true;
            CSS.preview(changedSelectors);
        },
        
        __stripComments: function(str) {
            return str.replace(/\/\*([\s\S])*?\*\//g, '');
        }
        
    };
    
    
    
    
    
    
    var FileHandler = {
        
        getFiles: function(path) {
            if (!path) path = '';
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    UI.CSS.displayImagePicker(xmlhttp.responseXML);
                    xmlhttp = null;
                }
            };
            xmlhttp.open('GET', Config.REMOTE_URI  + '&path=' + escape(path) + '&nc=' + new Date().getTime(), true);
            xmlhttp.send(null);
            return true;
        }
    };
    
    
    
    
    
    var UI = {
        boxes: [],
        boxOffsetX: 35,
        boxOffsetY: 30,
        zIndex: 9999,

        __dragTargetId: null,

        statusMsg: function(msg, cls) {
            UI.clearStatusMsg();
            
            var target = UI.__getBox();
            if (!target) {
                var box = Util.createElement('div', 'chameleon-status-msg');
                box.appendChild(document.createTextNode(msg));
                box.style.zIndex = ++UI.zIndex;
                UI.addToDoc(box);
            } else {
         
                var statusTable = Util.createElement('table', 'chameleon-status-msg');
                var statusTableBody = Util.createElement('tbody');
                var statusRow = Util.createElement('tr');
                var statusIconCell = Util.createElement('td');
                var statusMsgCell = Util.createElement('td');
                var statusBtnCell = Util.createElement('td');
                
                if (cls) {
                    statusIconCell.className = cls;
                }
                statusMsgCell.appendChild(document.createTextNode(msg));
                statusBtnCell.appendChild(UI.createButton('chameleon-status-msg-btn', 'OK', 'Clear this message', UI.clearStatusMsg));
                
                statusRow.appendChild(statusIconCell);
                statusRow.appendChild(statusMsgCell);
                statusRow.appendChild(statusBtnCell);
                statusTableBody.appendChild(statusRow);
                statusTable.appendChild(statusTableBody);
                
                target.appendChild(statusTable);
            }
        },

        clearStatusMsg: function() {
            var obj = document.getElementById('chameleon-status-msg');
            if (obj) {
                Util.removeElement(obj);
            }
        },

        addToDoc: function(content) {
            document.getElementsByTagName('body')[0].appendChild(content);
        },

        makeDraggableBox: function(id, x, y) {
            if ((x + 500) > screen.width) {
                var offset = x + 525 - screen.width;
                x -= offset;
            }
            
            var box = Util.createElement('div', id);
            box.style.left = x + 'px';
            box.style.top = y + 'px';
            box.style.zIndex = ++UI.zIndex;

            var topBar = Util.createElement('div', id + '-handle');
            var closeBtn = Util.createElement('div', id + '-close');
            closeBtn.appendChild(document.createTextNode('x'));
            closeBtn.setAttribute('title', 'Close');
            topBar.setAttribute('title', 'Drag me!');
            
            UI.__dragTargetId = id + '-handle';

            Util.addEvent(closeBtn, 'click', UI.closeBoxes);
            Util.addEvent(topBar, 'mousedown', UI.__startDrag);
            Util.addEvent(topBar, 'mousedown', UI.__bringToFront);
            Util.addEvent(topBar, 'mouseup', UI.__stopDrag);

            topBar.appendChild(closeBtn);
            box.appendChild(topBar);
   
            UI.boxes.push(id);

            return box;
        },
        
        closeAllBoxes: function() {
            var n = UI.boxes.length;
            while (n--) {
                Util.removeElement(document.getElementById(UI.boxes[n]));
                UI.boxes.splice(n, 1);
            }
            UI.__dragTargetId = null;
        },

        closeBoxes: function(e, box) {
            if (!box) {
                var target = e.target || e.srcElement;
                var box = target.parentNode.parentNode;
            }
                        
            var n = UI.boxes.length;
            while (n--) {
                if (UI.boxes[n] == box.id) {
                    break;
                }
                Util.removeElement(document.getElementById(UI.boxes[n]));
                UI.boxes.splice(n, 1);
            }
            Util.removeElement(box);
            UI.boxes.splice(n, 1);
            UI.__dragTargetId = (UI.boxes.length) ? UI.boxes[UI.boxes.length - 1] + '-handle' : null;
        },

        __startDrag: function(e) {
            var target = e.target || e.srcElement;
            var mouseCoords = Pos.getMouse(e);
            var elementCoords = Pos.getElement(target);
            target.mouseX = mouseCoords.x - elementCoords.x;
            target.mouseY = mouseCoords.y - elementCoords.y;    

            UI.__dragTargetId = target.id;

            Util.addEvent(document, 'mousemove', UI.__drag);
        },

        __stopDrag: function(e) {
            Util.removeEvent(document, 'mousemove', UI.__drag);
        },

        __drag: function(e) {
            var target = document.getElementById(UI.__dragTargetId);

            var mouseCoords = Pos.getMouse(e);
            target.parentNode.style.left = (mouseCoords.x - target.mouseX) + 'px';
            target.parentNode.style.top = (mouseCoords.y - target.mouseY) + 'px';
            
            if (e.preventDefault) {
                e.preventDefault();
            } else if (window.event) {
                window.event.returnValue = false;
            }
        },

        __bringToFront: function(e) {
            var target = e.target || e.srcElement;
            target.parentNode.style.zIndex = ++UI.zIndex;
        },
        
        __getBox: function() {
            var obj = document.getElementById(UI.__dragTargetId);
            if (obj && obj.parentNode) {
                return obj.parentNode;
            }
            return false;
        },
        
        
        
        
        setupPane: function(tabs, parentId, tabId, active) {
            for (var i = 0; i < tabs.length; ++i) {
                var obj = document.getElementById(tabId + '-tab-' + tabs[i]);
                if (obj) {
                    obj.className = tabId + ((active == tabs[i]) ? '-tab-active' : '-tab');
                }
            }

            var parent = document.getElementById(parentId);
            if (parent && parent.firstChild) {
                Util.removeElement(parent.firstChild);
            }
            return parent;
        },
        
        setupButtons: function() {
            var parentId = arguments[0];
            var parent = document.getElementById(parentId);
            if (!parent) return;

            var btns = parent.getElementsByTagName('input');
            for (var i = 0; i < btns.length; ++i) {
                btns[i].style.display = 'none';
            }

            for (var i = 1; i < arguments.length; ++i) {
                var id = parentId + '-' + arguments[i];
                var btn = document.getElementById(id);
                if (btn) {
                    btn.style.display = 'inline';
                }
            }
        },
        
        createButton: function(id, value, title, fn, hidden) {
            var btn = Util.createElement('input', id);
            btn.setAttribute('type', 'submit');
            btn.setAttribute('value', value);
            btn.setAttribute('title', title);
            btn.className = 'chameleon-btn';
            if (hidden) {
                btn.style.display = 'none';
            }

            Util.addEvent(btn, 'click', fn);
            return btn;
        },

        setOverflow: function(obj, height, forced) {
            if (obj.offsetHeight > height || forced) {
                obj.style.height = height + 'px';
                obj.style.overflow = 'scroll';
            }
        }
    };
    
    
    UI.Selector = {
        controlsId: 'chameleon-selector-controls',
        viewedProp: null,
        displayPropWatch: false,
        sections: ['choose', 'overview', 'free-edit'],
        
        
        editWindow: function(e) {
            if (!e.shiftKey) {
                return;
            }

            var target = e.target || e.srcElement;
            var tmpStruct = climbTree(target);
            if (typeof tmpStruct == 'string') {
                return;
            }
            
            hotspotMode = false;

            var box = document.getElementById('chameleon-selector-box');
            if (box) UI.closeBoxes(true, box);

            struct = tmpStruct;
            CSS.Selector.reset();

            var coords = Pos.getMouse(e);
            var box = UI.makeDraggableBox('chameleon-selector-box', coords.x, coords.y);


            var instructions = Util.createElement('p');
            instructions.appendChild(document.createTextNode('Create a CSS selector to edit, browse an overview of your edited styles or edit your complete stylesheet by hand.'));
            instructions.className = 'chameleon-instructions';
            box.appendChild(instructions);

            var tabsContainer = Util.createElement('table', 'chameleon-selector-tabs');
            var tabsBody = Util.createElement('tbody');
            var tabs = Util.createElement('tr');
 
            tabs.appendChild(UI.Selector.__createTab('Choose', UI.Selector.__editSelector, true, 'Choose'));
            tabs.appendChild(UI.Selector.__createTab('Overview', UI.Selector.__displayOverview, false, 'Overview'));
            tabs.appendChild(UI.Selector.__createTab('Free Edit', UI.Selector.__editCode, false, 'Free Edit'));

            tabsBody.appendChild(tabs);
            tabsContainer.appendChild(tabsBody);

            box.appendChild(tabsContainer);

            var styleControls = Util.createElement('div', UI.Selector.controlsId);
            box.appendChild(styleControls);
            box.appendChild(UI.Selector.__addButtons());

            UI.addToDoc(box);

            UI.Selector.__editSelector();
            
            if (e.preventDefault) {
                e.preventDefault();
            } else if (window.event) {
                window.event.returnValue = false;
            }
        },
        
       
        __listProps: function(e) {
             var target = e.target || e.srcElement;
             
             Util.removeElement(document.getElementById('chameleon-selector-element-list'));
             UI.Selector.viewedProp = target.options[target.selectedIndex].value;
             if (!document.getElementById('chameleon-selector-list')) {
                 target.parentNode.parentNode.appendChild(UI.Selector.__elementList(target.options[target.selectedIndex].value));
             } else {
                 target.parentNode.parentNode.insertBefore(UI.Selector.__elementList(target.options[target.selectedIndex].value), document.getElementById('chameleon-selector-list'));
             }
        },
        
        __editSelector: function() {
            var parent = UI.setupPane(UI.Selector.sections, UI.Selector.controlsId, 'chameleon-selector', 'choose');
            UI.setupButtons('chameleon-selector-buttons', 'edit', 'check');

            var container = Util.createElement('div');

            var instructions = Util.createElement('p');
            instructions.appendChild(document.createTextNode('Please choose the element you wish to style.'));
            container.appendChild(instructions);
            
            var options = Util.createElement('p');
            
            if (UI.Selector.__displayPropWatch) {
                            
                var selectProp = Util.createElement('select', 'chameleon-selector-prop-select');
                var optionProp = Util.createElement('option');
                optionProp.appendChild(document.createTextNode('Select a CSS property to view'));
                optionProp.setAttribute('value', '');
                selectProp.appendChild(optionProp);
            
                for (var i = 0; i < Config.PROPS_LIST.length; ++i) {
                    optionProp = Util.createElement('option');
                    optionProp.setAttribute('value', Config.PROPS_LIST[i]);
                    if (UI.Selector.viewedProp == Config.PROPS_LIST[i]) {
                        optionProp.setAttribute('selected', 'selected');
                    }
                    optionProp.appendChild(document.createTextNode(Config.PROPS_LIST[i]));
                    selectProp.appendChild(optionProp); 
                }
            
                Util.addEvent(selectProp, 'change', UI.Selector.__listProps);
            
                options.appendChild(selectProp);
 
            }
            
            var togglePropWatch = Util.createElement('a');
            togglePropWatch.setAttribute('title', 'The property inspector allows you to check the current value of a range of CSS properties for these elements');
            togglePropWatch.appendChild(document.createTextNode(' (' + (UI.Selector.__displayPropWatch ? 'Hide property inspector' : 'Show property inspector') + ')'));
            Util.addEvent(togglePropWatch, 'click', UI.Selector.__togglePropWatch);
            options.appendChild(togglePropWatch);
            
            
            container.appendChild(options);
            
            container.appendChild(UI.Selector.__elementList());

            parent.appendChild(container);

            UI.Selector.displaySelector(CSS.Selector.trimmed);
        },
        
        __togglePropWatch: function() {
            UI.Selector.__displayPropWatch = !UI.Selector.__displayPropWatch;
            UI.Selector.__editSelector();
        },
        
        __displayOverview: function(e, selectors, selector) {
            var parent = UI.setupPane(UI.Selector.sections, UI.Selector.controlsId, 'chameleon-selector', 'overview');
            UI.setupButtons('chameleon-selector-buttons');
            
            var container = Util.createElement('div', 'chameleon-style-overview-container');
            parent.appendChild(container); // doing it this way is much faster than creating the table then applying the overflow
            UI.setOverflow(container, 350, true);
            
            var overviewTable = Util.createElement('table', 'chameleon-style-overview');
            var overviewTableBody = Util.createElement('tbody');
            
            if (!selectors) {

                for (var sel in CSS.__localCSS) {
                    var overviewTableRow = Util.createElement('tr');
                
                    var overviewTableCell = Util.createElement('th');
                    overviewTableCell.className = 'selector';
                    overviewTableCell.appendChild(document.createTextNode(sel));
                    overviewTableRow.appendChild(overviewTableCell);
                    overviewTableCell = Util.createElement('td');

                    var overviewEditLink = Util.createElement('a');
                    overviewEditLink.value = sel;
                    overviewEditLink.appendChild(document.createTextNode('[edit]'));
                    Util.addEvent(overviewEditLink, 'click', UI.CSS.launchEditWindow);
                    overviewTableCell.className = 'selector';
                    overviewTableCell.appendChild(overviewEditLink);

                    overviewTableRow.appendChild(overviewTableCell);
                    overviewTableBody.appendChild(overviewTableRow);
                    for (var prop in CSS.__localCSS[sel]) {
                        overviewTableRow = Util.createElement('tr');
                        overviewTableCell = Util.createElement('td');
                        overviewTableCell.className = 'prop';
                        overviewTableCell.appendChild(document.createTextNode(prop));
                        overviewTableRow.appendChild(overviewTableCell);
                        overviewTableCell = Util.createElement('td');
                        overviewTableCell.className = 'value';
                        overviewTableCell.appendChild(document.createTextNode(CSS.__localCSS[sel][prop]));
                        overviewTableRow.appendChild(overviewTableCell);
                        overviewTableBody.appendChild(overviewTableRow);
                    }
                }
            } else {
            
                var n = selectors.length;
                
                if (!CSS.__localCSS[selector]) {
                    var overviewTableRow = Util.createElement('tr');
                
                    var overviewTableCell = Util.createElement('th');
                    overviewTableCell.className = 'current-selector';
                    overviewTableCell.appendChild(document.createTextNode(selector));
                    overviewTableRow.appendChild(overviewTableCell);
                    overviewTableCell = Util.createElement('td');

                    var overviewEditLink = Util.createElement('a');
                    overviewEditLink.value = selector;
                    overviewEditLink.appendChild(document.createTextNode('[edit]'));
                    Util.addEvent(overviewEditLink, 'click', UI.CSS.launchEditWindow);
                    overviewTableCell.className = 'current-selector';
                    overviewTableCell.appendChild(overviewEditLink);

                    overviewTableRow.appendChild(overviewTableCell);
                    overviewTableBody.appendChild(overviewTableRow);
                }
                
                for (var i = 0; i < n; ++i) {
                    var sel = selectors[i];
                    
                    var overviewTableRow = Util.createElement('tr');
                
                    var overviewTableCell = Util.createElement('th');
                    overviewTableCell.className = (sel == selector) ? 'current-selector' : 'selector';
                    overviewTableCell.appendChild(document.createTextNode(sel));
                    overviewTableRow.appendChild(overviewTableCell);
                    overviewTableCell = Util.createElement('td');

                    var overviewEditLink = Util.createElement('a');
                    overviewEditLink.value = sel;
                    overviewEditLink.appendChild(document.createTextNode('[edit]'));
                    Util.addEvent(overviewEditLink, 'click', UI.CSS.launchEditWindow);
                    overviewTableCell.className = (sel == selector) ? 'current-selector' : 'selector';
                    overviewTableCell.appendChild(overviewEditLink);

                    overviewTableRow.appendChild(overviewTableCell);
                    overviewTableBody.appendChild(overviewTableRow);
                    
                    for (var prop in CSS.__localCSS[sel]) {
                        overviewTableRow = Util.createElement('tr');
                        overviewTableCell = Util.createElement('td');
                        overviewTableCell.className = 'prop';
                        overviewTableCell.appendChild(document.createTextNode(prop));
                        overviewTableRow.appendChild(overviewTableCell);
                        overviewTableCell = Util.createElement('td');
                        overviewTableCell.className = 'value';
                        overviewTableCell.appendChild(document.createTextNode(CSS.__localCSS[sel][prop]));
                        overviewTableRow.appendChild(overviewTableCell);
                        overviewTableBody.appendChild(overviewTableRow);
                    }
                }
            
            }

            overviewTable.appendChild(overviewTableBody);            
            container.appendChild(overviewTable);
        },
        
        __elementList: function(showComputedStyle) {
            if (!showComputedStyle && UI.Selector.viewedProp) {
                showComputedStyle = UI.Selector.viewedProp;
            }
            
            var list = Util.createElement('ol', 'chameleon-selector-element-list');
            var n = struct.length;
            var classStr = '';
            var idStr = '';

            var pseudoClasses = ['link', 'active', 'visited', 'hover', 'focus'];

            while (n--) {
                var row = n % 2;

                var item = Util.createElement('li');
                item.className = 'row' + row;
                var tag = Util.createElement('span', 'chameleon-tag-name-' + n);
                tag.appendChild(document.createTextNode(struct[n].tagname));
                tag.selectorValue = struct[n].tagname;
                tag.position = n;
               
                UI.Selector.__autoHighlight(tag);

                Util.addEvent(tag, 'click', CSS.Selector.modify);

                item.appendChild(tag);

                if (idStr = struct[n].id) {
                    var id = Util.createElement('span', 'chameleon-id-attr-' + n);
                    id.selectorValue = struct[n].tagname + '#' + idStr; 
                    id.position = n;      
                    id.appendChild(document.createTextNode('#' + idStr));

                    UI.Selector.__autoHighlight(id);

                    Util.addEvent(id, 'click', CSS.Selector.modify);
                    item.appendChild(id);
                }

                if (struct[n].classname) {
                    var classArr = struct[n].classname.split(' ');
                    for (var i = 0; i < classArr.length; ++i) {
                        var cn = Util.createElement('span', 'chameleon-class-attr-' + n + '-' + i);
                        cn.selectorValue = struct[n].tagname + '.' + classArr[i];
                        cn.position = n;      
                        cn.appendChild(document.createTextNode('.' + classArr[i]));

                        UI.Selector.__autoHighlight(cn);

                        Util.addEvent(cn, 'click', CSS.Selector.modify);
                        item.appendChild(cn);
                    }
                }
                if (struct[n].tagname == 'a') {
                    for (var i = 0; i < pseudoClasses.length; ++i) {
                        var pc = Util.createElement('span', 'chameleon-pseudo-class' + n + '-' + i);
                        pc.selectorValue = struct[n].tagname + ':' + pseudoClasses[i];

                        pc.position = n;      
                        pc.appendChild(document.createTextNode(':' + pseudoClasses[i]));

                        UI.Selector.__autoHighlight(pc);

                        Util.addEvent(pc, 'click', CSS.Selector.modify);
                        item.appendChild(pc);
                    }
                }
                
                if (showComputedStyle) {
                    var sides = ['top', 'right', 'bottom', 'left'];
                    
                    if (document.defaultView && document.defaultView.getComputedStyle) {
                        if (showComputedStyle == 'margin' || showComputedStyle == 'padding') {
                            var styleVal = [];
                            for (var i = 0; i < 4; ++i) {                                
                                styleVal.push(document.defaultView.getComputedStyle(struct[n].el, null).getPropertyValue(showComputedStyle + '-' + sides[i]))
                            }
                            
                            if (styleVal[0] == styleVal[1] && styleVal[1] == styleVal[2] && styleVal[2] == styleVal[3]) {
                                styleVal = styleVal[0];
                            } else if (styleVal[0] == styleVal[2] && styleVal[1] == styleVal[3]) {
                                styleVal = styleVal[0] + ' ' + styleVal[1];
                            } else if (styleVal[1] == styleVal[3]) {
                                styleVal = styleVal[0] + ' ' + styleVal[1] + ' ' + styleVal[2];
                            } else {
                                styleVal = styleVal.join(' ');
                            }
                        } else {                    
                            var styleVal = document.defaultView.getComputedStyle(struct[n].el, null).getPropertyValue(showComputedStyle);
                        }

                        
                        if (styleVal.indexOf('rgb') != -1) {
                            styleVal = UI.Selector.__formatColor(styleVal);
                        }
 
                    } else if (struct[n].el.currentStyle) {
                        var propBits = showComputedStyle.split('-');
                        for (var i = 1; i < propBits.length; ++i) {
                            propBits[i] = propBits[i].charAt(0).toUpperCase() + propBits[i].substring(1);
                        }
                        var styleVal = struct[n].el.currentStyle[propBits.join('')];                       
                    }
                    
                    var sp = Util.createElement('span');
                    sp.className = 'prop-value';
                    sp.appendChild(document.createTextNode(styleVal));
                    
                    item.appendChild(sp);
                }

                
                list.appendChild(item);
            }
            
            return list;
        },
        
        
        __formatColor: function(color) {
            var newColor = '';            
            colorBits = color.replace(/rgb\(|[ \)]/g, '').split(',');
            var hexCol = (colorBits[0] << 16 | colorBits[1] << 8 | colorBits[2]).toString(16);
            while (hexCol.length < 6) {
                hexCol = '0' + hexCol;
            }
            return '#' + hexCol;
        },
        
        
        __editCode: function() {
            var parent = UI.setupPane(UI.Selector.sections, UI.Selector.controlsId, 'chameleon-selector', 'free-edit');
            UI.setupButtons('chameleon-selector-buttons', 'revert', 'save-local', 'save-server');

            var container = Util.createElement('div');
            var textarea = Util.createElement('textarea', 'chameleon-free-edit-all-field');
            
            textarea.style.width = '100%';
            textarea.style.height = '350px';
            Util.addEvent(textarea, 'blur', CSS.FreeEdit.saveComplete);

            container.appendChild(textarea);

            parent.appendChild(container);
            textarea.value = CSS.toString(); // avoid Konqueror bug
        },
        
        
        
        
        __selectorList: function() {
            return Util.createElement('ol', 'chameleon-selector-list');
        },
        
        
        
        
        __createTab: function(str, fn, active, title) {
            var id = 'chameleon-selector-tab-' + str.replace(/ +/, '-').toLowerCase();
            var tab = Util.createElement('td', id);
            tab.appendChild(document.createTextNode(((title) ? title : str)));
            tab.className = (active) ? 'chameleon-selector-tab-active' : 'chameleon-selector-tab';
            Util.addEvent(tab, 'click', fn);
            return tab;
        },

        __addButtons: function() {
            var p = Util.createElement('p', 'chameleon-selector-buttons');
            p.style.textAlign = 'right';

            p.appendChild(UI.createButton('chameleon-selector-buttons-check', 'Compare', 'Check for other similar selectors already in your styles', CSS.checkSpec));
            p.appendChild(UI.createButton('chameleon-selector-buttons-revert', 'Revert', 'Revert to the version currently on the server', CSS.hardReset));
            p.appendChild(UI.createButton('chameleon-selector-buttons-save-local', 'Save Temp', 'Save these changes to a temporary file on the server', CSS.updateTemp));
            p.appendChild(UI.createButton('chameleon-selector-buttons-save-server', 'Save Server', 'Save these changes to the server', CSS.updateRemote))
            p.appendChild(UI.createButton('chameleon-selector-buttons-edit', 'Set Styles', 'Create and edit styles for this CSS selector', UI.CSS.editWindow));

            return p;
        },
        
        
        
        
        __autoHighlight: function(el) {
            if (CSS.Selector.full[el.position] && CSS.Selector.full[el.position].val == el.selectorValue) {
                UI.Selector.highlight(el);
            } else {
                UI.Selector.unhighlight(el);
            }
        },
        
        highlight: function(el) {
            UI.Selector.unhighlight(el);
            el.className += 'active-selector';
        },

        unhighlight: function(el) {
            el.className = el.className.replace(/\bactive-selector\b/, '');
        },
        
        
        
        
        displaySelector: function(selector) {
            var n = selector.length;

            var list = document.getElementById('chameleon-selector-list');
            if (!list && n != 0) {
                var parent = document.getElementById(UI.Selector.controlsId).firstChild;
                list = UI.Selector.__selectorList();
                parent.appendChild(list);
            } else if (list && n == 0) {
                Util.removeElement(list);
            } else if (list) {
                while (list.hasChildNodes()) {
                    Util.removeElement(list.firstChild);
                }
            }

            if (n == 0) return;

            var item = Util.createElement('li');
            item.appendChild(document.createTextNode('Style ' + UI.Selector.__describe(selector[--n])));
            list.appendChild(item);
            while (n--) {
                item = Util.createElement('li');
                item.appendChild(document.createTextNode('That are descended from ' + UI.Selector.__describe(selector[n])));
                list.appendChild(item);
            }
            
            UI.setOverflow(list, 100);
        },

        __describe: function(txt) {
            if (!txt) return '';
            
            if (txt.indexOf(':') != -1) {
                var parts = txt.split(':');
                var pc = ' the "' + parts.pop() + '" state of ';
                txt = parts.shift();
            } else {
                var pc = '';
            }

            if (txt.indexOf('#') != -1) {
                var parts = txt.split('#');
                return pc + parts[0] + ' tags with the id "' + parts[1] + '"';
            }
            if (txt.indexOf('.') != -1) {
                var parts = txt.split('.');
                return pc + parts[0] + ' tags with the class "' + parts[1] + '"';
            }
            return pc + txt + ' tags';
        }
    };
    
    
    
    UI.CSS = {
        redraw: null,
        colorType: null,
        controlsId: 'chameleon-style-controls',
        sections: ['text', 'backgrounds', 'borders-all', 'borders-separate', 'free-edit'],
        
        __borderEditGroup: true,
        
        editWindow: function(e) {
            if (CSS.Selector.get() == '') {
                UI.statusMsg('First you have to choose which item to style!', 'chameleon-notice');
                return;
            }
            
            var box = document.getElementById('chameleon-style-box');
            if (box) UI.closeBoxes(true, box);

            var coords = Pos.getElement(document.getElementById('chameleon-selector-box'));
            var box = UI.makeDraggableBox('chameleon-style-box', coords.x + UI.boxOffsetX, coords.y + UI.boxOffsetY);

            var instructions = Util.createElement('p');
            if (!hotspotMode) {
                instructions.appendChild(document.createTextNode('Add/Edit styles for the CSS selector "' + CSS.Selector.get() + '"'));
            } else {
                instructions.appendChild(document.createTextNode('Add/Edit styles for ' + UI.HotSpots.getString()));
            }
            instructions.className = 'chameleon-instructions';
            box.appendChild(instructions);

            var tabsContainer = Util.createElement('table', 'chameleon-style-tabs');
            var tabsBody = Util.createElement('tbody');
            var tabs = Util.createElement('tr');
 
            tabs.appendChild(UI.CSS.__createTab('Text', UI.CSS.__editText, true, 'Text'));
            tabs.appendChild(UI.CSS.__createTab('Backgrounds', UI.CSS.__editBackgrounds, false, 'Backgrounds'));
            tabs.appendChild(UI.CSS.__createTab('Borders (All)', UI.CSS.__editBordersAll, false, 'Borders (All)'));
            tabs.appendChild(UI.CSS.__createTab('Borders (Separate)', UI.CSS.__editBordersSeparate, false, 'Borders (Separate)'));
            tabs.appendChild(UI.CSS.__createTab('Free Edit', UI.CSS.__editCode, false, 'Free Edit'));

            tabsBody.appendChild(tabs);
            tabsContainer.appendChild(tabsBody);

            box.appendChild(tabsContainer);

            var styleControls = Util.createElement('div', UI.CSS.controlsId);
            box.appendChild(styleControls);
            box.appendChild(UI.CSS.__addButtons());

            UI.addToDoc(box);
            
            UI.CSS.__editText();
        },
        
        
        
        launchEditWindow: function(e) {
            var target = e.target || e.srcElement;
            CSS.Selector.set(target.value);
            UI.CSS.editWindow(e);
        },
        
        
        __editText: function(e, redraw) {
            UI.CSS.redraw = arguments.callee;
            UI.CSS.colorType = 'color';

            var containerTable = document.getElementById('chameleon-style-edit-text-container');
            if (!containerTable) {
                var parent = UI.setupPane(UI.CSS.sections, UI.CSS.controlsId, 'chameleon-style', 'text');
                containerTable = Util.createElement('table', 'chameleon-style-edit-text-container');
                var container = Util.createElement('tbody');

                var row = UI.CSS.__inputField('color', '-input-color', Check.color);
                container.appendChild(row.node);

                row = UI.CSS.__selectBox('font-family', '-select-font-family', Check.fontFamily, Config.FONTS_LIST);
                container.appendChild(row.node);        

                row = UI.CSS.__inputField('font-family', '-input-font-family', Check.fontFamily, !row.meta.sel);
                container.appendChild(row.node);

                row = UI.CSS.__inputField('font-size', '-input-font-size', Check.fontSize);
                container.appendChild(row.node);

                row = UI.CSS.__inputField('line-height', '-input-line-height', Check.lineHeight);
                container.appendChild(row.node);

                row = UI.CSS.__selectBox('font-weight', '-select-font-weight', Check.fontWeight, Config.FONT_WEIGHTS);
                container.appendChild(row.node);

                row = UI.CSS.__selectBox('font-style', '-select-font-style', Check.fontStyle, Config.FONT_STYLES);
                container.appendChild(row.node);
                
                row = UI.CSS.__selectBox('text-align', '-select-text-align', Check.textAlign, Config.TEXT_ALIGN);
                container.appendChild(row.node);

                row = UI.CSS.__selectBox('text-decoration', '-select-text-decoration', Check.textDecoration, Config.TEXT_DECORATION);
                container.appendChild(row.node);

                containerTable.appendChild(container);
                parent.appendChild(containerTable);
            } else {
                if (redraw == 'color') {
                    UI.CSS.__setColorDisplay(UI.CSS.colorType, UI.CSS.__getPropValue(UI.CSS.colorType));
                }
            }
        },
        
        __editBackgrounds: function(e, redraw) {
            UI.CSS.redraw = arguments.callee;
            UI.CSS.colorType = 'background-color';

            var containerTable = document.getElementById('chameleon-style-edit-backgrounds-container');
            if (!containerTable) {
                var parent = UI.setupPane(UI.CSS.sections, UI.CSS.controlsId, 'chameleon-style', 'backgrounds');
                containerTable = Util.createElement('table', 'chameleon-style-edit-backgrounds-container');
                var container = Util.createElement('tbody');

                var row = UI.CSS.__inputField('background-color', '-input-background-color', Check.color);
                container.appendChild(row.node);

                row = UI.CSS.__inputField('background-image', '-input-background-image', Check.backgroundImage);
                container.appendChild(row.node);

                var extraFields = row.meta;
                
                row = UI.CSS.__selectBox('background-repeat', '-select-background-repeat', Check.backgroundRepeat, Config.REPEAT_LIST, !extraFields);
                container.appendChild(row.node);

                row = UI.CSS.__selectBox('background-position', '-select-background-position', Check.backgroundPosition, Config.POSITION_LIST, !extraFields);
                container.appendChild(row.node);   

                containerTable.appendChild(container);
                parent.appendChild(containerTable);
            } else {
                if (redraw == 'color') {
                    UI.CSS.__setColorDisplay(UI.CSS.colorType, UI.CSS.__getPropValue(UI.CSS.colorType));
                } else if (redraw == 'image') {
                    var val = UI.CSS.__getPropValue('background-image');
                    UI.CSS.__setImageDisplay(val);
                    if (val == 'none' || val == '') {
                        document.getElementById(UI.CSS.controlsId + '-row-select-background-repeat').style.display = 'none';
                        document.getElementById(UI.CSS.controlsId + '-row-select-background-position').style.display = 'none';
                    } else {
                        try {
                            document.getElementById(UI.CSS.controlsId + '-row-select-background-repeat').style.display = 'table-row';
                            document.getElementById(UI.CSS.controlsId + '-row-select-background-position').style.display = 'table-row';
                        } catch(e) {
                            document.getElementById(UI.CSS.controlsId + '-row-select-background-repeat').style.display = 'block';
                            document.getElementById(UI.CSS.controlsId + '-row-select-background-position').style.display = 'block';
                        }
                    }
                }
            }
            var imgPreview = document.getElementById('chameleon-image-preview');
            if (imgPreview) {
                imgPreview.setAttribute('width', '20');
                imgPreview.setAttribute('height', '20');
            }
        },
        
        __editBordersAll: function(e, redraw) {
            UI.CSS.redraw = arguments.callee;
            UI.CSS.colorType = 'border-color';
                        
            var containerTable = document.getElementById('chameleon-style-edit-borders-all-container');
            if (!containerTable) {
               
                var parent = UI.setupPane(UI.CSS.sections, UI.CSS.controlsId, 'chameleon-style', 'borders-all');
                containerTable = Util.createElement('table', 'chameleon-style-edit-borders-all-container');
                var container = Util.createElement('tbody');

                var row = UI.CSS.__inputField('border-width', '-input-border-width', Check.borderWidth);
                container.appendChild(row.node);
  
                row = UI.CSS.__inputField('border-color', '-input-border-color', Check.color);
                container.appendChild(row.node);
             
                row = UI.CSS.__selectBox('border-style', '-select-border-style', Check.borderStyle, Config.BORDER_LIST);
                container.appendChild(row.node);

                containerTable.appendChild(container);
                parent.appendChild(containerTable);
            } else {
                if (redraw == 'color') {
                    UI.CSS.__setColorDisplay(UI.CSS.colorType, UI.CSS.__getPropValue(UI.CSS.colorType));
                }
            }
        },
        
        __editBordersSeparate: function(e, redraw) {
            UI.CSS.redraw = arguments.callee;
            
            var containerTable = document.getElementById('chameleon-style-edit-borders-separate-container');
            if (!containerTable) {
                var parent = UI.setupPane(UI.CSS.sections, UI.CSS.controlsId, 'chameleon-style', 'borders-separate');
                containerTable = Util.createElement('table', 'chameleon-style-edit-borders-separate-container');
                var container = Util.createElement('tbody');

                var row = UI.CSS.__inputField('border-top-width', '-input-border-top-width', Check.borderWidth);
                container.appendChild(row.node);
  
                row = UI.CSS.__inputField('border-top-color', '-input-border-top-color', Check.color, false, UI.CSS.__setColorType);
                container.appendChild(row.node);
             
                row = UI.CSS.__selectBox('border-top-style', '-select-border-top-style', Check.borderStyle, Config.BORDER_LIST);
                container.appendChild(row.node); 


                row = UI.CSS.__inputField('border-right-width', '-input-border-right-width', Check.borderWidth);
                container.appendChild(row.node);
 
                row = UI.CSS.__inputField('border-right-color', '-input-border-right-color', Check.color, false, UI.CSS.__setColorType);
                container.appendChild(row.node);

                row = UI.CSS.__selectBox('border-right-style', '-select-border-right-style', Check.borderStyle, Config.BORDER_LIST);
                container.appendChild(row.node);  

 
                row = UI.CSS.__inputField('border-bottom-width', '-input-border-bottom-width', Check.borderWidth);
                container.appendChild(row.node);

                row = UI.CSS.__inputField('border-bottom-color', '-input-border-bottom-color', Check.color, false, UI.CSS.__setColorType);
                container.appendChild(row.node);

                row = UI.CSS.__selectBox('border-bottom-style', '-select-border-bottom-style', Check.borderStyle, Config.BORDER_LIST);
                container.appendChild(row.node);   

 
                row = UI.CSS.__inputField('border-left-width', '-input-border-left-width', Check.borderWidth);
                container.appendChild(row.node);

                row = UI.CSS.__inputField('border-left-color', '-input-border-left-color', Check.color, false, UI.CSS.__setColorType);
                container.appendChild(row.node);

                row = UI.CSS.__selectBox('border-left-style', '-select-border-left-style', Check.borderStyle, Config.BORDER_LIST);
                container.appendChild(row.node);
                
                containerTable.appendChild(container);
                parent.appendChild(containerTable);
            } else {
                if (redraw == 'color') {
                    UI.CSS.__setColorDisplay(UI.CSS.colorType, UI.CSS.__getPropValue(UI.CSS.colorType));
                }
            }
        },
        
        __editCode: function(e) {
            UI.CSS.redraw = arguments.callee;

            var parent = UI.setupPane(UI.CSS.sections, UI.CSS.controlsId, 'chameleon-style', 'free-edit');

            var container = Util.createElement('div');
            var textarea = Util.createElement('textarea', 'chameleon-free-edit-field');
            
            textarea.style.width = '100%';
            textarea.style.height = '350px';

            Util.addEvent(textarea, 'focus', CSS.FreeEdit.setInitial);
            Util.addEvent(textarea, 'blur', CSS.FreeEdit.saveSelector);

            container.appendChild(textarea);
            parent.appendChild(container);
            textarea.value = CSS.getSelectorCSS(); // avoid Konqueror bug
        },
        
        
        
        
        
        
        
        __getPropValue: function(prop) {
            var val = UI.CSS.__getBorderPropValue(prop);
            if (val === '') {
                return false;
            }
            
            if (val === false) {
                val = CSS.getPropValue(prop);
            }
            return val;
        },
        
        
        __setColorDisplay: function(prop, value, field, picker) {
            if (!field) var field = document.getElementById(UI.CSS.controlsId + '-input-' + prop);
            if (!picker) var picker = document.getElementById(UI.CSS.controlsId + '-color-picker-' + prop);
            
            if (!field || !picker) return;
            
            field.value = value;
            try {
                picker.style.backgroundColor = (value != '') ? value.replace(/[ ]*\!important/, '') : '#000';
                if (!picker.style.backgroundColor) {
                    UI.statusMsg(value + ' is an Invalid color!', 'chameleon-error');
                }
            } catch(e) {
                UI.statusMsg(value + ' is an Invalid color!', 'chameleon-error');
            }
        },
        
        __setImageDisplay: function(value, field, picker) {
            if (!field) var field = document.getElementById(UI.CSS.controlsId + '-input-background-image');
            if (!picker) var picker = document.getElementById(UI.CSS.controlsId + '-background-image-picker');
            
            var preview = document.getElementById('chameleon-image-preview');
            
            if (!field || !picker) return;
            
            field.value = value;
            if (value != '') {
                if (!preview) {
                    preview = Util.createElement('img', 'chameleon-image-preview');
                    picker.appendChild(preview);
                }
                
                if (field.value != 'none') {
                    preview.setAttribute('src', CSS.fixPath(value.replace(/[ ]*\!important/, '')));
                } else {
                    preview.setAttribute('src', CSS.fixPath('ui/images/none.gif'));
                }
                preview.setAttribute('title', 'Open image picker');
                Util.addEvent(preview, 'click', UI.CSS.__loadImagePicker);

                picker.style.backgroundColor = 'transparent';
            } else {
                if (preview) {
                    Util.removeElement(preview);
                }
                picker.style.backgroundColor = '#000';
                picker.setAttribute('title', 'Open image picker');
                Util.addEvent(picker, 'click', UI.CSS.__loadImagePicker);
            }
            
            
        },
        
        __shorthandWarningIcon: function() {
             var img = Util.createElement('img');
             img.setAttribute('src', CSS.fixPath('ui/images/notice.gif'));
             img.style.margin = '0 2px -5px 0';
             img.setAttribute('title', 'Currently this property has specific values set for one or more individual sides. Updating the value here will set this property for all sides, overwriting these individual values.');  
             return img;
        },
        
        __inputField: function(prop, id, validate, hidden, init) {
            var row = Util.createElement('tr', UI.CSS.controlsId + '-row' + id);
            id = UI.CSS.controlsId + id;
            
            var labelCell = Util.createElement('td');
            var fieldCell = Util.createElement('td');

            var field = Util.createElement('input', id);
            field.setAttribute('type', 'text');
            field.className = 'chameleon-input-text';
            
            
            var val = UI.CSS.__getPropValue(prop);
            if (val !== false) {
                field.value = val;
            } else {
                labelCell.appendChild(UI.CSS.__shorthandWarningIcon());
            }
            
            Util.addEvent(field, 'blur', validate);
            if (init) {
                Util.addEvent(field, 'focus', init);
            }
            
            labelCell.appendChild(document.createTextNode(UI.CSS.__formatProp(prop) + ': '));
            labelCell.className = 'label';

            fieldCell.appendChild(field);
             
            row.appendChild(labelCell);
            row.appendChild(fieldCell);
            
            if (prop == 'color' || prop.indexOf('-color') != -1) {
                var colorCell = Util.createElement('td');
                var colorPicker = Util.createElement('div', UI.CSS.controlsId + '-color-picker-' + prop);

                colorPicker.setAttribute('title', 'Open color picker');
                UI.CSS.__setColorDisplay(prop, field.value, field, colorPicker);
                
                Util.addEvent(colorPicker, 'click', UI.CSS.__displayColorPicker);
                if (init) {
                    Util.addEvent(colorPicker, 'click', init);
                }

                colorCell.appendChild(colorPicker);
                row.appendChild(colorCell);
            } else if (prop.indexOf('-image') != -1) {
                var imgCell = Util.createElement('td');
                var imgPicker = Util.createElement('div', UI.CSS.controlsId + '-background-image-picker');
                
                UI.CSS.__setImageDisplay(field.value, field, imgPicker);

                imgCell.appendChild(imgPicker);
                row.appendChild(imgCell);
                
            } else {
                fieldCell.setAttribute('colspan', '2');
            }
            if (hidden) {
                row.style.display = 'none';
            }
            return {node: row, meta: (field.value == 'none') ? false : field.value};
        },
        
        
        __selectBox: function(prop, id, validate, src, hidden) {
            var row = Util.createElement('tr', UI.CSS.controlsId + '-row' + id);
            id = UI.CSS.controlsId + id;

            var labelCell = Util.createElement('td');
            var fieldCell = Util.createElement('td');
            fieldCell.setAttribute('colspan', '2');
            
            var currentValue = UI.CSS.__getPropValue(prop);
            if (currentValue === false) {
                labelCell.appendChild(UI.CSS.__shorthandWarningIcon());
                currentValue = '';
            }

            labelCell.appendChild(document.createTextNode(UI.CSS.__formatProp(prop) + ': '));
            labelCell.className = 'label';

            var field = Util.createElement('select', id);
            var op = Util.createElement('option');
            op.setAttribute('value', '');
            op.appendChild(document.createTextNode('Please select'));
            field.appendChild(op);

            var selected = false;
            var otherSelected = false;
        
            for (var i = 0; i < src.length; ++i) {
                op = Util.createElement('option');
                op.setAttribute('value', src[i]);
                op.appendChild(document.createTextNode(src[i]));
                if (src[i] != 'other' && src[i] == currentValue) {
                    op.setAttribute('selected', 'selected');
                    selected = true;
                } else if (src[i].toLowerCase() == 'other' && currentValue != '' && !selected) {
                    op.setAttribute('selected', 'selected');
                    selected = true;
                    otherSelected = true;
                }
                field.appendChild(op);
            }

            Util.addEvent(field, 'change', validate);

            fieldCell.appendChild(field);
            row.appendChild(labelCell);
            row.appendChild(fieldCell);

            if (hidden) {
                row.style.display = 'none';
            }

            return {node: row, meta: {sel: otherSelected, value: currentValue}};
        },
        
        
        
        __createTab: function(str, fn, active, title) {
            var id = 'chameleon-style-tab-' + str.replace(/[\( ]+/, '-').replace(/[\)]+/, '').toLowerCase();
            var tab = Util.createElement('td', id);
            tab.appendChild(document.createTextNode((title) ? title : str));
            tab.className = (active) ? 'chameleon-style-tab-active' : 'chameleon-style-tab';
            Util.addEvent(tab, 'click', fn);
            return tab;
        },
        
        __addButtons: function() {
            var p = Util.createElement('p', 'chameleon-style-buttons');
            p.style.textAlign = 'right';

            p.appendChild(UI.createButton('chameleon-style-buttons-revert', 'Revert', 'Discard all temporarily saved changes', CSS.hardReset));
            p.appendChild(UI.createButton('chameleon-style-buttons-save-local', 'Save Temp', 'Save these changes in a temporary file on the server', CSS.updateTemp));
            p.appendChild(UI.createButton('chameleon-style-buttons-save-server', 'Save Server', 'Save these changes to the server', CSS.updateRemote));

            return p;
        },
        
        __formatProp: function(txt) {
            if (txt.length > 15 && txt.indexOf('-') != -1) {
                return txt.split('-').slice(1).join('-');
            }
            return txt;
        },




        __loadImagePicker: function(e) {
            var target = e.target || e.srcElement;
        
            if (target.value) {
                UI.statusMsg('Loading file list for ' + target.value + '...', 'chameleon-working');
                FileHandler.getFiles(target.value);
            } else {
                UI.statusMsg('Loading file list...', 'chameleon-working');
                FileHandler.getFiles('root');
            }
        },
        
        displayImagePicker: function(xmldata) {
            UI.clearStatusMsg();

            var box = document.getElementById('chameleon-file-box');
            if (box) UI.closeBoxes(true, box);

            var coords = Pos.getElement(document.getElementById('chameleon-style-box'));
            box = UI.makeDraggableBox('chameleon-file-box', coords.x + UI.boxOffsetX, coords.y + UI.boxOffsetY);
            
            if (xmldata.firstChild.nodeName.toLowerCase() == 'chameleon_error') {
                UI.statusMsg('There was an error reading files from the server:\n' + xmldata.firstChild.firstChild.nodeValue + '.', 'chameleon-error');
                return;
            }
            
            var files = xmldata.firstChild;
            var hasFiles = false;
        
            var infoTable = Util.createElement('table');
            var infoTableBody = Util.createElement('tbody');
            var infoTableRow = Util.createElement('tr');

            var path = files.getAttribute('path');
            if (path.indexOf('/') != -1) {
                var parentPath = path.substring(0, path.lastIndexOf('/'));
                var parentCell = Util.createElement('td');
                var parentLink = Util.createElement('p', 'chameleon-files-parent');
                parentLink.value = parentPath;
                parentLink.className = 'chameleon-image-folder';
                parentLink.appendChild(document.createTextNode('Parent folder'));
                Util.addEvent(parentLink, 'click', UI.CSS.__loadImagePicker);
                parentCell.appendChild(parentLink);
                infoTableRow.appendChild(parentCell);
            } 

            var location = Util.createElement('td', 'chameleon-files-location');
            var locationPara = Util.createElement('p');
            var locationTxt = Util.createElement('span');
            locationTxt.appendChild(document.createTextNode('Location: '));
            locationPara.appendChild(locationTxt);
            locationPara.appendChild(document.createTextNode(path));
            location.appendChild(locationPara);

            infoTableRow.appendChild(location);
            infoTableBody.appendChild(infoTableRow);
            infoTable.appendChild(infoTableBody);
            box.appendChild(infoTable);
        
            var fileList = Util.createElement('div');

            for (var i = 0; i < files.childNodes.length; ++i) {
                if (files.childNodes[i].nodeType != Node.ELEMENT_NODE) {
                    continue;
                }
                hasFiles = true;

                var fileItemContainer = Util.createElement('p');
                var fileItem = Util.createElement('span');
                fileItem.value = files.childNodes[i].firstChild.nodeValue;
                fileItem.appendChild(document.createTextNode(fileItem.value.split('/').pop()));
                if (files.childNodes[i].getAttribute('type') == 'img') {
                    Util.addEvent(fileItem, 'click', Check.backgroundImage);
                } else {
                    fileItemContainer.className = 'chameleon-image-folder';
                    Util.addEvent(fileItem, 'click', UI.CSS.__loadImagePicker);
                }
                fileItemContainer.appendChild(fileItem);
                fileList.appendChild(fileItemContainer);
            }

            if (!hasFiles) {
                var fileItem = Util.createElement('p');
                fileItem.appendChild(document.createTextNode('No images were found in this folder'));
                fileList.appendChild(fileItem);
            }

            box.appendChild(fileList);
            UI.addToDoc(box);

            UI.setOverflow(fileList, 350);
        },
        
        
   
   
        __displayColorPicker: function(e) {
            var box = document.getElementById('chameleon-color-box');
            if (box) UI.closeBoxes(true, box);
            
            var extraColors = ['000000', '333333', '666666', '999999', 'cccccc', 'ffffff', 'ff0000', '00ff00', '0000ff', 'ffff00', 'ff00ff', '00ffff'];

            var coords = Pos.getElement(document.getElementById('chameleon-style-box'));
            box = UI.makeDraggableBox('chameleon-color-box', coords.x + UI.boxOffsetX, coords.y + UI.boxOffsetY);

            var container = Util.createElement('div', 'chameleon-color-palette');
            box.appendChild(container);

            var x = 0; var y = 0; var xx = 0; var yi = 0;
            for (var r = 0; r < 256; r += 51) {
                for (var g = 0; g < 256; g += 51) {
                    for (var b = 0; b < 256; b += 51) {
                        var col = (r << 16 | g << 8 | b).toString(16);
                        while (col.length < 6) {
                            col = '0' + col;
                        }
                        
                        yi = (xx > 17) ? 5 : 0;
                                                
                        var colorTab = Util.createElement('div');
                        colorTab.style.position = 'absolute';
                        colorTab.style.left = ((15 * x) + 17) + 'px';
                        colorTab.style.top = (15 * (yi + y)) + 'px';
                        colorTab.style.width = colorTab.style.height = '15px';
                        colorTab.style.backgroundColor = colorTab.value = '#' + col;

                        colorTab.setAttribute('title', '#' + col);

                        container.appendChild(colorTab);
                        
                        if (x == 17) {
                            x = 0;
                            if (xx == 35) {
                                xx = 0;
                            } else {
                                ++xx;
                                ++y;
                            }
                        } else {
                            ++x;
                            ++xx;
                        }
                    }                
                }
            }
            
            for (var i = 0; i < extraColors.length; ++i) {
                var colorTab = Util.createElement('div');
                colorTab.style.position = 'absolute';
                colorTab.style.left = '0px';
                colorTab.style.top = (15 * i) + 'px';
                colorTab.style.width = colorTab.style.height = '15px';
                colorTab.style.backgroundColor = colorTab.value = '#' + extraColors[i];

                colorTab.setAttribute('title', '#' + extraColors[i]);

                container.appendChild(colorTab);
            }
            
            Util.addEvent(container, 'click', Check.color);

            container.style.height = (((y + yi) * 15) + 20) + 'px';

            UI.addToDoc(box);
        },
        
      
      
        __setColorType: function(e) {
            var target = e.target || e.srcElement;

            UI.CSS.colorType = UI.CSS.getBorderProp(target.id);
        },
        
        
        getBorderProp: function(id) {
            var separators = ['color-picker', 'input', 'select'];
            for (var i = 0; i < separators.length; ++i) {
                if (id.indexOf('-' + separators[i] + '-') != -1) {
                    return id.split('-' + separators[i] + '-').pop();
                }
            }
            return '';
        },

        __getBorderPropValue: function(prop) {
            var matches = prop.match(/^border\-([^\-]+)$/);
            if (matches) {
                var p1 = CSS.getPropValue('border-left-' + matches[1]);
                var p2 = CSS.getPropValue('border-right-' + matches[1]);
                var p3 = CSS.getPropValue('border-top-' + matches[1]);
                var p4 = CSS.getPropValue('border-bottom-' + matches[1]);
                if (!p1 && !p2 && !p3 && !p4) {
                    return false;
                }
                
                if (!(p1 && p2 && p3 && p4)) {
                    return '';
                }
                
                return (p1 == p2 && p2 == p3 && p3 == p4) ?  p1 : ''; 
            }
            return false;
        }
          
    };
   
    
    
    UI.HotSpots = {
        __selectors: null,
        __counter: 0,
        __lookup: {},
        
        init: function() {
            var box = Util.createElement('div', 'chameleon-launch-hotspots');
            box.appendChild(document.createTextNode('Load hotspots'));
            box.style.zIndex = ++UI.zIndex;
            
            box.hotSpotsOn = false;
            Util.addEvent(box, 'click', UI.HotSpots.__load);
            
            UI.addToDoc(box);
        },
        
        getString: function() {
            var sel = CSS.Selector.get();
            if (UI.HotSpots.__selectors[sel]) {
                return UI.HotSpots.__selectors[sel] + '.';
            }
            return '"' + sel + '"';
        },
        
        __load: function(e) {
            var target = e.target || e.srcElement;
            target.hotSpotsOn = !target.hotSpotsOn;
            
            UI.HotSpots.__counter = 0;
            UI.HotSpots.__lookup = {};
            
            if (!target.hotSpotsOn) {
                target.firstChild.nodeValue = 'Show hotspots';
                UI.HotSpots.__clear();
                return;
            }
            target.firstChild.nodeValue = 'Hide hotspots';
          
            if (!UI.HotSpots.__selectors) {
                UI.HotSpots.__selectors = {};
                UI.HotSpots.__selectors['body'] = 'The body of the page (all pages)';
                UI.HotSpots.__selectors['body#site-index'] = 'The body of the homepage';
                UI.HotSpots.__selectors['body#course-view'] = 'The body of the course index page';
                UI.HotSpots.__selectors['div#header'] = 'The page header';
                UI.HotSpots.__selectors['div#header-home'] = 'The page header on the homepage';
                UI.HotSpots.__selectors['div#header-home h1.headermain'] = 'The header text on the homepage';
                UI.HotSpots.__selectors['div#header h1.headermain'] = 'The header text';
                UI.HotSpots.__selectors['div.sideblock'] = 'Blocks';
                UI.HotSpots.__selectors['td#right-column div.sideblock'] = 'Blocks in the right hand column';
                UI.HotSpots.__selectors['td#left-column div.sideblock'] = 'Blocks in the left hand column';
                UI.HotSpots.__selectors['div.sideblock div.header'] = 'The block headings';
                UI.HotSpots.__selectors['td#right-column div.sideblock div.header'] = 'The block headings in the right hand column';
                UI.HotSpots.__selectors['td#left-column div.sideblock div.header'] = 'The block headings in the left hand column';
                UI.HotSpots.__selectors['div.sideblock div.title'] = 'The text in the block headings';
                UI.HotSpots.__selectors['td#right-column div.sideblock div.title'] = 'The text in the block headings in the right hand column';
                UI.HotSpots.__selectors['td#left-column div.sideblock div.title'] = 'The text in the block headings in the left hand column';
                UI.HotSpots.__selectors['div.headingblock'] = 'The heading at the top of the middle column';
                UI.HotSpots.__selectors['table.topics'] = 'The topic sections in a course';
                UI.HotSpots.__selectors['table.topics td.side'] = 'The sides of the topic sections';
                UI.HotSpots.__selectors['table.topics td.left'] = 'The left side of the topic sections';
                UI.HotSpots.__selectors['table.topics td.right'] = 'The right side of the topic sections';
                UI.HotSpots.__selectors['table.topics tr.current div.summary'] = 'The summary of the highlighted topic';
                UI.HotSpots.__selectors['table.topics tr.current td.content'] = 'The content of the highlighted topic';
                UI.HotSpots.__selectors['a'] = 'Links';
                UI.HotSpots.__selectors['a.dimmed'] = 'Greyed out links';
                UI.HotSpots.__selectors['div#footer'] = 'The footer of the page';
                UI.HotSpots.__selectors['div.logininfo'] = 'The "You are logged in as..." text';
                UI.HotSpots.__selectors['div.navbar'] = 'The navigation bar';
                UI.HotSpots.__selectors['div.breadcrumb'] = 'The navigation trail';
                UI.HotSpots.__selectors['table.generaltable tr.r0'] = 'Odd numbered table rows';
                UI.HotSpots.__selectors['table.generaltable tr.r1'] = 'Even numbered table rows';
            }
            
            UI.HotSpots.__parse();
        },
        
        __parse: function() {
            var pos = {};
            
            for (var sel in UI.HotSpots.__selectors) {
                var matches = cssQuery(sel);
                var nm = matches.length;
                if (!nm) {
                    continue;
                }
                
                for (var j = 0; j < nm; ++j) {
                    if (matches[j].hasAttribute && matches[j].hasAttribute('id') && matches[j].getAttribute('id').indexOf('chameleon') != -1) {
                        continue;
                    }
                    
                    if (!matches[j].chameleonHotspotId) {
                        var coords = Pos.getElement(matches[j]);
                        coords.x = 20 * Math.round(coords.x / 20);
                        coords.y = 20 * Math.round(coords.y / 20);
                        
                        while (pos[coords.x + '-' + coords.y]) {
                            coords.x += 20;
                        }
                        pos[coords.x + '-' + coords.y] = true;
                        
                        var button = UI.HotSpots.__makeButton(UI.HotSpots.__selectors[sel], coords.x, coords.y);
                        UI.addToDoc(button);
                        
                        matches[j].chameleonHotspotId = button.id;
                        UI.HotSpots.__lookup[button.id] = sel;
                        break;
                    } else {
                        UI.HotSpots.__lookup[matches[j].chameleonHotspotId] += '|' + sel;
                        document.getElementById(matches[j].chameleonHotspotId).title += ", " + UI.HotSpots.__selectors[sel];
                        
                        break;
                    }
                }
            }
            
            pos = null;
            matches = null;
        },
        
        
        __clear: function() {
            for (var sel in UI.HotSpots.__selectors) {
                var matches = cssQuery(sel);
                var nm = matches.length;
                if (!nm) {
                    continue;
                }
                
                for (var j = 0; j < nm; ++j) {
                    if (matches[j].chameleonHotspotId) {
                        UI.HotSpots.__lookup[matches[j].chameleonHotspotId] = null;
                        Util.removeElement(document.getElementById(matches[j].chameleonHotspotId));
                        matches[j].chameleonHotspotId = null;
                        break;
                    }
                }
            }          
        },
     
        
        __makeButton: function(title, x, y) {
            var d = Util.createElement('img', 'chameleon-hotspot-' + ++UI.HotSpots.__counter);
            d.style.width = d.style.height = '20px';
            d.style.position = 'absolute';
            d.style.left = (x - 5) + 'px';
            d.style.top = (y + 15) + 'px';
            d.style.cursor = 'pointer';
            
            d.setAttribute('src', CSS.fixPath('ui/images/hotspot.gif'));
            d.setAttribute('title', title);
            Util.addEvent(d, 'click', UI.HotSpots.__launch);
            return d;
        },
        
        __launch: function(e) {
            var target = e.target || e.srcElement;
            var selectors = UI.HotSpots.__lookup[target.id].split('|');
                       
            var coords = Pos.getMouse(e);
            
            hotspotMode = true;
                
            var box = document.getElementById('chameleon-selector-box');
            if (box) UI.closeBoxes(true, box);
                
            var box = UI.makeDraggableBox('chameleon-selector-box', coords.x, coords.y);
            
            if (selectors.length > 1) {
                var instructions = Util.createElement('p');
                instructions.appendChild(document.createTextNode('This element matches more than one selector, please choose which you would like to style.'));
                instructions.className = 'chameleon-instructions';
                box.appendChild(instructions);
            }
            
            var selList = Util.createElement('ul');
            for (var i = 0; i < selectors.length; ++i) {
                var item = Util.createElement('li');
                var itemLink = Util.createElement('a');
                itemLink.appendChild(document.createTextNode('Add/Edit styles for ' + UI.HotSpots.__selectors[selectors[i]]));
                itemLink.value = selectors[i];
                Util.addEvent(itemLink, 'click', UI.HotSpots.__launchCSSEditor);
                    
                item.appendChild(itemLink);
                    
                selList.appendChild(item);
                    
                box.appendChild(selList);   
            }
            UI.addToDoc(box);
        },
        
        __launchCSSEditor: function(e, value) {
            var target = e.target || e.srcElement;
            
            if (!value) {
                var value = target.value;
            }
            CSS.Selector.set(value);
            UI.CSS.editWindow(e);
        }
        
    };
   

    
   
    
    
    var Check = {
        color: function(e) {
            var target = e.target || e.srcElement;
            if (e.type == 'click' && !target.value) return;
            
            var originalColor = UI.CSS.__getPropValue(UI.CSS.colorType);
            if (originalColor != target.value) {
                CSS.setPropValue(UI.CSS.colorType, target.value);
                UI.CSS.redraw.call(null, null, 'color');
            }
            if (e.type == 'click') {
                UI.closeBoxes(true, target.parentNode.parentNode);
            }
        },
        
        backgroundImage: function(e) {
            var target = e.target || e.srcElement;

            CSS.setPropValue('background-image', target.value);
            UI.CSS.redraw.call(null, null, 'image');
            if (e.type == 'click') {
                UI.closeBoxes(true, document.getElementById('chameleon-file-box'));
            }
        },   
        
        backgroundRepeat: function(e) {
            var target = e.target || e.srcElement;
            var value = target.options[target.options.selectedIndex].value.toLowerCase();
            CSS.setPropValue('background-repeat', value);
        },
        
        backgroundPosition: function(e) {
            var target = e.target || e.srcElement;
            var value = target.options[target.options.selectedIndex].value.toLowerCase();
            CSS.setPropValue('background-position', value);
        },
        
        borderWidth: function(e) {
            var target = e.target || e.srcElement;

            var hasUnits = false;
            for (var i = 0; i < Config.UNITS.length; ++i) {
                if (target.value.indexOf(Config.UNITS[i]) > 0) {
                    hasUnits = true;
                    break;
                }
            }

            var val = parseInt(target.value);
            if (isNaN(val)) {
                if (!target.value.match(/thin|medium|thick/)) {
                    target.value = '';
                }
            } else if (!hasUnits) {
                target.value = val + 'px';
            }
            CSS.setPropValue(UI.CSS.getBorderProp(target.id), target.value);  
        },
        
        borderStyle: function(e) {
            var target = e.target || e.srcElement;
            var value = target.options[target.options.selectedIndex].value.toLowerCase();
            CSS.setPropValue(UI.CSS.getBorderProp(target.id), value);
        },
        
        fontStyle: function(e) {
            var target = e.target || e.srcElement;
            var value = target.options[target.options.selectedIndex].value.toLowerCase();
            CSS.setPropValue('font-style', value);
        },
        
        fontWeight: function(e) {
            var target = e.target || e.srcElement;
            var value = target.options[target.options.selectedIndex].value.toLowerCase();
            CSS.setPropValue('font-weight', value);
        },
        
        fontSize: function(e) {
            var target = e.target || e.srcElement;
            CSS.setPropValue('font-size', target.value);
        },
        
        lineHeight: function(e) {
            var target = e.target || e.srcElement;
            CSS.setPropValue('line-height', target.value);
        },
        
        fontFamily: function(e) {
            var target = e.target || e.srcElement;
            var n = target.nodeName.toLowerCase();
            
            if (n == 'select') {
                var value = target.options[target.options.selectedIndex].value.toLowerCase();
                var fontFamilyInputRow = target.parentNode.parentNode.nextSibling;
                if (value == 'other') {
                   try {
                       fontFamilyInputRow.style.display = 'table-row';
                   } catch(e) {
                       fontFamilyInputRow.style.display = 'block';
                   }
                } else {
                   if (value != '') {
                       fontFamilyInputRow.style.display = 'none';
                   }
                   CSS.setPropValue('font-family', value);
                }
            } else {
                CSS.setPropValue('font-family', target.value);
            }
        },
        
        textDecoration: function(e) {
            var target = e.target || e.srcElement;
            var value = target.options[target.options.selectedIndex].value.toLowerCase();
            CSS.setPropValue('text-decoration', value);
        },
        
        textAlign: function(e) {
            var target = e.target || e.srcElement;
            var value = target.options[target.options.selectedIndex].value.toLowerCase();
            CSS.setPropValue('text-align', value);
        }
    };
    
    













    var debugMsg = function(msg) {
        //if (window.opera) window.opera.postError(msg);
    };




    var climbTree = function(src) {
        var struct = [];
        while (src.parentNode) {
            if (src.nodeType == Node.ELEMENT_NODE) {
                if (src.getAttribute && src.getAttribute('id') && src.getAttribute('id').indexOf('chameleon-') != -1) {
                    return src.getAttribute('id');
                }
                var elementObj = {tagname: src.nodeName.toLowerCase()};
                if (src.getAttribute && src.getAttribute('id')) {
                    elementObj.id = src.getAttribute('id');
                }
                if (src.className) {
                    elementObj.classname = src.className;
                }
                elementObj.el = src;
                struct.push(elementObj);
            }
            src = src.parentNode;
        }
        return struct;
    };



    var setup = function() {
        UI.clearStatusMsg();
        
        // UI.HotSpots.init();
        
        var crumb = new cookie('chameleon_server_save_required');
        if (crumb.read() == 1) {
            CSS.requireRemoteSave();
        }
        
        Util.addEvent(window, 'unload', CSS.unloadPrompt);
        Util.addEvent(window, 'unload', Util.cleanUp);
        Util.addEvent(document, 'mousedown', UI.Selector.editWindow);
        
        //CSS.clearTheme();
    };

    var startSetup = function() {
        UI.statusMsg('Chameleon is loading...');

        if (!CSS.loadRemote(true)) {
            alert('Your browser must support XMLHttpRequest! Supported browsers include Internet Explorer, Mozilla Firefox, Safari and Opera');
        }
    };

    Util.addEvent(window, 'load', startSetup);

})();
