package flare.vis.operator.distortion
{
	import flash.geom.Rectangle;
	
	/**
	 * Computes a bifocal distortion of space, magnifying a focus region of space
	 * and uniformly demagnifying the rest of the space. The affect is akin to
	 * passing a magnifying glass over the data.
	 * 
	 * <p>
	 * For more details on this form of transformation, see Y. K. Leung and 
	 * M. D. Apperley, "A Review and Taxonomy of Distortion-Oriented Presentation
	 * Techniques", in Transactions of Computer-Human Interaction (TOCHI),
	 * 1(2): 126-160 (1994). Available online at
	 * <a href="portal.acm.org/citation.cfm?id=180173&dl=ACM">
	 * portal.acm.org/citation.cfm?id=180173&dl=ACM</a>.
	 * </p>
	 */
	public class BifocalDistortion extends Distortion
	{
		private var _rx:Number;
		private var _mx:Number;
		private var _ry:Number;
		private var _my:Number;
		
		/**
	     * <p>Create a new BifocalDistortion with the specified range and 
	     * magnification along both axes.</p>
	     * 
	     * <p><strong>NOTE:</strong>if the range value times the magnification
	     * value is greater than 1, the resulting distortion can exceed the
	     * display bounds.</p>
	     * 
	     * @param xrange the range around the focus that should be magnified along
	     *  the x direction. This specifies the horizontal size of the magnified 
	     *  focus region, and should be a value between 0 and 1, 0 indicating no
	     *  focus region and 1 indicating the whole display.
	     * @param xmag how much magnification along the x direction should be used
	     *  in the focal area
	     * @param yrange the range around the focus that should be magnified along
	     *  the y direction. This specifies the vertical size of the magnified 
	     *  focus region, and should be a value between 0 and 1, 0 indicating no
	     *  focus region and 1 indicating the whole display.
	     * @param ymag how much magnification along the y direction should be used
	     *  in the focal area
	     */
		public function BifocalDistortion(xRange:Number=0.1, xMagnify:Number=3,
			yRange:Number=0.1, yMagnify:Number=3)
		{
			_rx = xRange;
	        _mx = xMagnify;
	        _ry = yRange;
	        _my = yMagnify;
	        _distortX = !(_rx == 0 || _mx == 1.0);
	        _distortY = !(_ry == 0 || _my == 1.0);
		}
		
		/** @inheritDoc */
		protected override function xDistort(x:Number):Number
		{
			return bifocal(x, layoutAnchor.x, _rx, _mx, _b.left, _b.right);	
		}
		
		/** @inheritDoc */
		protected override function yDistort(y:Number):Number
		{
			return bifocal(y, layoutAnchor.y, _ry, _my, _b.top, _b.bottom);	
		}
		
		/** @inheritDoc */
		protected override function sizeDistort(bb:Rectangle, x:Number, y:Number):Number
		{
			var xmag:Boolean = false, ymag:Boolean = false;
			var m:Number, c:Number, a:Number, min:Number, max:Number;
        
	        if (_distortX) {
	            c = (bb.left+bb.right)/2;
	            a = layoutAnchor.x;
	            min = _b.left;
	            max = _b.right;
	            m = c<a ? a-min : max-a;
	            if (m == 0) m = max-min;
	            xmag = (Math.abs(c-a) <= _rx*m )
	        }
	        
	        if (_distortY) {
	            c = (bb.top+bb.bottom)/2;
	            a = layoutAnchor.y;
	            min = _b.top;
	            max = _b.bottom;
	            m = c<a ? a-min : max-a;
	            if (m == 0) m = max-min;
	            ymag = (Math.abs(c-a) <= _ry*m);
	        }
	        
	        if (xmag && !_distortY) {
	            return _mx;
	        } else if (ymag && !_distortX) {
	            return _my;
	        } else if (xmag && ymag) {
	            return Math.min(_mx, _my);
	        } else {
	            return Math.min((1-_rx*_mx)/(1-_rx), (1-_ry*_my)/(1-_ry));
	        }
		}
		
		private function bifocal(x:Number, a:Number, r:Number, 
                           mag:Number, min:Number, max:Number):Number
	    {
	    	var m:Number, v:Number, s:Number, bx:Number;
	        m = (x<a ? a-min : max-a);
	        if ( m == 0 ) m = max-min;
	        v = x - a, s = m*r;
	        if ( Math.abs(v) <= s ) {  // in focus
	            return v*mag + a;
	        } else {                   // out of focus
	            bx = r*mag;
	            x = ((Math.abs(v)-s) / m) * ((1-bx)/(1-r));
	            return (v<0?-1:1)*m*(x + bx) + a;
	        }
	    }
		
	} // end of class BifocalDistortion
}