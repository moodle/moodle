package {
	import flare.tests.AnimationTests;
	import flare.tests.DataIOTests;
	import flare.tests.DataTests;
	import flare.tests.ExpressionTests;
	import flare.tests.SortTests;
	import flare.tests.StringFormatTests;
	import flare.tests.TreeTests;
	
	import unitest.TestSuite;
	
	[SWF(width="800", height="600", backgroundColor="#ffffff", frameRate="30")]
	public class tests extends TestSuite
	{	
		public function tests()
		{
			addTest(new AnimationTests());
			addTest(new StringFormatTests());
			addTest(new ExpressionTests());
			addTest(new DataTests());
			addTest(new TreeTests());
			addTest(new SortTests());
			addTest(new DataIOTests());
			run();
		}
		
	} // end of class tests
}
