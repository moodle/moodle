package
{
	import flare.vis.controls.HoverControl;
	
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;

	public class Selector extends Sprite
	{
		public var param:String;
		public var options:XMLList;
		public var active:SelectorOption = null;
		
		public function Selector(param:String, options:XMLList, func:Function, active:String = null, borderWidth:Number = -1)
		{
			this.param = param;
			this.options = options;
		
			for each(var option:XML in options) {
				var selectorOption:SelectorOption;
				
				if(active != null && active != "" && option.value == active) {
					selectorOption = new SelectorOption(param, option, true);
					this.active = selectorOption;
					selectorOption.alpha = 1;
				} else {
					selectorOption = new SelectorOption(param, option, false);
					selectorOption.alpha = 0.4;
				}
				
				var hc:HoverControl = new HoverControl(selectorOption);
				selectorOption.x = 10;
				selectorOption.y = this.height;
				selectorOption.addEventListener(MouseEvent.CLICK, func);
				hc.onRollOver = rollOver;
				hc.onRollOut = rollOut;
				addChild(selectorOption);
			}
			
			this.graphics.lineStyle(1, 0x303030, 0.50);
			
			if(borderWidth < 0) {
				this.graphics.drawRoundRect(1, 1, this.width, this.height, 15, 15);
			} else if(borderWidth != 0) {
				this.graphics.drawRoundRect(1, 1, max(borderWidth, this.width + 10), this.height, 15, 15);
			}
		}
		
		/**
     	 * Simple function to retrun the greatest of two ints.
     	 * @param num1 the first number to test
     	 * @param num2 the second number to test
     	 * @return the largest value between num1 and num2.
     	 */
     	private function max(num1:int, num2:int):int {
     		if(num1 > num2) {
     			return num1;
     		} else {
     			return num2;
     		}
     	}
		
		private function rollOver(soption:SelectorOption):void {
			soption.filters = [new GlowFilter(0xFFFF55, 0.8, 6, 6, 10)];
		}
		
		private function rollOut(soption:SelectorOption):void {
			soption.filters = null;
		}
	}
}