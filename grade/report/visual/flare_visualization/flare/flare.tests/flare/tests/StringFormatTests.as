package flare.tests
{
	import flare.util.Strings;
	import unitest.TestCase;
	
	public class StringFormatTests extends TestCase
	{
		public function StringFormatTests() {
			addTest("testNumberFormatting");
			addTest("testDateTimeFormatting");
		}
		
		private function run_tests(tests:Array):void {
			var pass:int, fail:int, i:int, s:String, t:Object, b:Boolean;
			for (pass=fail=i=0; i<tests.length; ++i) {
				t = tests[i];
				s = Strings.format("{0"+t.format+"}", t.input);
				assertEquals(t.result, s, t.format);
			}
		}

		public function testNumberFormatting():void
		{
			// -- Number Formatting ----------------------------
			var d:Number = 12345.9876543210;
			var c:Number = 12345.30100;
			var num_tests:Array = [
				// custom formatters
				{format:":##;(##)", input:-1234, result:"(1234)"},
				{format:":##", input:1234, result:"1234"},
				{format:":00", input:1234, result:"1234"},
				{format:":#####", input:123, result:"123"},
				{format:":00000", input:123, result:"00123"},
				{format:":(###) ### - ####", input:1234567890, result:"(123) 456 - 7890"},
				{format:":#.##", input:1.2, result:"1.2"},
				{format:":0.00", input:1.2, result:"1.20"},
				{format:":00.00", input:1.2, result:"01.20"},
				{format:":#,#", input:1234567890, result:"1,234,567,890"},
				{format:":#,,", input:1234567890, result:"1235"},
				{format:":#,,,", input:1234567890, result:"1"},
				{format:":#,##0,,", input:1234567890, result:"1,235"},
				{format:":#0.##%", input:0.086, result:"8.6%"},
				{format:":0.###E+0", input:86000, result:"8.6E+4"},
				{format:":0.###E+000", input:86000, result:"8.6E+004"},
				{format:":0.###E-000", input:86000, result:"8.6E004"},
				{format:":[##-##-##]", input:123456, result:"[12-34-56]"},
				{format:":##;(##)", input:1234, result:"1234"},
				{format:":0000000", input:c, result:"0012345"},
				{format:":#######", input:c, result:"12345"},
				{format:":0000000.0000#", input:c, result:"0012345.3010"},
				{format:":#######.#####", input:c, result:"12345.301"},
				{format:":00000000%", input:c, result:"01234530%"},
				{format:":########%", input:c, result:"1234530%"},
				{format:":0.00e00", input:c, result:"1.23e04"},
				{format:":#.##e00", input:c, result:"1.23e04"},
				{format:":0.e+00.-00", input:c, result:"1.e+04-23"},
				{format:":#.e+00.-##", input:c, result:"1.e+04-23"},
				{format:":000,", input:c, result:"012"},
				{format:":###,", input:c, result:"12"},
				{format:":0,", input:c, result:"12"},
				{format:":#,", input:c, result:"12"},
				{format:",-8:#,", input:c, result:"12      "},
				{format:",8:#,", input:c, result:"      12"},
				{format:":00,000.00", input:c, result:"12,345.30"},
				{format:":##,###.##", input:c, result:"12,345.3"},
				{format:":0,0.00", input:c, result:"12,345.30"},
				{format:":#,#.##", input:c, result:"12,345.3"},
				{format:":$0;($0);Zero", input:123, result:"$123"},
				{format:":$0;($0);Zero", input:-123, result:"($123)"},
				{format:":$0;($0);Zero", input:0, result:"Zero"},
				{format:":0000000", input:d, result:"0012346"},
				{format:":#######", input:d, result:"12346"},
				{format:":0000000.00", input:d, result:"0012345.99"},
				{format:":#######.##", input:d, result:"12345.99"},
				{format:":000.000e0,0%", input:32100.123, result:"321.001e42%"},
	            {format:":000.000%", input:32100.123, result:"3210012.300%"},
	            {format:":000%", input:32100.123, result:"3210012%"},
	            {format:":0,0.0", input:32100.123, result:"32,100.1"},
	            {format:":0", input:32100.123, result:"32100"},
	            {format:":00", input:32100.123, result:"32100"},
	            {format:":0,0000,0", input:32100123, result:"32,100,123"},
	            {format:":00000,0,.000000", input:32100123.123456789, result:"032,100.123123"},
	            {format:":###.###e#,#%", input:32100.123, result:"3210012.3e%"},
	            {format:":0,000%", input:3210.01, result:"321,001%"},
	            {format:":00%00", input:3210.01, result:"3210%01"},
	            {format:":0.00e+0-00e0", input:123456789, result:"1.23e+8-45e7"},
	            {format:":0.00e+", input:123456789, result:"123456789.00e+"},
	            {format:":0.00e", input:123456789, result:"123456789.00e"},
	            {format:":0ea.00e+00", input:123456789, result:"1ea.23e+08"},
	            {format:":###e+00.00", input:123456789, result:"123e+06.46"},
	            {format:":0.00e+0", input:123456789000000000, result:"1.23e+17"},
	            {format:":0,0.0,0,,,", input:1234567.89, result:"1,234,567.89"},
	            {format:":0,0-0-0,0.00", input:1234567.89, result:"1,234,-5-67.89"},
	            {format:":###000", input:12345, result:"12345"},
	            {format:":000###", input:12345, result:"012345"},
	            {format:":0000000,00", input:12345, result:"000,012,345"},
	            {format:":0.########e+0", input:1.234e56, result:"1.234e+56"},
	            {format:":0.########e-0", input:4.321e-56, result:"4.321e-56"},
	            {format:":0.0",input:7.1E-15, result:"0.0"},
	            {format:":0.0",input:1+7.1E-15, result:"1.0"},
	            // standard formatters
	            {format:":c", input:12345, result:"$12,345.00"},
	            {format:":C04", input:12345, result:"$12,345.0000"},
	            {format:":d", input:12345, result:"12345"},
	            {format:":D6", input:12345, result:"012345"},	            
	            {format:":e", input:12345, result:"1.23e+4"},
	            {format:":E", input:0.00012345, result:"1.23E-4"},
	            {format:":e4", input:12345000000, result:"1.2345e+10"},
	            {format:":f4", input:123.45, result:"123.4500"},
	            {format:":F2", input:123.45, result:"123.45"},
	            {format:":n", input:12345, result:"12,345.00"},
	            {format:":N4", input:12345.0001, result:"12,345.0001"},
	            {format:":x", input:255, result:"ff"},
	            {format:":x4", input:255, result:"00ff"},
	            {format:":X5", input:255, result:"000FF"},
			];
			run_tests(num_tests);
		}		

		public function testDateTimeFormatting():void
		{			
			// -- Date/Time Formatting -------------------------
			var d1:Date = new Date(1979,5,15);
			var d2:Date = new Date(1979,11,7,13,8,12,123);
			var date_tests:Array = [
				{format:":yyyy/MM/dd", input:d1, result:"1979/06/15"},
				{format:":yy/MM/dd", input:d1, result:"79/06/15"},
				{format:":yy/MM/dd", input:d2, result:"79/12/07"},
				{format:":%h", input:d2, result:"1"},
				{format:":d", input:d2, result:"12/07/1979"},
				{format:":ddd MMMM d, yyyy", input:d2, result:"Fri December 7, 1979"},
				{format:":MMM-dd", input:d2, result:"Dec-07"},
				{format:":HH:mm:ss tt", input:d2, result:"13:08:12 PM"},
				{format:":hh:mm:ss tt", input:d2, result:"01:08:12 PM"},
				{format:":dddd h:mmt", input:d2, result:"Friday 1:08P"},
				{format:":'Freaky' dddd \"the 13th\"", input:d2, result:"Freaky Friday the 13th"}
			];
			run_tests(date_tests);
		}
		
	} // end of class StringFormatTests
}