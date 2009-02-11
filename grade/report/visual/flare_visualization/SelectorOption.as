package
{
	import flare.display.TextSprite;
	
	import flash.text.TextFormat;
	
	public class SelectorOption extends TextSprite {
		public var param:String;
		public var value:String;
		public var active:Boolean = false;
	
		public function SelectorOption(param:String, option:XML, active:Boolean = false) {
			this.param = param;
			this.value = option.value;
			this.active = active;
			
			super(option.title, new TextFormat("timesnewromen"));
		}
	}
}