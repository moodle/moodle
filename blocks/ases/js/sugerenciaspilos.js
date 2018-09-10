/*
 */

(function($) {
	$.fn.sugerirInteligente =	function(options) {
		
		// Define default options.
		var defaults = {
			boxId: '%-suggestions', // % is filled with the field's ID, allowing for multiple Smart Suggests per page
			classPrefix: 'ss-',
			timeoutLength: 500,
			src: '',
			resultsText: '$ de % resultados',
			noResultsText: 'No encontrado.',
			showEmptyCategories: false,
			fillBox: false,
			fillBoxWith: 'primary',
			executeCode: true,
			showImages: true,
			minChars: 2,
			idinstancia: '',
		};
		
		// Merge defaults with user-defined options.
		var options = $.extend(defaults, options);
		
		// Get correct box ID
		options.boxId = options.boxId.replace('%', $(this).attr('id'));
		
		// Define other variables.
		var lastQuery = '';
		var data;
		
		// Create the wrapper and the suggestion box.
		$(this).wrap('<div class="'+options.classPrefix+'wrap"></div>');
		$(this).attr('autocomplete', 'off');
		$(this).after('<ul class="'+options.classPrefix+'box" id="'+options.boxId+'" style="display: none;"></ul>');
		var inputObj = $(this);
		var boxObj = $('#'+options.boxId);
		
		
		
		// Refresh the suggestion box for every keyup event.
		var timeout = null;
		inputObj.keyup(function(event) {
			
			// If any key but the enter key or tab key was pressed, continue.
			if (event.keyCode != 13 && event.keyCode != 9)
			{
			
				// Get the query (the value of the input field).
				var q = inputObj.val();
				
				// If the query is empty or doesn't meet the minChar requirement, close the box. If not, keep going.
				if (q == '' || q.length < options.minChars)
				{
					boxObj.fadeOut();
					unsetTimeout(timeout);
				}
				else
				{
					// Check the timeout.
					if (timeout != null)
					{
						unsetTimeout(timeout);
					}
					
					timeout = setTimeout(function() {
												
						// Once the timeout length has passed, continue to refresh the box.

						// Set the "last query" variable.
						lastQuery = q;

						// Get the JSON data.
						$.getJSON(options.src+"?q="+q+'&idinstancia='+options.idinstancia, function(data, textStatus) {
							// Check to make sure that the JSON call was a success.
							if (textStatus == 'success')
							{
								// Create the suggestion HTML.
								var output = "";
								
								// Determine if there is any data in the categories.
								var has_data = false;
								$.each(data, function(i, group) {
									if (group['data'].length > 0)
									{
										has_data = true;
									}
								});
								
								if (!has_data)
								{
									output += '<li class="'+options.classPrefix+'header">'+"\n";
									output += '<p class="'+options.classPrefix+'header-text">'+options.noResultsText+'</p>'+"\n";
									output += '<p class="'+options.classPrefix+'header-limit">0 resultados</p>'+"\n";
									output += '</li>';
								}
								else
								{
									$.each(data, function(i, group) {
										
										if (options.showEmptyCategories || (!options.showEmptyCategories && group['data'].length != 0))
										{
											var limit = group['header']['limit'];
											var count = 0;

											// Create the group wrapper and header.
											output += '<li class="'+options.classPrefix+'header">'+"\n";
											output += '<p class="'+options.classPrefix+'header-text">'+group['header']['title']+'</p>'+"\n";
											output += '<p class="'+options.classPrefix+'header-limit">'+options.resultsText.replace("%", group['header']['num']).replace("$", (group['header']['limit'] < group['data'].length ? group['header']['limit'] : group['data'].length))+'</p>'+"\n";
											output += '</li>';

											// Run through each of the group items in this group and add them to the HTML.
											var fill_code = (options.fillBox) ? 'document.getElementById(\''+inputObj.attr('id')+'\').value = \'%\';' : '';
											$.each(group['data'], function (j, item) {
												if (count < limit)
												{
													// Build the link opening tab.
													var link_open = '<a href="';
													link_open += (item['url'] != undefined) ? item['url'] : 'javascript: void(0);';
													link_open += '" ';
													link_open += (item['onclick'] != undefined) ? ' onclick="'+fill_code.replace("%", item[options.fillBoxWith])+(options.executeCode ? item['onclick'] : '')+'" ' : '';
													link_open += '>';

													// Open the item wrapper DIV and the anchor.
													output += '<li class="'+options.classPrefix+'result">'+link_open+"\n";

													// Create the various HTML elements, including the image, primary text, and secondary text.
													output += '<table border="0" cellspacing="0" cellpadding="0" width="100%"><tr>';
													output += (item['image'] != undefined && options.showImages) ? '<td width="5"><img class= "image" src="'+item['image']+'" /></td>'+"\n" : '';
													output += '<td>';
													output += '<p>';
													output += (item['primary'] != undefined) ? '<span class="'+options.classPrefix+'result-title">'+item['primary']+"</span><br />\n" : '';
													output += (item['secondary'] != undefined) ? ''+item['secondary']+''+"\n" : '';
													output += '</p>'+"\n";
													output += '</td>';
													output += '</tr></table>';

													// Close the item wrapper DIV and the anchor.
													output += '</a></li>'+"\n";
												}

												count++;
											});
										}
									});
								}

								// Display the new suggestion box.
								boxObj.html(output);
								
								boxObj.css('position', 'absolute');
								boxObj.css('top', inputObj.offset().top+ 2*inputObj.outerHeight() - 300);
								boxObj.css('left', inputObj.offset().left - 210);
								//boxObj.css('top',  '105px');
								//boxObj.css('left', '200px');
								
								boxObj.fadeIn();
								
							}
						});
						
					}, options.timeoutLength);
				}
				
			}
			
		});
		
		
		
		// Whenever the input field is blurred, close the suggestion box.
		inputObj.blur(function() {
			boxObj.fadeOut();
		});
		
		// If the lastQuery variable is equal to what's currently in the input field, show the box. This means that the results will still be valid for what's in the input field.
		inputObj.focus(function(){
			if (inputObj.val() == lastQuery && inputObj.val() != '')
			{
				boxObj.fadeIn();
			}
		});
	};
	
	
	
	function unsetTimeout(timeout)
	{
		clearTimeout(timeout);
		timeout = null;
	};
})(jQuery);
