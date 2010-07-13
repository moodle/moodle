package {
	
	import flash.events.Event;
	import flash.net.NetConnection;
	import flash.net.Responder;
	
	import nl.demonsters.debugger.MonsterDebugger;

	/**
	 * Wrapper class for the NetConnection/Responder instances
	 * 
	 * This program is free software. It comes without any warranty, to
	 * the extent permitted by applicable law. You can redistribute it
	 * and/or modify it under the terms of the Do What The Fuck You Want
	 * To Public License, Version 2, as published by Sam Hocevar. See
	 * http://sam.zoy.org/wtfpl/COPYING for more details.
	 * 
	 * @author Jordi Boggiano <j.boggiano@seld.be>
	 */			
	public class AMFConnector extends NetConnection {
		private var responder:Responder;
		public var data:Object;
		public var error:Boolean = false;
	
		public function AMFConnector(url:String) {
			responder = new Responder(onSuccess, onError);
			connect(url);
		}
		
		/**
		 * executes a command on the remote server, passing all the given arguments along
		 */
		public function exec(command:String, args:Array = null):void
		{
			if (args == null) args = [];
			args.unshift(responder);
			args.unshift(command);
			(call as Function).apply(this, args);
		} 
		
		/**
		 * handles success 
		 */ 
		protected function onSuccess(result:Object):void {
			MonsterDebugger.trace(this, {'result':result});
			data = result;
			dispatchEvent(new Event(Event.COMPLETE));
			data = null;
		}

		/**
		 * handles errors 
		 */ 
		protected function onError(result:Object):void {
			data = result;
			MonsterDebugger.trace(this, {'result':result});
			error = true;
			dispatchEvent(new Event(Event.COMPLETE));
			error = false;
			data = null;
		}
	}
}