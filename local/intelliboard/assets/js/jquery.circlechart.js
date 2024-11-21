// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

(function($){
 	$.fn.extend({
		percentcircle: function(options) {
			var defaults = {
		        animate : true,
				diameter : 100,
				guage: 2,
				coverBg: '#fff',
				bgColor: '#efefef',
				fillColor: '#5c93c8',
				percentSize: '15px',
				percentWeight: 'normal'
			};
			defaults =  $.extend(defaults, options);
			var	styles = {
				    cirContainer : {
					    'width':defaults.diameter,
						'height':defaults.diameter
					},
					cir : {
					    'position': 'relative',
					    'text-align': 'center',
					    'width': defaults.diameter,
					    'height': defaults.diameter,
					    'border-radius': '100%',
					    'background-color': defaults.bgColor,
					    'background-image' : 'linear-gradient(91deg, transparent 50%, '+defaults.bgColor+' 50%), linear-gradient(90deg, '+defaults.bgColor+' 50%, transparent 50%)'
					},
					cirCover: {
						'position': 'relative',
					    'top': defaults.guage,
					    'left': defaults.guage,
					    'text-align': 'center',
					    'width': defaults.diameter - (defaults.guage * 2),
					    'height': defaults.diameter - (defaults.guage * 2),
					    'border-radius': '100%',
					    'background-color': defaults.coverBg
					},
					percent: {
						'display':'block',
						'width': defaults.diameter,
					    'height': defaults.diameter,
					    'line-height': defaults.diameter + 'px',
					    'vertical-align': 'middle',
					    'font-size': defaults.percentSize,
					    'font-weight': defaults.percentWeight,
					    'color': defaults.fillColor
                    }
				};
			var that = this,
					template = '<div><div class="ab"><div class="cir"><span class="perc">{{percentage}}</span></div></div></div>';
			function init(){
				that.each(function(){
					var $this = $(this),
						real_number = $this.data('percent'),
						perc = Math.round(real_number), //get the percentage from the element
						deg = perc * 3.6,
						stop = defaults.animate ? 0 : deg,
						$chart = $(template.replace('{{percentage}}',(isNaN(real_number))?real_number:Math.max(real_number, perc)+'%'));
						$chart.css(styles.cirContainer).find('.ab').css(styles.cir).find('.cir').css(styles.cirCover).find('.perc').css(styles.percent);

					$this.append($chart);
					setTimeout(function(){
						animateChart(deg,parseInt(stop),$chart.find('.ab')); //both values set to the same value to keep the function from looping and animating
					},250);
	   	    	});
			}
			var animateChart = function (stop,curr,$elm){
				var deg = curr;
				if(curr <= stop){
					if (deg>=180){
						$elm.css('background-image','linear-gradient(' + (90+deg) + 'deg, transparent 50%, '+defaults.fillColor+' 50%),linear-gradient(90deg, '+defaults.fillColor+' 50%, transparent 50%)');
			  	    }else{
			  		    $elm.css('background-image','linear-gradient(' + (deg-90) + 'deg, transparent 50%, '+defaults.bgColor+' 50%),linear-gradient(90deg, '+defaults.fillColor+' 50%, transparent 50%)');
			  	    }
					curr ++;
					setTimeout(function(){
						animateChart(stop,curr,$elm);
					},1);
				}
			};
			init();
   	    }
	});
})(jQuery);
