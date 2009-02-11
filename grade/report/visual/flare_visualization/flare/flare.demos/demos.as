package {
	import flare.animate.Transitioner;
	import flare.demos.Animation;
	import flare.demos.Bars;
	import flare.demos.Chart;
	import flare.demos.Distortion;
	import flare.demos.GraphView;
	import flare.demos.Pie;
	import flare.demos.Smoke;
	import flare.demos.Stacks;
	import flare.demos.Timeline;
	import flare.demos.TreeMap;
	import flare.util.Button;
	
	import flash.display.DisplayObject;
	import flash.display.Sprite;
	import flash.events.MouseEvent;

	[SWF(width="800", height="550", backgroundColor="#ffffff", frameRate="30")]
	public class demos extends Sprite
	{
		private var _demos:Array;
		private var _cancel:Button;
		private var _buttons:Sprite;
		private var _demo:Sprite;
		private var _logo:FlareLogo;
		private var _cur:uint;
		
		public function demos()
		{
			// create logo
			_logo = new FlareLogo();
			_logo.x = stage.stageWidth / 2;
			_logo.y = stage.stageHeight/2 - _logo.height / 2;
			_logo.play();
			addChild(_logo);
				
			// create demos
			_demo = new Sprite();
			addChild(_demo);
			_buttons = createDemos();
			_buttons.x = (stage.stageWidth - _buttons.width) / 2;
			_buttons.y = stage.stageHeight - _buttons.height*3;
			addChild(_buttons);
			
			// create cancel button
			_cancel = new Button("Back");
			_cancel.visible = false;
			_cancel.x = stage.stageWidth - 10 - _cancel.width;
			_cancel.y = stage.stageHeight - 10 - _cancel.height;
			_cancel.addEventListener(MouseEvent.CLICK, cancel);
			addChild(_cancel);
		}
		
		private function createDemos():Sprite
		{
			_demos = new Array();
			_demos.push(new Animation());
			_demos.push(new Smoke());
			_demos.push(new Distortion());
			_demos.push(new GraphView());
			_demos.push(new TreeMap());
			_demos.push(new Stacks());
			_demos.push(new Timeline());
			_demos.push(new Chart());
			_demos.push(new Bars());
			_demos.push(new Pie());
			
			var s:Sprite = new Sprite();
			var w:Number = 0;
			
			for (var i:uint=0; i<_demos.length; ++i) {
				var b:Button = new Button(_demos[i].name);
				b.addEventListener(MouseEvent.CLICK, showDemo);
				b.x = w;
				w += b.width + 4;
				s.addChild(b);
			}
			return s;
		}
		
		private function showDemo(event:MouseEvent):void
		{
			var tgt:DisplayObject = event.target as DisplayObject;
			_cur = _buttons.getChildIndex(tgt);
			_demo.alpha = 0;
			_demo.addChild(_demos[_cur] as Sprite);
			_cancel.alpha = 0;
			_cancel.visible = true;
			
			var t:Transitioner = new Transitioner(1);
			t.$(_buttons).alpha = 0;
			t.$(_logo).alpha = 0;
			t.onEnd = function():void {
				_buttons.visible = false;
			};
			t.play();
						
			t = new Transitioner(1);
			t.delay = 0.5;
			t.onStart = function():void {
				_demos[_cur].play();
			}
			t.$(_cancel).alpha = 1;
			t.$(_demo).alpha = 1;
			t.play();
			_logo.pause();
		}
		
		private function cancel(event:MouseEvent):void
		{
			_buttons.visible = true;
			
			var t:Transitioner = new Transitioner(1);
			t.$(_demo).alpha = 0;
			t.$(_cancel).alpha = 0;
			t.onEnd = function():void {
				_demos[_cur].stop();
				_demo.removeChild(_demos[_cur] as Sprite);
				_cur = 0;
				_cancel.visible = false;
			}
			t.play();
			
			t = new Transitioner(1);
			t.delay = 0.5;
			t.$(_buttons).alpha = 1;
			t.$(_logo).alpha = 1;
			t.play();
			_logo.play();
		}
		
	}
}