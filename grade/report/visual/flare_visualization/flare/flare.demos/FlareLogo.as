package
{
	import flash.display.Sprite;
	import flare.animate.Transitioner;
	import flash.display.DisplayObject;
	import flare.animate.Sequence;
	import flare.animate.Tween;
	import flare.animate.Easing;
	import flash.filters.GlowFilter;
	import flare.animate.Pause;
	import flare.animate.Transition;
	import flare.animate.Parallel;

	public class FlareLogo extends Sprite
	{
		private var points:Array = [
		/*f*/ 0,2, 1,1, 1,2, 1,3, 1,4, 1,5, 1,6, 1,7, 2,0, 2,2, 3,0, 3,2, 
		/*l*/ 5,0, 5,7, 6,0, 6,1, 6,2, 6,3, 6,4, 6,5, 6,6, 6,7, 7,7, 
		/*a*/ 9,5, 9,6, 10,2, 10,4, 10,7, 11,2, 11,4, 11,7, 12,2, 12,4, 12,6, 13,3, 13,4, 13,5, 13,6, 13,7, 
		/*r*/ 15,2, 15,3, 15,4, 15,5, 15,6, 15,7, 16,3, 17,2, 18,2, 19,3, 
		/*e*/ 21,3, 21,4, 21,5, 21,6, 22,2, 22,4, 22,7, 23,2, 23,4, 23,7, 24,2, 24,4, 24,7, 25,3, 25,4, 25,7
		];
		
		private var w:Number = 10;
		private var h:Number = 10;
		private var xb:Number = -25/2;
		private var yb:Number = -7/2;
		
		private var _dance:Transition;
		private var _glow:Transition;
		
		public function FlareLogo()
		{
			var inc:Number = 2, linc:Number = 4;
			var con:Sprite = new Sprite();
			for (var i:int=0; i<points.length; i+=2) {
				var s:Sprite = new Sprite();
				s.graphics.beginFill(0, 1);
				s.graphics.drawRect(-(w+inc)/2,-(h+inc)/2,w+inc,h+inc);
				s.graphics.endFill();
				con.addChild(s);
			}
			addChild(con);
			
			layoutT(con, Transitioner.DEFAULT, 0);
			var seq:Sequence = new Sequence(
				new Pause(6),
				layoutT(con, new Transitioner(3), linc),
				layoutY(con, new Transitioner(2,Easing.easeOutBounce), linc),
				new Pause(1),
				layoutC(con, new Transitioner(3,Easing.easeInPoly(3)), linc),
				new Tween(con, 6, {rotation:180}, false, Easing.easeInPoly(3)),
				new Parallel(
					new Tween(con, 6, {rotation:360}, false, Easing.easeOutPoly(3)),
					layoutX(con, new Transitioner(6), linc)
				),
				new Tween(con, 0, {rotation:0}),
				layoutT(con, new Transitioner(3), linc),
				layoutT(con, new Transitioner(3), 0),
				new Pause(21)
			);
			seq.onEnd = function():void { seq.play(); }
			_dance = seq;
			
			// do the glow...
			con.filters = [new GlowFilter(0xff0000, 0.5, 0, 0)];
			var g1:Tween = new Tween(con,3,{"filters[0].blurX":15,"filters[0].blurY":15});
			var g2:Tween = new Tween(con,3,{"filters[0].blurX":0,"filters[0].blurY":0});
			g1.easing = g2.easing = Easing.none;
			
			var glow:Sequence = new Sequence(g1,g2);
			glow.easing = Easing.easeInOutPoly(2);
			glow.onEnd = function():void { glow.play(); }
			_glow = glow;
		}
		
		public function play():void {
			_dance.play();
			_glow.play();
		}
		
		public function pause():void {
			_dance.pause();
			_glow.pause();
		}
		
		private function layoutT(s:Sprite, t:Transitioner, a:Number):Transitioner
		{
			for (var i:int=0; i<s.numChildren; ++i) {
				var b:DisplayObject = s.getChildAt(i);
				t.$(b).x = (w+a) * (xb + points[2*i]);
				t.$(b).y = (h+a) * (yb + points[2*i+1]);
			}
			return t;
		}
		
		private function layoutX(s:Sprite, t:Transitioner, a:Number):Transitioner
		{
			var xc:Object = {}; for (var i:int=0; i<8; ++i) xc[i] = 0;
			
			for (i=0; i<s.numChildren; ++i) {
				var b:DisplayObject = s.getChildAt(i);
				t.$(b).x = 100 + (w+a) * (xb + xc[points[2*i+1]]);
				t.$(b).y = (h+a) * (yb + points[2*i+1]);
				xc[points[2*i+1]]++;
			}
			return t;
		}
		
		private function layoutY(s:Sprite, t:Transitioner, a:Number):Transitioner
		{
			var yc:Object = {}; for (var i:int=0; i<26; ++i) yc[i] = 7;
			
			for (i=s.numChildren; --i>=0;) {
				var b:DisplayObject = s.getChildAt(i);
				t.$(b).x = (w+a) * (xb + points[2*i]);
				t.$(b).y = (h+a) * (yb + yc[points[2*i]]);
				yc[points[2*i]]--;
			}
			return t;
		}
		
		private function layoutC(s:Sprite, t:Transitioner, a:Number):Transitioner
		{
			var sort:Array = new Array(s.numChildren);
			for (var i:int=0; i<s.numChildren; ++i) {
				var b:DisplayObject = s.getChildAt(i);
				sort[i] = Math.atan2(b.y, b.x);
			}
			sort = sort.sort(Array.NUMERIC | Array.RETURNINDEXEDARRAY);
			
			for (i=0; i<sort.length; ++i) {
				var f:Number = 1.0 - i / s.numChildren;
				b = s.getChildAt(sort[i]);
				t.$(b).x = -200 * Math.cos(2*Math.PI*f);
				t.$(b).y =  200 * Math.sin(2*Math.PI*f);
			}
			return t;
		}
	}
}