function dhtmlXCalendarObject(inps, skin) {
	
	// parse inputs
	this.i = {};
	
	this.uid = function() {
		if (!this.uidd) this.uidd = new Date().getTime();
		return this.uidd++;
	}
	
	var p = null;
	if (typeof(inps) == "string") {
		var t0 = document.getElementById(inps);
	} else {
		var t0 = inps;
	}
	if (t0 && typeof(t0) == "object" && t0.tagName && String(t0.tagName).toLowerCase() != "input") p = t0;
	t0 = null;
	
	// single param
	if (typeof(inps) != "object" || !inps.length) inps = [inps];
	for (var q=0; q<inps.length; q++) {
		if (typeof(inps[q]) == "string") inps[q] = (document.getElementById(inps[q])||null);
		if (inps[q] != null && inps[q].tagName && String(inps[q].tagName).toLowerCase() == "input") {
			this.i[this.uid()] = inps[q];
		}
		inps[q] = null;
	}
	
	this.skin = (skin != null ? skin : (typeof(dhtmlx) != "undefined" && typeof(dhtmlx.skin) == "string" ? dhtmlx.skin : "dhx_skyblue"));
	
	this.setSkin = function(skin){
		this.skin = skin;
		this.base.className = "dhtmlxcalendar_container dhtmlxcalendar_skin_"+this.skin;
		this._ifrSize();
	}
	
	// create base
	this.base = document.createElement("DIV");
	this.base.className = "dhtmlxcalendar_container";
	this.base.style.display = "none";
	
	if (p != null) {
		this._hasParent = true;
		p.appendChild(this.base);
		p = null;
	} else { 
		document.body.appendChild(this.base);
	}
	
	this.setParent = function(p) {
		if (this._hasParent) {
			if (typeof(p) == "object") {
				p.appendChild(this.base);
			} else if (typeof(p) == "string") {
				document.getElementById(p).appendChild(this.base);
			}
		}
	}
	
	this.setSkin(this.skin);
	
	this.base.onclick = function(e) {
		e = e||event;
		e.cancelBubble = true;
	}
	
	this.loadUserLanguage = function(lang) {
		if (!this.langData[lang]) return;
		this.lang = lang;
		this.setWeekStartDay(this.langData[this.lang].weekstart);
		// month selector
		if (this.msCont) {
			var e = 0;
			for (var q=0; q<this.msCont.childNodes.length; q++) {
				for (var w=0; w<this.msCont.childNodes[q].childNodes.length; w++) {
					this.msCont.childNodes[q].childNodes[w].innerHTML = this.langData[this.lang].monthesSNames[e++];
				}
			}
		}
	}
	
	// build month and year header
	this.contMonth = document.createElement("DIV");
	this.contMonth.className = "dhtmlxcalendar_month_cont";
	this.contMonth.onselectstart = function(e){e=e||event;e.cancelBubble=true;e.returnValue=false;return false;}
	this.base.appendChild(this.contMonth);
	
	var ul = document.createElement("UL");
	ul.className = "dhtmlxcalendar_line";
	this.contMonth.appendChild(ul);
	
	var li = document.createElement("LI");
	li.className = "dhtmlxcalendar_cell dhtmlxcalendar_month_hdr";
	li.innerHTML = "<div class='dhtmlxcalendar_month_arrow dhtmlxcalendar_month_arrow_left' onmouseover='this.className=\"dhtmlxcalendar_month_arrow dhtmlxcalendar_month_arrow_left_hover\";' onmouseout='this.className=\"dhtmlxcalendar_month_arrow dhtmlxcalendar_month_arrow_left\";'></div>"+
			"<span class='dhtmlxcalendar_month_label_month'>Month</span><span class='dhtmlxcalendar_month_label_year'>Year</span>"+
			"<div class='dhtmlxcalendar_month_arrow dhtmlxcalendar_month_arrow_right' onmouseover='this.className=\"dhtmlxcalendar_month_arrow dhtmlxcalendar_month_arrow_right_hover\";' onmouseout='this.className=\"dhtmlxcalendar_month_arrow dhtmlxcalendar_month_arrow_right\";'></div>";
	ul.appendChild(li);
	
	var that = this;
	li.onclick = function(e) {
		e = e||event;
		var t = (e.target||e.srcElement);
		// change month by clicking left-right arrows
		if (t.className && t.className.indexOf("dhtmlxcalendar_month_arrow") === 0) {
			that._hideSelector();
			var ind = (t.parentNode.firstChild==t?-1:1);
			var k0 = new Date(that._activeMonth);
			that._drawMonth(new Date(that._activeMonth.getFullYear(), that._activeMonth.getMonth()+ind, 1, 0, 0, 0, 0));
			that.callEvent("onArrowClick", [k0, new Date(that._activeMonth)]);
			return;
		}
		// show month selector
		if (t.className && t.className == "dhtmlxcalendar_month_label_month") {
			e.cancelBubble = true;
			that._showSelector("month",31,21,"selector_month",true);
			return;
		}
		// show year selector
		if (t.className && t.className == "dhtmlxcalendar_month_label_year") {
			e.cancelBubble = true;
			that._showSelector("year",42,21,"selector_year",true);
			return;
		}
		// hide selector if it visible
		that._hideSelector();
	}
	
	// build days names
	this.contDays = document.createElement("DIV");
	this.contDays.className = "dhtmlxcalendar_days_cont";
	this.base.appendChild(this.contDays);
	
	this.setWeekStartDay = function(ind) {
		// 1..7 = Mo-Su, also 0 = Su
		if (ind == 0) ind = 7;
		this._wStart = Math.min(Math.max((isNaN(ind)?1:ind),1),7);
		this._drawDaysOfWeek();
	}
	
	this._drawDaysOfWeek = function() {
		if (this.contDays.childNodes.length == 0) {
			var ul = document.createElement("UL");
			ul.className = "dhtmlxcalendar_line";
			this.contDays.appendChild(ul);
		} else {
			var ul = this.contDays.firstChild;
		}
		
		var w = this._wStart;
		var k = this.langData[this.lang].daysSNames;
		k.push(String(this.langData[this.lang].daysSNames[0]).valueOf());
		
		for (var q=0; q<7; q++) {
			if (ul.childNodes[q] == null) {
				var li = document.createElement("LI");
				ul.appendChild(li);
			} else {
				var li = ul.childNodes[q];
			}
			li.className = "dhtmlxcalendar_cell"+(w>=6?" dhtmlxcalendar_day_weekday_cell":"")+(q==0?"_first":"");
			li.innerHTML = k[w];
			if (++w > 7) w = 1;
		}
		if (this._activeMonth != null) this._drawMonth(this._activeMonth);
	}
	
	this._wStart = this.langData[this.lang].weekstart;
	this.setWeekStartDay(this._wStart);
	
	// dates container
	this.contDates = document.createElement("DIV");
	this.contDates.className = "dhtmlxcalendar_dates_cont";
	this.base.appendChild(this.contDates);
	
	this.contDates.onclick = function(e){
		e = e||event;
		var t = (e.target||e.srcElement);
		if (t._date != null && !t._css_dis) {
			
			var t1 = that._activeDate.getHours();
			var t2 = that._activeDate.getMinutes();
			var d0 = t._date;
			
			// cjeck if allow to modify input
			if (that.checkEvent("onBeforeChange")) {
				if (!that.callEvent("onBeforeChange",[new Date(t._date.getFullYear(),t._date.getMonth(),t._date.getDate(),t1,t2)])) return;
			}
			
			if (that._activeDateCell != null) {
				that._activeDateCell._css_date = false;
				that._updateCellStyle(that._activeDateCell._q, that._activeDateCell._w);
			}
			
			// update month if day from prev/next month clicked
			var refreshView = (that._hasParent && that._activeDate.getFullYear()+"_"+that._activeDate.getMonth() != d0.getFullYear()+"_"+d0.getMonth());
			
			that._nullDate = false;
			that._activeDate = new Date(d0.getFullYear(),d0.getMonth(),d0.getDate(),t1,t2);
			
			that._activeDateCell = t;
			that._activeDateCell._css_date = true;
			that._activeDateCell._css_hover = false;
			that._lastHover = null;
			that._updateCellStyle(that._activeDateCell._q, that._activeDateCell._w);
			
			if (refreshView) that._drawMonth(that._activeDate);
			
			// update date in input if any
			if (that._activeInp && that.i[that._activeInp]) {
				that.i[that._activeInp].value = that._dateToStr(new Date(that._activeDate.getTime()));
			}
			// hide
			if (!that._hasParent) that._hide();
			//
			that.callEvent("onClick",[new Date(that._activeDate.getTime())]);
			
		}
	}
	
	this.contDates.onmouseover = function(e) {
		e = e||event;
		var t = (e.target||e.srcElement);
		if (t._date != null) { // && t != that._activeDateCell) { // skip hover for selected date
			t._css_hover = true;
			that._updateCellStyle(t._q, t._w);
			that._lastHover = t;
		}
	}
	this.contDates.onmouseout = function() {
		that._clearDayHover();
	}
	
	this._lastHover = null;
	this._clearDayHover = function() {
		if (!this._lastHover) return;
		this._lastHover._css_hover = false;
		this._updateCellStyle(this._lastHover._q, this._lastHover._w);
		this._lastHover = null;
	}
	
	// build cells
	for (var q=0; q<6; q++) {
		var ul = document.createElement("UL");
		ul.className = "dhtmlxcalendar_line";
		this.contDates.appendChild(ul);
		for (var w=0; w<7; w++) {
			var li = document.createElement("LI");
			li.className = "dhtmlxcalendar_cell";
			ul.appendChild(li);
		}
	}
	
	
	// timepicker
	
	this.contTime = document.createElement("DIV");
	this.contTime.className = "dhtmlxcalendar_time_cont";
	this.base.appendChild(this.contTime);
	
	this.showTime = function() {
		if (String(this.base.className).search("dhtmlxcalendar_time_hidden") > 0) this.base.className = String(this.base.className).replace(/dhtmlxcalendar_time_hidden/gi,"");
		this._ifrSize();
	}
	
	this.hideTime = function() {
		if (String(this.base.className).search("dhtmlxcalendar_time_hidden") < 0) this.base.className += " dhtmlxcalendar_time_hidden";
		this._ifrSize();
	}
	
	var ul = document.createElement("UL");
	ul.className = "dhtmlxcalendar_line";
	this.contTime.appendChild(ul);
	
	var li = document.createElement("LI");
	li.className = "dhtmlxcalendar_cell dhtmlxcalendar_time_hdr";
	li.innerHTML = "<div class='dhtmlxcalendar_time_label'></div><span class='dhtmlxcalendar_label_hours'></span><span class='dhtmlxcalendar_label_colon'>:</span><span class='dhtmlxcalendar_label_minutes'></span>";
	ul.appendChild(li);
	
	li.onclick = function(e) {
		e = e||event;
		var t = (e.target||e.srcElement);
		// show hours selector
		if (t.className && t.className == "dhtmlxcalendar_label_hours") {
			e.cancelBubble = true;
			that._showSelector("hours",3,115,"selector_hours",true);
			return;
		}
		// show minutes selector
		if (t.className && t.className == "dhtmlxcalendar_label_minutes") {
			e.cancelBubble = true;
			that._showSelector("minutes",59,115,"selector_minutes",true);
			return;
		}
		// hide selector if it visible
		that._hideSelector();
	}
	
	
	this._activeMonth = null;
	
	this._activeDate = new Date();
	this._activeDateCell = null;
	
	this.setDate = function(d) {
		
		this._nullDate = (typeof(d) == "undefined" || d === "" || !d);
		
		if (!(d instanceof Date)) {
			d = this._strToDate(String(d||""));
			if (d == "Invalid Date") d = new Date();
		}
		
		var time = d.getTime();
		
		// out of range
		if (this._isOutOfRange(time)) return;
		
		this._activeDate = new Date(time);
		this._drawMonth(this._nullDate?new Date():this._activeDate);
		this._updateVisibleHours();
		this._updateVisibleMinutes();
	}
	
	this.getDate = function(formated) {
		if (this._nullDate) return null;
		var t = new Date(this._activeDate.getTime());
		if (formated) return this._dateToStr(t);
		return t;
	}
	
	this._drawMonth = function(d) {
		if (!(d instanceof Date)) return;
		if (isNaN(d.getFullYear())) d = new Date(this._activeMonth.getFullYear(), this._activeMonth.getMonth(), 1, 0, 0, 0, 0);
		
		this._activeMonth = new Date(d.getFullYear(), d.getMonth(), 1, 0, 0, 0, 0);
		
		this._activeDateCell = null;
		
		var first = new Date(this._activeMonth.getTime());
		var d0 = first.getDay();
		
		var e0 = d0-this._wStart;
		if (e0 < 0) e0 = e0+7;
		first.setDate(first.getDate()-e0);
		
		var mx = d.getMonth();
		var dx = new Date(this._activeDate.getFullYear(), this._activeDate.getMonth(), this._activeDate.getDate(), 0, 0, 0, 0).getTime();
		var i = 0;
		for (var q=0; q<6; q++) {
			var ws = this._wStart;
			for (var w=0; w<7; w++) {
				var d2 = new Date(first.getFullYear(), first.getMonth(), first.getDate()+i++, 0, 0, 0, 0);
				this.contDates.childNodes[q].childNodes[w].innerHTML = d2.getDate();
				var day = d2.getDay();
				var time = d2.getTime();
				
				this.contDates.childNodes[q].childNodes[w]._date = new Date(time);
				this.contDates.childNodes[q].childNodes[w]._q = q;
				this.contDates.childNodes[q].childNodes[w]._w = w;
				this.contDates.childNodes[q].childNodes[w]._css_month = (d2.getMonth()==mx);
				this.contDates.childNodes[q].childNodes[w]._css_date = (!this._nullDate&&time==dx);
				this.contDates.childNodes[q].childNodes[w]._css_weekend = (ws>=6);
				this.contDates.childNodes[q].childNodes[w]._css_dis = this._isOutOfRange(time);
				this.contDates.childNodes[q].childNodes[w]._css_holiday = (this._holidays[time] == true);
				
				this._updateCellStyle(q, w);
				
				if (time==dx) this._activeDateCell = this.contDates.childNodes[q].childNodes[w];
				
				if (++ws > 7) ws = 1;
			}
		}
		
		this.contMonth.firstChild.firstChild.childNodes[1].innerHTML = this.langData[this.lang].monthesFNames[d.getMonth()];
		this.contMonth.firstChild.firstChild.childNodes[2].innerHTML = d.getFullYear();
		
	}
	
	this._updateCellStyle = function(q, w) {
		
		var r = this.contDates.childNodes[q].childNodes[w];
		
		var s = "dhtmlxcalendar_cell dhtmlxcalendar_cell";
		
		// this/another month
		s += (r._css_month ? "_month" : "");
		
		// selected date
		s += (r._css_date ? "_date" : "");
		
		// is weekend
		s += (r._css_weekend ? "_weekend" : "");
		
		// is holiday
		s += (r._css_holiday ? "_holiday" : "");
		
		// is cell disabled
		s += (r._css_dis ? "_dis" : "");
		
		// is cell hover (only if not disabled)
		s += (r._css_hover && !r._css_dis ? "_hover" : "");
		
		r.className = s;
		r = null;
		
	}
	
	/* global selector obj */
	
	this._initSelector = function(type,css) {
		
		if (!this._selCover) {
			this._selCover = document.createElement("DIV");
			this._selCover.className = "dhtmlxcalendar_selector_cover";
			this.base.appendChild(this._selCover);
		}

		if (!this._sel) {
			
			this._sel = document.createElement("DIV");
			this._sel.className = "dhtmlxcalendar_selector_obj";
			this.base.appendChild(this._sel);
			
			this._sel.appendChild(document.createElement("TABLE"));
			this._sel.firstChild.className = "dhtmlxcalendar_selector_table";
			this._sel.firstChild.cellSpacing = 0;
			this._sel.firstChild.cellPadding = 0;
			this._sel.firstChild.border = 0;
			this._sel.firstChild.appendChild(document.createElement("TBODY"));
			this._sel.firstChild.firstChild.appendChild(document.createElement("TR"));
			
			this._sel.firstChild.firstChild.firstChild.appendChild(document.createElement("TD"));
			this._sel.firstChild.firstChild.firstChild.appendChild(document.createElement("TD"));
			this._sel.firstChild.firstChild.firstChild.appendChild(document.createElement("TD"));
			
			this._sel.firstChild.firstChild.firstChild.childNodes[0].className = "dhtmlxcalendar_selector_cell_left";
			this._sel.firstChild.firstChild.firstChild.childNodes[1].className = "dhtmlxcalendar_selector_cell_middle";
			this._sel.firstChild.firstChild.firstChild.childNodes[2].className = "dhtmlxcalendar_selector_cell_right";
			this._sel.firstChild.firstChild.firstChild.childNodes[0].innerHTML = "&nbsp;";
			this._sel.firstChild.firstChild.firstChild.childNodes[2].innerHTML = "&nbsp;";
			
			this._sel.firstChild.firstChild.firstChild.childNodes[0].onmouseover = function(){
				this.className = "dhtmlxcalendar_selector_cell_left dhtmlxcalendar_selector_cell_left_hover";
			}
			this._sel.firstChild.firstChild.firstChild.childNodes[0].onmouseout = function(){
				this.className = "dhtmlxcalendar_selector_cell_left";
			}
			
			this._sel.firstChild.firstChild.firstChild.childNodes[2].onmouseover = function(){
				this.className = "dhtmlxcalendar_selector_cell_right dhtmlxcalendar_selector_cell_right_hover";
			}
			this._sel.firstChild.firstChild.firstChild.childNodes[2].onmouseout = function(){
				this.className = "dhtmlxcalendar_selector_cell_right";
			}
			
			this._sel.firstChild.firstChild.firstChild.childNodes[0].onclick = function(e){
				e = e||event;
				e.cancelBubble = true;
				that._scrollYears(-1);
			}
			
			this._sel.firstChild.firstChild.firstChild.childNodes[2].onclick = function(e){
				e = e||event;
				e.cancelBubble = true;
				that._scrollYears(1);
			}
			
			this._sel._ta = {};
			
			this._selHover = null;
			
			this._sel.onmouseover = function(e) {
				e = e||event;
				var t = (e.target||e.srcElement);
				if (t._cell === true) {
					if (that._selHover != t) that._clearSelHover();
					if (String(t.className).match(/^\s{0,}dhtmlxcalendar_selector_cell\s{0,}$/gi) !=null) {
						t.className += " dhtmlxcalendar_selector_cell_hover";
						that._selHover = t;
					}
				}
			}
			
			this._sel.onmouseout = function() {
				that._clearSelHover();
			}
			
			this._sel.appendChild(document.createElement("DIV"));
			this._sel.lastChild.className = "dhtmlxcalendar_selector_obj_arrow";
		}
		
		// check if already inited
		if (this._sel._ta[type] == true) return;
		
		// init month
		if (type == "month") {
			
			this._msCells = {};
			
			this.msCont = document.createElement("DIV");
			this.msCont.className = "dhtmlxcalendar_area_"+css;
			this._sel.firstChild.firstChild.firstChild.childNodes[1].appendChild(this.msCont);
			
			var i = 0;
			for (var q=0; q<4; q++) {
				var ul = document.createElement("UL");
				ul.className = "dhtmlxcalendar_selector_line";
				this.msCont.appendChild(ul);
				for (var w=0; w<3; w++) {
					var li = document.createElement("LI");
					li.innerHTML = this.langData[this.lang].monthesSNames[i];
					li.className = "dhtmlxcalendar_selector_cell";
					ul.appendChild(li);
					li._month = i;
					li._cell = true;
					this._msCells[i++] = li;
				}
			}
			
			this.msCont.onclick = function(e) {
				e = e||event;
				e.cancelBubble = true;
				var t = (e.target||e.srcElement);
				if (t._month != null) {
					that._hideSelector();
					that._updateActiveMonth();
					that._drawMonth(new Date(that._activeMonth.getFullYear(), t._month, 1, 0, 0, 0, 0));
					that._doOnSelectorChange();
				}
			}
			
		}
		
		// init year
		if (type == "year") {
			
			this._ysCells = {};
			
			this.ysCont = document.createElement("DIV");
			this.ysCont.className = "dhtmlxcalendar_area_"+css;
			this._sel.firstChild.firstChild.firstChild.childNodes[1].appendChild(this.ysCont);
			
			for (var q=0; q<4; q++) {
				var ul = document.createElement("UL");
				ul.className = "dhtmlxcalendar_selector_line";
				this.ysCont.appendChild(ul);
				for (var w=0; w<3; w++) {
					var li = document.createElement("LI");
					li.className = "dhtmlxcalendar_selector_cell";
					li._cell = true;
					ul.appendChild(li);
				}
			}
			
			this.ysCont.onclick = function(e) {
				e = e||event;
				e.cancelBubble = true;
				var t = (e.target||e.srcElement);
				if (t._year != null) {
					that._hideSelector();
					that._drawMonth(new Date(t._year, that._activeMonth.getMonth(), 1, 0, 0, 0, 0));
					that._doOnSelectorChange();
				}
			}
			
		}
		
		// init hours
		if (type == "hours") {
			
			this._hsCells = {};
			
			this.hsCont = document.createElement("DIV");
			this.hsCont.className = "dhtmlxcalendar_area_"+css;
			this._sel.firstChild.firstChild.firstChild.childNodes[1].appendChild(this.hsCont);
			
			var i = 0;
			for (var q=0; q<4; q++) {
				var ul = document.createElement("UL");
				ul.className = "dhtmlxcalendar_selector_line";
				this.hsCont.appendChild(ul);
				for (var w=0; w<6; w++) {
					var li = document.createElement("LI");
					li.innerHTML = this._fixLength(i,2);
					li.className = "dhtmlxcalendar_selector_cell";
					ul.appendChild(li);
					li._hours = i;
					li._cell = true;
					this._hsCells[i++] = li;
				}
			}
			
			this.hsCont.onclick = function(e) {
				e = e||event;
				e.cancelBubble = true;
				var t = (e.target||e.srcElement);
				if (t._hours != null) {
					that._hideSelector();
					that._activeDate.setHours(t._hours);
					that._updateActiveHours();
					that._updateVisibleHours();
					that._doOnSelectorChange();
				}
			}
			
		}
		
		// init minutes
		if (type == "minutes") {
			
			this._rsCells = {};
			
			this.rsCont = document.createElement("DIV");
			this.rsCont.className = "dhtmlxcalendar_area_"+css;
			this._sel.firstChild.firstChild.firstChild.childNodes[1].appendChild(this.rsCont);
			
			var i = 0;
			for (var q=0; q<4; q++) {
				var ul = document.createElement("UL");
				ul.className = "dhtmlxcalendar_selector_line";
				this.rsCont.appendChild(ul);
				for (var w=0; w<3; w++) {
					var li = document.createElement("LI");
					li.innerHTML = this._fixLength(i,2);
					li.className = "dhtmlxcalendar_selector_cell";
					ul.appendChild(li);
					li._minutes = i;
					li._cell = true;
					this._rsCells[i] = li;
					i+=5;
				}
			}
			
			this.rsCont.onclick = function(e) {
				e = e||event;
				e.cancelBubble = true;
				var t = (e.target||e.srcElement);
				if (t._minutes != null) {
					that._hideSelector();
					that._activeDate.setMinutes(t._minutes);
					that._updateActiveMinutes();
					that._updateVisibleMinutes();
					that._doOnSelectorChange();
				}
			}
			
		}
		
		// mark that selector of current type is inited
		this._sel._ta[type] = true;
	}
	
	this._showSelector = function(type,x,y,css,autoHide) {
		
		if (autoHide === true && this._sel != null && this._isSelectorVisible() && type == this._sel._t) {
			this._hideSelector();
			return;
		}
		
		if (this.skin == "dhx_terrace") {
			x += {month: 14, year:27, hours: 19, minutes: 24}[type];
			y += {month:  8, year: 8, hours: 14, minutes: 14}[type];
		}
		
		if (!this._sel || !this._sel._ta[type]) this._initSelector(type,css);
		
		// show selector cover
		this._selCover.style.display = "";
		
		// show selector
		this._sel._t = type;
		this._sel.style.left = x+"px";
		this._sel.style.top = y+"px";
		this._sel.style.display = "";
		this._sel.className = "dhtmlxcalendar_selector_obj dhtmlxcalendar_"+css;
		
		// callbacks
		this._doOnSelectorShow(type);
	}
	
	this._doOnSelectorShow = function(type) {
		if (type == "month") this._updateActiveMonth();
		if (type == "year") this._updateYearsList(this._activeMonth);
		if (type == "hours") this._updateActiveHours();
		if (type == "minutes") this._updateActiveMinutes();
	}
	
	this._hideSelector = function() {
		if (!this._sel) return;
		this._sel.style.display = "none";
		this._selCover.style.display = "none";
	}
	
	this._isSelectorVisible = function() {
		if (!this._sel) return false;
		return (this._sel.style.display != "none");
	}
	
	this._doOnSelectorChange = function(state) {
		this.callEvent("onChange",[new Date(this._activeMonth.getFullYear(), this._activeMonth.getMonth(), this._activeDate.getDate(), this._activeDate.getHours(), this._activeDate.getMinutes(), this._activeDate.getSeconds()),state]);
	}
	
	this._clearSelHover = function() {
		if (!this._selHover) return;
		this._selHover.className = String(this._selHover.className.replace(/dhtmlxcalendar_selector_cell_hover/gi,""));
		this._selHover = null;
	}
	
	
	/* month selector */
	
	this._updateActiveMonth = function() {
		if (typeof(this._msActive) != "undefined" && typeof(this._msCells[this._msActive]) != "undefined") this._msCells[this._msActive].className = "dhtmlxcalendar_selector_cell";
		this._msActive = this._activeMonth.getMonth();
		this._msCells[this._msActive].className = "dhtmlxcalendar_selector_cell dhtmlxcalendar_selector_cell_active";
	}
	
	/* year selector */
	
	this._updateActiveYear = function() {
		var i = this._activeMonth.getFullYear();
		if (this._ysCells[i]) this._ysCells[i].className = "dhtmlxcalendar_selector_cell dhtmlxcalendar_selector_cell_active";
	}
	
	this._updateYearsList = function(d) {
		for (var a in this._ysCells) {
			this._ysCells[a] = null;
			delete this._ysCells[a];
		}
		//
		var i = 12*Math.floor(d.getFullYear()/12);
		for (var q=0; q<4; q++) {
			for (var w=0; w<3; w++) {
				this.ysCont.childNodes[q].childNodes[w].innerHTML = i;
				this.ysCont.childNodes[q].childNodes[w]._year = i;
				this.ysCont.childNodes[q].childNodes[w].className = "dhtmlxcalendar_selector_cell";
				this._ysCells[i++] = this.ysCont.childNodes[q].childNodes[w];
			}
		}
		this._updateActiveYear();
	}
	
	this._scrollYears = function(i) {
		var y = (i<0?this.ysCont.firstChild.firstChild._year:this.ysCont.lastChild.lastChild._year)+i;
		var d = new Date(y, this._activeMonth.getMonth(), 1, 0, 0, 0, 0);
		this._updateYearsList(d);
	}
	
	/* hours selector */
	
	// update hours in selector
	this._updateActiveHours = function() {
		if (typeof(this._hsActive) != "undefined" && typeof(this._hsCells[this._hsActive]) != "undefined") this._hsCells[this._hsActive].className = "dhtmlxcalendar_selector_cell";
		this._hsActive = this._activeDate.getHours();
		this._hsCells[this._hsActive].className = "dhtmlxcalendar_selector_cell dhtmlxcalendar_selector_cell_active";
	}
	
	// update hours in calendar
	this._updateVisibleHours = function() {
		this.contTime.firstChild.firstChild.childNodes[1].innerHTML = this._fixLength(this._activeDate.getHours(),2);
	}
	
	/* minutes selector */
	
	// update minutes in selector
	this._updateActiveMinutes = function() {
		if (typeof(this._rsActive) != "undefined" && typeof(this._rsCells[this._rsActive]) != "undefined") this._rsCells[this._rsActive].className = "dhtmlxcalendar_selector_cell";
		this._rsActive = this._activeDate.getMinutes();
		if (typeof(this._rsCells[this._rsActive]) != "undefined") this._rsCells[this._rsActive].className = "dhtmlxcalendar_selector_cell dhtmlxcalendar_selector_cell_active";
	}
	
	// update minutes in calendar
	this._updateVisibleMinutes = function() {
		this.contTime.firstChild.firstChild.childNodes[3].innerHTML = this._fixLength(this._activeDate.getMinutes(),2);
	}
	
	/* some common functionality */
	
	this._fixLength = function(t, r) {
		while (String(t).length < r) t = "0"+String(t);
		return t;
	}
	
	this._dateFormat = "";
	this._dateFormatRE = null;
	
	this.setDateFormat = function(format) {
		this._dateFormat = format;
		this._dateFormatRE = new RegExp(String(this._dateFormat).replace(/%[a-zA-Z]+/g,function(t){
			var t2 = t.replace(/%/,"");
			switch (t2) {
				case "n":
				case "j":
				case "g":
				case "G":
					return "\\d{1,2}";
				case "m":
				case "d":
				case "H":
				case "i":
				case "s":
				case "y":
					return "\\d{2}";
				case "Y":
					return "\\d{4}";
				case "M":
					return "("+that.langData[that.lang].monthesSNames.join("|").toLowerCase()+"){1,}";
				case "F":
					return "("+that.langData[that.lang].monthesFNames.join("|").toLowerCase()+"){1,}";
			}
			return t;
		}),"i");
	}
	
	this.setDateFormat("%Y-%m-%d");
	
	// get index by value
	this._getInd = function(val,ar) {
		for (var q=0; q<ar.length; q++) if (ar[q].toLowerCase() == val) return q;
		return -1;
	}
	
	this._strToDate = function(val, format) {
		
		format = (format||this._dateFormat);
		
		
		var v = val.match(/[a-z0-9]{1,}/gi);
		var f = format.match(/%[a-zA-Z]/g);
		
		if (!v || v.length != f.length) return "Invalid Date";
		
		// sorting
		/*
		Year	y,Y	1
		Month	n,m,M,F	2
		Day	d,j	3
		Hours	H,G,h,g	4
		Minutes	i	5
		Seconds	s	6
		*/
		
		var p = {"%y":1,"%Y":1,"%n":2,"%m":2,"%M":2,"%F":2,"%d":3,"%j":3,"%H":4,"%G":4,"%h":4,"%g":4,"%i":5,"%s":6};
		var v2 = {};
		var f2 = {};
		for (var q=0; q<f.length; q++) {
			if (typeof(p[f[q]]) != "undefined") {
				var ind = p[f[q]];
				if (!v2[ind]){v2[ind]=[];f2[ind]=[];}
				v2[ind].push(v[q]);
				f2[ind].push(f[q]);
			}
		}
		v = [];
		f = [];
		for (var q=1; q<=6; q++) {
			if (v2[q] != null) {
				for (var w=0; w<v2[q].length; w++) {
					v.push(v2[q][w]);
					f.push(f2[q][w]);
				}
			}
		}
		
		// parsing date
		var r = new Date();
		r.setDate(1); // fix for 31th
		
		for (var q=0; q<v.length; q++) {
			
			switch (f[q]) {
				case "%d":
				case "%j":
				case "%d":
				case "%j":
				case "%n":
				case "%m":
				case "%Y":
				case "%H":
				case "%G":
				case "%i":
				case "%s":
					if (!isNaN(v[q])) r[{"%d":"setDate","%j":"setDate","%n":"setMonth","%m":"setMonth","%Y":"setFullYear","%H":"setHours","%G":"setHours","%i":"setMinutes","%s":"setSeconds"}[f[q]]](Number(v[q])+(f[q]=="%m"||f[q]=="%n"?-1:0));
					break;
				case "%M":
				case "%F":
					var k = this._getInd(v[q].toLowerCase(),that.langData[that.lang][{"%M":"monthesSNames","%F":"monthesFNames"}[f[q]]]);
					if (k >= 0) r.setMonth(k);
					break;
				case "%y":
					if (!isNaN(v[q])) {
						var v0 = Number(v[q]);
						r.setFullYear(v0+(v0>50?1900:2000));
					}
					break;
				case "%g":
				case "%h":
					if (!isNaN(v[q])) {
						var v0 = Number(v[q]);
						if (v0 <= 12 && v0 >= 0) r.setHours(v0+(this._getInd("pm",v)>=0?12:0));
					}
					break;

			}
			
		}
		
		return r;
	}
	
	this._dateToStr = function(val, format) {
		if (val instanceof Date) {
			var z = function(t) {
				return (String(t).length==1?"0"+String(t):t);
			}
			var k = function(t) {
				switch(t) {
					case "%d": return z(val.getDate());
					case "%j": return val.getDate();
					case "%D": return that.langData[that.lang].daysSNames[val.getDay()];
					case "%l": return that.langData[that.lang].daysFNames[val.getDay()];
					// %W - ISO-8601 week number of year, weeks starting on Monday; 1)
					case "%m": return z(val.getMonth()+1);
					case "%n": return val.getMonth()+1;
					case "%M": return that.langData[that.lang].monthesSNames[val.getMonth()];
					case "%F": return that.langData[that.lang].monthesFNames[val.getMonth()];
					case "%y": return z(val.getYear()%100);
					case "%Y": return val.getFullYear();
					case "%g": return (val.getHours()+11)%12+1;
					case "%h": return z((val.getHours()+11)%12+1);
					case "%G": return val.getHours();
					case "%H": return z(val.getHours());
					case "%i": return z(val.getMinutes());
					case "%s": return z(val.getSeconds());
					case "%a": return (val.getHours()>11?"pm":"am");
					case "%A": return (val.getHours()>11?"PM":"AM");
					case "%%": "%";
					default: return t;
				}
			}
			var t = String(format||this._dateFormat).replace(/%[a-zA-Z]/g, k);
		}
		return (t||String(val));
	}
	
	this._updateDateStr = function(str) {
		
		// check if valid str
		if (str == "") {
			this.setDate(new Date());
			this.callEvent("onChange",[null,true]);
			return;
		} else {
			if (!this._dateFormatRE || !str.match(this._dateFormatRE)) return;
		}
		
		
		var r = this._strToDate(str);
		if (!(r instanceof Date)) return;
		
		this._nullDate = false;
		this._activeDate = r;
		
		this._drawMonth(this._nullDate?new Date():this._activeDate);
		
		this._updateVisibleMinutes();
		this._updateVisibleHours();
		
		if (this._sel && this._isSelectorVisible()) this._doOnSelectorShow(this._sel._t);
		this._doOnSelectorChange(true);
		
	}
	
	this.setFormatedDate = function(format, str, a, return_only) {
		var date = this._strToDate(str, format);
		if (return_only) return date;
		this.setDate(date);
	}

	this.getFormatedDate = function(format, date) {
		if (this._nullDate) return "";
		if (!(date && date instanceof Date)) date = new Date(this._activeDate);
		return this._dateToStr(date, format);
	}
	
	/* show/hide calendar */
	
	// public show/hide
	
	this.show = function(id) {
		// if id not set - try show in container
		if (!id && this._hasParent) {
			this._show();
			return;
		}
		// if input id not specified show near first found
		// if nothing found - do not show
		if (typeof(id) == "object" && typeof(id._dhtmlxcalendar_uid) != "undefined" && this.i[id._dhtmlxcalendar_uid] == id) {
			this._show(id._dhtmlxcalendar_uid);
			return;
		}
		if (typeof(id) == "undefined") { for (var a in this.i) if (!id) id = a; }
		if (!id) return;
		this._show(id);
	}
	
	this.hide = function() {
		if (this._isVisible()) this._hide();
	}
	
	this.isVisible = function() {
		return this._isVisible();
	}
	
	
	this.draw = function() {
		// deprecated
		this.show();
	}
	
	this.close = function() {
		// deprecated
		this.hide();
	}
	
	// private show/hide
	
	this._activeInp = null;
	
	this.pos = "bottom";
	this.setPosition = function(x, y) {
		this._px = null;
		this._py = null;
		if (x == "right" || x == "bottom") {
			this.pos = x;
		} else {
			this.pos = "int";
			if (typeof(x) != "undefined" && !isNaN(x)) {
				this.base.style.left = x+"px";
				this._px = x;
			}
			if (typeof(y) != "undefined" && !isNaN(y)) {
				this.base.style.top = y+"px";
				this._py = y;
			}
			this._ifrSize();
		}
	}
	
	this._show = function(inpId, autoHide) {
		if (autoHide === true && this._activeInp == inpId && this._isVisible()) {
			this._hide();
			return;
		}
		if (!inpId) {
			if (this._px && this._py) {
				this.base.style.left = this._px+"px";
				this.base.style.top = this._py+"px";
			} else {
				this.base.style.left = "0px";
				this.base.style.top = "0px";
			}
		} else {
			if (this.pos == "right") {
				this.base.style.left = this._getLeft(this.i[inpId])+this.i[inpId].offsetWidth-1+"px";
				this.base.style.top = this._getTop(this.i[inpId])+"px";
			} else if (this.pos == "bottom") {
				this.base.style.left = this._getLeft(this.i[inpId])+"px";
				this.base.style.top = this._getTop(this.i[inpId])+this.i[inpId].offsetHeight-1+"px";
			} else {
				this.base.style.left = (this._px||0)+"px";
				this.base.style.top = (this._py||0)+"px";
			}
			this._activeInp = inpId;
		}
		this._hideSelector();
		this.base.style.display = "";
		this._ifrSize();
		if (this._ifr) this._ifr.style.display = "";
	}
	
	this._hide = function() {
		this._hideSelector();
		this.base.style.display = "none";
		this._activeInp = null;
		if (this._ifr) this._ifr.style.display = "none";
	}
	
	this._isVisible = function() {
		return (this.base.style.display!="none");
	}
		
	this._getLeft = function(obj) {
		return this._posGetOffset(obj).left;
	}
	
	this._getTop = function(obj) {
		return this._posGetOffset(obj).top;
	}
	
	this._posGetOffsetSum = function(elem) {
		var top=0, left=0;
		while(elem) {
			top = top + parseInt(elem.offsetTop);
			left = left + parseInt(elem.offsetLeft);
			elem = elem.offsetParent;
		}
		return {top: top, left: left};
	}
	this._posGetOffsetRect = function(elem) {
		var box = elem.getBoundingClientRect();
		var body = document.body;
		var docElem = document.documentElement;
		var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop;
		var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft;
		var clientTop = docElem.clientTop || body.clientTop || 0;
		var clientLeft = docElem.clientLeft || body.clientLeft || 0;
		var top  = box.top +  scrollTop - clientTop;
		var left = box.left + scrollLeft - clientLeft;
		return { top: Math.round(top), left: Math.round(left) };                                 
	}
	this._posGetOffset = function(elem) {
		return this[elem.getBoundingClientRect?"_posGetOffsetRect":"_posGetOffsetSum"](elem);
	}
	
	this._rangeActive = false;
	this._rangeFrom = null;
	this._rangeTo = null;
	this._rangeSet = {};
	
	this.setInsensitiveDays = function(d) {
		
		// !works in append mode
		var t = this._extractDates(d);
		for (var q=0; q<t.length; q++) this._rangeSet[new Date(t[q].getFullYear(),t[q].getMonth(),t[q].getDate(),0,0,0,0).getTime()] = true;
		
		this._drawMonth(this._activeMonth);
		
	}
	
	this.clearInsensitiveDays = function() {
		this._clearRangeSet();
		this._drawMonth(this._activeMonth);
	}
	
	this._holidays = {};
	this.setHolidays = function(r) {
		if (r == null) {
			this._clearHolidays();
		} else if (r != null) {
			var t = this._extractDates(r);
			for (var q=0; q<t.length; q++) this._holidays[new Date(t[q].getFullYear(),t[q].getMonth(),t[q].getDate(),0,0,0,0).getTime()] = true;
		}
		this._drawMonth(this._activeMonth);
	}
	
	this._extractDates = function(r) {
		// r = array of dates or comma-separated string list
		// return array with dates
		if (typeof(r) == "string" || r instanceof Date) r = [r];
		var t = [];
		for (var q=0; q<r.length; q++) {
			if (typeof(r[q]) == "string") {
				var e = r[q].split(",");
				for (var w=0; w<e.length; w++) t.push(this._strToDate(e[w]));
			} else if (r[q] instanceof Date) {
				t.push(r[q]);
			}
		}
		return t;
	}
	
	this._clearRange = function() {
		this._rangeActive = false;
		this._rangeType = null;
		this._rangeFrom = null;
		this._rangeTo = null;
	}
	
	this._clearRangeSet = function() {
		for (var a in this._rangeSet) {
			this._rangeSet[a] = null;
			delete this._rangeSet[a];
		}
	}
	
	this._clearHolidays = function() {
		for (var a in this._holidays) {
			this._holidays[a] = null;
			delete this._holidays[a];
		}
	}
	
	this._isOutOfRange = function(time) {
		
		if (this._rangeSet[time] == true) return true;
		
		if (this._rangeActive) {
			
			if (this._rangeType == "in" && (time<this._rangeFrom || time>this._rangeTo)) return true;
			if (this._rangeType == "out" && (time>=this._rangeFrom && time<=this._rangeTo)) return true;
			if (this._rangeType == "from" && time<this._rangeFrom)return true;
			if (this._rangeType == "to" && time>this._rangeTo) return true;
		}
		
		var t0 = new Date(time);
		
		if (this._rangeWeek) {
			if (this._rangeWeekData[t0.getDay()] === true) return true;
		}
		
		if (this._rangeMonth) {
			if (this._rangeMonthData[t0.getDate()] === true) return true;
		}
		
		if (this._rangeYear) {
			if (this._rangeYearData[t0.getMonth()+"_"+t0.getDate()] === true) return true;
		}
		
		return false;
		
	}
	
	this.clearSensitiveRange = function() {
		this._clearRange();
		this._drawMonth(this._activeMonth);
	}
	
	this.setSensitiveRange = function(from, to, ins) {
		
		var f = false;
		
		// set range
		if (from != null && to != null) {
			
			if (!(from instanceof Date)) from = this._strToDate(from);
			if (!(to instanceof Date)) to = this._strToDate(to);
			
			if (from.getTime() > to.getTime()) return;
			
			this._rangeFrom = new Date(from.getFullYear(),from.getMonth(),from.getDate(),0,0,0,0).getTime();
			this._rangeTo = new Date(to.getFullYear(),to.getMonth(),to.getDate(),0,0,0,0).getTime();
			this._rangeActive = true;
			this._rangeType = "in";
			
			f = true;
		}
		
		// set range "from date"
		if (!f && from != null && to == null) {
			
			if (!(from instanceof Date)) from = this._strToDate(from);
			this._rangeFrom = new Date(from.getFullYear(),from.getMonth(),from.getDate(),0,0,0,0).getTime();
			this._rangeTo = null;
			
			if (ins === true) this._rangeFrom++;
			
			this._rangeActive = true;
			this._rangeType = "from";
			
			f = true;
			
		}
		
		// set range "to date"
		if (!f && from == null && to != null) {
			
			if (!(to instanceof Date)) to = this._strToDate(to);
			this._rangeFrom = null;
			this._rangeTo = new Date(to.getFullYear(),to.getMonth(),to.getDate(),0,0,0,0).getTime();
			
			if (ins === true) this._rangeTo--;
			
			this._rangeActive = true;
			this._rangeType = "to";
			
			f = true;
			
		}
		
		if (f) this._drawMonth(this._activeMonth);
	}
	
	this.setInsensitiveRange = function(from, to) {
		
		if (from != null && to != null) {
			
			if (!(from instanceof Date)) from = this._strToDate(from);
			if (!(to instanceof Date)) to = this._strToDate(to);
			
			if (from.getTime() > to.getTime()) return;
			
			this._rangeFrom = new Date(from.getFullYear(),from.getMonth(),from.getDate(),0,0,0,0).getTime();
			this._rangeTo = new Date(to.getFullYear(),to.getMonth(),to.getDate(),0,0,0,0).getTime();
			this._rangeActive = true;
			this._rangeType = "out";
			
			this._drawMonth(this._activeMonth);
			return;
		}
		
		if (from != null && to == null) {
			this.setSensitiveRange(null, from, true);
			return;
		}
		
		if (from == null && to != null) {
			this.setSensitiveRange(to, null, true);
			return;
		}
		
	}
	
	//
	this.disableDays = function(mode, d) {
		
		if (mode == "week") {
			
			// !! works in replace mode
			
			if (typeof(d) != "object" && typeof(d.length) == "undefined") d = [d];
			
			if (!this._rangeWeekData) this._rangeWeekData = {};
			for (var a in this._rangeWeekData) {
				this._rangeWeekData[a] = false;
				delete this._rangeWeekData[a];
			}
			
			for (var q=0; q<d.length; q++) {
				this._rangeWeekData[d[q]] = true;
				if (d[q] == 7) this._rangeWeekData[0] = true;
			}
			this._rangeWeek = true;
		}
		
		if (mode == "month") {
			
			// !! works in replace mode
			
			if (typeof(d) != "object" && typeof(d.length) == "undefined") d = [d];
			
			if (!this._rangeMonthData) this._rangeMonthData = {};
			for (var a in this._rangeMonthData) {
				this._rangeMonthData[a] = false;
				delete this._rangeMonthData[a];
			}
			for (var q=0; q<d.length; q++) this._rangeMonthData[d[q]] = true;
			
			this._rangeMonth = true;
		}
		
		if (mode == "year") {
			
			// !! works in replace mode
			
			var t = this._extractDates(d);
			
			if (!this._rangeYearData) this._rangeYearData = {};
			for (var a in this._rangeYearData) {
				this._rangeYearData[a] = false;
				delete this._rangeYearData[a];
			}
			for (var q=0; q<t.length; q++) this._rangeYearData[t[q].getMonth()+"_"+t[q].getDate()] = true;
			
			this._rangeYear = true;
		}
		
		this._drawMonth(this._activeMonth);
	}
	
	this.enableDays = function(mode) {
		
		if (mode == "week") {
			this._rangeWeek = false;
		}
		
		if (mode == "month") {
			this._rangeMonth = false;
		}
		
		if (mode == "year") {
			this._rangeYear = false;
		}
		
		this._drawMonth(this._activeMonth);
	}
	
	this._updateFromInput = function(t) {
		if (this._nullInInput && ((t.value).replace(/\s/g,"")).length == 0) {
			this.setDate(null);
		} else {
			this._updateDateStr(t.value);
		}
		t = null;
	}
	
	// global events
	this._doOnClick = function(e) {
		e = e||event;
		var t = (e.target||e.srcElement);
		// completely close alien calendar (both selector and container) inly if any assigned input clicked
		// otherwise hide selector and container separately
		if (t._dhtmlxcalendar_uid && t._dhtmlxcalendar_uid != that._activeInp && that._isVisible()&&that._activeInp) {
			that._hide();
			return;
		}
		if (!t._dhtmlxcalendar_uid || !that.i[t._dhtmlxcalendar_uid]) { // !that.i[t._dhtmlxcalendar_uid] means alien input, for several calendar instances
			if (that._isSelectorVisible()) that._hideSelector(); else if (!that._hasParent && that._isVisible()) that._hide();
		}
	}
	
	this._doOnKeyDown = function(e) {
		e = e||event;
		if (e.keyCode == 27 || e.keyCode == 13) {
			if (that._isSelectorVisible()) that._hideSelector(); else if (that._isVisible() && !that._hasParent) that._hide();
		}
	}
	
	// inputs events
	this._doOnInpClick = function(e) {
		e = e||event;
		var t = (e.target||e.srcElement);
		if (!t._dhtmlxcalendar_uid) return;
		if (!that._listenerEnabled) {
			that._updateFromInput(t);
		}
		that._show(t._dhtmlxcalendar_uid, true);
	}
	
	this._doOnInpKeyUp = function(e) {
		e = e||event;
		var t = (e.target||e.srcElement);
		if (e.keyCode == 13 || !t._dhtmlxcalendar_uid) return;  // do nothing on esc key
		// otherwise try to update calendar's date
		if (!that._listenerEnabled) that._updateFromInput(t);
	}
	
	this._doOnUnload = function() {
		if (that && that.unload) that.unload();
	}
	
	if (window.addEventListener) {
		document.body.addEventListener("click", that._doOnClick, false);
		window.addEventListener("keydown", that._doOnKeyDown, false);
		window.addEventListener("unload", that._doOnUnload, false);
	} else {
		document.body.attachEvent("onclick", that._doOnClick);
		document.body.attachEvent("onkeydown", that._doOnKeyDown);
		window.attachEvent("onunload", that._doOnUnload);
	}
	
	this.attachObj = function(obj) {
		if (typeof(obj) == "string") obj = document.getElementById(obj);
		var a = this.uid();
		this.i[a] = obj;
		this._attachEventsToObject(a);
	}
	
	this.detachObj = function(obj) {
		if (typeof(obj) == "string") obj = document.getElementById(obj);
		var a = obj._dhtmlxcalendar_uid;
		if (this.i[a] != null) {
			this._detachEventsFromObject(a);
			this.i[a]._dhtmlxcalendar_uid = null;
			this.i[a] = null;
			delete this.i[a];
		}
	}
	
	this._attachEventsToObject = function(a) {
		this.i[a]._dhtmlxcalendar_uid = a;
		if (window.addEventListener) {
			this.i[a].addEventListener("click", that._doOnInpClick, false);
			this.i[a].addEventListener("keyup", that._doOnInpKeyUp, false);
		} else {
			this.i[a].attachEvent("onclick", that._doOnInpClick);
			this.i[a].attachEvent("onkeyup", that._doOnInpKeyUp);
		}
	}
	
	// listener
	this.enableListener = function(t) {
		if (window.addEventListener) {
			t.addEventListener("focus", that._listenerEvFocus, false);
			t.addEventListener("blur", that._listenerEvBlur, false);
		} else {
			t.attachEvent("onfocus", that._listenerEvFocus);
			t.attachEvent("onblur", that._listenerEvBlur);
		}
		t = null;
	}
	
	this.disableListener = function(t) {
		t._f0 = false;
		if (this._tmListener) window.clearTimeout(this._tmListener);
		if (window.addEventListener) {
			t.removeEventListener("focus", that._listenerEvFocus, false);
			t.removeEventListener("blur", that._listenerEvBlur, false);
		} else {
			t.detachEvent("onfocus", that._listenerEvFocus);
			t.detachEvent("onblur", that._listenerEvBlur);
		}
		t = null;
	}
	
	this._startListener = function(t) {
		if (this._tmListener) window.clearTimeout(this._tmListener);
		if (typeof(t._v1) == "undefined") t._v1 = t.value;
		if (t._v1 != t.value) {
			this._updateFromInput(t);
			t._v1 = t.value;
		}
		if (t._f0) this._tmListener = window.setTimeout(function(){that._startListener(t);},100);
	}
	
	this._listenerEvFocus = function(e) {
		e = e||event;
		var t = e.target||e.srcElement;
		t._f0 = true;
		that._startListener(t)
		t = null;
	}
	this._listenerEvBlur = function(e) {
		e = e||event;
		var t = e.target||e.srcElement;
		t._f0 = false;
		t = null;
	}
	
	
	//
	this._detachEventsFromObject = function(a) {
		if (window.addEventListener) {
			this.i[a].removeEventListener("click", that._doOnInpClick, false);
			this.i[a].removeEventListener("keyup", that._doOnInpKeyUp, false);
		} else {
			this.i[a].detachEvent("onclick", that._doOnInpClick);
			this.i[a].detachEvent("onkeyup", that._doOnInpKeyUp);
		}
	}
	
	for (var a in this.i) this._attachEventsToObject(a);
	
	/* internal events */
	
	this.evs = {};
	this.attachEvent = function(name, func) {
		var eId = this.uid();
		this.evs[eId] = {name: String(name).toLowerCase(), func: func};
		return eId;
	}
	this.detachEvent = function(id) {
		if (this.evs[id]) {
			this.evs[id].name = null;
			this.evs[id].func = null;
			this.evs[id] = null;
			delete this.evs[id];
		}
	}
	this.callEvent = function(name, params) {
		var u = true;
		var n = String(name).toLowerCase();
		params = (params||[]);
		for (var a in this.evs) {
			if (this.evs[a].name == n) {
				var r = this.evs[a].func.apply(this,params);
				u = (u && r);
			}
		}
		return u;
	}
	this.checkEvent = function(name) {
		var u = false;
		var n = String(name).toLowerCase();
		for (var a in this.evs) u = (u || this.evs[a].name == n);
		return u;
	}
	
	/* unload */
	
	this.unload = function() {
		
		this._activeDate = null;
		this._activeDateCell = null;
		this._activeInp = null;
		this._activeMonth = null;
		this._dateFormat = null;
		this._dateFormatRE = null;
		this._lastHover = null;
		
		this.uid = null;
		this.uidd = null;
		
		if (this._tmListener) window.clearTimeout(this._tmListener);
		this._tmListener = null;
		
		/* main events */
		
		if (window.addEventListener) {
			document.body.removeEventListener("click", that._doOnClick, false);
			window.removeEventListener("keydown", that._doOnKeyDown, false);
			window.removeEventListener("unload", that._doOnUnload, false);
		} else {
			document.body.detachEvent("onclick", that._doOnClick);
			document.body.detachEvent("onkeydown", that._doOnKeyDown);
			window.detachEvent("onunload", that._doOnKeyDown);
		}
		
		this._doOnClick = null;
		this._doOnKeyDown = null;
		this._doOnUnload = null;
		
		/* assigned inputs */
		
		for (var a in this.i) {
			// marker
			this.i[a]._dhtmlxcalendar_uid = null;
			
			// events
			if (window.addEventListener) {
				this.i[a].removeEventListener("click", that._doOnInpClick, false);
				this.i[a].removeEventListener("keyup", that._doOnInpKeyUp, false);
			} else {
				this.i[a].detachEvent("onclick", that._doOnInpClick);
				this.i[a].detachEvent("onkeyup", that._doOnInpKeyUp);
			}
			
			this.disableListener(this.i[a]);
			
			this.i[a] = null;
			delete this.i[a];
			
		}
		
		this.i = null;
		
		this._doOnInpClick = null;
		this._doOnInpKeyUp = null;
		
		/* obj events */
		
		for (var a in this.evs) this.detachEvent(a);
		this.evs = null;
		
		this.attachEvent = null;
		this.detachEvent = null;
		this.checkEvent = null;
		this.callEvent = null;
		
		/* months */
		
		this.contMonth.onselectstart = null;
		
		// li
		this.contMonth.firstChild.firstChild.onclick = null;
		
		// arrows
		this.contMonth.firstChild.firstChild.firstChild.onmouseover = null;
		this.contMonth.firstChild.firstChild.firstChild.onmouseout = null;
		this.contMonth.firstChild.firstChild.lastChild.onmouseover = null;
		this.contMonth.firstChild.firstChild.lastChild.onmouseout = null;
		
		while (this.contMonth.firstChild.firstChild.childNodes.length > 0) this.contMonth.firstChild.firstChild.removeChild(this.contMonth.firstChild.firstChild.lastChild);
		
		// li
		this.contMonth.firstChild.removeChild(this.contMonth.firstChild.firstChild);
		
		// ul
		this.contMonth.removeChild(this.contMonth.firstChild);
		
		// div
		this.contMonth.parentNode.removeChild(this.contMonth);
		this.contMonth = null;
		
		/* days */
		
		// li
		while (this.contDays.firstChild.childNodes.length > 0) this.contDays.firstChild.removeChild(this.contDays.firstChild.lastChild);
		
		// ul
		this.contDays.removeChild(this.contDays.firstChild);
		
		// div
		this.contDays.parentNode.removeChild(this.contDays);
		this.contDays = null;
		
		/* dates */
		
		this.contDates.onclick = null;
		this.contDates.onmouseover = null;
		this.contDates.onmouseout = null;
		
		while (this.contDates.childNodes.length > 0) {
			while (this.contDates.lastChild.childNodes.length > 0) {
				// li
				this.contDates.lastChild.lastChild._css_date = null;
				this.contDates.lastChild.lastChild._css_month = null;
				this.contDates.lastChild.lastChild._css_weekend = null;
				this.contDates.lastChild.lastChild._css_hover = null;
				this.contDates.lastChild.lastChild._date = null;
				this.contDates.lastChild.lastChild._q = null;
				this.contDates.lastChild.lastChild._w = null;
				this.contDates.lastChild.removeChild(this.contDates.lastChild.lastChild);
			}
			// ul
			this.contDates.removeChild(this.contDates.lastChild);
		}
		
		// div
		this.contDates.parentNode.removeChild(this.contDates);
		this.contDates = null;
		
		/* time */
		
		this.contTime.firstChild.firstChild.onclick = null;
		
		// labels
		while (this.contTime.firstChild.firstChild.childNodes.length > 0) this.contTime.firstChild.firstChild.removeChild(this.contTime.firstChild.firstChild.lastChild);
		
		// li
		this.contTime.firstChild.removeChild(this.contTime.firstChild.firstChild);
		
		// ul
		this.contTime.removeChild(this.contTime.firstChild);
		
		// div
		this.contTime.parentNode.removeChild(this.contTime);
		this.contTime = null;
		
		/* selector */
		
		this._lastHover = null;
		
		// month selector
		if (this.msCont) {
			
			this.msCont.onclick = null;
			this._msActive = null;
			
			// li
			for (var a in this._msCells) {
				this._msCells[a]._cell = null;
				this._msCells[a]._month = null;
				this._msCells[a].parentNode.removeChild(this._msCells[a]);
				this._msCells[a] = null;
			}
			this._msCells = null;
			
			// ul
			while (this.msCont.childNodes.length > 0) this.msCont.removeChild(this.msCont.lastChild);
			
			// div
			this.msCont.parentNode.removeChild(this.msCont);
			this.msCont = null;
			
		}
		
		// years selector
		if (this.ysCont) {
			
			this.ysCont.onclick = null;
			
			// li
			for (var a in this._ysCells) {
				this._ysCells[a]._cell = null;
				this._ysCells[a]._year = null;
				this._ysCells[a].parentNode.removeChild(this._ysCells[a]);
				this._ysCells[a] = null;
			}
			this._ysCells = null;
			
			// ul
			while (this.ysCont.childNodes.length > 0) this.ysCont.removeChild(this.ysCont.lastChild);
			
			// div
			this.ysCont.parentNode.removeChild(this.ysCont);
			this.ysCont = null;
			
		}
		
		// hours selector
		if (this.hsCont) {
			
			this.hsCont.onclick = null;
			this._hsActive = null;
			
			// li
			for (var a in this._hsCells) {
				this._hsCells[a]._cell = null;
				this._hsCells[a]._hours = null;
				this._hsCells[a].parentNode.removeChild(this._hsCells[a]);
				this._hsCells[a] = null;
			}
			this._hsCells = null;
			
			// ul
			while (this.hsCont.childNodes.length > 0) this.hsCont.removeChild(this.hsCont.lastChild);
			
			// div
			this.hsCont.parentNode.removeChild(this.hsCont);
			this.hsCont = null;
			
		}
		
		// minutes selector
		if (this.rsCont) {
			
			this.rsCont.onclick = null;
			this._rsActive = null;
			
			// li
			for (var a in this._rsCells) {
				this._rsCells[a]._cell = null;
				this._rsCells[a]._minutes = null;
				this._rsCells[a].parentNode.removeChild(this._rsCells[a]);
				this._rsCells[a] = null;
			}
			this._rsCells = null;
			
			// ul
			while (this.rsCont.childNodes.length > 0) this.rsCont.removeChild(this.rsCont.lastChild);
			
			// div
			this.rsCont.parentNode.removeChild(this.rsCont);
			this.rsCont = null;
			
		}
		
		// selector cover
		if (this._selCover) {
			this._selCover.parentNode.removeChild(this._selCover);
			this._selCover = null;
		}
		
		// selector object
		if (this._sel) {
			
			for (var a in this._sel._ta) this._sel._ta[a] = null;
			this._sel._ta = null;
			this._sel._t = null;
			
			this._sel.onmouseover = null;
			this._sel.onmouseout = null;
			
			// td
			while (this._sel.firstChild.firstChild.firstChild.childNodes.length > 0) {
				this._sel.firstChild.firstChild.firstChild.lastChild.onclick = null;
				this._sel.firstChild.firstChild.firstChild.lastChild.onmouseover = null;
				this._sel.firstChild.firstChild.firstChild.lastChild.onmouseout = null;
				this._sel.firstChild.firstChild.firstChild.removeChild(this._sel.firstChild.firstChild.firstChild.lastChild);
			}
			
			// tr
			this._sel.firstChild.firstChild.removeChild(this._sel.firstChild.firstChild.firstChild);
			
			// tbody
			this._sel.firstChild.removeChild(this._sel.firstChild.firstChild);
			
			// table and arrow div
			while (this._sel.childNodes.length > 0) this._sel.removeChild(this._sel.lastChild);
			
			// object
			this._sel.parentNode.removeChild(this._sel);
			this._sel = null;
		}
		
		
		/* base */
		
		this.base.onclick = null;
		this.base.parentNode.removeChild(this.base);
		this.base = null;
		
		/* methods */
		
		this._clearDayHover = null;
		this._clearSelHover = null;
		this._doOnSelectorChange = null;
		this._doOnSelectorShow = null;
		this._drawMonth = null;
		this._fixLength = null;
		this._getLeft = null;
		this._getTop = null;
		this._ifrSize = null;
		this._hide = null;
		this._hideSelector = null;
		this._initSelector = null;
		this._isSelectorVisible = null;
		this._isVisible = null;
		this._posGetOffset = null;
		this._posGetOffsetRect = null;
		this._posGetOffsetSum = null;
		this._scrollYears = null;
		this._show = null;
		this._showSelector = null;
		this._strToDate = null;
		this._updateActiveHours = null;
		this._updateActiveMinutes = null;
		this._updateActiveMonth = null;
		this._updateActiveYear = null;
		this._updateCellStyle = null;
		this._updateDateStr = null;
		this._updateVisibleHours = null;
		this._updateVisibleMinutes = null;
		this._updateYearsList = null;
		this.enableIframe = null;
		this.hide = null;
		this.hideTime = null;
		this.setDate = null;
		this.setDateFormat = null;
		this.setYearsRange = null;
		this.show = null;
		this.showTime = null;
		this.unload = null;
		
		for (var a in this) delete this[a];
		
		a = that = null;
		
	}
	
	
	// set init date
	this.setDate(this._activeDate);
	
	return this;
};

dhtmlXCalendarObject.prototype.setYearsRange = function(){}; // deprecated

dhtmlXCalendarObject.prototype.lang = "en";
dhtmlXCalendarObject.prototype.langData = {
	"en": {
		dateformat: "%Y-%m-%d",
		monthesFNames: ["January","February","March","April","May","June","July","August","September","October","November","December"],
		monthesSNames: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
		daysFNames: ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
		daysSNames: ["Su","Mo","Tu","We","Th","Fr","Sa"],
		weekstart: 1
	}
};

dhtmlXCalendarObject.prototype.enableIframe = function(mode) {
	if (mode == true) {
		if (!this._ifr) {
			this._ifr = document.createElement("IFRAME");
			this._ifr.frameBorder = 0;
			this._ifr.border = 0;
			this._ifr.setAttribute("src","javascript:false;");
			this._ifr.className = "dhtmlxcalendar_ifr";
			this._ifr.onload = function(){
				this.onload = null;
				this.contentWindow.document.open("text/html", "replace");
				this.contentWindow.document.write("<html><head><style>html,body{width:100%;height:100%;overflow:hidden;margin:0px;}</style></head><body</body></html>");
			}
			this.base.parentNode.insertBefore(this._ifr, this.base);
			this._ifrSize();
		}
	} else {
		if (this._ifr) {
			this._ifr.parentNode.removeChild(this._ifr);
			this._ifr = null;
		}
	}
};

dhtmlXCalendarObject.prototype._ifrSize = function() {
	if (this._ifr) {
		this._ifr.style.left = this.base.style.left;
		this._ifr.style.top = this.base.style.top;
		this._ifr.style.width = this.base.offsetWidth+"px";
		this._ifr.style.height = this.base.offsetHeight+"px";
	}
};

dhtmlxCalendarObject = dhtmlXCalendarObject;
