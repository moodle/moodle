///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

package {
	import flash.display.SimpleButton;
	import flash.text.TextField;
	import flash.text.TextFormat;

	public class Button extends SimpleButton {
		private var upColor:uint = 0x9999FF;
    	private var overColor:uint = 0xBABAFF;
    	private var downColor:uint = 0xBBBBFF;
    	private var fmt:TextFormat = new TextFormat("monospace", 12, 0, null, null, null, null, null, "center");
    	private var buttonText:String;
    	private var alphaValue:Number = 0.6;
    	
    	public var props:Object = new Object();
		
		public function Button(text:String, settings:XMLList = null) {
			if(settings != null) {
				upColor = parseInt(settings.bgcolor, 16);
				overColor = upColor + 0x212100;
				downColor = upColor + 0x222200;
				alphaValue = settings.alpha;
				
				fmt = new TextFormat(settings.text.font, settings.text.size, 0, null, null, null, null, null, "center");
			
				downState = new ButtonDisplayState(text, fmt, downColor, alphaValue, settings.line);
        		overState = new ButtonDisplayState(text, fmt, overColor, alphaValue, settings.line);
        		upState = new ButtonDisplayState(text, fmt, upColor, alphaValue, settings.line);
			} else {
				downState = new ButtonDisplayState(text, fmt, downColor, alphaValue);
        		overState = new ButtonDisplayState(text, fmt, overColor, alphaValue);
        		upState = new ButtonDisplayState(text, fmt, upColor, alphaValue);
			}
			
			buttonText = text;
        	hitTestState = upState;
        	useHandCursor = true;	
		}
		
		public function set text(text:String):void {
			buttonText = text;
			ButtonDisplayState(downState).text = text;
			ButtonDisplayState(overState).text = text;
			ButtonDisplayState(upState).text = text;
		}
		
		public function get text():String {
			return buttonText;
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
	private var buttonText:String;
	private var fmt:TextFormat;
	private var ts:TextSprite;
	private var alphaValue:Number
	private var lineSettings:XMLList;

	public function ButtonDisplayState(text:String, fmt:TextFormat, bgColor:uint = 0x9999FF, alphaValue:Number = 0.6, lineSettings:XMLList = null) {
		this.bgColor = bgColor;
		buttonText = text;
		this.fmt = fmt;
		this.ts = new TextSprite(text, fmt);
 		this.alphaValue = alphaValue;
 		this.lineSettings = lineSettings;
 
		var tf:TextField = ts.textField;
		
        w = tf.width = tf.textWidth + 6;
        h = tf.height = tf.textHeight + 4;
		ts.dirty();
		
        addChild(ts);
        draw();
    }

    private function draw():void {
		graphics.beginFill(bgColor, alphaValue);
        
        if(lineSettings == null) {
        	graphics.lineStyle(1, 0x4444FF, 0.3, true);
        } else {
        	graphics.lineStyle(lineSettings.size, parseInt(lineSettings.color, 16), lineSettings.alpha, true);
        }
        
        graphics.drawRoundRect(0, 0, w, h, 10, 10);
        graphics.endFill();
	}
	
	public function set text(text:String):void {
		buttonText = text;
		ts.text = text;
	}
}