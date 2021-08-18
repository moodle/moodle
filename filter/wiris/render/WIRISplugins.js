(function(){
var $hxClasses = $hxClasses || {},$estr = function() { return js.Boot.__string_rec(this,''); };
var EReg = $hxClasses["EReg"] = function(r,opt) {
	opt = opt.split("u").join("");
	this.r = new RegExp(r,opt);
};
EReg.__name__ = ["EReg"];
EReg.prototype = {
	customReplace: function(s,f) {
		var buf = new StringBuf();
		while(true) {
			if(!this.match(s)) break;
			buf.b += Std.string(this.matchedLeft());
			buf.b += Std.string(f(this));
			s = this.matchedRight();
		}
		buf.b += Std.string(s);
		return buf.b;
	}
	,replace: function(s,by) {
		return s.replace(this.r,by);
	}
	,split: function(s) {
		var d = "#__delim__#";
		return s.replace(this.r,d).split(d);
	}
	,matchedPos: function() {
		if(this.r.m == null) throw "No string matched";
		return { pos : this.r.m.index, len : this.r.m[0].length};
	}
	,matchedRight: function() {
		if(this.r.m == null) throw "No string matched";
		var sz = this.r.m.index + this.r.m[0].length;
		return this.r.s.substr(sz,this.r.s.length - sz);
	}
	,matchedLeft: function() {
		if(this.r.m == null) throw "No string matched";
		return this.r.s.substr(0,this.r.m.index);
	}
	,matched: function(n) {
		return this.r.m != null && n >= 0 && n < this.r.m.length?this.r.m[n]:(function($this) {
			var $r;
			throw "EReg::matched";
			return $r;
		}(this));
	}
	,match: function(s) {
		if(this.r.global) this.r.lastIndex = 0;
		this.r.m = this.r.exec(s);
		this.r.s = s;
		return this.r.m != null;
	}
	,r: null
	,__class__: EReg
}
var Hash = $hxClasses["Hash"] = function() {
	this.h = { };
};
Hash.__name__ = ["Hash"];
Hash.prototype = {
	toString: function() {
		var s = new StringBuf();
		s.b += Std.string("{");
		var it = this.keys();
		while( it.hasNext() ) {
			var i = it.next();
			s.b += Std.string(i);
			s.b += Std.string(" => ");
			s.b += Std.string(Std.string(this.get(i)));
			if(it.hasNext()) s.b += Std.string(", ");
		}
		s.b += Std.string("}");
		return s.b;
	}
	,iterator: function() {
		return { ref : this.h, it : this.keys(), hasNext : function() {
			return this.it.hasNext();
		}, next : function() {
			var i = this.it.next();
			return this.ref["$" + i];
		}};
	}
	,keys: function() {
		var a = [];
		for( var key in this.h ) {
		if(this.h.hasOwnProperty(key)) a.push(key.substr(1));
		}
		return HxOverrides.iter(a);
	}
	,remove: function(key) {
		key = "$" + key;
		if(!this.h.hasOwnProperty(key)) return false;
		delete(this.h[key]);
		return true;
	}
	,exists: function(key) {
		return this.h.hasOwnProperty("$" + key);
	}
	,get: function(key) {
		return this.h["$" + key];
	}
	,set: function(key,value) {
		this.h["$" + key] = value;
	}
	,h: null
	,__class__: Hash
}
var HxOverrides = $hxClasses["HxOverrides"] = function() { }
HxOverrides.__name__ = ["HxOverrides"];
HxOverrides.dateStr = function(date) {
	var m = date.getMonth() + 1;
	var d = date.getDate();
	var h = date.getHours();
	var mi = date.getMinutes();
	var s = date.getSeconds();
	return date.getFullYear() + "-" + (m < 10?"0" + m:"" + m) + "-" + (d < 10?"0" + d:"" + d) + " " + (h < 10?"0" + h:"" + h) + ":" + (mi < 10?"0" + mi:"" + mi) + ":" + (s < 10?"0" + s:"" + s);
}
HxOverrides.strDate = function(s) {
	switch(s.length) {
	case 8:
		var k = s.split(":");
		var d = new Date();
		d.setTime(0);
		d.setUTCHours(k[0]);
		d.setUTCMinutes(k[1]);
		d.setUTCSeconds(k[2]);
		return d;
	case 10:
		var k = s.split("-");
		return new Date(k[0],k[1] - 1,k[2],0,0,0);
	case 19:
		var k = s.split(" ");
		var y = k[0].split("-");
		var t = k[1].split(":");
		return new Date(y[0],y[1] - 1,y[2],t[0],t[1],t[2]);
	default:
		throw "Invalid date format : " + s;
	}
}
HxOverrides.cca = function(s,index) {
	var x = s.charCodeAt(index);
	if(x != x) return undefined;
	return x;
}
HxOverrides.substr = function(s,pos,len) {
	if(pos != null && pos != 0 && len != null && len < 0) return "";
	if(len == null) len = s.length;
	if(pos < 0) {
		pos = s.length + pos;
		if(pos < 0) pos = 0;
	} else if(len < 0) len = s.length + len - pos;
	return s.substr(pos,len);
}
HxOverrides.remove = function(a,obj) {
	var i = 0;
	var l = a.length;
	while(i < l) {
		if(a[i] == obj) {
			a.splice(i,1);
			return true;
		}
		i++;
	}
	return false;
}
HxOverrides.iter = function(a) {
	return { cur : 0, arr : a, hasNext : function() {
		return this.cur < this.arr.length;
	}, next : function() {
		return this.arr[this.cur++];
	}};
}
var IntHash = $hxClasses["IntHash"] = function() {
	this.h = { };
};
IntHash.__name__ = ["IntHash"];
IntHash.prototype = {
	toString: function() {
		var s = new StringBuf();
		s.b += Std.string("{");
		var it = this.keys();
		while( it.hasNext() ) {
			var i = it.next();
			s.b += Std.string(i);
			s.b += Std.string(" => ");
			s.b += Std.string(Std.string(this.get(i)));
			if(it.hasNext()) s.b += Std.string(", ");
		}
		s.b += Std.string("}");
		return s.b;
	}
	,iterator: function() {
		return { ref : this.h, it : this.keys(), hasNext : function() {
			return this.it.hasNext();
		}, next : function() {
			var i = this.it.next();
			return this.ref[i];
		}};
	}
	,keys: function() {
		var a = [];
		for( var key in this.h ) {
		if(this.h.hasOwnProperty(key)) a.push(key | 0);
		}
		return HxOverrides.iter(a);
	}
	,remove: function(key) {
		if(!this.h.hasOwnProperty(key)) return false;
		delete(this.h[key]);
		return true;
	}
	,exists: function(key) {
		return this.h.hasOwnProperty(key);
	}
	,get: function(key) {
		return this.h[key];
	}
	,set: function(key,value) {
		this.h[key] = value;
	}
	,h: null
	,__class__: IntHash
}
var IntIter = $hxClasses["IntIter"] = function(min,max) {
	this.min = min;
	this.max = max;
};
IntIter.__name__ = ["IntIter"];
IntIter.prototype = {
	next: function() {
		return this.min++;
	}
	,hasNext: function() {
		return this.min < this.max;
	}
	,max: null
	,min: null
	,__class__: IntIter
}
var List = $hxClasses["List"] = function() {
	this.length = 0;
};
List.__name__ = ["List"];
List.prototype = {
	map: function(f) {
		var b = new List();
		var l = this.h;
		while(l != null) {
			var v = l[0];
			l = l[1];
			b.add(f(v));
		}
		return b;
	}
	,filter: function(f) {
		var l2 = new List();
		var l = this.h;
		while(l != null) {
			var v = l[0];
			l = l[1];
			if(f(v)) l2.add(v);
		}
		return l2;
	}
	,join: function(sep) {
		var s = new StringBuf();
		var first = true;
		var l = this.h;
		while(l != null) {
			if(first) first = false; else s.b += Std.string(sep);
			s.b += Std.string(l[0]);
			l = l[1];
		}
		return s.b;
	}
	,toString: function() {
		var s = new StringBuf();
		var first = true;
		var l = this.h;
		s.b += Std.string("{");
		while(l != null) {
			if(first) first = false; else s.b += Std.string(", ");
			s.b += Std.string(Std.string(l[0]));
			l = l[1];
		}
		s.b += Std.string("}");
		return s.b;
	}
	,iterator: function() {
		return { h : this.h, hasNext : function() {
			return this.h != null;
		}, next : function() {
			if(this.h == null) return null;
			var x = this.h[0];
			this.h = this.h[1];
			return x;
		}};
	}
	,remove: function(v) {
		var prev = null;
		var l = this.h;
		while(l != null) {
			if(l[0] == v) {
				if(prev == null) this.h = l[1]; else prev[1] = l[1];
				if(this.q == l) this.q = prev;
				this.length--;
				return true;
			}
			prev = l;
			l = l[1];
		}
		return false;
	}
	,clear: function() {
		this.h = null;
		this.q = null;
		this.length = 0;
	}
	,isEmpty: function() {
		return this.h == null;
	}
	,pop: function() {
		if(this.h == null) return null;
		var x = this.h[0];
		this.h = this.h[1];
		if(this.h == null) this.q = null;
		this.length--;
		return x;
	}
	,last: function() {
		return this.q == null?null:this.q[0];
	}
	,first: function() {
		return this.h == null?null:this.h[0];
	}
	,push: function(item) {
		var x = [item,this.h];
		this.h = x;
		if(this.q == null) this.q = x;
		this.length++;
	}
	,add: function(item) {
		var x = [item];
		if(this.h == null) this.h = x; else this.q[1] = x;
		this.q = x;
		this.length++;
	}
	,length: null
	,q: null
	,h: null
	,__class__: List
}
var Reflect = $hxClasses["Reflect"] = function() { }
Reflect.__name__ = ["Reflect"];
Reflect.hasField = function(o,field) {
	return Object.prototype.hasOwnProperty.call(o,field);
}
Reflect.field = function(o,field) {
	var v = null;
	try {
		v = o[field];
	} catch( e ) {
	}
	return v;
}
Reflect.setField = function(o,field,value) {
	o[field] = value;
}
Reflect.getProperty = function(o,field) {
	var tmp;
	return o == null?null:o.__properties__ && (tmp = o.__properties__["get_" + field])?o[tmp]():o[field];
}
Reflect.setProperty = function(o,field,value) {
	var tmp;
	if(o.__properties__ && (tmp = o.__properties__["set_" + field])) o[tmp](value); else o[field] = value;
}
Reflect.callMethod = function(o,func,args) {
	return func.apply(o,args);
}
Reflect.fields = function(o) {
	var a = [];
	if(o != null) {
		var hasOwnProperty = Object.prototype.hasOwnProperty;
		for( var f in o ) {
		if(hasOwnProperty.call(o,f)) a.push(f);
		}
	}
	return a;
}
Reflect.isFunction = function(f) {
	return typeof(f) == "function" && !(f.__name__ || f.__ename__);
}
Reflect.compare = function(a,b) {
	return a == b?0:a > b?1:-1;
}
Reflect.compareMethods = function(f1,f2) {
	if(f1 == f2) return true;
	if(!Reflect.isFunction(f1) || !Reflect.isFunction(f2)) return false;
	return f1.scope == f2.scope && f1.method == f2.method && f1.method != null;
}
Reflect.isObject = function(v) {
	if(v == null) return false;
	var t = typeof(v);
	return t == "string" || t == "object" && !v.__enum__ || t == "function" && (v.__name__ || v.__ename__);
}
Reflect.deleteField = function(o,f) {
	if(!Reflect.hasField(o,f)) return false;
	delete(o[f]);
	return true;
}
Reflect.copy = function(o) {
	var o2 = { };
	var _g = 0, _g1 = Reflect.fields(o);
	while(_g < _g1.length) {
		var f = _g1[_g];
		++_g;
		o2[f] = Reflect.field(o,f);
	}
	return o2;
}
Reflect.makeVarArgs = function(f) {
	return function() {
		var a = Array.prototype.slice.call(arguments);
		return f(a);
	};
}
var Std = $hxClasses["Std"] = function() { }
Std.__name__ = ["Std"];
Std["is"] = function(v,t) {
	return js.Boot.__instanceof(v,t);
}
Std.string = function(s) {
	return js.Boot.__string_rec(s,"");
}
Std["int"] = function(x) {
	return x | 0;
}
Std.parseInt = function(x) {
	var v = parseInt(x,10);
	if(v == 0 && (HxOverrides.cca(x,1) == 120 || HxOverrides.cca(x,1) == 88)) v = parseInt(x);
	if(isNaN(v)) return null;
	return v;
}
Std.parseFloat = function(x) {
	return parseFloat(x);
}
Std.random = function(x) {
	return Math.floor(Math.random() * x);
}
var StringBuf = $hxClasses["StringBuf"] = function() {
	this.b = "";
};
StringBuf.__name__ = ["StringBuf"];
StringBuf.prototype = {
	toString: function() {
		return this.b;
	}
	,addSub: function(s,pos,len) {
		this.b += HxOverrides.substr(s,pos,len);
	}
	,addChar: function(c) {
		this.b += String.fromCharCode(c);
	}
	,add: function(x) {
		this.b += Std.string(x);
	}
	,b: null
	,__class__: StringBuf
}
var StringTools = $hxClasses["StringTools"] = function() { }
StringTools.__name__ = ["StringTools"];
StringTools.urlEncode = function(s) {
	return encodeURIComponent(s);
}
StringTools.urlDecode = function(s) {
	return decodeURIComponent(s.split("+").join(" "));
}
StringTools.htmlEscape = function(s) {
	return s.split("&").join("&amp;").split("<").join("&lt;").split(">").join("&gt;");
}
StringTools.htmlUnescape = function(s) {
	return s.split("&gt;").join(">").split("&lt;").join("<").split("&amp;").join("&");
}
StringTools.startsWith = function(s,start) {
	return s.length >= start.length && HxOverrides.substr(s,0,start.length) == start;
}
StringTools.endsWith = function(s,end) {
	var elen = end.length;
	var slen = s.length;
	return slen >= elen && HxOverrides.substr(s,slen - elen,elen) == end;
}
StringTools.isSpace = function(s,pos) {
	var c = HxOverrides.cca(s,pos);
	return c >= 9 && c <= 13 || c == 32;
}
StringTools.ltrim = function(s) {
	var l = s.length;
	var r = 0;
	while(r < l && StringTools.isSpace(s,r)) r++;
	if(r > 0) return HxOverrides.substr(s,r,l - r); else return s;
}
StringTools.rtrim = function(s) {
	var l = s.length;
	var r = 0;
	while(r < l && StringTools.isSpace(s,l - r - 1)) r++;
	if(r > 0) return HxOverrides.substr(s,0,l - r); else return s;
}
StringTools.trim = function(s) {
	return StringTools.ltrim(StringTools.rtrim(s));
}
StringTools.rpad = function(s,c,l) {
	var sl = s.length;
	var cl = c.length;
	while(sl < l) if(l - sl < cl) {
		s += HxOverrides.substr(c,0,l - sl);
		sl = l;
	} else {
		s += c;
		sl += cl;
	}
	return s;
}
StringTools.lpad = function(s,c,l) {
	var ns = "";
	var sl = s.length;
	if(sl >= l) return s;
	var cl = c.length;
	while(sl < l) if(l - sl < cl) {
		ns += HxOverrides.substr(c,0,l - sl);
		sl = l;
	} else {
		ns += c;
		sl += cl;
	}
	return ns + s;
}
StringTools.replace = function(s,sub,by) {
	return s.split(sub).join(by);
}
StringTools.hex = function(n,digits) {
	var s = "";
	var hexChars = "0123456789ABCDEF";
	do {
		s = hexChars.charAt(n & 15) + s;
		n >>>= 4;
	} while(n > 0);
	if(digits != null) while(s.length < digits) s = "0" + s;
	return s;
}
StringTools.fastCodeAt = function(s,index) {
	return s.charCodeAt(index);
}
StringTools.isEOF = function(c) {
	return c != c;
}
var ValueType = $hxClasses["ValueType"] = { __ename__ : ["ValueType"], __constructs__ : ["TNull","TInt","TFloat","TBool","TObject","TFunction","TClass","TEnum","TUnknown"] }
ValueType.TNull = ["TNull",0];
ValueType.TNull.toString = $estr;
ValueType.TNull.__enum__ = ValueType;
ValueType.TInt = ["TInt",1];
ValueType.TInt.toString = $estr;
ValueType.TInt.__enum__ = ValueType;
ValueType.TFloat = ["TFloat",2];
ValueType.TFloat.toString = $estr;
ValueType.TFloat.__enum__ = ValueType;
ValueType.TBool = ["TBool",3];
ValueType.TBool.toString = $estr;
ValueType.TBool.__enum__ = ValueType;
ValueType.TObject = ["TObject",4];
ValueType.TObject.toString = $estr;
ValueType.TObject.__enum__ = ValueType;
ValueType.TFunction = ["TFunction",5];
ValueType.TFunction.toString = $estr;
ValueType.TFunction.__enum__ = ValueType;
ValueType.TClass = function(c) { var $x = ["TClass",6,c]; $x.__enum__ = ValueType; $x.toString = $estr; return $x; }
ValueType.TEnum = function(e) { var $x = ["TEnum",7,e]; $x.__enum__ = ValueType; $x.toString = $estr; return $x; }
ValueType.TUnknown = ["TUnknown",8];
ValueType.TUnknown.toString = $estr;
ValueType.TUnknown.__enum__ = ValueType;
var Type = $hxClasses["Type"] = function() { }
Type.__name__ = ["Type"];
Type.getClass = function(o) {
	if(o == null) return null;
	return o.__class__;
}
Type.getEnum = function(o) {
	if(o == null) return null;
	return o.__enum__;
}
Type.getSuperClass = function(c) {
	return c.__super__;
}
Type.getClassName = function(c) {
	var a = c.__name__;
	return a.join(".");
}
Type.getEnumName = function(e) {
	var a = e.__ename__;
	return a.join(".");
}
Type.resolveClass = function(name) {
	var cl = $hxClasses[name];
	if(cl == null || !cl.__name__) return null;
	return cl;
}
Type.resolveEnum = function(name) {
	var e = $hxClasses[name];
	if(e == null || !e.__ename__) return null;
	return e;
}
Type.createInstance = function(cl,args) {
	switch(args.length) {
	case 0:
		return new cl();
	case 1:
		return new cl(args[0]);
	case 2:
		return new cl(args[0],args[1]);
	case 3:
		return new cl(args[0],args[1],args[2]);
	case 4:
		return new cl(args[0],args[1],args[2],args[3]);
	case 5:
		return new cl(args[0],args[1],args[2],args[3],args[4]);
	case 6:
		return new cl(args[0],args[1],args[2],args[3],args[4],args[5]);
	case 7:
		return new cl(args[0],args[1],args[2],args[3],args[4],args[5],args[6]);
	case 8:
		return new cl(args[0],args[1],args[2],args[3],args[4],args[5],args[6],args[7]);
	default:
		throw "Too many arguments";
	}
	return null;
}
Type.createEmptyInstance = function(cl) {
	function empty() {}; empty.prototype = cl.prototype;
	return new empty();
}
Type.createEnum = function(e,constr,params) {
	var f = Reflect.field(e,constr);
	if(f == null) throw "No such constructor " + constr;
	if(Reflect.isFunction(f)) {
		if(params == null) throw "Constructor " + constr + " need parameters";
		return f.apply(e,params);
	}
	if(params != null && params.length != 0) throw "Constructor " + constr + " does not need parameters";
	return f;
}
Type.createEnumIndex = function(e,index,params) {
	var c = e.__constructs__[index];
	if(c == null) throw index + " is not a valid enum constructor index";
	return Type.createEnum(e,c,params);
}
Type.getInstanceFields = function(c) {
	var a = [];
	for(var i in c.prototype) a.push(i);
	HxOverrides.remove(a,"__class__");
	HxOverrides.remove(a,"__properties__");
	return a;
}
Type.getClassFields = function(c) {
	var a = Reflect.fields(c);
	HxOverrides.remove(a,"__name__");
	HxOverrides.remove(a,"__interfaces__");
	HxOverrides.remove(a,"__properties__");
	HxOverrides.remove(a,"__super__");
	HxOverrides.remove(a,"prototype");
	return a;
}
Type.getEnumConstructs = function(e) {
	var a = e.__constructs__;
	return a.slice();
}
Type["typeof"] = function(v) {
	switch(typeof(v)) {
	case "boolean":
		return ValueType.TBool;
	case "string":
		return ValueType.TClass(String);
	case "number":
		if(Math.ceil(v) == v % 2147483648.0) return ValueType.TInt;
		return ValueType.TFloat;
	case "object":
		if(v == null) return ValueType.TNull;
		var e = v.__enum__;
		if(e != null) return ValueType.TEnum(e);
		var c = v.__class__;
		if(c != null) return ValueType.TClass(c);
		return ValueType.TObject;
	case "function":
		if(v.__name__ || v.__ename__) return ValueType.TObject;
		return ValueType.TFunction;
	case "undefined":
		return ValueType.TNull;
	default:
		return ValueType.TUnknown;
	}
}
Type.enumEq = function(a,b) {
	if(a == b) return true;
	try {
		if(a[0] != b[0]) return false;
		var _g1 = 2, _g = a.length;
		while(_g1 < _g) {
			var i = _g1++;
			if(!Type.enumEq(a[i],b[i])) return false;
		}
		var e = a.__enum__;
		if(e != b.__enum__ || e == null) return false;
	} catch( e ) {
		return false;
	}
	return true;
}
Type.enumConstructor = function(e) {
	return e[0];
}
Type.enumParameters = function(e) {
	return e.slice(2);
}
Type.enumIndex = function(e) {
	return e[1];
}
Type.allEnums = function(e) {
	var all = [];
	var cst = e.__constructs__;
	var _g = 0;
	while(_g < cst.length) {
		var c = cst[_g];
		++_g;
		var v = Reflect.field(e,c);
		if(!Reflect.isFunction(v)) all.push(v);
	}
	return all;
}
var com = com || {}
if(!com.wiris) com.wiris = {}
if(!com.wiris.js) com.wiris.js = {}
com.wiris.js.JsBrowserData = $hxClasses["com.wiris.js.JsBrowserData"] = function() {
};
com.wiris.js.JsBrowserData.__name__ = ["com","wiris","js","JsBrowserData"];
com.wiris.js.JsBrowserData.prototype = {
	identity: null
	,versionSearch: null
	,subString: null
	,prop: null
	,string: null
	,__class__: com.wiris.js.JsBrowserData
}
com.wiris.js.JsOSData = $hxClasses["com.wiris.js.JsOSData"] = function() {
};
com.wiris.js.JsOSData.__name__ = ["com","wiris","js","JsOSData"];
com.wiris.js.JsOSData.prototype = {
	identity: null
	,subString: null
	,string: null
	,__class__: com.wiris.js.JsOSData
}
com.wiris.js.JsBrowser = $hxClasses["com.wiris.js.JsBrowser"] = function() {
	this.dataBrowser = new Array();
	this.addBrowser("navigator.userAgent",null,"Edge",null,"Edge");
	this.addBrowser("navigator.userAgent",null,"Chrome",null,"Chrome");
	this.addBrowser("navigator.userAgent",null,"OmniWeb",null,"OmniWeb");
	this.addBrowser("navigator.vendor",null,"Apple","Version","Safari");
	this.addBrowser(null,"window.opera",null,"Version","Opera");
	this.addBrowser("navigator.vendor",null,"iCab",null,"iCab");
	this.addBrowser("navigator.vendor",null,"KDE",null,"Konkeror");
	this.addBrowser("navigator.userAgent",null,"Firefox",null,"Firefox");
	this.addBrowser("navigator.vendor",null,"Camino",null,"Camino");
	this.addBrowser("navigator.userAgent",null,"Netscape",null,"Netscape");
	this.addBrowser("navigator.userAgent",null,"MSIE","MSIE","Explorer");
	this.addBrowser("navigator.userAgent",null,"Trident","rv","Explorer");
	this.addBrowser("navigator.userAgent",null,"Gecko","rv","Mozilla");
	this.addBrowser("navigator.userAgent",null,"Mozilla","Mozilla","Netscape");
	this.dataOS = new Array();
	this.addOS("navigator.platform","Win","Windows");
	this.addOS("navigator.platform","Mac","Mac");
	this.addOS("navigator.userAgent","iPhone","iOS");
	this.addOS("navigator.userAgent","iPad","iOS");
	this.addOS("navigator.userAgent","Android","Android");
	this.addOS("navigator.platform","Linux","Linux");
	this.setBrowser();
	this.setOS();
	this.setTouchable();
};
com.wiris.js.JsBrowser.__name__ = ["com","wiris","js","JsBrowser"];
com.wiris.js.JsBrowser.prototype = {
	isTouchable: function() {
		return this.touchable;
	}
	,isAndroid: function() {
		return this.os == "Android";
	}
	,isMac: function() {
		return this.os == "Mac";
	}
	,isIOS: function() {
		return this.os == "iOS";
	}
	,isFF: function() {
		return this.browser == "Firefox";
	}
	,isSafari: function() {
		return this.browser == "Safari";
	}
	,isChrome: function() {
		return this.browser == "Chrome";
	}
	,isEdge: function() {
		return this.browser == "Edge";
	}
	,isIE: function() {
		return this.browser == "Explorer";
	}
	,getVersion: function() {
		return this.ver;
	}
	,getOS: function() {
		return this.os;
	}
	,getBrowser: function() {
		return this.browser;
	}
	,searchVersion: function(prop,search) {
		var str = js.Boot.__cast(eval(prop) , String);
		var index = str.indexOf(search);
		if(index == -1) return null;
		return "" + Std.parseFloat(HxOverrides.substr(str,index + search.length + 1,null));
	}
	,setTouchable: function() {
		if(this.isIOS() || this.isAndroid()) {
			this.touchable = true;
			return;
		}
		this.touchable = false;
	}
	,setOS: function() {
		var i = HxOverrides.iter(this.dataOS);
		while(i.hasNext()) {
			var s = i.next();
			var str = js.Boot.__cast(eval(s.string) , String);
			if(str.indexOf(s.subString) != -1) {
				this.os = s.identity;
				return;
			}
		}
	}
	,setBrowser: function() {
		var i = HxOverrides.iter(this.dataBrowser);
		while(i.hasNext()) {
			var b = i.next();
			if(b.string != null) {
				var obj = eval(b.string);
				if(obj != null) {
					var str = js.Boot.__cast(obj , String);
					if(str.indexOf(b.subString) != -1) {
						this.browser = b.identity;
						this.ver = this.searchVersion("navigator.userAgent",b.versionSearch);
						if(this.ver == null) this.ver = this.searchVersion("navigator.appVersion",b.versionSearch);
						return;
					}
				}
			}
		}
	}
	,addOS: function(string,subString,identity) {
		var s = new com.wiris.js.JsOSData();
		s.string = string;
		s.subString = subString;
		s.identity = identity;
		this.dataOS.push(s);
	}
	,addBrowser: function(string,prop,subString,versionSearch,identity) {
		var b = new com.wiris.js.JsBrowserData();
		b.string = string;
		b.prop = prop;
		b.subString = subString;
		b.versionSearch = versionSearch != null?versionSearch:identity;
		b.identity = identity;
		this.dataBrowser.push(b);
	}
	,touchable: null
	,os: null
	,ver: null
	,browser: null
	,dataOS: null
	,dataBrowser: null
	,__class__: com.wiris.js.JsBrowser
}
com.wiris.js.JsCharacters = $hxClasses["com.wiris.js.JsCharacters"] = function() { }
com.wiris.js.JsCharacters.__name__ = ["com","wiris","js","JsCharacters"];
com.wiris.js.JsCharacters.getSafeXMLCharactersEntities = function() {
	return { tagOpener : "&laquo;", tagCloser : "&raquo;", doubleQuote : "&uml;", realDoubleQuote : "&quot;"};
}
com.wiris.js.JsCharacters.getXMLCharacters = function() {
	return { id : "xmlCharacters", tagOpener : "<", tagCloser : ">", doubleQuote : "\"", ampersand : "&", quote : "'"};
}
com.wiris.js.JsCharacters.getSafeXMLCharacters = function() {
	return { id : "safeXmlCharacters", tagOpener : "«", tagCloser : "»", doubleQuote : "¨", ampersand : "§", quote : "`", realDoubleQuote : "¨"};
}
com.wiris.js.JsMathML = $hxClasses["com.wiris.js.JsMathML"] = function() { }
com.wiris.js.JsMathML.__name__ = ["com","wiris","js","JsMathML"];
com.wiris.js.JsMathML.decodeSafeMathML = function(input) {
	var safeXMLCharactersEntities = com.wiris.js.JsCharacters.getSafeXMLCharactersEntities();
	var xmlCharacters = com.wiris.js.JsCharacters.getXMLCharacters();
	var safeXMLCharacters = com.wiris.js.JsCharacters.getSafeXMLCharacters();
	var tagOpenerEntity = safeXMLCharactersEntities.tagOpener;
	var tagCloserEntity = safeXMLCharactersEntities.tagCloser;
	var doubleQuoteEntity = safeXMLCharactersEntities.doubleQuote;
	var realDoubleQuoteEntity = safeXMLCharactersEntities.realDoubleQuote;
	var inputCopy = input.slice();
	inputCopy = inputCopy.split(tagOpenerEntity).join(safeXMLCharacters.tagOpener);
	inputCopy = inputCopy.split(tagCloserEntity).join(safeXMLCharacters.tagCloser);
	inputCopy = inputCopy.split(doubleQuoteEntity).join(safeXMLCharacters.doubleQuote);
	inputCopy = inputCopy.split(realDoubleQuoteEntity).join(safeXMLCharacters.realDoubleQuote);
	var tagOpener = safeXMLCharacters.tagOpener;
	var tagCloser = safeXMLCharacters.tagCloser;
	var doubleQuote = safeXMLCharacters.doubleQuote;
	var realDoubleQuote = safeXMLCharacters.realDoubleQuote;
	var ampersand = safeXMLCharacters.ampersand;
	var quote = safeXMLCharacters.quote;
	inputCopy = inputCopy.split(tagOpener).join(xmlCharacters.tagOpener);
	inputCopy = inputCopy.split(tagCloser).join(xmlCharacters.tagCloser);
	inputCopy = inputCopy.split(doubleQuote).join(xmlCharacters.doubleQuote);
	inputCopy = inputCopy.split(ampersand).join(xmlCharacters.ampersand);
	inputCopy = inputCopy.split(quote).join(xmlCharacters.quote);
	var returnValue = "";
	var currentEntity = null;
	var i = 0;
	while(i < inputCopy.length) {
		var character = inputCopy.charAt(i);
		if(currentEntity == null) {
			if(character == "$") currentEntity = ""; else returnValue += character;
		} else if(character == ";") {
			returnValue += "&" + currentEntity;
			currentEntity = null;
		} else if(character.match(new EReg("([a-zA-Z0-9#._-] | '-')",""))) currentEntity += character; else {
			returnValue += "$" + "currentEntity";
			currentEntity = null;
			i -= 1;
		}
		i++;
	}
	return returnValue;
}
com.wiris.js.JsPluginListener = $hxClasses["com.wiris.js.JsPluginListener"] = function() { }
com.wiris.js.JsPluginListener.__name__ = ["com","wiris","js","JsPluginListener"];
com.wiris.js.JsPluginListener.prototype = {
	afterParseLatex: null
	,afterParse: null
	,__class__: com.wiris.js.JsPluginListener
}
com.wiris.js.JsPluginTools = $hxClasses["com.wiris.js.JsPluginTools"] = function() {
	this.tryReady();
};
com.wiris.js.JsPluginTools.__name__ = ["com","wiris","js","JsPluginTools"];
com.wiris.js.JsPluginTools.instance = null;
com.wiris.js.JsPluginTools.main = function() {
	var ev;
	ev = com.wiris.js.JsPluginTools.getInstance();
	haxe.Timer.delay($bind(ev,ev.tryReady),100);
}
com.wiris.js.JsPluginTools.getInstance = function() {
	if(com.wiris.js.JsPluginTools.instance == null) com.wiris.js.JsPluginTools.instance = new com.wiris.js.JsPluginTools();
	return com.wiris.js.JsPluginTools.instance;
}
com.wiris.js.JsPluginTools.bypassEncapsulation = function() {
	if(window.com == null) window.com = { };
	if(window.com.wiris == null) window.com.wiris = { };
	if(window.com.wiris.js == null) window.com.wiris.js = { };
	if(window.com.wiris.js.JsPluginTools == null) window.com.wiris.js.JsPluginTools = com.wiris.js.JsPluginTools.getInstance();
}
com.wiris.js.JsPluginTools.prototype = {
	md5encode: function(content) {
		return haxe.Md5.encode(content);
	}
	,doLoad: function() {
		this.ready = true;
		com.wiris.js.JsPluginTools.instance = this;
		com.wiris.js.JsPluginTools.bypassEncapsulation();
	}
	,tryReady: function() {
		this.ready = false;
		if(js.Lib.document.readyState) {
			this.doLoad();
			this.ready = true;
		}
		if(!this.ready) haxe.Timer.delay($bind(this,this.tryReady),100);
	}
	,ready: null
	,__class__: com.wiris.js.JsPluginTools
}
com.wiris.js.JsPluginViewer = $hxClasses["com.wiris.js.JsPluginViewer"] = function() {
	this._wrs_conf_imageFormat = null;
	this.javaServicePath = "/pluginwiris_engine/app";
	this.wiriseditormathmlattribute = null;
	this.performanceenabled = null;
	this.eventListenersArray = [];
	this.callsShowImageNumber = 0;
	this.callsLatexToMathml = 0;
	this.params = new Hash();
	this.mode = com.wiris.js.JsPluginViewer.USE_CREATE_IMAGE;
	this.zoom = 1;
	this.viewer = "";
	this.lang = "inherit";
	this.safeXml = false;
	this.ready = false;
	this.extension = "@param.configuration.script.extension@";
	this.localpath = "@param.configuration.script.local.path@";
	this.absoluteURL = "@param.configuration.script.base.path@";
	if(this.extension.indexOf("@") >= 0) this.extension = "";
	if(this.localpath.indexOf("@") >= 0) this.localpath = "/app";
	if(this.absoluteURL.indexOf("@") >= 0) this.absoluteURL = "/java-java-context-root";
};
com.wiris.js.JsPluginViewer.__name__ = ["com","wiris","js","JsPluginViewer"];
com.wiris.js.JsPluginViewer.instance = null;
com.wiris.js.JsPluginViewer.main = function() {
	var ev;
	ev = com.wiris.js.JsPluginViewer.getInstance();
	haxe.Timer.delay($bind(ev,ev.tryReady),100);
}
com.wiris.js.JsPluginViewer.getInstance = function() {
	if(com.wiris.js.JsPluginViewer.instance == null) com.wiris.js.JsPluginViewer.instance = new com.wiris.js.JsPluginViewer();
	return com.wiris.js.JsPluginViewer.instance;
}
com.wiris.js.JsPluginViewer.bypassEncapsulation = function() {
	if(window.com == null) window.com = { };
	if(window.com.wiris == null) window.com.wiris = { };
	if(window.com.wiris.js == null) window.com.wiris.js = { };
	if(window.com.wiris.js.JsPluginViewer == null) window.com.wiris.js.JsPluginViewer = com.wiris.js.JsPluginViewer.getInstance();
}
com.wiris.js.JsPluginViewer.prototype = {
	removeViewerListener: function(listener) {
		while(HxOverrides.remove(this.eventListenersArray,listener)) {
		}
	}
	,addViewerListener: function(listener) {
		this.eventListenersArray.push(listener);
	}
	,getBaseURL: function() {
		return this.baseURL;
	}
	,queryToParams: function(query) {
		var ss = query.split("&");
		var h = new Hash();
		var _g = 0;
		while(_g < ss.length) {
			var s = ss[_g];
			++_g;
			var kv = s.split("=");
			if(kv.length > 1) h.set(kv[0],StringTools.urlDecode(kv[1]));
		}
		return h;
	}
	,isPerformanceEnabled: function() {
		var data;
		data = this.callGetVariableKeys("wirispluginperformance");
		if(haxe.Json.parse(data).status != "ok") return false; else return haxe.Json.parse(data).result.wirispluginperformance == "true"?true:false;
	}
	,callGetVariableKeys: function(variableKeys) {
		var con;
		var data;
		var url;
		url = (this.absoluteURL.length > 0?this.absoluteURL:this.baseURL + this.localpath) + "/configurationjson" + this.extension;
		con = new js.XMLHttpRequest();
		data = "?variablekeys=" + variableKeys;
		con.open("GET",url + data,false);
		con.setRequestHeader("Content-type","application/x-www-form-urlencoded; charset=utf-8");
		con.send(null);
		return con.responseText;
	}
	,callService: function(mml,servicename) {
		var con;
		var height = 0;
		var width = 0;
		var baseline = 0;
		var text = null;
		var data;
		var url;
		con = new js.XMLHttpRequest();
		url = (this.absoluteURL.length > 0?this.absoluteURL:this.baseURL + this.localpath) + "/service" + this.extension;
		data = "service=" + servicename;
		data += "&metrics=true&centerbaseline=false&mml=" + StringTools.urlEncode(mml);
		data += "&lang=" + this.lang;
		if(this.zoom != 1) data += "&zoom=" + this.zoom;
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("Calling: " + url,{ fileName : "JsPluginViewer.hx", lineNumber : 928, className : "com.wiris.js.JsPluginViewer", methodName : "callService"});
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("POST:" + data,{ fileName : "JsPluginViewer.hx", lineNumber : 930, className : "com.wiris.js.JsPluginViewer", methodName : "callService"});
		con.open("POST",url,false);
		con.setRequestHeader("Content-type","application/x-www-form-urlencoded; charset=utf-8");
		con.send(data);
		return con.responseText;
	}
	,latexToMathml: function(latex,nodeAfter,asynchronously,callbackFunc) {
		var _g = this;
		var con = new js.XMLHttpRequest();
		var url = (this.absoluteURL.length > 0?this.absoluteURL:this.baseURL + this.localpath) + "/service" + this.extension;
		var data = "service=latex2mathml";
		data += "&latex=" + StringTools.urlEncode(latex);
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("Calling: " + url,{ fileName : "JsPluginViewer.hx", lineNumber : 862, className : "com.wiris.js.JsPluginViewer", methodName : "latexToMathml"});
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("POST:" + data,{ fileName : "JsPluginViewer.hx", lineNumber : 864, className : "com.wiris.js.JsPluginViewer", methodName : "latexToMathml"});
		var onloadFunc = function(e) {
			try {
				var mathml = haxe.Json.parse(con.responseText).result.text;
				var newMathml = js.Lib.document.createElement("math");
				var mathmlSpan = js.Lib.document.createElement("span");
				mathmlSpan.appendChild(newMathml);
				e.target.wiris.nodeAfter.parentElement.insertBefore(mathmlSpan,e.target.wiris.nodeAfter.nextSibling);
				newMathml.outerHTML = mathml;
				if(--_g.callsLatexToMathml == 0) {
					var _g2 = 0, _g1 = _g.eventListenersArray.length;
					while(_g2 < _g1) {
						var i = _g2++;
						try {
							_g.eventListenersArray[i].afterParseLatex();
						} catch( e1 ) {
						}
					}
				}
				e.target.wiris.callbackFunc(mathmlSpan);
				_g.parseElement(mathmlSpan,asynchronously);
			} catch( e1 ) {
				if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("LatexToMathml call failed!",{ fileName : "JsPluginViewer.hx", lineNumber : 890, className : "com.wiris.js.JsPluginViewer", methodName : "latexToMathml"});
			}
		};
		con.open("POST",url,asynchronously);
		var newDynamic = { };
		con.wiris = newDynamic;
		con.wiris.nodeAfter = nodeAfter;
		con.wiris.callbackFunc = callbackFunc;
		con.setRequestHeader("Content-type","application/x-www-form-urlencoded; charset=utf-8");
		con.onload = onloadFunc;
		con.onerror = function(e) {
			if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("LatexToMathml call failed!",{ fileName : "JsPluginViewer.hx", lineNumber : 903, className : "com.wiris.js.JsPluginViewer", methodName : "latexToMathml"});
		};
		con.send(data);
	}
	,callShowimage: function(container,mml,img,asynchronously,callbackFunc) {
		var _g = this;
		var con;
		var height = 0;
		var width = 0;
		var baseline = 0;
		var text = null;
		var data;
		var url;
		con = new js.XMLHttpRequest();
		var mmlEntities;
		mmlEntities = "";
		var _g1 = 0, _g2 = mml.length;
		while(_g1 < _g2) {
			var i = _g1++;
			var character;
			character = mml.charAt(i);
			if(HxOverrides.cca(mml,i) > 128) mmlEntities += "&#" + HxOverrides.cca(mml,i) + ";"; else mmlEntities += character;
		}
		var md5 = haxe.Md5.encode("centerbaseline=false\nmml=" + mmlEntities + "\n");
		url = (this.absoluteURL.length > 0?this.absoluteURL:this.baseURL + this.localpath) + "/showimage" + this.extension;
		if(new com.wiris.js.JsBrowser().isIE()) data = "?formula=" + md5 + "&lang=" + this.lang + "&useragent=IE"; else data = "?formula=" + md5 + "&lang=" + this.lang;
		data += "&version=" + com.wiris.js.JsPluginViewer.VERSION;
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("Calling: " + url,{ fileName : "JsPluginViewer.hx", lineNumber : 740, className : "com.wiris.js.JsPluginViewer", methodName : "callShowimage"});
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("GET:" + data,{ fileName : "JsPluginViewer.hx", lineNumber : 742, className : "com.wiris.js.JsPluginViewer", methodName : "callShowimage"});
		con.open("GET",url + data,asynchronously);
		con.setRequestHeader("Content-type","application/x-www-form-urlencoded; charset=utf-8");
		var onloadFunc = function(e) {
			var getResultFunc = function(e1) {
				var result = { };
				try {
					result = haxe.Json.parse(con.responseText).result;
				} catch( e2 ) {
					console.warn("Formula is malformed and it can't be rendered");
					return;
				}
				e1.target.wiris.img.src = result.format == "svg"?"data:image/svg+xml;charset=utf8,":"data:image/png;base64,";
				e1.target.wiris.img.src = e1.target.wiris.img.src + StringTools.urlEncode(result.content);
				var dpi = result.dpi == null?96:result.dpi;
				var scaleDpi = _g.zoom * (96 / dpi);
				var scaledHeight = result.height * scaleDpi | 0;
				var scaledWitdh = result.width * scaleDpi | 0;
				var scaledBaseLine = result.baseline * scaleDpi | 0;
				if(result.height > 0) {
					if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace(_g.calculateAlignment(height,baseline),{ fileName : "JsPluginViewer.hx", lineNumber : 766, className : "com.wiris.js.JsPluginViewer", methodName : "callShowimage"});
					e1.target.wiris.img.style.verticalAlign = "-" + _g.calculateAlignment(scaledHeight,scaledBaseLine) + "px";
					e1.target.wiris.img.style.height = "" + scaledHeight + "px";
					e1.target.wiris.img.style.width = "" + scaledWitdh + "px";
				}
				if(_g.wiriseditormathmlattribute == null) {
					var wirisEditorMathmlAttributeCall = haxe.Json.parse(_g.callGetVariableKeys("wiriseditormathmlattribute"));
					if(wirisEditorMathmlAttributeCall.status != "ok") _g.wiriseditormathmlattribute = "data-mathml"; else _g.wiriseditormathmlattribute = wirisEditorMathmlAttributeCall.result.wiriseditormathmlattribute;
				}
				e1.target.wiris.img.setAttribute(_g.wiriseditormathmlattribute,e1.target.wiris.mml);
				e1.target.wiris.img.setAttribute("class","Wirisformula");
				e1.target.wiris.img.setAttribute("role","math");
				if(result.alt != null) e1.target.wiris.img.alt = result.alt; else {
					var accessibilityResponse = _g.callService(e1.target.wiris.mml,"mathml2accessible");
					if(haxe.Json.parse(accessibilityResponse).status != "error") e1.target.wiris.img.alt = haxe.Json.parse(accessibilityResponse).result.text;
				}
				if(--_g.callsShowImageNumber == 0) {
					var _g2 = 0, _g1 = _g.eventListenersArray.length;
					while(_g2 < _g1) {
						var i = _g2++;
						try {
							_g.eventListenersArray[i].afterParse();
						} catch( e2 ) {
						}
					}
				}
				callbackFunc();
			};
			if(haxe.Json.parse(con.responseText).status == "warning") {
				con.open("POST",url,e.target.wiris.asynchronously);
				con.setRequestHeader("Content-type","application/x-www-form-urlencoded; charset=utf-8");
				var onloadFunc1 = function(e1) {
					getResultFunc(e1);
				};
				if(new com.wiris.js.JsBrowser().isIE()) data = "centerbaseline=false&mml=" + StringTools.urlEncode(e.target.wiris.mml) + "&useragent=IE"; else data = "centerbaseline=false&mml=" + StringTools.urlEncode(e.target.wiris.mml);
				var newDynamic = { };
				con.wiris = newDynamic;
				con.wiris.callbackFunc = callbackFunc;
				con.wiris.getResultFunc = getResultFunc;
				con.wiris.mml = mml;
				con.wiris.img = img;
				con.wiris.container = container;
				con.onload = onloadFunc1;
				con.onerror = function(e1) {
					if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("ShowImage call failed!",{ fileName : "JsPluginViewer.hx", lineNumber : 830, className : "com.wiris.js.JsPluginViewer", methodName : "callShowimage"});
				};
				con.send(data);
			} else getResultFunc(e);
		};
		var newDynamic = { };
		con.wiris = newDynamic;
		con.wiris.callbackFunc = callbackFunc;
		con.wiris.mml = mml;
		con.wiris.img = img;
		con.wiris.container = container;
		con.wiris.asynchronously = asynchronously;
		con.onload = onloadFunc;
		con.onerror = function(e) {
			if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("ShowImage call failed!",{ fileName : "JsPluginViewer.hx", lineNumber : 850, className : "com.wiris.js.JsPluginViewer", methodName : "callShowimage"});
		};
		con.send(null);
	}
	,callCreateImage: function(mml,img) {
		var con;
		var height = 0;
		var width = 0;
		var baseline = 0;
		var text = null;
		var data;
		var url;
		con = new js.XMLHttpRequest();
		url = (this.absoluteURL.length > 0?this.absoluteURL:this.baseURL + this.localpath) + "/createimage" + this.extension;
		data = "metrics=true&centerbaseline=false&mml=" + StringTools.urlEncode(mml);
		data += "&lang=" + this.lang;
		if(this.zoom != 1) data += "&zoom=" + this.zoom;
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("Calling: " + url,{ fileName : "JsPluginViewer.hx", lineNumber : 669, className : "com.wiris.js.JsPluginViewer", methodName : "callCreateImage"});
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("POST:" + data,{ fileName : "JsPluginViewer.hx", lineNumber : 671, className : "com.wiris.js.JsPluginViewer", methodName : "callCreateImage"});
		con.open("POST",url,false);
		con.setRequestHeader("Content-type","application/x-www-form-urlencoded; charset=utf-8");
		con.send(data);
		var s = con.responseText;
		var i = s.indexOf("?");
		if(i >= 0) {
			var scaleDpi = 1;
			var h = this.queryToParams(HxOverrides.substr(s,i + 1,null));
			if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace(h.get("formula"),{ fileName : "JsPluginViewer.hx", lineNumber : 681, className : "com.wiris.js.JsPluginViewer", methodName : "callCreateImage"});
			if(h.exists("dpi")) scaleDpi = this.zoom * (Std.parseInt(h.get("dpi")) / 96);
			baseline = Std.parseInt(h.get("cb")) / scaleDpi | 0;
			height = Std.parseInt(h.get("ch")) / scaleDpi | 0;
			width = Std.parseInt(h.get("cw")) / scaleDpi | 0;
			text = h.get("text");
		}
		img.src = con.responseText;
		if(height > 0) {
			if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace(this.calculateAlignment(height,baseline),{ fileName : "JsPluginViewer.hx", lineNumber : 692, className : "com.wiris.js.JsPluginViewer", methodName : "callCreateImage"});
			img.style.verticalAlign = "-" + this.calculateAlignment(height,baseline) + "px";
			img.style.height = "" + height + "px";
			img.style.width = "" + width + "px";
		}
		img.setAttribute("data-mathml",mml);
		img.setAttribute("class","Wirisformula");
		img.setAttribute("role","math");
		var accessibility = this.callService(mml,"mathml2accessible");
		if(accessibility != null) img.alt = haxe.Json.parse(accessibility).result.text;
	}
	,calculateAlignment: function(height,baseline) {
		var result;
		result = 0;
		result = height - baseline;
		return result;
	}
	,getTechnology: function() {
		if(HxOverrides.substr(com.wiris.js.JsPluginViewer.TECH,1,null) == "param.js.tech.discover@") {
			var con;
			con = new js.XMLHttpRequest();
			con.open("GET",this.baseURL + "/../tech.txt",false);
			con.send(null);
			var s = con.responseText;
			return StringTools.trim(s.split("#")[0]);
		} else return com.wiris.js.JsPluginViewer.TECH;
	}
	,processMathML: function(mml,container,asynchronously,callbackFunc) {
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace(mml,{ fileName : "JsPluginViewer.hx", lineNumber : 614, className : "com.wiris.js.JsPluginViewer", methodName : "processMathML"});
		var img = js.Lib.document.createElement("img");
		if(this.performanceenabled == null) this.performanceenabled = this.isPerformanceEnabled();
		if(this.mode == com.wiris.js.JsPluginViewer.USE_CREATE_IMAGE && this.performanceenabled) this.callShowimage(container,mml,img,asynchronously,callbackFunc); else if(this.mode == com.wiris.js.JsPluginViewer.USE_CREATE_IMAGE && !this.performanceenabled) this.callCreateImage(mml,img); else img.src = this.baseURL + this.localpath + "/showimage" + this.extension + "?mml=" + StringTools.urlEncode(mml);
		container.appendChild(img);
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace(img.src,{ fileName : "JsPluginViewer.hx", lineNumber : 628, className : "com.wiris.js.JsPluginViewer", methodName : "processMathML"});
	}
	,getMathML_IE7: function(mathNode0) {
		var mathml = "";
		var mathNode = mathNode0;
		while(mathNode != null && mathNode.nodeName != "/MATH") {
			if(mathNode.nodeType == 3) mathml += mathNode.nodeValue; else {
				var nodeName = mathNode.nodeName.toLowerCase();
				if(nodeName.charAt(0) == "/") mathml += "</" + HxOverrides.substr(nodeName,1,null) + ">"; else {
					mathml += "<" + nodeName;
					var attributes = mathNode.attributes;
					var i = 0;
					while(i < attributes.length) {
						var attribute = attributes[i];
						if(attribute.nodeValue != "" && attribute.nodeValue != null && attribute.nodeValue != "inherit") mathml += " " + attribute.nodeName + "=\"" + attribute.nodeValue + "\"";
						++i;
					}
					var counter = 1;
					var nextMathNode = mathNode.nextSibling;
					while(nextMathNode != null && counter > 0) {
						var nextNodeName = nextMathNode.nodeName.toLowerCase();
						if(nextNodeName == nodeName) ++counter; else if(nextNodeName == "/" + nodeName) --counter;
						nextMathNode = nextMathNode.nextSibling;
					}
					if(counter > 0) mathml += "/";
					mathml += ">";
				}
			}
			var nextMathNode = mathNode.nextSibling;
			if(mathNode != mathNode0) mathNode.parentNode.removeChild(mathNode);
			mathNode = nextMathNode;
		}
		if(mathNode.nodeName == "/MATH") mathNode.parentNode.removeChild(mathNode);
		mathml += "</math>";
		return mathml;
	}
	,removeSemanticsMathml: function(mathml) {
		var mathTagEnd = "</math>";
		var openSemantics = "<semantics>";
		var openAnnotation = "<annotation";
		var mathmlWithoutSemantics = mathml;
		var startSemantics = mathml.indexOf(openSemantics);
		if(startSemantics != -1) {
			var startAnnotation = mathml.indexOf(openAnnotation,startSemantics + openSemantics.length);
			if(startAnnotation != -1) mathmlWithoutSemantics = mathml.substring(0,startSemantics) + mathml.substring(startSemantics + openSemantics.length,startAnnotation) + mathTagEnd;
		}
		return mathmlWithoutSemantics;
	}
	,replaceNodes: function(mathNodes,n,asynchronously,callbackFunc) {
		if(n >= mathNodes.length) return;
		var mathNode = mathNodes[n];
		var mathml = null;
		var browser = new com.wiris.js.JsBrowser();
		if(browser.getBrowser() == "Explorer" && (browser.getVersion() == "6" || browser.getVersion() == "7") && navigator.appVersion.indexOf("Trident") == -1) {
			if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("Is ie7",{ fileName : "JsPluginViewer.hx", lineNumber : 483, className : "com.wiris.js.JsPluginViewer", methodName : "replaceNodes"});
			mathml = this.getMathML_IE7(mathNode);
		}
		var container = js.Lib.document.createElement("span");
		if(mathNode.parentNode != null) mathNode.parentNode.replaceChild(container,mathNode);
		if(mathml == null) {
			container.appendChild(mathNode);
			mathml = container.innerHTML;
			container.removeChild(mathNode);
		}
		var index = mathml.indexOf("<math");
		mathml = HxOverrides.substr(mathml,index,mathml.length - index);
		var containerMath = js.Lib.document.createElement("span");
		var containerMathElement = js.Lib.document.createElement("math");
		containerMath.appendChild(containerMathElement);
		containerMathElement.outerHTML = mathml;
		mathml = new XMLSerializer().serializeToString(containerMath.children[0]);
		this.processMathML(mathml,container,asynchronously,callbackFunc);
		var self = this;
		self.replaceNodes(mathNodes,n + 1,asynchronously,callbackFunc);
	}
	,replaceLatexInTextNode: function(pos,node,asynchronously,callbackFunc) {
		var textContent;
		textContent = node.textContent;
		if(pos < textContent.length) {
			var nextLatexPosistion = this.getNextLatexPos(pos,textContent);
			if(nextLatexPosistion != null) {
				var leftText = textContent.substring(pos,nextLatexPosistion.start);
				var leftTextNode = js.Lib.document.createTextNode(leftText);
				node.parentNode.insertBefore(leftTextNode,node);
				var latex = textContent.substring(nextLatexPosistion.start + "$$".length,nextLatexPosistion.end);
				this.latexToMathml(latex,leftTextNode,asynchronously,callbackFunc);
				this.replaceLatexInTextNode(nextLatexPosistion.end + "$$".length,node,asynchronously,callbackFunc);
			} else {
				var text = textContent.substring(pos);
				var textNode = js.Lib.document.createTextNode(text);
				node.parentNode.insertBefore(textNode,node);
				node.parentNode.removeChild(node);
			}
		} else node.parentNode.removeChild(node);
	}
	,getNextLatexPos: function(pos,text) {
		var firstLatexTags = text.indexOf("$$",pos);
		var secondLatexTags = firstLatexTags == -1?-1:text.indexOf("$$",firstLatexTags + "$$".length);
		return firstLatexTags != -1 && secondLatexTags != -1?{ start : firstLatexTags, end : secondLatexTags}:null;
	}
	,findLatex: function(node) {
		var foundLatex = false;
		var dollarIndex = node.nodeValue.indexOf("$$");
		if(dollarIndex != -1) {
			dollarIndex = node.nodeValue.indexOf("$$",dollarIndex + 2);
			if(dollarIndex != -1) foundLatex = true;
		}
		return foundLatex;
	}
	,parseLatexDocument: function(asynchronously,callbackFunc) {
		this.parseLatexElement(js.Lib.document,asynchronously,callbackFunc);
	}
	,parseLatexElement: function(element,asynchronously,callbackFunc) {
		if(callbackFunc == null) callbackFunc = function() {
		};
		if(asynchronously == null) {
			if(this.asyncParam) asynchronously = this.asyncParam;
		}
		var domTextNodes = this.findTextNodes(element);
		var latexToProcess = [];
		var i = 0;
		while(i < domTextNodes.length) {
			var node = domTextNodes[i];
			if(this.findLatex(node)) latexToProcess.push(node);
			i++;
		}
		if(latexToProcess.length == 0) {
			var _g1 = 0, _g = this.eventListenersArray.length;
			while(_g1 < _g) {
				var i1 = _g1++;
				try {
					this.eventListenersArray[i1].afterParseLatex();
				} catch( e ) {
				}
			}
		} else {
			this.callsLatexToMathml = latexToProcess.length;
			var _g1 = 0, _g = latexToProcess.length;
			while(_g1 < _g) {
				var i1 = _g1++;
				var node = latexToProcess[i1];
				this.replaceLatexInTextNode(0,node,asynchronously,callbackFunc);
			}
		}
	}
	,findTextNodes: function(el) {
		var textNodes = [];
		var walk = document.createTreeWalker(el,NodeFilter.SHOW_TEXT,null,false);
		var node;
		while(node = walk.nextNode()) if(!this.thereIsParentTextArea(node)) textNodes.push(node);
		return textNodes;
	}
	,thereIsParentTextArea: function(el) {
		var thereIs = false;
		var parentNode = el.parentNode;
		while(!thereIs && parentNode != null) {
			if(parentNode.nodeName == "TEXTAREA") thereIs = true;
			parentNode = parentNode.parentNode;
		}
		return thereIs;
	}
	,isEditable: function(element) {
		while(element != null) {
			if(element.contentEditable == "true") return true;
			element = element.parentNode;
		}
		return false;
	}
	,parseElement: function(element,asynchronously,callbackFunc) {
		if(!this.ready) throw "Document is not loaded.";
		if(callbackFunc == null) callbackFunc = function() {
		};
		if(asynchronously == null) {
			if(this.asyncParam) asynchronously = this.asyncParam;
		}
		var mathNodes = element.getElementsByTagName("math");
		var arr = new Array();
		var _g1 = 0, _g = mathNodes.length;
		while(_g1 < _g) {
			var x = _g1++;
			if(!this.isEditable(mathNodes[x])) arr.push(mathNodes[x]);
		}
		this.callsShowImageNumber = arr.length;
		if(this.callsShowImageNumber == 0) {
			var _g1 = 0, _g = this.eventListenersArray.length;
			while(_g1 < _g) {
				var i = _g1++;
				try {
					this.eventListenersArray[i].afterParse();
				} catch( e ) {
				}
			}
		}
		this.replaceNodes(arr,0,asynchronously,callbackFunc);
	}
	,parseDocument: function(asynchronously,callbackFunc,safeXml) {
		if(safeXml) this.parseSafeMathMLElement(js.Lib.document,asynchronously,callbackFunc); else this.parseElement(js.Lib.document,asynchronously,callbackFunc);
	}
	,getMathMLPositionsAtNode: function(node,mathmlPositions) {
		var safeXMLCharacters = com.wiris.js.JsCharacters.getSafeXMLCharacters();
		if(node.nodeType == 3) {
			var startMathmlTag = safeXMLCharacters.tagOpener + "math";
			var endMathmlTag = safeXMLCharacters.tagOpener + "/math" + safeXMLCharacters.tagCloser;
			var start = node.textContent.indexOf(startMathmlTag);
			var end = 0;
			while(start != -1) {
				end = node.textContent.indexOf(endMathmlTag,start + startMathmlTag.length);
				if(end == -1) break;
				var nextMathML = node.textContent.indexOf(startMathmlTag,end + endMathmlTag.length);
				if(nextMathML >= 0 && end > nextMathML) break;
				var safeMathml = node.textContent.substring(start,end + endMathmlTag.length);
				node.textContent = node.textContent.substring(0,start) + node.textContent.substring(end + endMathmlTag.length);
				node = node.splitText(start);
				start = node.textContent.indexOf(startMathmlTag);
				mathmlPositions.push({ safeMML : safeMathml, nextElement : node});
			}
		}
	}
	,getMathMLPositionsAtElementAndChildren: function(element,mathmlPositions) {
		this.getMathMLPositionsAtNode(element,mathmlPositions);
		var childNodes = Array.from(element.childNodes);
		if(childNodes.length > 0) {
			var _g1 = 0, _g = childNodes.length;
			while(_g1 < _g) {
				var i = _g1++;
				var child = childNodes[i];
				this.getMathMLPositionsAtElementAndChildren(child,mathmlPositions);
			}
		}
	}
	,parseSafeMathMLElement: function(element,asynchronously,callbackFunc) {
		if(!this.ready) throw "Document is not loaded.";
		var mathmlPositions = [];
		this.getMathMLPositionsAtElementAndChildren(element,mathmlPositions);
		var _g1 = 0, _g = mathmlPositions.length;
		while(_g1 < _g) {
			var i = _g1++;
			var mathmlPosition = mathmlPositions[i];
			var newNode = document.createElement("math");
			mathmlPosition.nextElement.parentNode.insertBefore(newNode,mathmlPosition.nextElement);
			newNode.outerHTML = com.wiris.js.JsMathML.decodeSafeMathML(mathmlPosition.safeMML);
		}
		this.parseElement(element,asynchronously,callbackFunc);
	}
	,doLoad: function() {
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("doLoad",{ fileName : "JsPluginViewer.hx", lineNumber : 120, className : "com.wiris.js.JsPluginViewer", methodName : "doLoad"});
		this.scriptName = "WIRISplugins.js";
		var col;
		col = js.Lib.document.getElementsByTagName("script");
		var _g1 = 0, _g = col.length;
		while(_g1 < _g) {
			var i = _g1++;
			var d;
			var src;
			d = col[i];
			src = d.src;
			var j = src.lastIndexOf(this.scriptName);
			if(j >= 0) {
				this.baseURL = HxOverrides.substr(src,0,j - 1);
				var k = src.indexOf("?",j);
				if(k >= 0) {
					var query = HxOverrides.substr(src,k + 1,null);
					this.params = this.queryToParams(query);
				}
				if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace(this.baseURL,{ fileName : "JsPluginViewer.hx", lineNumber : 139, className : "com.wiris.js.JsPluginViewer", methodName : "doLoad"});
			}
		}
		this.tech = this.getTechnology();
		if(this.tech == "php") {
			this.extension = ".php";
			this.localpath = "";
			this.absoluteURL = "";
		} else if(this.tech == "aspx") {
			this.extension = ".aspx";
			this.localpath = "";
			this.absoluteURL = "";
		} else if(this.tech == "local-java") {
			this.extension = "";
			this.localpath = "/../app";
			this.absoluteURL = "";
		} else if(this.tech == "nodejs") {
			this.extension = "";
			this.localpath = "/../integration";
			this.absoluteURL = "";
		} else if(this.tech == "java") {
			this.extension = "";
			this.localpath = "";
			this.absoluteURL = "";
		} else if(this.tech == "ruby") {
			this.extension = "";
			this.absoluteURL = "/wirispluginengine/integration";
		} else if(this.tech == "server") {
			this.absoluteURL = "https://www.wiris.net/demo/plugins/app";
			this.extension = "";
			this.localpath = "";
		}
		this.ready = true;
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("Tech:" + this.tech,{ fileName : "JsPluginViewer.hx", lineNumber : 177, className : "com.wiris.js.JsPluginViewer", methodName : "doLoad"});
		if(this.params.exists("viewer")) this.viewer = this.params.get("viewer");
		this.asyncParam = this.params.exists("async") && this.params.get("async") == "true"?true:false;
		if(this.params.exists("zoom")) this.zoom = Std.parseFloat(this.params.get("zoom"));
		if(this.params.exists("dpi")) this.zoom *= Std.parseFloat(this.params.get("dpi")) / 96;
		if(this.params.exists("lang")) this.lang = this.params.get("lang"); else this.lang = "en";
		if(this.params.exists("safeXml")) this.safeXml = this.params.get("safeXml") == "true"?true:false; else this.safeXml = false;
		if(this.lang == "inherit") this.lang = js.Lib.document.getElementsByTagName("html")[0].lang;
		if(this.viewer == "image") this.parseDocument(this.asyncParam,null,this.safeXml); else if(this.viewer == "latex") this.parseLatexDocument(this.asyncParam); else if(this.viewer == "all") {
			this.parseLatexDocument(this.asyncParam);
			this.parseDocument(this.asyncParam);
		}
		if(com.wiris.js.JsPluginViewer.DEBUG) haxe.Log.trace("Language:" + this.lang,{ fileName : "JsPluginViewer.hx", lineNumber : 213, className : "com.wiris.js.JsPluginViewer", methodName : "doLoad"});
		com.wiris.js.JsPluginViewer.instance = this;
		com.wiris.js.JsPluginViewer.bypassEncapsulation();
	}
	,tryReady: function() {
		this.ready = false;
		if(js.Lib.document.readyState) {
			this.doLoad();
			this.ready = true;
		}
		if(!this.ready) haxe.Timer.delay($bind(this,this.tryReady),100);
	}
	,eventListenersArray: null
	,callsLatexToMathml: null
	,callsShowImageNumber: null
	,_wrs_conf_imageFormat: null
	,ready: null
	,javaServicePath: null
	,tech: null
	,safeXml: null
	,lang: null
	,wiriseditormathmlattribute: null
	,performanceenabled: null
	,asyncParam: null
	,viewer: null
	,zoom: null
	,absoluteURL: null
	,localpath: null
	,extension: null
	,mode: null
	,params: null
	,scriptName: null
	,baseURL: null
	,__class__: com.wiris.js.JsPluginViewer
}
if(!com.wiris.system) com.wiris.system = {}
com.wiris.system.JsBrowserData = $hxClasses["com.wiris.system.JsBrowserData"] = function() {
};
com.wiris.system.JsBrowserData.__name__ = ["com","wiris","system","JsBrowserData"];
com.wiris.system.JsBrowserData.prototype = {
	identity: null
	,versionSearch: null
	,subString: null
	,prop: null
	,string: null
	,__class__: com.wiris.system.JsBrowserData
}
com.wiris.system.JsOSData = $hxClasses["com.wiris.system.JsOSData"] = function() {
};
com.wiris.system.JsOSData.__name__ = ["com","wiris","system","JsOSData"];
com.wiris.system.JsOSData.prototype = {
	identity: null
	,subString: null
	,string: null
	,__class__: com.wiris.system.JsOSData
}
com.wiris.system.JsBrowser = $hxClasses["com.wiris.system.JsBrowser"] = function() {
	this.dataBrowser = new Array();
	this.addBrowser("navigator.userAgent",null,"Edge",null,"Edge");
	this.addBrowser("navigator.userAgent",null,"Chrome",null,"Chrome");
	this.addBrowser("navigator.userAgent",null,"OmniWeb",null,"OmniWeb");
	this.addBrowser("navigator.vendor",null,"Apple","Version","Safari");
	this.addBrowser(null,"window.opera",null,"Version","Opera");
	this.addBrowser("navigator.vendor",null,"iCab",null,"iCab");
	this.addBrowser("navigator.vendor",null,"KDE",null,"Konkeror");
	this.addBrowser("navigator.userAgent",null,"Firefox",null,"Firefox");
	this.addBrowser("navigator.vendor",null,"Camino",null,"Camino");
	this.addBrowser("navigator.userAgent",null,"Netscape",null,"Netscape");
	this.addBrowser("navigator.userAgent",null,"MSIE","MSIE","Explorer");
	this.addBrowser("navigator.userAgent",null,"Trident","rv","Explorer");
	this.addBrowser("navigator.userAgent",null,"Gecko","rv","Mozilla");
	this.addBrowser("navigator.userAgent",null,"Mozilla","Mozilla","Netscape");
	this.dataOS = new Array();
	this.addOS("navigator.platform","Win","Windows");
	this.addOS("navigator.platform","Mac","Mac");
	this.addOS("navigator.userAgent","iPhone","iOS");
	this.addOS("navigator.userAgent","iPad","iOS");
	this.addOS("navigator.userAgent","Android","Android");
	this.addOS("navigator.platform","Linux","Linux");
	if(window.matchMedia != null) this.hasCoarsePointer = window.matchMedia("(any-pointer: coarse)").matches; else this.hasCoarsePointer = false;
	this.setBrowser();
	this.setOS();
	this.touchable = this.isIOS() || this.isAndroid();
};
com.wiris.system.JsBrowser.__name__ = ["com","wiris","system","JsBrowser"];
com.wiris.system.JsBrowser.prototype = {
	isTouchable: function() {
		return this.touchable;
	}
	,isAndroid: function() {
		return this.os == "Android";
	}
	,isMac: function() {
		return this.os == "Mac";
	}
	,isIOS: function() {
		return this.os == "iOS" || this.os == "Mac" && this.hasCoarsePointer;
	}
	,isFF: function() {
		return this.browser == "Firefox";
	}
	,isSafari: function() {
		return this.browser == "Safari";
	}
	,isChrome: function() {
		return this.browser == "Chrome";
	}
	,isEdge: function() {
		return this.browser == "Edge";
	}
	,isIE: function() {
		return this.browser == "Explorer";
	}
	,getVersion: function() {
		return this.ver;
	}
	,getOS: function() {
		return this.os;
	}
	,getBrowser: function() {
		return this.browser;
	}
	,searchVersion: function(prop,search) {
		var str = js.Boot.__cast(eval(prop) , String);
		var index = str.indexOf(search);
		if(index == -1) return null;
		return "" + Std.parseFloat(HxOverrides.substr(str,index + search.length + 1,null));
	}
	,setOS: function() {
		var i = HxOverrides.iter(this.dataOS);
		while(i.hasNext()) {
			var s = i.next();
			var str = js.Boot.__cast(eval(s.string) , String);
			if(str.indexOf(s.subString) != -1) {
				this.os = s.identity;
				return;
			}
		}
	}
	,setBrowser: function() {
		var i = HxOverrides.iter(this.dataBrowser);
		while(i.hasNext()) {
			var b = i.next();
			if(b.string != null) {
				var obj = eval(b.string);
				if(obj != null) {
					var str = js.Boot.__cast(obj , String);
					if(str.indexOf(b.subString) != -1) {
						this.browser = b.identity;
						this.ver = this.searchVersion("navigator.userAgent",b.versionSearch);
						if(this.ver == null) this.ver = this.searchVersion("navigator.appVersion",b.versionSearch);
						return;
					}
				}
			}
		}
	}
	,addOS: function(string,subString,identity) {
		var s = new com.wiris.system.JsOSData();
		s.string = string;
		s.subString = subString;
		s.identity = identity;
		this.dataOS.push(s);
	}
	,addBrowser: function(string,prop,subString,versionSearch,identity) {
		var b = new com.wiris.system.JsBrowserData();
		b.string = string;
		b.prop = prop;
		b.subString = subString;
		b.versionSearch = versionSearch != null?versionSearch:identity;
		b.identity = identity;
		this.dataBrowser.push(b);
	}
	,hasCoarsePointer: null
	,touchable: null
	,os: null
	,ver: null
	,browser: null
	,dataOS: null
	,dataBrowser: null
	,__class__: com.wiris.system.JsBrowser
}
com.wiris.system.JsDOMUtils = $hxClasses["com.wiris.system.JsDOMUtils"] = function() { }
com.wiris.system.JsDOMUtils.__name__ = ["com","wiris","system","JsDOMUtils"];
com.wiris.system.JsDOMUtils.ieTouchEvents = null;
com.wiris.system.JsDOMUtils.init = function() {
	if(com.wiris.system.JsDOMUtils.initialized) return;
	com.wiris.system.JsDOMUtils.ieTouchEvents = new Hash();
	com.wiris.system.JsDOMUtils.ieTouchEvents.set("touchstart","MSPointerDown");
	com.wiris.system.JsDOMUtils.ieTouchEvents.set("touchmove","MSPointerMove");
	com.wiris.system.JsDOMUtils.ieTouchEvents.set("touchend","MSPointerUp");
	com.wiris.system.JsDOMUtils.initialized = true;
	com.wiris.system.JsDOMUtils.addEventListener(js.Lib.document,"MSPointerDown",function(e) {
		com.wiris.system.JsDOMUtils.internetExplorerPointers.set("" + e.pointerId,e);
	});
	com.wiris.system.JsDOMUtils.addEventListener(js.Lib.document,"MSPointerUp",function(e) {
		com.wiris.system.JsDOMUtils.internetExplorerPointers = new Hash();
	});
	com.wiris.system.JsDOMUtils.addEventListener(js.Lib.document,"scroll",function(e) {
		com.wiris.system.JsDOMUtils.internetExplorerPointers = new Hash();
	});
	var touched = false;
	var triggerEvents = function(listeners) {
		var i = HxOverrides.iter(listeners);
		while(i.hasNext()) {
			var callbackFunction = i.next();
			callbackFunction();
		}
	};
	com.wiris.system.JsDOMUtils.addEventListener(js.Lib.document,"touchstart",function(e) {
		if(!com.wiris.system.JsDOMUtils.browser.touchable) {
			com.wiris.system.JsDOMUtils.browser.touchable = true;
			triggerEvents(com.wiris.system.JsDOMUtils.touchDeviceListeners);
		}
		touched = true;
	});
	var onTouchEnd = function(e) {
		if(!com.wiris.system.JsDOMUtils.browser.touchable) {
			com.wiris.system.JsDOMUtils.browser.touchable = true;
			triggerEvents(com.wiris.system.JsDOMUtils.touchDeviceListeners);
		}
		touched = true;
		setTimeout(function() {
			touched = false;
		},500);
	};
	com.wiris.system.JsDOMUtils.addEventListener(js.Lib.document,"touchend",onTouchEnd);
	com.wiris.system.JsDOMUtils.addEventListener(js.Lib.document,"touchleave",onTouchEnd);
	com.wiris.system.JsDOMUtils.addEventListener(js.Lib.document,"touchcancel",onTouchEnd);
	com.wiris.system.JsDOMUtils.addEventListener(js.Lib.document,"mousemove",function(e) {
		if(!touched) {
			if(com.wiris.system.JsDOMUtils.browser.touchable) {
				com.wiris.system.JsDOMUtils.browser.touchable = false;
				triggerEvents(com.wiris.system.JsDOMUtils.mouseDeviceListeners);
			}
		}
	});
}
com.wiris.system.JsDOMUtils.addEventListener = function(element,eventName,handler) {
	return com.wiris.system.JsDOMUtils.addEventListenerImpl(element,eventName,handler,false);
}
com.wiris.system.JsDOMUtils.addEventListenerImpl = function(element,eventName,handler,useCapture) {
	if(navigator.msPointerEnabled && com.wiris.system.JsDOMUtils.ieTouchEvents.exists(eventName)) {
		eventName = com.wiris.system.JsDOMUtils.ieTouchEvents.get(eventName);
		return com.wiris.system.JsDOMUtils.addEventListenerImpl(element,eventName,function(e) {
			if(e.pointerType == "touch") {
				if(eventName == "MSPointerUp") com.wiris.system.JsDOMUtils.internetExplorerPointers.remove("" + e.pointerId); else com.wiris.system.JsDOMUtils.internetExplorerPointers.set("" + e.pointerId,e);
				e.touches = new Array();
				var i = com.wiris.system.JsDOMUtils.internetExplorerPointers.iterator();
				while(i.hasNext()) e.touches.push(i.next());
				handler(e);
			}
		},useCapture);
	}
	if(eventName == "touchhold" && com.wiris.system.JsDOMUtils.browser.isTouchable()) {
		var timeout = null;
		var startX = -1;
		var startY = -1;
		var descriptor = com.wiris.system.JsDOMUtils.addEventListenerImpl(element,"touchstart",function(e) {
			touching = true;
			startX = e.touches[0].clientX;
			startY = e.touches[0].clientY;
			timeout = setTimeout(function() {
				timeout = null;
				handler(e);
			},500);
		},useCapture);
		descriptor.subDescriptors.push(com.wiris.system.JsDOMUtils.addEventListenerImpl(element,"touchmove",function(e) {
			if(timeout != null) {
				if(Math.abs(e.touches[0].clientX - startX) > com.wiris.system.JsDOMUtils.TOUCHHOLD_MOVE_MARGIN || Math.abs(e.touches[0].clientY - startY) > com.wiris.system.JsDOMUtils.TOUCHHOLD_MOVE_MARGIN) {
					clearTimeout(timeout);
					timeout = null;
				} else com.wiris.system.JsDOMUtils.cancelEvent(e);
			}
		},useCapture));
		descriptor.subDescriptors.push(com.wiris.system.JsDOMUtils.addEventListenerImpl(element,"touchend",function(e) {
			if(timeout != null) {
				clearTimeout(timeout);
				timeout = null;
			}
		},useCapture));
		return descriptor;
	}
	var descriptor = { };
	descriptor.element = element;
	descriptor.eventName = eventName;
	descriptor.handler = handler;
	descriptor.useCapture = useCapture;
	descriptor.subDescriptors = new Array();
	if(eventName == "dblclick") {
		var event = null;
		var touching = false;
		var firstClickTimestamp = null;
		descriptor.subDescriptors.push(com.wiris.system.JsDOMUtils.addEventListenerImpl(element,"touchstart",function(e) {
			touching = true;
			event = e;
		},useCapture));
		descriptor.subDescriptors.push(com.wiris.system.JsDOMUtils.addEventListenerImpl(element,"touchmove",function(e) {
			touching = false;
		},useCapture));
		descriptor.subDescriptors.push(com.wiris.system.JsDOMUtils.addEventListenerImpl(element,"touchend",function(e) {
			if(touching) {
				touching = false;
				var currentTimestamp = new Date().getTime();
				if(firstClickTimestamp == null || currentTimestamp > firstClickTimestamp + 500) firstClickTimestamp = currentTimestamp; else {
					event._wrs_dealAsTouch = true;
					handler(e);
				}
			}
		},useCapture));
	}
	if(element.attachEvent) element.attachEvent("on" + eventName,function() {
		handler(window.event);
	}); else {
		var options = { };
		options.capture = useCapture;
		if(eventName == "touchmove") options.passive = false;
		element.addEventListener(eventName,handler,options);
	}
	return descriptor;
}
com.wiris.system.JsDOMUtils.removeEventListener = function(descriptor) {
	if(com.wiris.system.JsDOMUtils.browser.isIE() && descriptor.element.detachEvent) descriptor.element.detachEvent("on" + Std.string(descriptor.eventName),descriptor.handler); else descriptor.element.removeEventListener(descriptor.eventName,descriptor.handler,descriptor.useCapture);
	var i = $iterator(descriptor.subDescriptors)();
	while(i.hasNext()) com.wiris.system.JsDOMUtils.removeEventListener(i.next());
}
com.wiris.system.JsDOMUtils.cancelEvent = function(e) {
	if(e.preventDefault) e.preventDefault(); else e.returnValue = false;
}
com.wiris.system.JsDOMUtils.fireEvent = function(element,eventName) {
	var event;
	if(document.createEvent) {
		event = document.createEvent("HTMLEvents");
		event.initEvent(eventName,true,true);
		event.eventName = eventName;
		element.dispatchEvent(event);
	} else {
		event = document.createEventObject();
		event.eventType = eventName;
		event.eventName = eventName;
		element.fireEvent("on" + eventName,event);
	}
}
com.wiris.system.JsDOMUtils.getComputedStyle = function(element) {
	if(element.currentStyle) return element.currentStyle;
	return document.defaultView.getComputedStyle(element,null);
}
com.wiris.system.JsDOMUtils.getComputedStyleProperty = function(element,name) {
	var value;
	if(document.defaultView && document.defaultView.getComputedStyle) {
		var computedStyle = document.defaultView.getComputedStyle(element,null);
		value = computedStyle == null?null:computedStyle.getPropertyValue(name);
	} else if(com.wiris.system.JsDOMUtils.browser.isIE() && element.currentStyle) {
		var camelName = com.wiris.system.JsDOMUtils.camelize(name);
		value = element.currentStyle[camelName];
		if(value != null && value.length > 0 && camelName != "zoom") {
			var firstChar = HxOverrides.cca(value,0);
			if(firstChar >= 48 && firstChar <= 57 && !StringTools.endsWith(value,"px")) {
				var originalLeft = element.style.left;
				var originalRuntimeLeft = element.runtimeStyle?element.runtimeStyle.left:null;
				if(originalRuntimeLeft != null) element.runtimeStyle.left = element.currentStyle.left;
				element.style.left = camelName == "fontSize"?"1em":value;
				value = element.style.pixelLeft + "px";
				element.style.left = originalLeft;
				if(originalRuntimeLeft != null) element.runtimeStyle.left = originalRuntimeLeft;
			}
		}
	} else value = element.style[com.wiris.system.JsDOMUtils.camelize(name)];
	return value;
}
com.wiris.system.JsDOMUtils.getPixelRatio = function() {
	var context = document.createElement("canvas").getContext("2d");
	var dpr = window.devicePixelRatio || 1;
	var bsr = context.webkitBackingStorePixelRatio || context.mozBackingStorePixelRatio || context.msBackingStorePixelRatio || context.oBackingStorePixelRatio || context.backingStorePixelRatio || 1;
	return dpr / bsr;
}
com.wiris.system.JsDOMUtils.camelize = function(str) {
	var start = 0;
	var pos;
	var sb = new StringBuf();
	while((pos = str.indexOf("-",start)) != -1) {
		sb.b += Std.string(HxOverrides.substr(str,start,pos - start));
		sb.b += Std.string(str.charAt(pos + 1).toUpperCase());
		start = pos + 2;
	}
	sb.b += Std.string(HxOverrides.substr(str,start,null));
	return sb.b;
}
com.wiris.system.JsDOMUtils.getElementsByClassName = function(parent,className,recursive) {
	var returnValue = new Array();
	var _g1 = 0, _g = parent.childNodes.length;
	while(_g1 < _g) {
		var i = _g1++;
		if(com.wiris.system.JsDOMUtils.hasClass(parent.childNodes[i],className)) returnValue.push(parent.childNodes[i]);
		if(recursive) returnValue = returnValue.concat(com.wiris.system.JsDOMUtils.getElementsByClassName(parent.childNodes[i],className,true));
	}
	return returnValue;
}
com.wiris.system.JsDOMUtils.getEventTarget = function(event) {
	if(event.srcElement) return event.srcElement;
	return event.target;
}
com.wiris.system.JsDOMUtils.getLeft = function(element) {
	return element.getBoundingClientRect().left;
}
com.wiris.system.JsDOMUtils.getRelativeLeft = function(element,parent) {
	if(parent == null) return com.wiris.system.JsDOMUtils.getLeft(element);
	return com.wiris.system.JsDOMUtils.getLeft(element) - com.wiris.system.JsDOMUtils.getLeft(parent);
}
com.wiris.system.JsDOMUtils.getTop = function(element) {
	return element.getBoundingClientRect().top;
}
com.wiris.system.JsDOMUtils.getRelativeTop = function(element,parent) {
	if(parent == null) return com.wiris.system.JsDOMUtils.getTop(element);
	return com.wiris.system.JsDOMUtils.getTop(element) - com.wiris.system.JsDOMUtils.getTop(parent);
}
com.wiris.system.JsDOMUtils.getWindowScroll = function() {
	var scroll = new Array();
	if(js.Lib.window.pageYOffset == undefined) {
		scroll[0] = js.Lib.document.documentElement.scrollLeft;
		scroll[1] = js.Lib.document.documentElement.scrollTop;
	} else {
		scroll[0] = js.Lib.window.pageXOffset;
		scroll[1] = js.Lib.window.pageYOffset;
	}
	return scroll;
}
com.wiris.system.JsDOMUtils.setWindowScroll = function(scroll) {
	var x = scroll[0] | 0;
	var y = scroll[1] | 0;
	js.Lib.window.scrollTo(x,y);
}
com.wiris.system.JsDOMUtils.isLeftBidiAware = function(keyCode,rtl) {
	return keyCode == 37 && !rtl || keyCode == 39 && rtl;
}
com.wiris.system.JsDOMUtils.getFirstDisplayedChild = function(parent) {
	var child = parent != null?parent.firstChild:parent;
	while(child != null) {
		if(com.wiris.system.JsDOMUtils.getComputedStyleProperty(child,"display") != "none") return child;
		child = child.nextSibling;
	}
	return null;
}
com.wiris.system.JsDOMUtils.isDescendant = function(parent,possibleDescendant) {
	if(parent == null || possibleDescendant == null) return false;
	while(possibleDescendant.parentNode != null) {
		if(possibleDescendant.parentNode == parent) return true;
		possibleDescendant = possibleDescendant.parentNode;
	}
	return false;
}
com.wiris.system.JsDOMUtils.parseDimension = function(x) {
	return x < 0 || x == null?0:x;
}
com.wiris.system.JsDOMUtils.removeChildren = function(element) {
	while(element.firstChild != null) element.removeChild(element.firstChild);
}
com.wiris.system.JsDOMUtils.hasClass = function(element,className) {
	if(element == null || element.className == null || !(element.className.split)) return false;
	var classes = element.className.split(" ");
	var i = HxOverrides.iter(classes);
	while(i.hasNext()) if(i.next() == className) return true;
	return false;
}
com.wiris.system.JsDOMUtils.addClass = function(element,className) {
	if(element == null) return;
	if(element.className == "") element.className = className; else if(!com.wiris.system.JsDOMUtils.hasClass(element,className)) element.className += " " + className;
}
com.wiris.system.JsDOMUtils.removeClass = function(element,className) {
	if(element == null || element.className == null || !(element.className.split)) return;
	var classes = element.className.split(" ");
	HxOverrides.remove(classes,className);
	element.className = classes.join(" ");
}
com.wiris.system.JsDOMUtils.toggleClass = function(element,className) {
	if(com.wiris.system.JsDOMUtils.hasClass(element,className)) com.wiris.system.JsDOMUtils.removeClass(element,className); else com.wiris.system.JsDOMUtils.addClass(element,className);
}
com.wiris.system.JsDOMUtils.activateClass = function(element,className) {
	if(!com.wiris.system.JsDOMUtils.hasClass(element,className)) com.wiris.system.JsDOMUtils.addClass(element,className);
}
com.wiris.system.JsDOMUtils.setCaretPosition = function(element,position,end) {
	if(element.setSelectionRange) element.setSelectionRange(position,end); else if(element.createTextRange) {
		var range = element.createTextRange();
		range.collapse(true);
		range.moveStart("character",position);
		range.moveEnd("character",end);
		range.select();
	}
}
com.wiris.system.JsDOMUtils.getSelectionBounds = function(element) {
	var bounds = new Array();
	if(element.selectionStart != null) {
		bounds[0] = element.selectionStart;
		bounds[1] = element.selectionEnd;
		return bounds;
	}
	var range = document.selection.createRange();
	if(range && range.parentElement() == element) {
		var length = element.value.length;
		var normalizedValue = element.value.split("\r\n").join("\n");
		var textInputRange = element.createTextRange();
		textInputRange.moveToBookmark(range.getBookmark());
		var endRange = element.createTextRange();
		endRange.collapse(false);
		if(textInputRange.compareEndPoints("StartToEnd",endRange) > -1) bounds[0] = bounds[1] = length; else {
			bounds[0] = -textInputRange.moveStart("character",-length);
			bounds[0] += normalizedValue.slice(0,bounds[0]).split("\n").length - 1;
			if(textInputRange.compareEndPoints("EndToEnd",endRange) > -1) bounds[1] = length; else {
				bounds[1] = -textInputRange.moveEnd("character",-length);
				bounds[1] += normalizedValue.slice(0,bounds[1]).split("\n").length - 1;
			}
		}
		return bounds;
	}
	return null;
}
com.wiris.system.JsDOMUtils.getMousePosition = function(target,e) {
	if(e._wrs_dealAsTouch) return com.wiris.system.JsDOMUtils.getTouchPosition(target,e.touches[0]);
	var elementScroll = new Array();
	elementScroll[0] = target.scrollLeft;
	elementScroll[1] = target.scrollTop;
	return com.wiris.system.JsDOMUtils.getMousePositionImpl(target,e,elementScroll,0);
}
com.wiris.system.JsDOMUtils.getMousePositionImpl = function(target,e,elementScroll,border) {
	var position = new Array();
	position[0] = e.clientX - com.wiris.system.JsDOMUtils.getLeft(target) - border + elementScroll[0];
	position[1] = e.clientY - com.wiris.system.JsDOMUtils.getTop(target) - border + elementScroll[1];
	return position;
}
com.wiris.system.JsDOMUtils.getMousePagePosition = function(target,e) {
	var pagePosition = new Array();
	pagePosition[0] = e.pageX;
	pagePosition[1] = e.pageY;
	return pagePosition;
}
com.wiris.system.JsDOMUtils.getScrollPosition = function(target,e) {
	var elementScroll = new Array();
	elementScroll[0] = target.scrollLeft;
	elementScroll[1] = target.scrollTop;
	return elementScroll;
}
com.wiris.system.JsDOMUtils.inFixedParent = function(element) {
	while(element != null) {
		if(com.wiris.system.JsDOMUtils.getComputedStyleProperty(element,"position") == "fixed") return true;
		element = element.offsetParent;
	}
	return false;
}
com.wiris.system.JsDOMUtils.getTouchPosition = function(target,touch) {
	var elementScroll = new Array();
	elementScroll[0] = target.scrollLeft;
	elementScroll[1] = target.scrollTop;
	return com.wiris.system.JsDOMUtils.getTouchPositionImpl(target,touch,elementScroll,0);
}
com.wiris.system.JsDOMUtils.getTouchPositionImpl = function(target,touch,elementScroll,border) {
	var position = new Array();
	position[0] = touch.clientX - com.wiris.system.JsDOMUtils.getLeft(target) - border + elementScroll[0];
	position[1] = touch.clientY - com.wiris.system.JsDOMUtils.getTop(target) - border + elementScroll[1];
	return position;
}
com.wiris.system.JsDOMUtils.getStandardButton = function(e) {
	if(e.touches) return 1;
	if("button" in e) return e.button + 1;
	return 0;
}
com.wiris.system.JsDOMUtils.vibrate = function(milliseconds) {
	if(navigator.vibrate) navigator.vibrate(milliseconds);
}
com.wiris.system.JsDOMUtils.onTouchDeviceDetected = function(callbackFunction) {
	com.wiris.system.JsDOMUtils.init();
	com.wiris.system.JsDOMUtils.touchDeviceListeners.push(callbackFunction);
}
com.wiris.system.JsDOMUtils.onMouseDeviceDetected = function(callbackFunction) {
	com.wiris.system.JsDOMUtils.init();
	com.wiris.system.JsDOMUtils.mouseDeviceListeners.push(callbackFunction);
}
com.wiris.system.JsDOMUtils.findScriptElement = function(reg) {
	var scripts = js.Lib.document.getElementsByTagName("script");
	var n = scripts.length;
	var i = 0;
	while(i < n) {
		var src = scripts[i].getAttribute("src");
		if(reg.match(src)) return scripts[i];
		++i;
	}
	return null;
}
com.wiris.system.JsDOMUtils.rewriteDefaultPaths = function(contextPath) {
	if(window.com.wiris.js.defaultServicePath != null && StringTools.startsWith(window.com.wiris.js.defaultServicePath,"http://")) {
		var protocol = js.Lib.window.location.protocol;
		var defaultServicePathWithoutProtocol = window.com.wiris.js.defaultServicePath.substr("http:".length);
		var reg = new EReg("(http:|https:)" + defaultServicePathWithoutProtocol + "/" + contextPath,"");
		if(com.wiris.system.JsDOMUtils.findScriptElement(reg) != null) protocol = reg.matched(1);
		if(protocol == "https:") {
			window.com.wiris.js.defaultServicePath = "https:" + defaultServicePathWithoutProtocol;
			if(window.com.wiris.js.defaultBasePath != null) window.com.wiris.js.defaultBasePath = "https:" + window.com.wiris.js.defaultBasePath.substr("http:".length);
		}
	}
}
com.wiris.system.JsDOMUtils.createCSSRules = function(selector,rules) {
	var style = js.Lib.document.createElement("style");
	style.type = "text/css";
	js.Lib.document.getElementsByTagName("head")[0].appendChild(style);
	if(style.sheet != null && style.sheet.insertRule != null) style.sheet.insertRule(selector + "{" + rules + "}",0); else if(style.styleSheet != null) style.styleSheet.addRule(selector,rules); else if(style.sheet != null) style.sheet.addRule(selector,rules);
}
com.wiris.system.JsDOMUtils.execCommand = function(command) {
	return document.execCommand(command);
}
com.wiris.system.JsDOMUtils.findServicePath = function(scriptName) {
	var scriptTags = js.Lib.document.getElementsByTagName("script");
	var servicePath = null;
	var _g1 = 0, _g = scriptTags.length;
	while(_g1 < _g) {
		var i = _g1++;
		var src = scriptTags[i].src;
		var j = src.lastIndexOf("/" + scriptName);
		if(j >= 0) servicePath = HxOverrides.substr(src,0,j); else if(src == scriptName) servicePath = "";
	}
	if(servicePath != null && StringTools.startsWith(servicePath,"http://") && js.Lib.window.location.protocol == "https:") servicePath = "https://" + HxOverrides.substr(servicePath,"http://".length,null);
	return servicePath;
}
com.wiris.system.JsDOMUtils.addScript = function(d,win,url) {
	if(win[url] == null) {
		win[url] = true;
		var script = d.createElement("script");
		script.setAttribute("type","text/javascript");
		script.setAttribute("src",url);
		d.getElementsByTagName("head")[0].appendChild(script);
	}
}
com.wiris.system.JsDOMUtils.loadTextFile = function(elem,func) {
	var w = js.Lib.window;
	if(w.File && w.FileReader && w.FileList && w.Blob) {
		var d = elem.ownerDocument;
		var fileInput = d.createElement("input");
		fileInput.setAttribute("name","data");
		fileInput.setAttribute("type","file");
		elem.appendChild(fileInput);
		com.wiris.system.JsDOMUtils.init();
		com.wiris.system.JsDOMUtils.addEventListener(fileInput,"change",function(e) {
			if(fileInput.files.length) {
				var file = fileInput.files[0];
				var reader = new FileReader();
				reader.onload = function(le) {
					func(le.target.result);
				};
				reader.readAsText(file);
			}
			elem.removeChild(fileInput);
		});
		fileInput.click();
	} else throw "Browser incompatible with JavaScript File API.";
}
com.wiris.system.JsDOMUtils.saveTextFile = function(elem,filename,filetype,contents) {
	var d = elem.ownerDocument;
	var w = js.Lib.window;
	if(w.Blob) {
		var blob = new Blob([contents], {'type': filetype});
		var a = d.createElement("a");
		if('download' in a && w.URL) {
			a.href = w.URL.createObjectURL(blob);
			a.download = filename;
			elem.appendChild(a);
			a.click();
			elem.removeChild(a);
			return;
		}
	}
	throw "Browser incompatible with HTML object download anchors.";
}
com.wiris.system.JsDOMUtils.trapFocus = function(disabledFocusContainer,focusableContainer) {
	var lastScroll = com.wiris.system.JsDOMUtils.getWindowScroll();
	var previousFocused = focusableContainer;
	var descriptor = com.wiris.system.JsDOMUtils.addEventListener(js.Lib.document,"focusin",function(e) {
		previousFocused = com.wiris.system.JsDOMUtils.getEventTarget(e);
	});
	descriptor.subDescriptors.push(com.wiris.system.JsDOMUtils.addEventListener(disabledFocusContainer,"focusin",function(e) {
		var focusedElement = com.wiris.system.JsDOMUtils.getEventTarget(e);
		if(!com.wiris.system.JsDOMUtils.isDescendant(focusableContainer,focusedElement)) {
			com.wiris.system.JsDOMUtils.setWindowScroll(lastScroll);
			var focusableElements = com.wiris.system.JsDOMUtils.getFocusableElements(js.Lib.document.body);
			var focusedElementIndex = 0;
			while(focusedElementIndex < focusableElements.length) {
				if(focusableElements[focusedElementIndex] == focusedElement) break;
				++focusedElementIndex;
			}
			var direction = previousFocused == focusedElement || com.wiris.system.JsDOMUtils.elementIsBefore(previousFocused,focusedElement)?1:-1;
			var alternative = com.wiris.system.JsDOMUtils.findFocusableAlternative(focusableElements,disabledFocusContainer,focusableContainer,focusedElementIndex,direction);
			if(alternative != null) alternative.focus();
			e.stopPropagation();
		} else lastScroll = com.wiris.system.JsDOMUtils.getWindowScroll();
	}));
	var focusedElement = js.Lib.document.activeElement;
	if(!com.wiris.system.JsDOMUtils.isDescendant(focusableContainer,focusedElement)) {
		com.wiris.system.JsDOMUtils.setWindowScroll(lastScroll);
		var focusableElements = com.wiris.system.JsDOMUtils.getFocusableElements(js.Lib.document.body);
		var focusedElementIndex = 0;
		while(focusedElementIndex < focusableElements.length) {
			if(focusableElements[focusedElementIndex] == focusedElement) break;
			++focusedElementIndex;
		}
		var alternative = com.wiris.system.JsDOMUtils.findFocusableAlternative(focusableElements,disabledFocusContainer,focusableContainer,focusedElementIndex,1);
		if(alternative != null) alternative.focus();
	}
	return descriptor;
}
com.wiris.system.JsDOMUtils.findFocusableAlternative = function(focusableElements,disabledFocusContainer,focusableContainer,focusedElementIndex,direction,stopOnIndex) {
	if(stopOnIndex == null) stopOnIndex = -100;
	if(focusedElementIndex == 0 && stopOnIndex == -100 && direction == -1) return com.wiris.system.JsDOMUtils.findFocusableAlternative(focusableElements,disabledFocusContainer,focusableContainer,0,1,focusableElements.length - 1);
	var originalFocusedElementIndex = focusedElementIndex;
	focusedElementIndex += direction;
	while(focusedElementIndex >= 0 && focusedElementIndex < focusableElements.length && com.wiris.system.JsDOMUtils.isDescendant(disabledFocusContainer,focusableElements[focusedElementIndex]) && (!com.wiris.system.JsDOMUtils.isDescendant(focusableContainer,focusableElements[focusedElementIndex]) || focusableElements[focusedElementIndex].offsetParent == null)) {
		focusedElementIndex += direction;
		if(focusedElementIndex == stopOnIndex) return null;
	}
	if(focusedElementIndex < 0) return com.wiris.system.JsDOMUtils.findFocusableAlternative(focusableElements,disabledFocusContainer,focusableContainer,focusableElements.length - 1,-1,originalFocusedElementIndex);
	if(focusedElementIndex >= focusableElements.length) return com.wiris.system.JsDOMUtils.findFocusableAlternative(focusableElements,disabledFocusContainer,focusableContainer,0,1,originalFocusedElementIndex);
	return focusableElements[focusedElementIndex];
}
com.wiris.system.JsDOMUtils.untrapFocus = function(descriptor) {
	com.wiris.system.JsDOMUtils.removeEventListener(descriptor);
}
com.wiris.system.JsDOMUtils.getFocusableElements = function(container) {
	var elements = container.querySelectorAll("button, [href], input, select, textarea, [tabindex]:not([tabindex=\"-1\"])");
	var focusableElements = new Array();
	var i = 0;
	while(i < elements.length) {
		if(com.wiris.system.JsDOMUtils.getComputedStyleProperty(elements[i],"display") != "none" && com.wiris.system.JsDOMUtils.getComputedStyleProperty(elements[i],"visibility") != "hidden") focusableElements.push(elements[i]);
		++i;
	}
	return focusableElements;
}
com.wiris.system.JsDOMUtils.elementIsBefore = function(elementA,elementB) {
	if(elementA == elementB) throw "Trying to compare the same element.";
	var pathA = com.wiris.system.JsDOMUtils.getElementPath(elementA);
	var pathB = com.wiris.system.JsDOMUtils.getElementPath(elementB);
	var i = 1;
	var n = Math.min(pathA.length,pathB.length) | 0;
	while(i < n) {
		if(pathA[i] != pathB[i]) return com.wiris.system.JsDOMUtils.getElementChildIndex(pathA[i]) < com.wiris.system.JsDOMUtils.getElementChildIndex(pathB[i]);
		++i;
	}
	return false;
}
com.wiris.system.JsDOMUtils.elementIsVisible = function(element) {
	return !!(element.offsetWidth || element.offsetHeight || element.getClientRects().length);
}
com.wiris.system.JsDOMUtils.getElementPath = function(element) {
	var path = new Array();
	while(element != null) {
		path.splice(0,0,element);
		element = element.parentNode;
	}
	return path;
}
com.wiris.system.JsDOMUtils.getElementChildIndex = function(element) {
	if(element.parentNode == null) return -1;
	var i = 0;
	while(i < element.parentNode.childNodes.length) {
		if(element.parentNode.childNodes[i] == element) return i;
		++i;
	}
	return -1;
}
com.wiris.system.JsDOMUtils.existsCSS = function(document,name) {
	var head = document.getElementsByTagName("head")[0];
	var links = head.getElementsByTagName("link");
	var i = 0;
	while(i < links.length) {
		var link = links[i++];
		var type = link.getAttribute("type");
		if(type == "text/css") {
			var href = link.getAttribute("href");
			if(href.indexOf(name) > 0) return true;
		}
	}
	return false;
}
com.wiris.system.JsDOMUtils.trimDuplicatedCSS = function(document,validSource,name) {
	var head = document.getElementsByTagName("head")[0];
	var links = head.getElementsByTagName("link");
	var i = 0;
	var keep = -1;
	while(i < links.length) {
		var link = links[i];
		var type = link.getAttribute("type");
		if(type == "text/css") {
			var href = link.getAttribute("href");
			var index = href.indexOf(validSource);
			if(keep < 0 && index >= 0 && href.indexOf(name,index + 1) >= 0) {
				keep = i;
				i = -1;
			} else if(keep >= 0 && keep != i && href.indexOf(name) >= 0) {
				head.removeChild(link);
				if(i-- < keep) keep--;
			}
		}
		i++;
	}
}
var haxe = haxe || {}
haxe.Http = $hxClasses["haxe.Http"] = function(url) {
	this.url = url;
	this.headers = new Hash();
	this.params = new Hash();
	this.async = true;
};
haxe.Http.__name__ = ["haxe","Http"];
haxe.Http.requestUrl = function(url) {
	var h = new haxe.Http(url);
	h.async = false;
	var r = null;
	h.onData = function(d) {
		r = d;
	};
	h.onError = function(e) {
		throw e;
	};
	h.request(false);
	return r;
}
haxe.Http.prototype = {
	onStatus: function(status) {
	}
	,onError: function(msg) {
	}
	,onData: function(data) {
	}
	,request: function(post) {
		var me = this;
		var r = new js.XMLHttpRequest();
		var onreadystatechange = function() {
			if(r.readyState != 4) return;
			var s = (function($this) {
				var $r;
				try {
					$r = r.status;
				} catch( e ) {
					$r = null;
				}
				return $r;
			}(this));
			if(s == undefined) s = null;
			if(s != null) me.onStatus(s);
			if(s != null && s >= 200 && s < 400) me.onData(r.responseText); else switch(s) {
			case null: case undefined:
				me.onError("Failed to connect or resolve host");
				break;
			case 12029:
				me.onError("Failed to connect to host");
				break;
			case 12007:
				me.onError("Unknown host");
				break;
			default:
				me.onError("Http Error #" + r.status);
			}
		};
		if(this.async) r.onreadystatechange = onreadystatechange;
		var uri = this.postData;
		if(uri != null) post = true; else {
			var $it0 = this.params.keys();
			while( $it0.hasNext() ) {
				var p = $it0.next();
				if(uri == null) uri = ""; else uri += "&";
				uri += StringTools.urlEncode(p) + "=" + StringTools.urlEncode(this.params.get(p));
			}
		}
		try {
			if(post) r.open("POST",this.url,this.async); else if(uri != null) {
				var question = this.url.split("?").length <= 1;
				r.open("GET",this.url + (question?"?":"&") + uri,this.async);
				uri = null;
			} else r.open("GET",this.url,this.async);
		} catch( e ) {
			this.onError(e.toString());
			return;
		}
		if(this.headers.get("Content-Type") == null && post && this.postData == null) r.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var $it1 = this.headers.keys();
		while( $it1.hasNext() ) {
			var h = $it1.next();
			r.setRequestHeader(h,this.headers.get(h));
		}
		r.send(uri);
		if(!this.async) onreadystatechange();
	}
	,setPostData: function(data) {
		this.postData = data;
	}
	,setParameter: function(param,value) {
		this.params.set(param,value);
	}
	,setHeader: function(header,value) {
		this.headers.set(header,value);
	}
	,params: null
	,headers: null
	,postData: null
	,async: null
	,url: null
	,__class__: haxe.Http
}
haxe.Json = $hxClasses["haxe.Json"] = function() {
};
haxe.Json.__name__ = ["haxe","Json"];
haxe.Json.parse = function(text) {
	return new haxe.Json().doParse(text);
}
haxe.Json.stringify = function(value) {
	return new haxe.Json().toString(value);
}
haxe.Json.prototype = {
	parseString: function() {
		var start = this.pos;
		var buf = new StringBuf();
		while(true) {
			var c = this.str.charCodeAt(this.pos++);
			if(c == 34) break;
			if(c == 92) {
				buf.b += HxOverrides.substr(this.str,start,this.pos - start - 1);
				c = this.str.charCodeAt(this.pos++);
				switch(c) {
				case 114:
					buf.b += String.fromCharCode(13);
					break;
				case 110:
					buf.b += String.fromCharCode(10);
					break;
				case 116:
					buf.b += String.fromCharCode(9);
					break;
				case 98:
					buf.b += String.fromCharCode(8);
					break;
				case 102:
					buf.b += String.fromCharCode(12);
					break;
				case 47:case 92:case 34:
					buf.b += String.fromCharCode(c);
					break;
				case 117:
					var uc = Std.parseInt("0x" + HxOverrides.substr(this.str,this.pos,4));
					this.pos += 4;
					buf.b += String.fromCharCode(uc);
					break;
				default:
					throw "Invalid escape sequence \\" + String.fromCharCode(c) + " at position " + (this.pos - 1);
				}
				start = this.pos;
			} else if(c != c) throw "Unclosed string";
		}
		buf.b += HxOverrides.substr(this.str,start,this.pos - start - 1);
		return buf.b;
	}
	,parseRec: function() {
		while(true) {
			var c = this.str.charCodeAt(this.pos++);
			switch(c) {
			case 32:case 13:case 10:case 9:
				break;
			case 123:
				var obj = { }, field = null, comma = null;
				while(true) {
					var c1 = this.str.charCodeAt(this.pos++);
					switch(c1) {
					case 32:case 13:case 10:case 9:
						break;
					case 125:
						if(field != null || comma == false) this.invalidChar();
						return obj;
					case 58:
						if(field == null) this.invalidChar();
						obj[field] = this.parseRec();
						field = null;
						comma = true;
						break;
					case 44:
						if(comma) comma = false; else this.invalidChar();
						break;
					case 34:
						if(comma) this.invalidChar();
						field = this.parseString();
						break;
					default:
						this.invalidChar();
					}
				}
				break;
			case 91:
				var arr = [], comma = null;
				while(true) {
					var c1 = this.str.charCodeAt(this.pos++);
					switch(c1) {
					case 32:case 13:case 10:case 9:
						break;
					case 93:
						if(comma == false) this.invalidChar();
						return arr;
					case 44:
						if(comma) comma = false; else this.invalidChar();
						break;
					default:
						if(comma) this.invalidChar();
						this.pos--;
						arr.push(this.parseRec());
						comma = true;
					}
				}
				break;
			case 116:
				var save = this.pos;
				if(this.str.charCodeAt(this.pos++) != 114 || this.str.charCodeAt(this.pos++) != 117 || this.str.charCodeAt(this.pos++) != 101) {
					this.pos = save;
					this.invalidChar();
				}
				return true;
			case 102:
				var save = this.pos;
				if(this.str.charCodeAt(this.pos++) != 97 || this.str.charCodeAt(this.pos++) != 108 || this.str.charCodeAt(this.pos++) != 115 || this.str.charCodeAt(this.pos++) != 101) {
					this.pos = save;
					this.invalidChar();
				}
				return false;
			case 110:
				var save = this.pos;
				if(this.str.charCodeAt(this.pos++) != 117 || this.str.charCodeAt(this.pos++) != 108 || this.str.charCodeAt(this.pos++) != 108) {
					this.pos = save;
					this.invalidChar();
				}
				return null;
			case 34:
				return this.parseString();
			case 48:case 49:case 50:case 51:case 52:case 53:case 54:case 55:case 56:case 57:case 45:
				this.pos--;
				if(!this.reg_float.match(HxOverrides.substr(this.str,this.pos,null))) throw "Invalid float at position " + this.pos;
				var v = this.reg_float.matched(0);
				this.pos += v.length;
				var f = Std.parseFloat(v);
				var i = f | 0;
				return i == f?i:f;
			default:
				this.invalidChar();
			}
		}
	}
	,nextChar: function() {
		return this.str.charCodeAt(this.pos++);
	}
	,invalidChar: function() {
		this.pos--;
		throw "Invalid char " + this.str.charCodeAt(this.pos) + " at position " + this.pos;
	}
	,doParse: function(str) {
		this.reg_float = new EReg("^-?(0|[1-9][0-9]*)(\\.[0-9]+)?([eE][+-]?[0-9]+)?","");
		this.str = str;
		this.pos = 0;
		return this.parseRec();
	}
	,quote: function(s) {
		this.buf.b += Std.string("\"");
		var i = 0;
		while(true) {
			var c = s.charCodeAt(i++);
			if(c != c) break;
			switch(c) {
			case 34:
				this.buf.b += Std.string("\\\"");
				break;
			case 92:
				this.buf.b += Std.string("\\\\");
				break;
			case 10:
				this.buf.b += Std.string("\\n");
				break;
			case 13:
				this.buf.b += Std.string("\\r");
				break;
			case 9:
				this.buf.b += Std.string("\\t");
				break;
			case 8:
				this.buf.b += Std.string("\\b");
				break;
			case 12:
				this.buf.b += Std.string("\\f");
				break;
			default:
				this.buf.b += String.fromCharCode(c);
			}
		}
		this.buf.b += Std.string("\"");
	}
	,toStringRec: function(v) {
		var $e = (Type["typeof"](v));
		switch( $e[1] ) {
		case 8:
			this.buf.b += Std.string("\"???\"");
			break;
		case 4:
			this.objString(v);
			break;
		case 1:
		case 2:
			this.buf.b += Std.string(v);
			break;
		case 5:
			this.buf.b += Std.string("\"<fun>\"");
			break;
		case 6:
			var c = $e[2];
			if(c == String) this.quote(v); else if(c == Array) {
				var v1 = v;
				this.buf.b += Std.string("[");
				var len = v1.length;
				if(len > 0) {
					this.toStringRec(v1[0]);
					var i = 1;
					while(i < len) {
						this.buf.b += Std.string(",");
						this.toStringRec(v1[i++]);
					}
				}
				this.buf.b += Std.string("]");
			} else if(c == Hash) {
				var v1 = v;
				var o = { };
				var $it0 = v1.keys();
				while( $it0.hasNext() ) {
					var k = $it0.next();
					o[k] = v1.get(k);
				}
				this.objString(o);
			} else this.objString(v);
			break;
		case 7:
			var e = $e[2];
			this.buf.b += Std.string(v[1]);
			break;
		case 3:
			this.buf.b += Std.string(v?"true":"false");
			break;
		case 0:
			this.buf.b += Std.string("null");
			break;
		}
	}
	,objString: function(v) {
		this.fieldsString(v,Reflect.fields(v));
	}
	,fieldsString: function(v,fields) {
		var first = true;
		this.buf.b += Std.string("{");
		var _g = 0;
		while(_g < fields.length) {
			var f = fields[_g];
			++_g;
			var value = Reflect.field(v,f);
			if(Reflect.isFunction(value)) continue;
			if(first) first = false; else this.buf.b += Std.string(",");
			this.quote(f);
			this.buf.b += Std.string(":");
			this.toStringRec(value);
		}
		this.buf.b += Std.string("}");
	}
	,toString: function(v) {
		this.buf = new StringBuf();
		this.toStringRec(v);
		return this.buf.b;
	}
	,reg_float: null
	,pos: null
	,str: null
	,buf: null
	,__class__: haxe.Json
}
haxe.Log = $hxClasses["haxe.Log"] = function() { }
haxe.Log.__name__ = ["haxe","Log"];
haxe.Log.trace = function(v,infos) {
	js.Boot.__trace(v,infos);
}
haxe.Log.clear = function() {
	js.Boot.__clear_trace();
}
haxe.Md5 = $hxClasses["haxe.Md5"] = function() {
};
haxe.Md5.__name__ = ["haxe","Md5"];
haxe.Md5.encode = function(s) {
	return new haxe.Md5().doEncode(s);
}
haxe.Md5.prototype = {
	doEncode: function(str) {
		var x = this.str2blks(str);
		var a = 1732584193;
		var b = -271733879;
		var c = -1732584194;
		var d = 271733878;
		var step;
		var i = 0;
		while(i < x.length) {
			var olda = a;
			var oldb = b;
			var oldc = c;
			var oldd = d;
			step = 0;
			a = this.ff(a,b,c,d,x[i],7,-680876936);
			d = this.ff(d,a,b,c,x[i + 1],12,-389564586);
			c = this.ff(c,d,a,b,x[i + 2],17,606105819);
			b = this.ff(b,c,d,a,x[i + 3],22,-1044525330);
			a = this.ff(a,b,c,d,x[i + 4],7,-176418897);
			d = this.ff(d,a,b,c,x[i + 5],12,1200080426);
			c = this.ff(c,d,a,b,x[i + 6],17,-1473231341);
			b = this.ff(b,c,d,a,x[i + 7],22,-45705983);
			a = this.ff(a,b,c,d,x[i + 8],7,1770035416);
			d = this.ff(d,a,b,c,x[i + 9],12,-1958414417);
			c = this.ff(c,d,a,b,x[i + 10],17,-42063);
			b = this.ff(b,c,d,a,x[i + 11],22,-1990404162);
			a = this.ff(a,b,c,d,x[i + 12],7,1804603682);
			d = this.ff(d,a,b,c,x[i + 13],12,-40341101);
			c = this.ff(c,d,a,b,x[i + 14],17,-1502002290);
			b = this.ff(b,c,d,a,x[i + 15],22,1236535329);
			a = this.gg(a,b,c,d,x[i + 1],5,-165796510);
			d = this.gg(d,a,b,c,x[i + 6],9,-1069501632);
			c = this.gg(c,d,a,b,x[i + 11],14,643717713);
			b = this.gg(b,c,d,a,x[i],20,-373897302);
			a = this.gg(a,b,c,d,x[i + 5],5,-701558691);
			d = this.gg(d,a,b,c,x[i + 10],9,38016083);
			c = this.gg(c,d,a,b,x[i + 15],14,-660478335);
			b = this.gg(b,c,d,a,x[i + 4],20,-405537848);
			a = this.gg(a,b,c,d,x[i + 9],5,568446438);
			d = this.gg(d,a,b,c,x[i + 14],9,-1019803690);
			c = this.gg(c,d,a,b,x[i + 3],14,-187363961);
			b = this.gg(b,c,d,a,x[i + 8],20,1163531501);
			a = this.gg(a,b,c,d,x[i + 13],5,-1444681467);
			d = this.gg(d,a,b,c,x[i + 2],9,-51403784);
			c = this.gg(c,d,a,b,x[i + 7],14,1735328473);
			b = this.gg(b,c,d,a,x[i + 12],20,-1926607734);
			a = this.hh(a,b,c,d,x[i + 5],4,-378558);
			d = this.hh(d,a,b,c,x[i + 8],11,-2022574463);
			c = this.hh(c,d,a,b,x[i + 11],16,1839030562);
			b = this.hh(b,c,d,a,x[i + 14],23,-35309556);
			a = this.hh(a,b,c,d,x[i + 1],4,-1530992060);
			d = this.hh(d,a,b,c,x[i + 4],11,1272893353);
			c = this.hh(c,d,a,b,x[i + 7],16,-155497632);
			b = this.hh(b,c,d,a,x[i + 10],23,-1094730640);
			a = this.hh(a,b,c,d,x[i + 13],4,681279174);
			d = this.hh(d,a,b,c,x[i],11,-358537222);
			c = this.hh(c,d,a,b,x[i + 3],16,-722521979);
			b = this.hh(b,c,d,a,x[i + 6],23,76029189);
			a = this.hh(a,b,c,d,x[i + 9],4,-640364487);
			d = this.hh(d,a,b,c,x[i + 12],11,-421815835);
			c = this.hh(c,d,a,b,x[i + 15],16,530742520);
			b = this.hh(b,c,d,a,x[i + 2],23,-995338651);
			a = this.ii(a,b,c,d,x[i],6,-198630844);
			d = this.ii(d,a,b,c,x[i + 7],10,1126891415);
			c = this.ii(c,d,a,b,x[i + 14],15,-1416354905);
			b = this.ii(b,c,d,a,x[i + 5],21,-57434055);
			a = this.ii(a,b,c,d,x[i + 12],6,1700485571);
			d = this.ii(d,a,b,c,x[i + 3],10,-1894986606);
			c = this.ii(c,d,a,b,x[i + 10],15,-1051523);
			b = this.ii(b,c,d,a,x[i + 1],21,-2054922799);
			a = this.ii(a,b,c,d,x[i + 8],6,1873313359);
			d = this.ii(d,a,b,c,x[i + 15],10,-30611744);
			c = this.ii(c,d,a,b,x[i + 6],15,-1560198380);
			b = this.ii(b,c,d,a,x[i + 13],21,1309151649);
			a = this.ii(a,b,c,d,x[i + 4],6,-145523070);
			d = this.ii(d,a,b,c,x[i + 11],10,-1120210379);
			c = this.ii(c,d,a,b,x[i + 2],15,718787259);
			b = this.ii(b,c,d,a,x[i + 9],21,-343485551);
			a = this.addme(a,olda);
			b = this.addme(b,oldb);
			c = this.addme(c,oldc);
			d = this.addme(d,oldd);
			i += 16;
		}
		return this.rhex(a) + this.rhex(b) + this.rhex(c) + this.rhex(d);
	}
	,ii: function(a,b,c,d,x,s,t) {
		return this.cmn(this.bitXOR(c,this.bitOR(b,~d)),a,b,x,s,t);
	}
	,hh: function(a,b,c,d,x,s,t) {
		return this.cmn(this.bitXOR(this.bitXOR(b,c),d),a,b,x,s,t);
	}
	,gg: function(a,b,c,d,x,s,t) {
		return this.cmn(this.bitOR(this.bitAND(b,d),this.bitAND(c,~d)),a,b,x,s,t);
	}
	,ff: function(a,b,c,d,x,s,t) {
		return this.cmn(this.bitOR(this.bitAND(b,c),this.bitAND(~b,d)),a,b,x,s,t);
	}
	,cmn: function(q,a,b,x,s,t) {
		return this.addme(this.rol(this.addme(this.addme(a,q),this.addme(x,t)),s),b);
	}
	,rol: function(num,cnt) {
		return num << cnt | num >>> 32 - cnt;
	}
	,str2blks: function(str) {
		var nblk = (str.length + 8 >> 6) + 1;
		var blks = new Array();
		var _g1 = 0, _g = nblk * 16;
		while(_g1 < _g) {
			var i = _g1++;
			blks[i] = 0;
		}
		var i = 0;
		while(i < str.length) {
			blks[i >> 2] |= HxOverrides.cca(str,i) << (str.length * 8 + i) % 4 * 8;
			i++;
		}
		blks[i >> 2] |= 128 << (str.length * 8 + i) % 4 * 8;
		var l = str.length * 8;
		var k = nblk * 16 - 2;
		blks[k] = l & 255;
		blks[k] |= (l >>> 8 & 255) << 8;
		blks[k] |= (l >>> 16 & 255) << 16;
		blks[k] |= (l >>> 24 & 255) << 24;
		return blks;
	}
	,rhex: function(num) {
		var str = "";
		var hex_chr = "0123456789abcdef";
		var _g = 0;
		while(_g < 4) {
			var j = _g++;
			str += hex_chr.charAt(num >> j * 8 + 4 & 15) + hex_chr.charAt(num >> j * 8 & 15);
		}
		return str;
	}
	,addme: function(x,y) {
		var lsw = (x & 65535) + (y & 65535);
		var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
		return msw << 16 | lsw & 65535;
	}
	,bitAND: function(a,b) {
		var lsb = a & 1 & (b & 1);
		var msb31 = a >>> 1 & b >>> 1;
		return msb31 << 1 | lsb;
	}
	,bitXOR: function(a,b) {
		var lsb = a & 1 ^ b & 1;
		var msb31 = a >>> 1 ^ b >>> 1;
		return msb31 << 1 | lsb;
	}
	,bitOR: function(a,b) {
		var lsb = a & 1 | b & 1;
		var msb31 = a >>> 1 | b >>> 1;
		return msb31 << 1 | lsb;
	}
	,__class__: haxe.Md5
}
haxe.Serializer = $hxClasses["haxe.Serializer"] = function() {
	this.buf = new StringBuf();
	this.cache = new Array();
	this.useCache = haxe.Serializer.USE_CACHE;
	this.useEnumIndex = haxe.Serializer.USE_ENUM_INDEX;
	this.shash = new Hash();
	this.scount = 0;
};
haxe.Serializer.__name__ = ["haxe","Serializer"];
haxe.Serializer.run = function(v) {
	var s = new haxe.Serializer();
	s.serialize(v);
	return s.toString();
}
haxe.Serializer.prototype = {
	serializeException: function(e) {
		this.buf.b += Std.string("x");
		this.serialize(e);
	}
	,serialize: function(v) {
		var $e = (Type["typeof"](v));
		switch( $e[1] ) {
		case 0:
			this.buf.b += Std.string("n");
			break;
		case 1:
			if(v == 0) {
				this.buf.b += Std.string("z");
				return;
			}
			this.buf.b += Std.string("i");
			this.buf.b += Std.string(v);
			break;
		case 2:
			if(Math.isNaN(v)) this.buf.b += Std.string("k"); else if(!Math.isFinite(v)) this.buf.b += Std.string(v < 0?"m":"p"); else {
				this.buf.b += Std.string("d");
				this.buf.b += Std.string(v);
			}
			break;
		case 3:
			this.buf.b += Std.string(v?"t":"f");
			break;
		case 6:
			var c = $e[2];
			if(c == String) {
				this.serializeString(v);
				return;
			}
			if(this.useCache && this.serializeRef(v)) return;
			switch(c) {
			case Array:
				var ucount = 0;
				this.buf.b += Std.string("a");
				var l = v.length;
				var _g = 0;
				while(_g < l) {
					var i = _g++;
					if(v[i] == null) ucount++; else {
						if(ucount > 0) {
							if(ucount == 1) this.buf.b += Std.string("n"); else {
								this.buf.b += Std.string("u");
								this.buf.b += Std.string(ucount);
							}
							ucount = 0;
						}
						this.serialize(v[i]);
					}
				}
				if(ucount > 0) {
					if(ucount == 1) this.buf.b += Std.string("n"); else {
						this.buf.b += Std.string("u");
						this.buf.b += Std.string(ucount);
					}
				}
				this.buf.b += Std.string("h");
				break;
			case List:
				this.buf.b += Std.string("l");
				var v1 = v;
				var $it0 = v1.iterator();
				while( $it0.hasNext() ) {
					var i = $it0.next();
					this.serialize(i);
				}
				this.buf.b += Std.string("h");
				break;
			case Date:
				var d = v;
				this.buf.b += Std.string("v");
				this.buf.b += Std.string(HxOverrides.dateStr(d));
				break;
			case Hash:
				this.buf.b += Std.string("b");
				var v1 = v;
				var $it1 = v1.keys();
				while( $it1.hasNext() ) {
					var k = $it1.next();
					this.serializeString(k);
					this.serialize(v1.get(k));
				}
				this.buf.b += Std.string("h");
				break;
			case IntHash:
				this.buf.b += Std.string("q");
				var v1 = v;
				var $it2 = v1.keys();
				while( $it2.hasNext() ) {
					var k = $it2.next();
					this.buf.b += Std.string(":");
					this.buf.b += Std.string(k);
					this.serialize(v1.get(k));
				}
				this.buf.b += Std.string("h");
				break;
			case haxe.io.Bytes:
				var v1 = v;
				var i = 0;
				var max = v1.length - 2;
				var charsBuf = new StringBuf();
				var b64 = haxe.Serializer.BASE64;
				while(i < max) {
					var b1 = v1.b[i++];
					var b2 = v1.b[i++];
					var b3 = v1.b[i++];
					charsBuf.b += Std.string(b64.charAt(b1 >> 2));
					charsBuf.b += Std.string(b64.charAt((b1 << 4 | b2 >> 4) & 63));
					charsBuf.b += Std.string(b64.charAt((b2 << 2 | b3 >> 6) & 63));
					charsBuf.b += Std.string(b64.charAt(b3 & 63));
				}
				if(i == max) {
					var b1 = v1.b[i++];
					var b2 = v1.b[i++];
					charsBuf.b += Std.string(b64.charAt(b1 >> 2));
					charsBuf.b += Std.string(b64.charAt((b1 << 4 | b2 >> 4) & 63));
					charsBuf.b += Std.string(b64.charAt(b2 << 2 & 63));
				} else if(i == max + 1) {
					var b1 = v1.b[i++];
					charsBuf.b += Std.string(b64.charAt(b1 >> 2));
					charsBuf.b += Std.string(b64.charAt(b1 << 4 & 63));
				}
				var chars = charsBuf.b;
				this.buf.b += Std.string("s");
				this.buf.b += Std.string(chars.length);
				this.buf.b += Std.string(":");
				this.buf.b += Std.string(chars);
				break;
			default:
				this.cache.pop();
				if(v.hxSerialize != null) {
					this.buf.b += Std.string("C");
					this.serializeString(Type.getClassName(c));
					this.cache.push(v);
					v.hxSerialize(this);
					this.buf.b += Std.string("g");
				} else {
					this.buf.b += Std.string("c");
					this.serializeString(Type.getClassName(c));
					this.cache.push(v);
					this.serializeFields(v);
				}
			}
			break;
		case 4:
			if(this.useCache && this.serializeRef(v)) return;
			this.buf.b += Std.string("o");
			this.serializeFields(v);
			break;
		case 7:
			var e = $e[2];
			if(this.useCache && this.serializeRef(v)) return;
			this.cache.pop();
			this.buf.b += Std.string(this.useEnumIndex?"j":"w");
			this.serializeString(Type.getEnumName(e));
			if(this.useEnumIndex) {
				this.buf.b += Std.string(":");
				this.buf.b += Std.string(v[1]);
			} else this.serializeString(v[0]);
			this.buf.b += Std.string(":");
			var l = v.length;
			this.buf.b += Std.string(l - 2);
			var _g = 2;
			while(_g < l) {
				var i = _g++;
				this.serialize(v[i]);
			}
			this.cache.push(v);
			break;
		case 5:
			throw "Cannot serialize function";
			break;
		default:
			throw "Cannot serialize " + Std.string(v);
		}
	}
	,serializeFields: function(v) {
		var _g = 0, _g1 = Reflect.fields(v);
		while(_g < _g1.length) {
			var f = _g1[_g];
			++_g;
			this.serializeString(f);
			this.serialize(Reflect.field(v,f));
		}
		this.buf.b += Std.string("g");
	}
	,serializeRef: function(v) {
		var vt = typeof(v);
		var _g1 = 0, _g = this.cache.length;
		while(_g1 < _g) {
			var i = _g1++;
			var ci = this.cache[i];
			if(typeof(ci) == vt && ci == v) {
				this.buf.b += Std.string("r");
				this.buf.b += Std.string(i);
				return true;
			}
		}
		this.cache.push(v);
		return false;
	}
	,serializeString: function(s) {
		var x = this.shash.get(s);
		if(x != null) {
			this.buf.b += Std.string("R");
			this.buf.b += Std.string(x);
			return;
		}
		this.shash.set(s,this.scount++);
		this.buf.b += Std.string("y");
		s = StringTools.urlEncode(s);
		this.buf.b += Std.string(s.length);
		this.buf.b += Std.string(":");
		this.buf.b += Std.string(s);
	}
	,toString: function() {
		return this.buf.b;
	}
	,useEnumIndex: null
	,useCache: null
	,scount: null
	,shash: null
	,cache: null
	,buf: null
	,__class__: haxe.Serializer
}
haxe.Timer = $hxClasses["haxe.Timer"] = function(time_ms) {
	var me = this;
	this.id = window.setInterval(function() {
		me.run();
	},time_ms);
};
haxe.Timer.__name__ = ["haxe","Timer"];
haxe.Timer.delay = function(f,time_ms) {
	var t = new haxe.Timer(time_ms);
	t.run = function() {
		t.stop();
		f();
	};
	return t;
}
haxe.Timer.measure = function(f,pos) {
	var t0 = haxe.Timer.stamp();
	var r = f();
	haxe.Log.trace(haxe.Timer.stamp() - t0 + "s",pos);
	return r;
}
haxe.Timer.stamp = function() {
	return new Date().getTime() / 1000;
}
haxe.Timer.prototype = {
	run: function() {
	}
	,stop: function() {
		if(this.id == null) return;
		window.clearInterval(this.id);
		this.id = null;
	}
	,id: null
	,__class__: haxe.Timer
}
haxe.Unserializer = $hxClasses["haxe.Unserializer"] = function(buf) {
	this.buf = buf;
	this.length = buf.length;
	this.pos = 0;
	this.scache = new Array();
	this.cache = new Array();
	var r = haxe.Unserializer.DEFAULT_RESOLVER;
	if(r == null) {
		r = Type;
		haxe.Unserializer.DEFAULT_RESOLVER = r;
	}
	this.setResolver(r);
};
haxe.Unserializer.__name__ = ["haxe","Unserializer"];
haxe.Unserializer.initCodes = function() {
	var codes = new Array();
	var _g1 = 0, _g = haxe.Unserializer.BASE64.length;
	while(_g1 < _g) {
		var i = _g1++;
		codes[haxe.Unserializer.BASE64.charCodeAt(i)] = i;
	}
	return codes;
}
haxe.Unserializer.run = function(v) {
	return new haxe.Unserializer(v).unserialize();
}
haxe.Unserializer.prototype = {
	unserialize: function() {
		switch(this.buf.charCodeAt(this.pos++)) {
		case 110:
			return null;
		case 116:
			return true;
		case 102:
			return false;
		case 122:
			return 0;
		case 105:
			return this.readDigits();
		case 100:
			var p1 = this.pos;
			while(true) {
				var c = this.buf.charCodeAt(this.pos);
				if(c >= 43 && c < 58 || c == 101 || c == 69) this.pos++; else break;
			}
			return Std.parseFloat(HxOverrides.substr(this.buf,p1,this.pos - p1));
		case 121:
			var len = this.readDigits();
			if(this.buf.charCodeAt(this.pos++) != 58 || this.length - this.pos < len) throw "Invalid string length";
			var s = HxOverrides.substr(this.buf,this.pos,len);
			this.pos += len;
			s = StringTools.urlDecode(s);
			this.scache.push(s);
			return s;
		case 107:
			return Math.NaN;
		case 109:
			return Math.NEGATIVE_INFINITY;
		case 112:
			return Math.POSITIVE_INFINITY;
		case 97:
			var buf = this.buf;
			var a = new Array();
			this.cache.push(a);
			while(true) {
				var c = this.buf.charCodeAt(this.pos);
				if(c == 104) {
					this.pos++;
					break;
				}
				if(c == 117) {
					this.pos++;
					var n = this.readDigits();
					a[a.length + n - 1] = null;
				} else a.push(this.unserialize());
			}
			return a;
		case 111:
			var o = { };
			this.cache.push(o);
			this.unserializeObject(o);
			return o;
		case 114:
			var n = this.readDigits();
			if(n < 0 || n >= this.cache.length) throw "Invalid reference";
			return this.cache[n];
		case 82:
			var n = this.readDigits();
			if(n < 0 || n >= this.scache.length) throw "Invalid string reference";
			return this.scache[n];
		case 120:
			throw this.unserialize();
			break;
		case 99:
			var name = this.unserialize();
			var cl = this.resolver.resolveClass(name);
			if(cl == null) throw "Class not found " + name;
			var o = Type.createEmptyInstance(cl);
			this.cache.push(o);
			this.unserializeObject(o);
			return o;
		case 119:
			var name = this.unserialize();
			var edecl = this.resolver.resolveEnum(name);
			if(edecl == null) throw "Enum not found " + name;
			var e = this.unserializeEnum(edecl,this.unserialize());
			this.cache.push(e);
			return e;
		case 106:
			var name = this.unserialize();
			var edecl = this.resolver.resolveEnum(name);
			if(edecl == null) throw "Enum not found " + name;
			this.pos++;
			var index = this.readDigits();
			var tag = Type.getEnumConstructs(edecl)[index];
			if(tag == null) throw "Unknown enum index " + name + "@" + index;
			var e = this.unserializeEnum(edecl,tag);
			this.cache.push(e);
			return e;
		case 108:
			var l = new List();
			this.cache.push(l);
			var buf = this.buf;
			while(this.buf.charCodeAt(this.pos) != 104) l.add(this.unserialize());
			this.pos++;
			return l;
		case 98:
			var h = new Hash();
			this.cache.push(h);
			var buf = this.buf;
			while(this.buf.charCodeAt(this.pos) != 104) {
				var s = this.unserialize();
				h.set(s,this.unserialize());
			}
			this.pos++;
			return h;
		case 113:
			var h = new IntHash();
			this.cache.push(h);
			var buf = this.buf;
			var c = this.buf.charCodeAt(this.pos++);
			while(c == 58) {
				var i = this.readDigits();
				h.set(i,this.unserialize());
				c = this.buf.charCodeAt(this.pos++);
			}
			if(c != 104) throw "Invalid IntHash format";
			return h;
		case 118:
			var d = HxOverrides.strDate(HxOverrides.substr(this.buf,this.pos,19));
			this.cache.push(d);
			this.pos += 19;
			return d;
		case 115:
			var len = this.readDigits();
			var buf = this.buf;
			if(this.buf.charCodeAt(this.pos++) != 58 || this.length - this.pos < len) throw "Invalid bytes length";
			var codes = haxe.Unserializer.CODES;
			if(codes == null) {
				codes = haxe.Unserializer.initCodes();
				haxe.Unserializer.CODES = codes;
			}
			var i = this.pos;
			var rest = len & 3;
			var size = (len >> 2) * 3 + (rest >= 2?rest - 1:0);
			var max = i + (len - rest);
			var bytes = haxe.io.Bytes.alloc(size);
			var bpos = 0;
			while(i < max) {
				var c1 = codes[buf.charCodeAt(i++)];
				var c2 = codes[buf.charCodeAt(i++)];
				bytes.b[bpos++] = (c1 << 2 | c2 >> 4) & 255;
				var c3 = codes[buf.charCodeAt(i++)];
				bytes.b[bpos++] = (c2 << 4 | c3 >> 2) & 255;
				var c4 = codes[buf.charCodeAt(i++)];
				bytes.b[bpos++] = (c3 << 6 | c4) & 255;
			}
			if(rest >= 2) {
				var c1 = codes[buf.charCodeAt(i++)];
				var c2 = codes[buf.charCodeAt(i++)];
				bytes.b[bpos++] = (c1 << 2 | c2 >> 4) & 255;
				if(rest == 3) {
					var c3 = codes[buf.charCodeAt(i++)];
					bytes.b[bpos++] = (c2 << 4 | c3 >> 2) & 255;
				}
			}
			this.pos += len;
			this.cache.push(bytes);
			return bytes;
		case 67:
			var name = this.unserialize();
			var cl = this.resolver.resolveClass(name);
			if(cl == null) throw "Class not found " + name;
			var o = Type.createEmptyInstance(cl);
			this.cache.push(o);
			o.hxUnserialize(this);
			if(this.buf.charCodeAt(this.pos++) != 103) throw "Invalid custom data";
			return o;
		default:
		}
		this.pos--;
		throw "Invalid char " + this.buf.charAt(this.pos) + " at position " + this.pos;
	}
	,unserializeEnum: function(edecl,tag) {
		if(this.buf.charCodeAt(this.pos++) != 58) throw "Invalid enum format";
		var nargs = this.readDigits();
		if(nargs == 0) return Type.createEnum(edecl,tag);
		var args = new Array();
		while(nargs-- > 0) args.push(this.unserialize());
		return Type.createEnum(edecl,tag,args);
	}
	,unserializeObject: function(o) {
		while(true) {
			if(this.pos >= this.length) throw "Invalid object";
			if(this.buf.charCodeAt(this.pos) == 103) break;
			var k = this.unserialize();
			if(!js.Boot.__instanceof(k,String)) throw "Invalid object key";
			var v = this.unserialize();
			o[k] = v;
		}
		this.pos++;
	}
	,readDigits: function() {
		var k = 0;
		var s = false;
		var fpos = this.pos;
		while(true) {
			var c = this.buf.charCodeAt(this.pos);
			if(c != c) break;
			if(c == 45) {
				if(this.pos != fpos) break;
				s = true;
				this.pos++;
				continue;
			}
			if(c < 48 || c > 57) break;
			k = k * 10 + (c - 48);
			this.pos++;
		}
		if(s) k *= -1;
		return k;
	}
	,get: function(p) {
		return this.buf.charCodeAt(p);
	}
	,getResolver: function() {
		return this.resolver;
	}
	,setResolver: function(r) {
		if(r == null) this.resolver = { resolveClass : function(_) {
			return null;
		}, resolveEnum : function(_) {
			return null;
		}}; else this.resolver = r;
	}
	,resolver: null
	,scache: null
	,cache: null
	,length: null
	,pos: null
	,buf: null
	,__class__: haxe.Unserializer
}
if(!haxe.io) haxe.io = {}
haxe.io.Bytes = $hxClasses["haxe.io.Bytes"] = function(length,b) {
	this.length = length;
	this.b = b;
};
haxe.io.Bytes.__name__ = ["haxe","io","Bytes"];
haxe.io.Bytes.alloc = function(length) {
	var a = new Array();
	var _g = 0;
	while(_g < length) {
		var i = _g++;
		a.push(0);
	}
	return new haxe.io.Bytes(length,a);
}
haxe.io.Bytes.ofString = function(s) {
	var a = new Array();
	var _g1 = 0, _g = s.length;
	while(_g1 < _g) {
		var i = _g1++;
		var c = s.charCodeAt(i);
		if(c <= 127) a.push(c); else if(c <= 2047) {
			a.push(192 | c >> 6);
			a.push(128 | c & 63);
		} else if(c <= 65535) {
			a.push(224 | c >> 12);
			a.push(128 | c >> 6 & 63);
			a.push(128 | c & 63);
		} else {
			a.push(240 | c >> 18);
			a.push(128 | c >> 12 & 63);
			a.push(128 | c >> 6 & 63);
			a.push(128 | c & 63);
		}
	}
	return new haxe.io.Bytes(a.length,a);
}
haxe.io.Bytes.ofData = function(b) {
	return new haxe.io.Bytes(b.length,b);
}
haxe.io.Bytes.prototype = {
	getData: function() {
		return this.b;
	}
	,toHex: function() {
		var s = new StringBuf();
		var chars = [];
		var str = "0123456789abcdef";
		var _g1 = 0, _g = str.length;
		while(_g1 < _g) {
			var i = _g1++;
			chars.push(HxOverrides.cca(str,i));
		}
		var _g1 = 0, _g = this.length;
		while(_g1 < _g) {
			var i = _g1++;
			var c = this.b[i];
			s.b += String.fromCharCode(chars[c >> 4]);
			s.b += String.fromCharCode(chars[c & 15]);
		}
		return s.b;
	}
	,toString: function() {
		return this.readString(0,this.length);
	}
	,readString: function(pos,len) {
		if(pos < 0 || len < 0 || pos + len > this.length) throw haxe.io.Error.OutsideBounds;
		var s = "";
		var b = this.b;
		var fcc = String.fromCharCode;
		var i = pos;
		var max = pos + len;
		while(i < max) {
			var c = b[i++];
			if(c < 128) {
				if(c == 0) break;
				s += fcc(c);
			} else if(c < 224) s += fcc((c & 63) << 6 | b[i++] & 127); else if(c < 240) {
				var c2 = b[i++];
				s += fcc((c & 31) << 12 | (c2 & 127) << 6 | b[i++] & 127);
			} else {
				var c2 = b[i++];
				var c3 = b[i++];
				s += fcc((c & 15) << 18 | (c2 & 127) << 12 | c3 << 6 & 127 | b[i++] & 127);
			}
		}
		return s;
	}
	,compare: function(other) {
		var b1 = this.b;
		var b2 = other.b;
		var len = this.length < other.length?this.length:other.length;
		var _g = 0;
		while(_g < len) {
			var i = _g++;
			if(b1[i] != b2[i]) return b1[i] - b2[i];
		}
		return this.length - other.length;
	}
	,sub: function(pos,len) {
		if(pos < 0 || len < 0 || pos + len > this.length) throw haxe.io.Error.OutsideBounds;
		return new haxe.io.Bytes(len,this.b.slice(pos,pos + len));
	}
	,blit: function(pos,src,srcpos,len) {
		if(pos < 0 || srcpos < 0 || len < 0 || pos + len > this.length || srcpos + len > src.length) throw haxe.io.Error.OutsideBounds;
		var b1 = this.b;
		var b2 = src.b;
		if(b1 == b2 && pos > srcpos) {
			var i = len;
			while(i > 0) {
				i--;
				b1[i + pos] = b2[i + srcpos];
			}
			return;
		}
		var _g = 0;
		while(_g < len) {
			var i = _g++;
			b1[i + pos] = b2[i + srcpos];
		}
	}
	,set: function(pos,v) {
		this.b[pos] = v & 255;
	}
	,get: function(pos) {
		return this.b[pos];
	}
	,b: null
	,length: null
	,__class__: haxe.io.Bytes
}
haxe.io.Error = $hxClasses["haxe.io.Error"] = { __ename__ : ["haxe","io","Error"], __constructs__ : ["Blocked","Overflow","OutsideBounds","Custom"] }
haxe.io.Error.Blocked = ["Blocked",0];
haxe.io.Error.Blocked.toString = $estr;
haxe.io.Error.Blocked.__enum__ = haxe.io.Error;
haxe.io.Error.Overflow = ["Overflow",1];
haxe.io.Error.Overflow.toString = $estr;
haxe.io.Error.Overflow.__enum__ = haxe.io.Error;
haxe.io.Error.OutsideBounds = ["OutsideBounds",2];
haxe.io.Error.OutsideBounds.toString = $estr;
haxe.io.Error.OutsideBounds.__enum__ = haxe.io.Error;
haxe.io.Error.Custom = function(e) { var $x = ["Custom",3,e]; $x.__enum__ = haxe.io.Error; $x.toString = $estr; return $x; }
if(!haxe.remoting) haxe.remoting = {}
haxe.remoting.Connection = $hxClasses["haxe.remoting.Connection"] = function() { }
haxe.remoting.Connection.__name__ = ["haxe","remoting","Connection"];
haxe.remoting.Connection.prototype = {
	call: null
	,resolve: null
	,__class__: haxe.remoting.Connection
}
haxe.remoting.Context = $hxClasses["haxe.remoting.Context"] = function() {
	this.objects = new Hash();
};
haxe.remoting.Context.__name__ = ["haxe","remoting","Context"];
haxe.remoting.Context.share = function(name,obj) {
	var ctx = new haxe.remoting.Context();
	ctx.addObject(name,obj);
	return ctx;
}
haxe.remoting.Context.prototype = {
	call: function(path,params) {
		if(path.length < 2) throw "Invalid path '" + path.join(".") + "'";
		var inf = this.objects.get(path[0]);
		if(inf == null) throw "No such object " + path[0];
		var o = inf.obj;
		var m = Reflect.field(o,path[1]);
		if(path.length > 2) {
			if(!inf.rec) throw "Can't access " + path.join(".");
			var _g1 = 2, _g = path.length;
			while(_g1 < _g) {
				var i = _g1++;
				o = m;
				m = Reflect.field(o,path[i]);
			}
		}
		if(!Reflect.isFunction(m)) throw "No such method " + path.join(".");
		return m.apply(o,params);
	}
	,addObject: function(name,obj,recursive) {
		this.objects.set(name,{ obj : obj, rec : recursive});
	}
	,objects: null
	,__class__: haxe.remoting.Context
}
haxe.remoting.HttpConnection = $hxClasses["haxe.remoting.HttpConnection"] = function(url,path) {
	this.__url = url;
	this.__path = path;
};
haxe.remoting.HttpConnection.__name__ = ["haxe","remoting","HttpConnection"];
haxe.remoting.HttpConnection.__interfaces__ = [haxe.remoting.Connection];
haxe.remoting.HttpConnection.urlConnect = function(url) {
	return new haxe.remoting.HttpConnection(url,[]);
}
haxe.remoting.HttpConnection.processRequest = function(requestData,ctx) {
	try {
		var u = new haxe.Unserializer(requestData);
		var path = u.unserialize();
		var args = u.unserialize();
		var data = ctx.call(path,args);
		var s = new haxe.Serializer();
		s.serialize(data);
		return "hxr" + s.toString();
	} catch( e ) {
		var s = new haxe.Serializer();
		s.serializeException(e);
		return "hxr" + s.toString();
	}
}
haxe.remoting.HttpConnection.prototype = {
	call: function(params) {
		var data = null;
		var h = new haxe.Http(this.__url);
		h.async = false;
		var s = new haxe.Serializer();
		s.serialize(this.__path);
		s.serialize(params);
		h.setHeader("X-Haxe-Remoting","1");
		h.setParameter("__x",s.toString());
		h.onData = function(d) {
			data = d;
		};
		h.onError = function(e) {
			throw e;
		};
		h.request(true);
		if(HxOverrides.substr(data,0,3) != "hxr") throw "Invalid response : '" + data + "'";
		data = HxOverrides.substr(data,3,null);
		return new haxe.Unserializer(data).unserialize();
	}
	,resolve: function(name) {
		var c = new haxe.remoting.HttpConnection(this.__url,this.__path.slice());
		c.__path.push(name);
		return c;
	}
	,__path: null
	,__url: null
	,__class__: haxe.remoting.HttpConnection
}
var js = js || {}
js.Boot = $hxClasses["js.Boot"] = function() { }
js.Boot.__name__ = ["js","Boot"];
js.Boot.__unhtml = function(s) {
	return s.split("&").join("&amp;").split("<").join("&lt;").split(">").join("&gt;");
}
js.Boot.__trace = function(v,i) {
	var msg = i != null?i.fileName + ":" + i.lineNumber + ": ":"";
	msg += js.Boot.__string_rec(v,"");
	var d;
	if(typeof(document) != "undefined" && (d = document.getElementById("haxe:trace")) != null) d.innerHTML += js.Boot.__unhtml(msg) + "<br/>"; else if(typeof(console) != "undefined" && console.log != null) console.log(msg);
}
js.Boot.__clear_trace = function() {
	var d = document.getElementById("haxe:trace");
	if(d != null) d.innerHTML = "";
}
js.Boot.isClass = function(o) {
	return o.__name__;
}
js.Boot.isEnum = function(e) {
	return e.__ename__;
}
js.Boot.getClass = function(o) {
	return o.__class__;
}
js.Boot.__string_rec = function(o,s) {
	if(o == null) return "null";
	if(s.length >= 5) return "<...>";
	var t = typeof(o);
	if(t == "function" && (o.__name__ || o.__ename__)) t = "object";
	switch(t) {
	case "object":
		if(o instanceof Array) {
			if(o.__enum__) {
				if(o.length == 2) return o[0];
				var str = o[0] + "(";
				s += "\t";
				var _g1 = 2, _g = o.length;
				while(_g1 < _g) {
					var i = _g1++;
					if(i != 2) str += "," + js.Boot.__string_rec(o[i],s); else str += js.Boot.__string_rec(o[i],s);
				}
				return str + ")";
			}
			var l = o.length;
			var i;
			var str = "[";
			s += "\t";
			var _g = 0;
			while(_g < l) {
				var i1 = _g++;
				str += (i1 > 0?",":"") + js.Boot.__string_rec(o[i1],s);
			}
			str += "]";
			return str;
		}
		var tostr;
		try {
			tostr = o.toString;
		} catch( e ) {
			return "???";
		}
		if(tostr != null && tostr != Object.toString) {
			var s2 = o.toString();
			if(s2 != "[object Object]") return s2;
		}
		var k = null;
		var str = "{\n";
		s += "\t";
		var hasp = o.hasOwnProperty != null;
		for( var k in o ) { ;
		if(hasp && !o.hasOwnProperty(k)) {
			continue;
		}
		if(k == "prototype" || k == "__class__" || k == "__super__" || k == "__interfaces__" || k == "__properties__") {
			continue;
		}
		if(str.length != 2) str += ", \n";
		str += s + k + " : " + js.Boot.__string_rec(o[k],s);
		}
		s = s.substring(1);
		str += "\n" + s + "}";
		return str;
	case "function":
		return "<function>";
	case "string":
		return o;
	default:
		return String(o);
	}
}
js.Boot.__interfLoop = function(cc,cl) {
	if(cc == null) return false;
	if(cc == cl) return true;
	var intf = cc.__interfaces__;
	if(intf != null) {
		var _g1 = 0, _g = intf.length;
		while(_g1 < _g) {
			var i = _g1++;
			var i1 = intf[i];
			if(i1 == cl || js.Boot.__interfLoop(i1,cl)) return true;
		}
	}
	return js.Boot.__interfLoop(cc.__super__,cl);
}
js.Boot.__instanceof = function(o,cl) {
	try {
		if(o instanceof cl) {
			if(cl == Array) return o.__enum__ == null;
			return true;
		}
		if(js.Boot.__interfLoop(o.__class__,cl)) return true;
	} catch( e ) {
		if(cl == null) return false;
	}
	switch(cl) {
	case Int:
		return Math.ceil(o%2147483648.0) === o;
	case Float:
		return typeof(o) == "number";
	case Bool:
		return o === true || o === false;
	case String:
		return typeof(o) == "string";
	case Dynamic:
		return true;
	default:
		if(o == null) return false;
		if(cl == Class && o.__name__ != null) return true; else null;
		if(cl == Enum && o.__ename__ != null) return true; else null;
		return o.__enum__ == cl;
	}
}
js.Boot.__cast = function(o,t) {
	if(js.Boot.__instanceof(o,t)) return o; else throw "Cannot cast " + Std.string(o) + " to " + Std.string(t);
}
js.Lib = $hxClasses["js.Lib"] = function() { }
js.Lib.__name__ = ["js","Lib"];
js.Lib.document = null;
js.Lib.window = null;
js.Lib.debug = function() {
	debugger;
}
js.Lib.alert = function(v) {
	alert(js.Boot.__string_rec(v,""));
}
js.Lib.eval = function(code) {
	return eval(code);
}
js.Lib.setErrorHandler = function(f) {
	js.Lib.onerror = f;
}
function $iterator(o) { if( o instanceof Array ) return function() { return HxOverrides.iter(o); }; return typeof(o.iterator) == 'function' ? $bind(o,o.iterator) : o.iterator; };
var $_;
function $bind(o,m) { var f = function(){ return f.method.apply(f.scope, arguments); }; f.scope = o; f.method = m; return f; };
if(Array.prototype.indexOf) HxOverrides.remove = function(a,o) {
	var i = a.indexOf(o);
	if(i == -1) return false;
	a.splice(i,1);
	return true;
}; else null;
Math.__name__ = ["Math"];
Math.NaN = Number.NaN;
Math.NEGATIVE_INFINITY = Number.NEGATIVE_INFINITY;
Math.POSITIVE_INFINITY = Number.POSITIVE_INFINITY;
$hxClasses.Math = Math;
Math.isFinite = function(i) {
	return isFinite(i);
};
Math.isNaN = function(i) {
	return isNaN(i);
};
String.prototype.__class__ = $hxClasses.String = String;
String.__name__ = ["String"];
Array.prototype.__class__ = $hxClasses.Array = Array;
Array.__name__ = ["Array"];
Date.prototype.__class__ = $hxClasses.Date = Date;
Date.__name__ = ["Date"];
var Int = $hxClasses.Int = { __name__ : ["Int"]};
var Dynamic = $hxClasses.Dynamic = { __name__ : ["Dynamic"]};
var Float = $hxClasses.Float = Number;
Float.__name__ = ["Float"];
var Bool = $hxClasses.Bool = Boolean;
Bool.__ename__ = ["Bool"];
var Class = $hxClasses.Class = { __name__ : ["Class"]};
var Enum = { };
var Void = $hxClasses.Void = { __ename__ : ["Void"]};
if(typeof(JSON) != "undefined") haxe.Json = JSON;
if(typeof document != "undefined") js.Lib.document = document;
if(typeof window != "undefined") {
	js.Lib.window = window;
	js.Lib.window.onerror = function(msg,url,line) {
		var f = js.Lib.onerror;
		if(f == null) return false;
		return f(msg,[url + ":" + line]);
	};
}
js.XMLHttpRequest = window.XMLHttpRequest?XMLHttpRequest:window.ActiveXObject?function() {
	try {
		return new ActiveXObject("Msxml2.XMLHTTP");
	} catch( e ) {
		try {
			return new ActiveXObject("Microsoft.XMLHTTP");
		} catch( e1 ) {
			throw "Unable to create XMLHttpRequest object.";
		}
	}
}:(function($this) {
	var $r;
	throw "Unable to create XMLHttpRequest object.";
	return $r;
}(this));
com.wiris.js.JsPluginViewer.USE_CREATE_IMAGE = 1;
com.wiris.js.JsPluginViewer.DEBUG = false;
com.wiris.js.JsPluginViewer.TECH = "server";
com.wiris.js.JsPluginViewer.VERSION = "7.23.0.1437";
com.wiris.system.JsDOMUtils.TOUCHHOLD_MOVE_MARGIN = 10;
com.wiris.system.JsDOMUtils.browser = new com.wiris.system.JsBrowser();
com.wiris.system.JsDOMUtils.initialized = false;
com.wiris.system.JsDOMUtils.touchDeviceListeners = new Array();
com.wiris.system.JsDOMUtils.mouseDeviceListeners = new Array();
com.wiris.system.JsDOMUtils.internetExplorerPointers = new Hash();
haxe.Serializer.USE_CACHE = false;
haxe.Serializer.USE_ENUM_INDEX = false;
haxe.Serializer.BASE64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789%:";
haxe.Unserializer.DEFAULT_RESOLVER = Type;
haxe.Unserializer.BASE64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789%:";
haxe.Unserializer.CODES = null;
haxe.remoting.HttpConnection.TIMEOUT = 10;
js.Lib.onerror = null;
com.wiris.js.JsPluginViewer.main();
delete Array.prototype.__class__; }());