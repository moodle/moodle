 package flare.demos
{
	import flare.animate.Transitioner;
	import flash.utils.Timer;
	import flash.events.TimerEvent;
	import flash.events.Event;
	import flare.vis.data.DataSprite;
	import flash.filters.GlowFilter;
	import flash.filters.DropShadowFilter;
	import flash.filters.BlurFilter;
	import flash.geom.Rectangle;
	import flare.vis.data.render.ShapeRenderer;
	
	public class Animation extends Demo
	{
		private var trans:Transitioner;
		private var timer:Timer;
		private var rev:Boolean = false;
		
		public function Animation() {
			name = "Animation";
			var N:uint = 9;
			
			var sr:ShapeRenderer = new ShapeRenderer();
			sr.defaultSize = 15;
			
			for (var i:int=-N/2; i<N/2; ++i) {
				var vi:DataSprite = new DataSprite();
				vi.renderer = sr;
				vi.x = 40*i;
				vi.fillColor = 0x8888ff;
				vi.fillAlpha = 0.8;
				vi.render();
				vi.mouseEnabled = false;
				addChild(vi);
			}

			getChildAt(0).filters = [new GlowFilter(0xff0000,1,10,10,2,5,false,false)];
			getChildAt(1).filters = [new DropShadowFilter()];
			getChildAt(2).filters = [new BlurFilter()];
			getChildAt(6).filters = [new BlurFilter()];
			getChildAt(7).filters = [new DropShadowFilter()];
			getChildAt(8).filters = [new GlowFilter(0xff0000,1,10,10,2,5,false,false)];
			
			x = WIDTH/2;
			y = HEIGHT/2;
				
			trans = new Transitioner(2.5);
			trans.delay = 0.5;
			var o:Object;
			with (trans) {
				// the $() function returns an object for setting target values
				o = trans.$(getChildAt(5)); o.x = 0; o.y = 200;  o.alpha = 0;
				o = trans.$(getChildAt(3)); o.x = 0; o.y = -200; o.alpha = 0;
				o = trans.$(getChildAt(7)); o.x = 0; o.y = -200; o.alpha = 0;
				o = trans.$(getChildAt(1)); o.x = 0; o.y = 200;  o.alpha = 0;
				o = trans.$(getChildAt(8)); o.x = 0; o.y = 200;  o.alpha = 0;
				o = trans.$(getChildAt(0)); o.x = 0; o.y = -200; o.alpha = 0;

				$(getChildAt(2)).fillColor = 0xffCC3355;
				$(getChildAt(6)).fillColor = 0xffCC3355;
				$(getChildAt(5)).scaleX = 20;
				$(getChildAt(5)).scaleY = 20;
				$(getChildAt(3)).scaleX = 20;
				$(getChildAt(3)).scaleY = 20;
				
				// how are they related?  _(obj).values == $(obj)
				// (unless the transitioner is in immediate mode. see docs for more!)
			}
		}
		
		public override function play():void
		{
			var rev:Boolean = false;
			trans.onEnd = function():void {
				trans.play(rev = !rev);
			}
			trans.play();
			this.addEventListener(Event.ENTER_FRAME, onRotate);
		}
		
		public override function stop():void
		{
			trans.onEnd = null;
			trans.stop();
			this.removeEventListener(Event.ENTER_FRAME, onRotate);
		}

		private function onRotate(event:Event) : void {
			this.rotation += 1;			
		}		
	}
}