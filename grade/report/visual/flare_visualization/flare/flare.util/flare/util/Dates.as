package flare.util
{	
	/**
	 * Utility methods for working with Date instances.
	 */
	public class Dates
	{
		// enumerated constants
		/** Constant indicating a time span of one or more years. */
		public static const YEARS:int        = 0;
		/** Constant indicating a time span on the order of months. */
		public static const MONTHS:int       = -1;
		/** Constant indicating a time span on the order of days. */
		public static const DAYS:int         = -2;
		/** Constant indicating a time span on the order of hours. */
		public static const HOURS:int        = -3;
		/** Constant indicating a time span on the order of minutes. */
		public static const MINUTES:int      = -4;
		/** Constant indicating a time span on the order of seconds. */
		public static const SECONDS:int      = -5;
		/** Constant indicating a time span on the order of milliseconds. */
		public static const MILLISECONDS:int = -6;
		/** Constant indicating a time span on the order of weeks. */
		public static const WEEKS:int = -10;
		
		/** Number of milliseconds in a minute. */
		public static const MS_MIN:Number  = 60*1000;
		/** Number of milliseconds in an hours. */
		public static const MS_HOUR:Number = 60*60*1000;
		/** Number of milliseconds in a day. */
		public static const MS_DAY:Number  = 24*60*60*1000;
		
		/**
		 * Constructor, throws an error if called, as this is an abstract class.
		 */
		public function Dates() {
			throw new Error("This is an abstract class.");
		}
		
		// -- Conversion ------------------------------------------------------
		
		/**
		 * Given a date, returns a date instance of the same time in Univeral
		 * Coordinated Time (UTC).
		 * @param d the date to convert
		 */
		public static function toUTC(d:Date) : Date {
			return new Date(d.fullYearUTC, d.monthUTC, d.dateUTC, d.hoursUTC,
							d.minutesUTC, d.secondsUTC, d.millisecondsUTC);
		}
		
		// -- Date Arithmetic -------------------------------------------------
		
		/**
		 * Adds years to a date instance.
		 * @param d the date
		 * @param x the number of years to add (can be negative to subtract)
		 * @return a new Date representing the new date and time
		 */
		public static function addYears(d:Date, x:int) : Date {
			return new Date(d.fullYear+x, d.month, d.date, d.hours, d.minutes, d.seconds, d.milliseconds);
		}
		
		/**
		 * Adds months to a date instance.
		 * @param d the date
		 * @param x the number of months to add (can be negative to subtract)
		 * @return a new Date representing the new date and time
		 */
		public static function addMonths(d:Date, x:int) : Date {
			var y:Number = Math.floor(x / 12); x -= 12*y;
			var m:Number = d.month + x;
			if (m > 11) {
				y += 1;
				m -= 12;
			} else if (m < 0) {
				y -= 1;
				m += 12;
			}
			return new Date(d.fullYear+y, m, d.date, d.hours, d.minutes, d.seconds, d.milliseconds);
		}
		
		/**
		 * Adds days to a date instance.
		 * @param d the date
		 * @param x the number of days to add (can be negative to subtract)
		 * @return a new Date representing the new date and time
		 */
		public static function addDays(d:Date, x:int) : Date {
			return new Date(d.time + MS_DAY * x);
		}
		
		/**
		 * Adds hours to a date instance.
		 * @param d the date
		 * @param x the number of hours to add (can be negative to subtract)
		 * @return a new Date representing the new date and time
		 */
		public static function addHours(d:Date, x:int) : Date {
			return new Date(d.time + MS_HOUR * x);
		}
		
		/**
		 * Adds minutes to a date instance.
		 * @param d the date
		 * @param x the number of minutes to add (can be negative to subtract)
		 * @return a new Date representing the new date and time
		 */
		public static function addMinutes(d:Date, x:int) : Date {
			return new Date(d.time + MS_MIN * x);
		}
		
		/**
		 * Adds seconds to a date instance.
		 * @param d the date
		 * @param x the number of seconds to add (can be negative to subtract)
		 * @return a new Date representing the new date and time
		 */
		public static function addSeconds(d:Date, x:int) : Date {
			return new Date(d.time + 1000 * x);
		}
		
		// -- Time Spans ------------------------------------------------------
				
		/**
		 * Rounds a date according to a particular time span. Date values are
		 * rounded to the minimum date/time value of the time span (the first
		 * day in a year, month, or week, or the beginning of a day, hours,
		 * minute, second, etc).
		 * @param t the date to round
		 * @param span the time span to which the date should be rounded, legal
		 *  values are YEARS, MONTHS, WEEKS, DAYS, HOURS, MINUTES, SECONDS, or
		 *  MILLISECONDS
		 * @param roundUp if true, the date will be rounded up to nearest value,
		 * otherwise it will be rounded down (the default)
		 * @return a new Date representing the rounded date and time.
		 */
		public static function roundTime(t:Date, span:int, roundUp:Boolean=false) : Date
		{
			var d:Date = t;
			if (span > YEARS) {
				d = new Date(t.fullYear, 0);
				if (roundUp) d = addYears(d, 1);
			} else if (span == MONTHS) {
				d = new Date(t.fullYear, t.month);
				if (roundUp) d = addMonths(d, 1);				
			} else if (span == DAYS) {
				d = new Date(t.fullYear, t.month, t.date);
				if (roundUp) d = addDays(d, 1);
			} else if (span == HOURS) {
				d = new Date(t.fullYear, t.month, t.date, t.hours);
				if (roundUp) d = addHours(d, 1);
			} else if (span == MINUTES) {
				d = new Date(t.fullYear, t.month, t.date, t.hours, t.minutes);
				if (roundUp) d = addMinutes(d, 1);
			} else if (span == SECONDS) {
				d = new Date(t.fullYear, t.month, t.date, t.hours, t.minutes, t.seconds);
				if (roundUp) d = addSeconds(d, 1);
			} else if (span == MILLISECONDS) {
				d = new Date(d.time + (roundUp ? 1 : -1));
			} else if (span == WEEKS) {
				d = new Date(t.fullYear, t.month, t.date);
				if (roundUp) {
					d = addDays(d, 7 - d.day);
				} else {
					d = addDays(d, -d.day);
				}
			}
			return d;
		}
		
		/**
		 * Given two dates, returns a measure of the time span between them.
		 * @param t the first date to compare
		 * @param s the second date to compare
		 * @return an integer value indicating the time span between dates. If
		 * the return value is positive, it represents the number of years
		 * between dates. Otherwise, the return value is one of MONTHS, DAYS,
		 * HOURS, MINUTES, SECONDS, or MILLISECONDS.
		 */
		public static function timeSpan(t:Date, s:Date) : int
		{
			var span:Number = s.time - t.time;
			var days:Number = span / MS_DAY;

            if (days >= 365*2) 			return (1 + s.fullYear-t.fullYear);
            else if (days >= 60)   		return MONTHS;
            else if (span/MS_DAY > 1)	return DAYS;
            else if (span/MS_HOUR > 1)	return HOURS;
            else if (span/MS_MIN > 1)	return MINUTES;
            else if (span/1000.0 > 1)	return SECONDS;
            else						return MILLISECONDS;
		}
		
		/**
		 * Returns the number of milliseconds needed to step one time step
		 * forward according to the given time span measure.
		 * @param span the time span for which to return a time step value.
		 *  Legal values are any positive numbers (representing years) or DAYS,
		 *  HOURS, MINUTES, SECONDS, and MILLISECONDS. Note that the MONTHS
		 *  time span is not supported and will result in a zero return value.
		 * @return the number of milliseconds needed to more one time step
		 *  ahead according to the input time span. For years (positive
		 *  integer input), this step is the nearest power of ten less than the
		 *  input value.
		 */
		public static function timeStep(span:int):Number {
			if (span > YEARS) {
				return Math.pow(10, Math.floor(Maths.log(Math.max(1,span-1),10)));
			} else if (span == MONTHS) {
				return 0;
			} else if (span == DAYS) {
				return MS_DAY;
			} else if (span == HOURS) {
				return MS_HOUR;
			} else if (span == MINUTES) {
				return MS_MIN;
			} else if (span == SECONDS) {
				return 1000;
			} else {
				return 1;
			}
		}
		
	} // end of class DateUtil
}