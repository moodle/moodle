package flare.vis.scale
{
	import flare.util.Dates;
	import flare.util.Maths;
	
	/**
	 * Scale for timelines represented using <code>Date</code> values. This
	 * scale represents a linear, quantitative time line. The class attempts
	 * to automatically configure date value labels based on the time span
	 * between the earliest and latest date in the scale. The label formatting
	 * pattern can also be manually set using the <code>labelFormat</code>
	 * property.
	 */
	public class TimeScale extends Scale
	{
		private var _dmin:Date = new Date(0);
		private var _dmax:Date = new Date(0);
		private var _smin:Date;
		private var _smax:Date;
		private var _autofmt:Boolean = true;
		
		/**
		 * Creates a new TimeScale.
		 * @param min the minimum (earliest) date value
		 * @param max the maximum (latest) date value
		 * @param flush the flush flag for scale padding
		 * @param labelFormat the formatting pattern for value labels
		 */
		public function TimeScale(min:Date=null, max:Date=null,
			flush:Boolean=false, labelFormat:String=null)
		{
			if (min) this.dataMin = min;
			if (max) this.dataMax = max;
			this.flush = flush;
			this.labelFormat = labelFormat;
		}
		
		/** @inheritDoc */
		public override function clone():Scale {
			return new TimeScale(_dmin, _dmax, _flush, _format);
		}
		
		// -- Properties ------------------------------------------------------
		
		/** @inheritDoc */
		public override function set flush(val:Boolean):void
		{
			_flush = val; updateScale();
		}
		
		/** @inheritDoc */
		public override function get labelFormat():String
		{
			return (_autofmt ? null : super.labelFormat);
		}
		
		public override function set labelFormat(fmt:String):void
		{
			if (fmt != null) {
				super.labelFormat = fmt;
				_autofmt = false;
			} else {
				_autofmt = true;
				updateScale();
			}
		}
		
		/** @inheritDoc */
		public override function get min():Object { return dataMin; }
		public override function set min(o:Object):void { dataMin = o as Date; }
		
		/** @inheritDoc */
		public override function get max():Object { return dataMax; }
		public override function set max(o:Object):void { dataMax = o as Date; }
		
		/** The minimum (earliest) Date value in the underlying data.
		 *  This property is the same as the <code>minimum</code>
		 *  property, but properly typed. */
		public function get dataMin():Date
		{
			return _dmin;
		}
		public function set dataMin(val:Date):void
		{
			_dmin = val; updateScale();
		}

		/** The maximum (latest) Date value in the underlying data.
		 *  This property is the same as the <code>maximum</code>
		 *  property, but properly typed. */
		public function get dataMax():Date
		{
			return _dmax;
		}
		public function set dataMax(val:Date):void
		{
			_dmax = val; updateScale();
		}
		
		/** The minimum (earliest) Date value in the scale. */
		public function get scaleMin():Date
		{
			return _smin;
		}
		
		/** The maximum (latest) Date value in the underlying data. */
		public function get scaleMax():Date
		{
			return _smax;
		}
		
		// -- Scale Methods ---------------------------------------------------
		
		/** @inheritDoc */
		public override function interpolate(value:Object):Number
		{
			var t:Number = value is Date ? (value as Date).time : Number(value);
			return Maths.invLinearInterp(t, _smin.time, _smax.time);
		}
		
		/** @inheritDoc */
		public override function lookup(f:Number):Object
		{
			var t:Number = Math.round(Maths.linearInterp(f, _smin.time, _smax.time));
			return new Date(t);
		}
		
		/**
		 * Updates the scale range when the data range is changed.
		 */
		protected function updateScale():void
		{
			var span:int = Dates.timeSpan(_dmin, _dmax);
			if (_flush) {
				_smin = _dmin;
				_smax = _dmax;
			} else {
				_smin = Dates.roundTime(_dmin, span, false);
				_smax = Dates.roundTime(_dmax, span, true);
			}
			if (_autofmt) {
				super.labelFormat = formatString(span);
			}
		}
		
		/**
		 * Determines the format string to be used based on a measure of
		 * the time span covered by this scale.
		 * @param span the time span covered by this scale. Should use the
		 *  format of the <code>flare.util.Dates</code> class.
		 * @return the label formatting pattern
		 */
		protected function formatString(span:int):String
		{
			if (span >= Dates.YEARS) {
				return "yyyy";
			} else if (span == Dates.MONTHS) {
				return "MMM";
			} else if (span == Dates.DAYS) {
				return "d";
			} else if (span == Dates.HOURS) {
				return "h:mmt";
			} else if (span == Dates.MINUTES) {
				return "h:mmt";
			} else if (span == Dates.SECONDS) {
				return "h:mm:ss";
			} else {
				return "s.fff";
			}
		}
		
		/** @inheritDoc */
		public override function values(num:int=-1):Array
		{   
            var a:Array = new Array();
            var span:int = Dates.timeSpan(_dmin, _dmax);
            var step:Number = Dates.timeStep(span);
			var max:Number = _smax.time;
            var d:Date = _flush ? Dates.roundTime(scaleMin, span, true) : scaleMin;

            if (span < Dates.MONTHS) {
            	for (var x:Number = _smin.time; x <= max; x += step) {
            		a.push(new Date(x));
            	}
            } else if (span == Dates.MONTHS) {
            	for (; d.time <= max; d = Dates.addMonths(d,1)) {
            		a.push(d);
            	}
            } else {
            	var y:int = int(step);
            	for (; d.time <= max; d = Dates.addYears(d,y)) {
            		a.push(d);
            	}
            }
			return a;
		}
		
	} // end of class TimeScale
}