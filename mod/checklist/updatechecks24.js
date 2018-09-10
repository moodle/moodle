/*global M*/
/*global window*/
M.mod_checklist = {
    init: function (Y, url, sesskey, cmid, updateprogress) {
        "use strict";
        Y.use('yui2-dom', 'yui2-event', 'yui2-connection', 'yui2-animation', function (Y) {
            var chk, YAHOO = Y.YUI2;
            chk = {
                serverurl: null,
                sesskey: null,
                cmid: null,
                updateprogress: 1,
                updatelist: null,
                updatetimeout: null,
                requiredcount: 0,
                optionalcount: 0,
                requiredchecked: 0,
                optionalchecked: 0,
                anim1: null,
                anim2: null,
                Y: null,

                init: function (Y, url, sesskey, cmid, updateprogress) {
                    this.Y = Y;
                    this.serverurl = url;
                    this.sesskey = sesskey;
                    this.cmid = cmid;
                    this.updateprogress = updateprogress;

                    var YE, YD, items, i, self = this;
                    YE = YAHOO.util.Event;
                    YD = YAHOO.util.Dom;

                    this.updatelist = [];
                    items = YD.getElementsByClassName('checklistitem');
                    for (i = 0; i<items.length; i += 1) {
                        YE.addListener(items[i], 'click', this.check_click, null, this);
                        if (YD.hasClass(items[i], 'itemoptional')) {
                            this.optionalcount += 1;
                            if (items[i].checked) {
                                this.optionalchecked += 1;
                            }
                        } else {
                            this.requiredcount += 1;
                            if (items[i].checked) {
                                this.requiredchecked += 1;
                            }
                        }
                    }

                    window.onunload = function () {
                        self.send_update_batch(true);
                    };
                },

                check_click: function (e) {
                    var YD = YAHOO.util.Dom, change, el;

                    el = e.currentTarget || e.srcElement;
                    // Update progress bar
                    if (this.updateprogress) {
                        change = -1;
                        if (el.checked) {
                            change = 1;
                        }
                        if (YD.hasClass(el, 'itemoptional')) {
                            this.optionalchecked += change;
                        } else {
                            this.requiredchecked += change;
                        }
                        this.update_progress_bar();
                    }

                    // Save check to list for updating
                    this.update_server(el.value, el.checked);
                },

                startanim: function (number, ya) {
                    if (number === 1) {
                        if (this.anim1) {
                            this.anim1.stop();
                        }
                        this.anim1 = ya;
                        this.anim1.animate();
                    } else if (number === 2) {
                        if (this.anim2) {
                            this.anim2.stop();
                        }
                        this.anim2 = ya;
                        this.anim2.animate();
                    }
                },

                update_progress_bar: function () {
                    var YD, YA, YE, prall, prreq, allpercent, inner, inneranim, oldpercent, oldanimpercent,
                        disppercent, reqpercent;
                    YD = YAHOO.util.Dom;
                    YA = YAHOO.util.Anim;
                    YE = YAHOO.util.Easing.easeOut;
                    prall = YD.get('checklistprogressall');
                    prreq = YD.get('checklistprogressrequired');

                    allpercent = (this.optionalchecked + this.requiredchecked) * 100.0 / (this.optionalcount + this.requiredcount);
                    inner = YD.getElementsByClassName('checklist_progress_inner', 'div', prall)[0];
                    inneranim = YD.getElementsByClassName('checklist_progress_anim', 'div', prall)[0];
                    oldpercent = parseFloat(YD.getStyle(inner, 'width').replace("%", ""));
                    if (allpercent>oldpercent) {
                        YD.setStyle(inneranim, 'width', allpercent + '%');
                        this.startanim(1, new YA(inner, {width: {from: oldpercent, to: allpercent, unit: '%'}}, 1, YE));
                    } else if (allpercent<oldpercent) {
                        YD.setStyle(inner, 'width', allpercent + '%');
                        oldanimpercent = parseFloat(YD.getStyle(inneranim, 'width').replace("%", ""));
                        this.startanim(1, new YA(inneranim, {width: {from: oldanimpercent, to: allpercent, unit: '%'}}, 1, YE));
                    }
                    disppercent = YD.getElementsByClassName('checklist_progress_percent', 'span', prall)[0];
                    disppercent.innerHTML = '&nbsp;' + allpercent.toFixed(0) + '% ';

                    if (prreq) {
                        reqpercent = this.requiredchecked * 100.0 / this.requiredcount;
                        inner = YD.getElementsByClassName('checklist_progress_inner', 'div', prreq)[0];
                        inneranim = YD.getElementsByClassName('checklist_progress_anim', 'div', prreq)[0];
                        oldpercent = parseFloat(YD.getStyle(inner, 'width').replace("%", ""));
                        if (reqpercent>oldpercent) {
                            YD.setStyle(inneranim, 'width', reqpercent + '%');
                            this.startanim(2, new YA(inner, {width: {from: oldpercent, to: reqpercent, unit: '%'}}, 1, YE));
                        } else if (reqpercent<oldpercent) {
                            YD.setStyle(inner, 'width', reqpercent + '%');
                            oldanimpercent = parseFloat(YD.getStyle(inneranim, 'width').replace("%", ""));
                            this.startanim(2, new YA(inneranim, {width: {from: oldanimpercent, to: reqpercent, unit: '%'}}, 1, YE));
                        }

                        disppercent = YD.getElementsByClassName('checklist_progress_percent', 'span', prreq)[0];
                        disppercent.innerHTML = '&nbsp;' + reqpercent.toFixed(0) + '% ';
                    }

                },

                update_server: function (itemid, state) {
                    var i, self = this;
                    for (i = 0; i<this.updatelist.length; i += 1) {
                        if (this.updatelist[i].itemid === itemid) {
                            if (this.updatelist[i].state !== state) {
                                this.updatelist.splice(i, 1);
                            }
                            return;
                        }
                    }

                    this.updatelist.push({'itemid': itemid, 'state': state});

                    if (this.updatetimeout) {
                        window.clearTimeout(this.updatetimeout);
                    }
                    this.updatetimeout = window.setTimeout(function () {
                        self.send_update_batch(false);
                    }, 500);
                    this.show_spinner();
                },

                send_update_batch: function (unload) {
                    var params, i, val, self = this, callback, YC, beacon;
                    // Send all updates after 1 second of inactivity (or on page unload)
                    if (this.updatetimeout) {
                        window.clearTimeout(this.updatetimeout);
                        this.updatetimeout = null;
                    }

                    if (this.updatelist.length === 0) {
                        return;
                    }

                    params = [];
                    for (i = 0; i<this.updatelist.length; i += 1) {
                        val = this.updatelist[i].state ? 1 : 0;
                        params.push('items[' + this.updatelist[i].itemid + ']=' + val);
                    }
                    params.push('sesskey=' + this.sesskey);
                    params.push('id=' + this.cmid);
                    params = params.join('&');

                    // Clear the list of updates to send
                    this.updatelist = [];

                    // Send message to server
                    if (!unload) {
                        callback = {
                            success: function (o) {
                                self.hide_spinner();
                                if (o.responseText !== 'OK') {
                                    window.alert(o.responseText);
                                }
                            },
                            failure: function (o) {
                                self.hide_spinner();
                                window.alert(o.statusText);
                            },
                            timeout: 5000
                        };

                        YC = YAHOO.util.Connect;
                        YC.asyncRequest('POST', this.serverurl, callback, params);
                    } else {
                        // Nasty hack to make it save everything on unload
                        beacon = new window.Image();
                        beacon.src = this.serverurl + '?' + params;
                    }
                },

                show_spinner: function () {
                    this.Y.one('#checklistspinner').setStyle('display', 'block');
                },

                hide_spinner: function () {
                    this.Y.one('#checklistspinner').setStyle('display', 'none');
                }
            };

            chk.init(Y, url, sesskey, cmid, updateprogress);
        });
    }
};
