/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


(function(){var NULL=null,UNDEFINED=undefined,TRUE=true,FALSE=false,SETX="setX",SETY="setY",SETXY="setXY",LEFT="left",BOTTOM="bottom",TOP="top",RIGHT="right",HEIGHT="height",WIDTH="width",POINTS="points",HIDDEN="hidden",ABSOLUTE="absolute",VISIBLE="visible",MOTION="motion",POSITION="position",EASEOUT="easeOut";Ext.enableFx=TRUE;Ext.Fx={switchStatements:function(key,fn,argHash){return fn.apply(this,argHash[key]);},slideIn:function(anchor,o){var me=this,el=me.getFxEl(),r,b,wrap,after,st,args,pt,bw,bh,xy=me.getXY(),dom=me.dom;o=o||{};anchor=anchor||"t";el.queueFx(o,function(){st=me.dom.style;me.fixDisplay();r=me.getFxRestore();b={x:xy[0],y:xy[1],0:xy[0],1:xy[1],width:dom.offsetWidth,height:dom.offsetHeight};b.right=b.x+b.width;b.bottom=b.y+b.height;me.setWidth(b.width).setHeight(b.height);wrap=me.fxWrap(r.pos,o,HIDDEN);st.visibility=VISIBLE;st.position=ABSOLUTE;function after(){el.fxUnwrap(wrap,r.pos,o);st.width=r.width;st.height=r.height;el.afterFx(o);}
pt={to:[b.x,b.y]};bw={to:b.width};bh={to:b.height};function argCalc(wrap,style,ww,wh,sXY,sXYval,s1,s2,w,h,p){var ret={};wrap.setWidth(ww).setHeight(wh);if(wrap[sXY])wrap[sXY](sXYval);style[s1]=style[s2]="0";if(w)ret.width=w;if(h)ret.height=h;if(p)ret.points=p;return ret;};args=me.switchStatements(anchor.toLowerCase(),argCalc,{t:[wrap,st,b.width,0,NULL,NULL,LEFT,BOTTOM,NULL,bh,NULL],l:[wrap,st,0,b.height,NULL,NULL,RIGHT,TOP,bw,NULL,NULL],r:[wrap,st,b.width,b.height,SETX,b.right,LEFT,TOP,NULL,NULL,pt],b:[wrap,st,b.width,b.height,SETY,b.bottom,LEFT,TOP,NULL,bh,pt],tl:[wrap,st,0,0,NULL,NULL,RIGHT,BOTTOM,bw,bh,pt],bl:[wrap,st,0,0,SETY,b.y+b.height,RIGHT,TOP,bw,bh,pt],br:[wrap,st,0,0,SETXY,[b.right,b.bottom],LEFT,TOP,bw,bh,pt],tr:[wrap,st,0,0,SETX,b.x+b.width,LEFT,BOTTOM,bw,bh,pt]});st.visibility=VISIBLE;wrap.show();arguments.callee.anim=wrap.fxanim(args,o,MOTION,.5,EASEOUT,after);});return me;},slideOut:function(anchor,o){var me=this,el=me.getFxEl(),xy=me.getXY(),dom=me.dom,wrap,st,r,b,a,zero={to:0};o=o||{};anchor=anchor||"t";el.queueFx(o,function(){r=me.getFxRestore();b={x:xy[0],y:xy[1],0:xy[0],1:xy[1],width:dom.offsetWidth,height:dom.offsetHeight};b.right=b.x+b.width;b.bottom=b.y+b.height;me.setWidth(b.width).setHeight(b.height);wrap=me.fxWrap(r.pos,o,VISIBLE);st=me.dom.style;st.visibility=VISIBLE;st.position=ABSOLUTE;wrap.setWidth(b.width).setHeight(b.height);function after(){o.useDisplay?el.setDisplayed(FALSE):el.hide();el.fxUnwrap(wrap,r.pos,o);st.width=r.width;st.height=r.height;el.afterFx(o);}
function argCalc(style,s1,s2,p1,v1,p2,v2,p3,v3){var ret={};style[s1]=style[s2]="0";ret[p1]=v1;if(p2)ret[p2]=v2;if(p3)ret[p3]=v3;return ret;};a=me.switchStatements(anchor.toLowerCase(),argCalc,{t:[st,LEFT,BOTTOM,HEIGHT,zero],l:[st,RIGHT,TOP,WIDTH,zero],r:[st,LEFT,TOP,WIDTH,zero,POINTS,{to:[b.right,b.y]}],b:[st,LEFT,TOP,HEIGHT,zero,POINTS,{to:[b.x,b.bottom]}],tl:[st,RIGHT,BOTTOM,WIDTH,zero,HEIGHT,zero],bl:[st,RIGHT,TOP,WIDTH,zero,HEIGHT,zero,POINTS,{to:[b.x,b.bottom]}],br:[st,LEFT,TOP,WIDTH,zero,HEIGHT,zero,POINTS,{to:[b.x+b.width,b.bottom]}],tr:[st,LEFT,BOTTOM,WIDTH,zero,HEIGHT,zero,POINTS,{to:[b.right,b.y]}]});arguments.callee.anim=wrap.fxanim(a,o,MOTION,.5,EASEOUT,after);});return me;},puff:function(o){o=o||{};var me=this,el=me.getFxEl(),r,st=me.dom.style,width=me.getWidth(),height=me.getHeight();el.queueFx(o,function(){me.clearOpacity();me.show();r=me.getFxRestore();function after(){o.useDisplay?el.setDisplayed(FALSE):el.hide();el.clearOpacity();el.setPositioning(r.pos);st.width=r.width;st.height=r.height;st.fontSize='';el.afterFx(o);}
arguments.callee.anim=me.fxanim({width:{to:me.adjustWidth(width*2)},height:{to:me.adjustHeight(height*2)},points:{by:[-width*.5,-height*.5]},opacity:{to:0},fontSize:{to:200,unit:"%"}},o,MOTION,.5,EASEOUT,after);});return me;},switchOff:function(o){o=o||{};var me=this,el=me.getFxEl();el.queueFx(o,function(){me.clearOpacity();me.clip();var r=me.getFxRestore(),st=me.dom.style,after=function(){o.useDisplay?el.setDisplayed(FALSE):el.hide();el.clearOpacity();el.setPositioning(r.pos);st.width=r.width;st.height=r.height;el.afterFx(o);};me.fxanim({opacity:{to:0.3}},NULL,NULL,.1,NULL,function(){me.clearOpacity();(function(){me.fxanim({height:{to:1},points:{by:[0,me.getHeight()*.5]}},o,MOTION,0.3,'easeIn',after);}).defer(100);});});return me;},highlight:function(color,o){o=o||{};var me=this,el=me.getFxEl(),attr=o.attr||"backgroundColor",a={};el.queueFx(o,function(){me.clearOpacity();me.show();function after(){el.dom.style[attr]=me.dom.style[attr];el.afterFx(o);}
a[attr]={from:color||"ffff9c",to:o.endColor||me.getColor(attr)||"ffffff"};arguments.callee.anim=me.fxanim(a,o,'color',1,'easeIn',after);});return me;},frame:function(color,count,o){var me=this,el=me.getFxEl(),proxy,active;o=o||{};el.queueFx(o,function(){color=color||"#C3DAF9"
if(color.length==6){color="#"+color;}
count=count||1;me.show();var xy=me.getXY(),dom=me.dom,b={x:xy[0],y:xy[1],0:xy[0],1:xy[1],width:dom.offsetWidth,height:dom.offsetHeight},proxy,queue=function(){proxy=Ext.get(document.body||document.documentElement).createChild({style:{visbility:HIDDEN,position:ABSOLUTE,"z-index":35000,border:"0px solid "+color}});return proxy.queueFx({},animFn);};arguments.callee.anim={isAnimated:true,stop:function(){count=0;proxy.stopFx();}};function animFn(){var scale=Ext.isBorderBox?2:1;active=proxy.anim({top:{from:b.y,to:b.y-20},left:{from:b.x,to:b.x-20},borderWidth:{from:0,to:10},opacity:{from:1,to:0},height:{from:b.height,to:b.height+20*scale},width:{from:b.width,to:b.width+20*scale}},{duration:o.duration||1,callback:function(){proxy.remove();--count>0?queue():el.afterFx(o);}});arguments.callee.anim={isAnimated:true,stop:function(){active.stop();}};};queue();});return me;},pause:function(seconds){var el=this.getFxEl(),t;el.queueFx({},function(){t=setTimeout(function(){el.afterFx({});},seconds*1000);arguments.callee.anim={isAnimated:true,stop:function(){clearTimeout(t);el.afterFx({});}};});return this;},fadeIn:function(o){var me=this,el=me.getFxEl();o=o||{};el.queueFx(o,function(){me.setOpacity(0);me.fixDisplay();me.dom.style.visibility=VISIBLE;var to=o.endOpacity||1;arguments.callee.anim=me.fxanim({opacity:{to:to}},o,NULL,.5,EASEOUT,function(){if(to==1){this.clearOpacity();}
el.afterFx(o);});});return me;},fadeOut:function(o){o=o||{};var me=this,style=me.dom.style,el=me.getFxEl(),to=o.endOpacity||0;el.queueFx(o,function(){arguments.callee.anim=me.fxanim({opacity:{to:to}},o,NULL,.5,EASEOUT,function(){if(to==0){me.visibilityMode==Ext.Element.DISPLAY||o.useDisplay?style.display="none":style.visibility=HIDDEN;me.clearOpacity();}
el.afterFx(o);});});return me;},scale:function(w,h,o){var me=this;me.shift(Ext.apply({},o,{width:w,height:h}));return me;},shift:function(o){var me=this;o=o||{};var el=me.getFxEl();el.queueFx(o,function(){var a={};for(var prop in o){if(o[prop]!=UNDEFINED){a[prop]={to:o[prop]};}}
a.width?a.width.to=me.adjustWidth(o.width):a;a.height?a.height.to=me.adjustWidth(o.height):a;if(a.x||a.y||a.xy){a.points=a.xy||{to:[a.x?a.x.to:me.getX(),a.y?a.y.to:me.getY()]};}
arguments.callee.anim=me.fxanim(a,o,MOTION,.35,EASEOUT,function(){el.afterFx(o);});});return me;},ghost:function(anchor,o){var me=this,el=me.getFxEl();o=o||{};anchor=anchor||"b";el.queueFx(o,function(){var r=me.getFxRestore(),w=me.getWidth(),h=me.getHeight(),st=me.dom.style,after=function(){if(o.useDisplay){el.setDisplayed(FALSE);}else{el.hide();}
el.clearOpacity();el.setPositioning(r.pos);st.width=r.width;st.width=r.width;el.afterFx(o);},a={opacity:{to:0},points:{}},pt=a.points;pt.by=me.switchStatements(anchor.toLowerCase(),function(v1,v2){return[v1,v2];},{t:[0,-h],l:[-w,0],r:[w,0],b:[0,h],tl:[-w,-h],bl:[-w,h],br:[w,h],tr:[w,-h]});arguments.callee.anim=me.fxanim(a,o,MOTION,.5,EASEOUT,after);});return me;},syncFx:function(){var me=this;me.fxDefaults=Ext.apply(me.fxDefaults||{},{block:FALSE,concurrent:TRUE,stopFx:FALSE});return me;},sequenceFx:function(){var me=this;me.fxDefaults=Ext.apply(me.fxDefaults||{},{block:FALSE,concurrent:FALSE,stopFx:FALSE});return me;},nextFx:function(){var ef=this.fxQueue[0];if(ef){ef.call(this);}},hasActiveFx:function(){return this.fxQueue&&this.fxQueue[0];},stopFx:function(finish){var me=this;if(me.hasActiveFx()){var cur=me.fxQueue[0];if(cur&&cur.anim){if(cur.anim.isAnimated){me.fxQueue=[cur];cur.anim.stop(finish!==undefined?finish:TRUE);}else{me.fxQueue=[];}}}
return me;},beforeFx:function(o){if(this.hasActiveFx()&&!o.concurrent){if(o.stopFx){this.stopFx();return TRUE;}
return FALSE;}
return TRUE;},hasFxBlock:function(){var q=this.fxQueue;return q&&q[0]&&q[0].block;},queueFx:function(o,fn){var me=this;if(!me.fxQueue){me.fxQueue=[];}
if(!me.hasFxBlock()){Ext.applyIf(o,me.fxDefaults);if(!o.concurrent){var run=me.beforeFx(o);fn.block=o.block;me.fxQueue.push(fn);if(run){me.nextFx();}}else{fn.call(me);}}
return me;},fxWrap:function(pos,o,vis){var me=this,wrap,wrapXY;if(!o.wrap||!(wrap=Ext.get(o.wrap))){if(o.fixPosition){wrapXY=me.getXY();}
var div=document.createElement("div");div.style.visibility=vis;wrap=Ext.get(me.dom.parentNode.insertBefore(div,me.dom));wrap.setPositioning(pos);if(wrap.isStyle(POSITION,"static")){wrap.position("relative");}
me.clearPositioning('auto');wrap.clip();wrap.dom.appendChild(me.dom);if(wrapXY){wrap.setXY(wrapXY);}}
return wrap;},fxUnwrap:function(wrap,pos,o){var me=this;me.clearPositioning();me.setPositioning(pos);if(!o.wrap){wrap.dom.parentNode.insertBefore(me.dom,wrap.dom);wrap.remove();}},getFxRestore:function(){var st=this.dom.style;return{pos:this.getPositioning(),width:st.width,height:st.height};},afterFx:function(o){var me=this;if(o.afterStyle){me.setStyle(o.afterStyle);}
if(o.afterCls){me.addClass(o.afterCls);}
if(o.remove==TRUE){me.remove();}
if(o.callback)o.callback.call(o.scope,me);if(!o.concurrent){me.fxQueue.shift();me.nextFx();}},getFxEl:function(){return Ext.get(this.dom);},fxanim:function(args,opt,animType,defaultDur,defaultEase,cb){animType=animType||'run';opt=opt||{};var anim=Ext.lib.Anim[animType](this.dom,args,(opt.duration||defaultDur)||.35,(opt.easing||defaultEase)||EASEOUT,cb,this);opt.anim=anim;return anim;}};Ext.Fx.resize=Ext.Fx.scale;Ext.Element.addMethods(Ext.Fx);})();