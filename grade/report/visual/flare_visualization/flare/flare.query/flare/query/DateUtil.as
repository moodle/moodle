package flare.query
{
	/**
	 * Utility class providing functions for manipulating <code>Date</code>
	 * objects. These functions are intended for use by the
	 * <code>Function</code> operator.
	 */
	public class DateUtil
	{

// ADDDATE()(v4.1.1) 	Add dates
// ADDTIME()(v4.1.1) 	Add time
// CONVERT_TZ()(v4.1.3) 	Convert from one timezone to another
// CURDATE() 	Return the current date
// CURRENT_DATE(), CURRENT_DATE 	Synonyms for CURDATE()
// CURRENT_TIME(), CURRENT_TIME 	Synonyms for CURTIME()
// CURTIME() 	Return the current time
// DATE_ADD() 	Add two dates
// DATE_FORMAT() 	Format date as specified
// DATE_SUB() 	Subtract two dates
// DATE()(v4.1.1) 	Extract the date part of a date or datetime expression
// DATEDIFF()(v4.1.1) 	Subtract two dates

// DAYNAME()(v4.1.21) 	Return the name of the weekday

		// DAYOFMONTH() 	Return the day of the month (1-31)
		// DAY()(v4.1.1) 	Synonym for DAYOFMONTH()
		public static function day(d:Date):int
		{
			return d.date;
		}

		// DAYOFWEEK() 	Return the weekday index of the argument
		// WEEKDAY() 	Return the weekday index
		public static function dayOfWeek(d:Date):int
		{
			return d.day;
		}
		
// DAYOFYEAR() 	Return the day of the year (1-366)
// EXTRACT 	Extract part of a date
// FROM_DAYS() 	Convert a day number to a date
// FROM_UNIXTIME() 	Format date as a UNIX timestamp
// GET_FORMAT()(v4.1.1) 	Return a date format string

		// HOUR() 	Extract the hour
		public static function hour(d:Date):int
		{
			return d.hours;
		}
		
// LAST_DAY(v4.1.1) 	Return the last day of the month for the argument
// MAKEDATE()(v4.1.1) 	Create a date from the year and day of year
// MAKETIME(v4.1.1) 	MAKETIME()

		// MICROSECOND()(v4.1.1) 	Return the microseconds from argument
		public static function microsecond(d:Date):int
		{
			return int(1000 * d.time) % 1000000;
		}
		
		// MINUTE() 	Return the minute from the argument
		public static function minute(d:Date):int
		{
			return d.minutes;
		}
		
		// MONTH() 	Return the month from the date passed
		public static function month(d:Date):int
		{
			return d.month;
		}
		
// MONTHNAME()(v4.1.21) 	Return the name of the month

		// NOW() 	Return the current date and time
		// LOCALTIME(), LOCALTIME 	Synonym for NOW()
		// LOCALTIMESTAMP, LOCALTIMESTAMP()(v4.0.6) 	Synonym for NOW()
		// CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP 	Synonyms for NOW()
		public static function now():Date
		{
			return new Date();
		}

// PERIOD_ADD() 	Add a period to a year-month
// PERIOD_DIFF() 	Return the number of months between periods
// QUARTER() 	Return the quarter from a date argument
// SEC_TO_TIME() 	Converts seconds to 'HH:MM:SS' format

		// SECOND() 	Return the second (0-59)
		public static function second(d:Date):int
		{
			return d.seconds;
		}

// STR_TO_DATE()(v4.1.1) 	Convert a string to a date
// SUBDATE() 	When invoked with three arguments a synonym for DATE_SUB()
// SUBTIME()(v4.1.1) 	Subtract times
// SYSDATE() 	Return the time at which the function executes
// TIME_FORMAT() 	Format as time
// TIME_TO_SEC() 	Return the argument converted to seconds
// TIME()(v4.1.1) 	Extract the time portion of the expression passed
// TIMEDIFF()(v4.1.1) 	Subtract time
// TIMESTAMP()(v4.1.1) 	With a single argument, this function returns the date or datetime expression. With two arguments, the sum of the arguments
// TIMESTAMPADD()(v5.0.0) 	Add an interval to a datetime expression
// TIMESTAMPDIFF()(v5.0.0) 	Subtract an interval from a datetime expression
// TO_DAYS() 	Return the date argument converted to days
// UNIX_TIMESTAMP() 	Return a UNIX timestamp
// TC_DATE()(v4.1.1) 	Return the current UTC date
// UTC_TIME()(v4.1.1) 	Return the current UTC time
// UTC_TIMESTAMP()(v4.1.1) 	Return the current UTC date and time
// WEEK() 	Return the week number
// WEEKOFYEAR()(v4.1.1) 	Return the calendar week of the date (1-53)

		// YEAR() 	Return the year
		public static function year(d:Date):int
		{
			return d.fullYear;
		}
		
// YEARWEEK() 	Return the year and week

	} // end of class DateUtil
}