package flare.util
{
	import flash.display.SimpleButton;
	import flash.text.TextField;
	import flash.text.TextFormat;

	public class Button extends SimpleButton
	{
		private var upColor:uint   = 0xDDDDDD;
    	private var overColor:uint = 0xEAEAEA;
    	private var downColor:uint = 0xCCCCCC;
    	private var fmt:TextFormat = new TextFormat("Arial",16,0,null,null,null,null,null,"center");
    	
    	public var props:Object = new Object();
		
		public function Button(text:String)
		{
			downState      = new ButtonDisplayState(text, fmt, downColor);
        	overState      = new ButtonDisplayState(text, fmt, overColor);
        	upState        = new ButtonDisplayState(text, fmt, upColor);
        	hitTestState   = upState;
        	useHandCursor  = true;	
		}
	}
}

import flash.display.Sprite;
import flash.text.TextField;
import flash.text.TextFormat;
import flash.text.TextFieldAutoSize;
import flare.display.TextSprite;

class ButtonDisplayState extends Sprite
{
    private var bgColor:uint;
    private var w:uint;
    private var h:uint;

    public function ButtonDisplayState(text:String, fmt:TextFormat, bgColor:uint) {//, w:uint, h:uint) {
        this.bgColor = bgColor;

		var ts:TextSprite = new TextSprite(text, fmt);
		var tf:TextField = ts.textField;
		
        w = tf.width = tf.textWidth + 6;
        h = tf.height = tf.textHeight + 4;
		ts.dirty();
		
        addChild(ts);
        draw();
    }

    private function draw():void {
        graphics.beginFill(bgColor);
        graphics.lineStyle(1, 0xaaaaaa, 1, true);
        graphics.drawRoundRect(0, 0, w, h, 8, 8);
        graphics.endFill();
    }
}