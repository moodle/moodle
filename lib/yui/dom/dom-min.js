/*
Copyright (c) 2007, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.3.0
*/

(function(){var Y=YAHOO.util,getStyle,setStyle,id_counter=0,propertyCache={},reClassNameCache={};var isOpera=YAHOO.env.ua.opera,isSafari=YAHOO.env.ua.webkit,isGecko=YAHOO.env.ua.gecko,isIE=YAHOO.env.ua.ie;var patterns={HYPHEN:/(-[a-z])/i,ROOT_TAG:/^body|html$/i};var toCamel=function(property){if(!patterns.HYPHEN.test(property)){return property;}
if(propertyCache[property]){return propertyCache[property];}
var converted=property;while(patterns.HYPHEN.exec(converted)){converted=converted.replace(RegExp.$1,RegExp.$1.substr(1).toUpperCase());}
propertyCache[property]=converted;return converted;};var getClassRegEx=function(className){var re=reClassNameCache[className];if(!re){re=new RegExp('(?:^|\\s+)'+className+'(?:\\s+|$)');reClassNameCache[className]=re;}
return re;};if(document.defaultView&&document.defaultView.getComputedStyle){getStyle=function(el,property){var value=null;if(property=='float'){property='cssFloat';}
var computed=document.defaultView.getComputedStyle(el,'');if(computed){value=computed[toCamel(property)];}
return el.style[property]||value;};}else if(document.documentElement.currentStyle&&isIE){getStyle=function(el,property){switch(toCamel(property)){case'opacity':var val=100;try{val=el.filters['DXImageTransform.Microsoft.Alpha'].opacity;}catch(e){try{val=el.filters('alpha').opacity;}catch(e){}}
return val/100;case'float':property='styleFloat';default:var value=el.currentStyle?el.currentStyle[property]:null;return(el.style[property]||value);}};}else{getStyle=function(el,property){return el.style[property];};}
if(isIE){setStyle=function(el,property,val){switch(property){case'opacity':if(YAHOO.lang.isString(el.style.filter)){el.style.filter='alpha(opacity='+val*100+')';if(!el.currentStyle||!el.currentStyle.hasLayout){el.style.zoom=1;}}
break;case'float':property='styleFloat';default:el.style[property]=val;}};}else{setStyle=function(el,property,val){if(property=='float'){property='cssFloat';}
el.style[property]=val;};}
var testElement=function(node,method){return node&&node.nodeType==1&&(!method||method(node));};YAHOO.util.Dom={get:function(el){if(!el||el.tagName||el.item){return el;}
if(YAHOO.lang.isString(el)){return document.getElementById(el);}
if(el.splice){var c=[];for(var i=0,len=el.length;i<len;++i){c[c.length]=Y.Dom.get(el[i]);}
return c;}
return el;},getStyle:function(el,property){property=toCamel(property);var f=function(element){return getStyle(element,property);};return Y.Dom.batch(el,f,Y.Dom,true);},setStyle:function(el,property,val){property=toCamel(property);var f=function(element){setStyle(element,property,val);};Y.Dom.batch(el,f,Y.Dom,true);},getXY:function(el){var f=function(el){if((el.parentNode===null||el.offsetParent===null||this.getStyle(el,'display')=='none')&&el!=document.body){return false;}
var parentNode=null;var pos=[];var box;var doc=el.ownerDocument;if(el.getBoundingClientRect){box=el.getBoundingClientRect();return[box.left+Y.Dom.getDocumentScrollLeft(el.ownerDocument),box.top+Y.Dom.getDocumentScrollTop(el.ownerDocument)];}
else{pos=[el.offsetLeft,el.offsetTop];parentNode=el.offsetParent;var hasAbs=this.getStyle(el,'position')=='absolute';if(parentNode!=el){while(parentNode){pos[0]+=parentNode.offsetLeft;pos[1]+=parentNode.offsetTop;if(isSafari&&!hasAbs&&this.getStyle(parentNode,'position')=='absolute'){hasAbs=true;}
parentNode=parentNode.offsetParent;}}
if(isSafari&&hasAbs){pos[0]-=el.ownerDocument.body.offsetLeft;pos[1]-=el.ownerDocument.body.offsetTop;}}
parentNode=el.parentNode;while(parentNode.tagName&&!patterns.ROOT_TAG.test(parentNode.tagName))
{if(Y.Dom.getStyle(parentNode,'display').search(/^inline|table-row.*$/i)){pos[0]-=parentNode.scrollLeft;pos[1]-=parentNode.scrollTop;}
parentNode=parentNode.parentNode;}
return pos;};return Y.Dom.batch(el,f,Y.Dom,true);},getX:function(el){var f=function(el){return Y.Dom.getXY(el)[0];};return Y.Dom.batch(el,f,Y.Dom,true);},getY:function(el){var f=function(el){return Y.Dom.getXY(el)[1];};return Y.Dom.batch(el,f,Y.Dom,true);},setXY:function(el,pos,noRetry){var f=function(el){var style_pos=this.getStyle(el,'position');if(style_pos=='static'){this.setStyle(el,'position','relative');style_pos='relative';}
var pageXY=this.getXY(el);if(pageXY===false){return false;}
var delta=[parseInt(this.getStyle(el,'left'),10),parseInt(this.getStyle(el,'top'),10)];if(isNaN(delta[0])){delta[0]=(style_pos=='relative')?0:el.offsetLeft;}
if(isNaN(delta[1])){delta[1]=(style_pos=='relative')?0:el.offsetTop;}
if(pos[0]!==null){el.style.left=pos[0]-pageXY[0]+delta[0]+'px';}
if(pos[1]!==null){el.style.top=pos[1]-pageXY[1]+delta[1]+'px';}
if(!noRetry){var newXY=this.getXY(el);if((pos[0]!==null&&newXY[0]!=pos[0])||(pos[1]!==null&&newXY[1]!=pos[1])){this.setXY(el,pos,true);}}};Y.Dom.batch(el,f,Y.Dom,true);},setX:function(el,x){Y.Dom.setXY(el,[x,null]);},setY:function(el,y){Y.Dom.setXY(el,[null,y]);},getRegion:function(el){var f=function(el){if((el.parentNode===null||el.offsetParent===null||this.getStyle(el,'display')=='none')&&el!=document.body){return false;}
var region=Y.Region.getRegion(el);return region;};return Y.Dom.batch(el,f,Y.Dom,true);},getClientWidth:function(){return Y.Dom.getViewportWidth();},getClientHeight:function(){return Y.Dom.getViewportHeight();},getElementsByClassName:function(className,tag,root,apply){tag=tag||'*';root=(root)?Y.Dom.get(root):null||document;if(!root){return[];}
var nodes=[],elements=root.getElementsByTagName(tag),re=getClassRegEx(className);for(var i=0,len=elements.length;i<len;++i){if(re.test(elements[i].className)){nodes[nodes.length]=elements[i];if(apply){apply.call(elements[i],elements[i]);}}}
return nodes;},hasClass:function(el,className){var re=getClassRegEx(className);var f=function(el){return re.test(el.className);};return Y.Dom.batch(el,f,Y.Dom,true);},addClass:function(el,className){var f=function(el){if(this.hasClass(el,className)){return false;}
el.className=YAHOO.lang.trim([el.className,className].join(' '));return true;};return Y.Dom.batch(el,f,Y.Dom,true);},removeClass:function(el,className){var re=getClassRegEx(className);var f=function(el){if(!this.hasClass(el,className)){return false;}
var c=el.className;el.className=c.replace(re,' ');if(this.hasClass(el,className)){this.removeClass(el,className);}
el.className=YAHOO.lang.trim(el.className);return true;};return Y.Dom.batch(el,f,Y.Dom,true);},replaceClass:function(el,oldClassName,newClassName){if(!newClassName||oldClassName===newClassName){return false;}
var re=getClassRegEx(oldClassName);var f=function(el){if(!this.hasClass(el,oldClassName)){this.addClass(el,newClassName);return true;}
el.className=el.className.replace(re,' '+newClassName+' ');if(this.hasClass(el,oldClassName)){this.replaceClass(el,oldClassName,newClassName);}
el.className=YAHOO.lang.trim(el.className);return true;};return Y.Dom.batch(el,f,Y.Dom,true);},generateId:function(el,prefix){prefix=prefix||'yui-gen';var f=function(el){if(el&&el.id){return el.id;}
var id=prefix+id_counter++;if(el){el.id=id;}
return id;};return Y.Dom.batch(el,f,Y.Dom,true)||f.apply(Y.Dom,arguments);},isAncestor:function(haystack,needle){haystack=Y.Dom.get(haystack);if(!haystack||!needle){return false;}
var f=function(node){if(haystack.contains&&node.nodeType&&!isSafari){return haystack.contains(node);}
else if(haystack.compareDocumentPosition&&node.nodeType){return!!(haystack.compareDocumentPosition(node)&16);}else if(node.nodeType){return!!this.getAncestorBy(node,function(el){return el==haystack;});}
return false;};return Y.Dom.batch(needle,f,Y.Dom,true);},inDocument:function(el){var f=function(el){if(isSafari){while(el=el.parentNode){if(el==document.documentElement){return true;}}
return false;}
return this.isAncestor(document.documentElement,el);};return Y.Dom.batch(el,f,Y.Dom,true);},getElementsBy:function(method,tag,root,apply){tag=tag||'*';root=(root)?Y.Dom.get(root):null||document;if(!root){return[];}
var nodes=[],elements=root.getElementsByTagName(tag);for(var i=0,len=elements.length;i<len;++i){if(method(elements[i])){nodes[nodes.length]=elements[i];if(apply){apply(elements[i]);}}}
return nodes;},batch:function(el,method,o,override){el=(el&&el.tagName)?el:Y.Dom.get(el);if(!el||!method){return false;}
var scope=(override)?o:window;if(el.tagName||(!el.item&&!el.slice)){return method.call(scope,el,o);}
var collection=[];for(var i=0,len=el.length;i<len;++i){collection[collection.length]=method.call(scope,el[i],o);}
return collection;},getDocumentHeight:function(){var scrollHeight=(document.compatMode!='CSS1Compat')?document.body.scrollHeight:document.documentElement.scrollHeight;var h=Math.max(scrollHeight,Y.Dom.getViewportHeight());return h;},getDocumentWidth:function(){var scrollWidth=(document.compatMode!='CSS1Compat')?document.body.scrollWidth:document.documentElement.scrollWidth;var w=Math.max(scrollWidth,Y.Dom.getViewportWidth());return w;},getViewportHeight:function(){var height=self.innerHeight;var mode=document.compatMode;if((mode||isIE)&&!isOpera){height=(mode=='CSS1Compat')?document.documentElement.clientHeight:document.body.clientHeight;}
return height;},getViewportWidth:function(){var width=self.innerWidth;var mode=document.compatMode;if(mode||isIE){width=(mode=='CSS1Compat')?document.documentElement.clientWidth:document.body.clientWidth;}
return width;},getAncestorBy:function(node,method){while(node=node.parentNode){if(testElement(node,method)){return node;}}
return null;},getAncestorByClassName:function(node,className){node=Y.Dom.get(node);if(!node){return null;}
var method=function(el){return Y.Dom.hasClass(el,className);};return Y.Dom.getAncestorBy(node,method);},getAncestorByTagName:function(node,tagName){node=Y.Dom.get(node);if(!node){return null;}
var method=function(el){return el.tagName&&el.tagName.toUpperCase()==tagName.toUpperCase();};return Y.Dom.getAncestorBy(node,method);},getPreviousSiblingBy:function(node,method){while(node){node=node.previousSibling;if(testElement(node,method)){return node;}}
return null;},getPreviousSibling:function(node){node=Y.Dom.get(node);if(!node){return null;}
return Y.Dom.getPreviousSiblingBy(node);},getNextSiblingBy:function(node,method){while(node){node=node.nextSibling;if(testElement(node,method)){return node;}}
return null;},getNextSibling:function(node){node=Y.Dom.get(node);if(!node){return null;}
return Y.Dom.getNextSiblingBy(node);},getFirstChildBy:function(node,method){var child=(testElement(node.firstChild,method))?node.firstChild:null;return child||Y.Dom.getNextSiblingBy(node.firstChild,method);},getFirstChild:function(node,method){node=Y.Dom.get(node);if(!node){return null;}
return Y.Dom.getFirstChildBy(node);},getLastChildBy:function(node,method){if(!node){return null;}
var child=(testElement(node.lastChild,method))?node.lastChild:null;return child||Y.Dom.getPreviousSiblingBy(node.lastChild,method);},getLastChild:function(node){node=Y.Dom.get(node);return Y.Dom.getLastChildBy(node);},getChildrenBy:function(node,method){var child=Y.Dom.getFirstChildBy(node,method);var children=child?[child]:[];Y.Dom.getNextSiblingBy(child,function(node){if(!method||method(node)){children[children.length]=node;}
return false;});return children;},getChildren:function(node){node=Y.Dom.get(node);if(!node){}
return Y.Dom.getChildrenBy(node);},getDocumentScrollLeft:function(doc){doc=doc||document;return Math.max(doc.documentElement.scrollLeft,doc.body.scrollLeft);},getDocumentScrollTop:function(doc){doc=doc||document;return Math.max(doc.documentElement.scrollTop,doc.body.scrollTop);},insertBefore:function(newNode,referenceNode){newNode=Y.Dom.get(newNode);referenceNode=Y.Dom.get(referenceNode);if(!newNode||!referenceNode||!referenceNode.parentNode){return null;}
return referenceNode.parentNode.insertBefore(newNode,referenceNode);},insertAfter:function(newNode,referenceNode){newNode=Y.Dom.get(newNode);referenceNode=Y.Dom.get(referenceNode);if(!newNode||!referenceNode||!referenceNode.parentNode){return null;}
if(referenceNode.nextSibling){return referenceNode.parentNode.insertBefore(newNode,referenceNode.nextSibling);}else{return referenceNode.parentNode.appendChild(newNode);}}};})();YAHOO.util.Region=function(t,r,b,l){this.top=t;this[1]=t;this.right=r;this.bottom=b;this.left=l;this[0]=l;};YAHOO.util.Region.prototype.contains=function(region){return(region.left>=this.left&&region.right<=this.right&&region.top>=this.top&&region.bottom<=this.bottom);};YAHOO.util.Region.prototype.getArea=function(){return((this.bottom-this.top)*(this.right-this.left));};YAHOO.util.Region.prototype.intersect=function(region){var t=Math.max(this.top,region.top);var r=Math.min(this.right,region.right);var b=Math.min(this.bottom,region.bottom);var l=Math.max(this.left,region.left);if(b>=t&&r>=l){return new YAHOO.util.Region(t,r,b,l);}else{return null;}};YAHOO.util.Region.prototype.union=function(region){var t=Math.min(this.top,region.top);var r=Math.max(this.right,region.right);var b=Math.max(this.bottom,region.bottom);var l=Math.min(this.left,region.left);return new YAHOO.util.Region(t,r,b,l);};YAHOO.util.Region.prototype.toString=function(){return("Region {"+"top: "+this.top+", right: "+this.right+", bottom: "+this.bottom+", left: "+this.left+"}");};YAHOO.util.Region.getRegion=function(el){var p=YAHOO.util.Dom.getXY(el);var t=p[1];var r=p[0]+el.offsetWidth;var b=p[1]+el.offsetHeight;var l=p[0];return new YAHOO.util.Region(t,r,b,l);};YAHOO.util.Point=function(x,y){if(YAHOO.lang.isArray(x)){y=x[1];x=x[0];}
this.x=this.right=this.left=this[0]=x;this.y=this.top=this.bottom=this[1]=y;};YAHOO.util.Point.prototype=new YAHOO.util.Region();YAHOO.register("dom",YAHOO.util.Dom,{version:"2.3.0",build:"442"});