/**
 * This is JavaScript code that handles drawing on mouse events and painting pre-existing drawings.
 * @package    qtype
 * @subpackage freehanddrawing
 * @copyright  ETHZ LET <jacob.shapiro@let.ethz.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

YUI.add('moodle-mod_choicegroup-form', function(Y) {
	var CSS = {
	},
	SELECTORS = {
			AVAILABLE_GRPS_SELECT: '#availablegroups',
			AVAILABLE_GRPS_SELECT_OPTIONS: "select[id='availablegroups'] option",
			SELECTED_GRPS_SELECT: '#id_selectedGroups',
			ADD_GRP_BTN: '#addGroupButton',
			DEL_GRP_BTN: '#removeGroupButton',
			FORM: '#mform1',
			LIMIT_UI_INPUT: '#ui_limit_input',
			LIMIT_UI_DIV: '#fitem_id_limit_0',
			LIMIT_UI_LABEL: '#label_for_limit_ui',
			APPLY_LIMIT_TO_ALL_GRPS_BTN: '#id_setlimit',
			ENABLE_DISABLE_LIMITING_SELECT: '#id_limitanswers',
			EXPAND_ALL_GRPNGS_BTN: '#expandButton',
			COLLAPSE_ALL_GRPNGS_BTN: '#collapseButton',
			SERIALIZED_SELECTED_GRPS_LIST: '#serializedselectedgroups',
			GLOBAL_LIMIT_INPUT: '#id_generallimitation',
			HIDDEN_LIMIT_INPUTS: 'input.limit_input_node',
	};
	Y.namespace('Moodle.mod_choicegroup.form');
	Y.Moodle.mod_choicegroup.form = {
			init: function() {

				// -------------------------------
				// Global Variables
				// -------------------------------

                var CHAR_LIMITUI_PAR_LEFT = M.util.get_string('char_limitui_parenthesis_start', 'choicegroup');
                var CHAR_LIMITUI_PAR_RIGHT = M.util.get_string('char_limitui_parenthesis_end', 'choicegroup');
                var CHAR_SELECT_BULLET_COLLAPSED = M.util.get_string('char_bullet_collapsed', 'choicegroup');
                var CHAR_SELECT_BULLET_EXPANDED = M.util.get_string('char_bullet_expanded', 'choicegroup');


				var availableGroupsNode = Y.one(SELECTORS.AVAILABLE_GRPS_SELECT);
				var addGroupButtonNode = Y.one(SELECTORS.ADD_GRP_BTN);
				var selectedGroupsNode = Y.one(SELECTORS.SELECTED_GRPS_SELECT);
				var removeGroupButtonNode = Y.one(SELECTORS.DEL_GRP_BTN);
				var formNode = Y.one(SELECTORS.FORM);
				var uiInputLimitNode = Y.one(SELECTORS.LIMIT_UI_INPUT);
				var applyLimitToAllGroupsButtonNode = Y.one(SELECTORS.APPLY_LIMIT_TO_ALL_GRPS_BTN);
				var limitAnswersSelectNode = Y.one(SELECTORS.ENABLE_DISABLE_LIMITING_SELECT);
				var limitInputUIDIVNode = Y.one(SELECTORS.LIMIT_UI_DIV);
				var expandButtonNode = Y.one(SELECTORS.EXPAND_ALL_GRPNGS_BTN);
				var collapseButtonNode = Y.one(SELECTORS.COLLAPSE_ALL_GRPNGS_BTN);
				var serializedSelectedGroupsListNode = Y.one(SELECTORS.SERIALIZED_SELECTED_GRPS_LIST);

				var groupingsNodesContainer = new Array();

				// --------------------------------
				// Global Functions
				// --------------------------------


				function removeElementFromArray(ar, from, to) {
					  var rest = ar.slice((to || from) + 1 || ar.length);
					  ar.length = from < 0 ? ar.length + from : from;
					  return ar.push.apply(ar, rest);
				}

				function getInputLimitNodeOfSelectedGroupNode(n) {
					return Y.one('#group_' + n.get('value') + '_limit');
				}

				function cleanSelectedGroupsList() {
					var optionsNodes = Y.all(SELECTORS.SELECTED_GRPS_SELECT + " option");
					optionsNodes.each(function(optNode) {
						if (optNode.get('parentNode') != null) {
						optNode.setContent(optNode.getContent().replace(/&nbsp;/gi,''));
						optionsNodes.each(function(opt2Node){
							if ((opt2Node != optNode) && (opt2Node.get('value') == optNode.get('value'))) {
								opt2Node.remove();
							}
						});
					}
					});
				}

				function addOptionNodeToSelectedGroupsList(optNode) {
					if (optNode.hasClass('grouping') == true) {
						// check if option is collapsed
						if (((typeof groupingsNodesContainer[optNode.get('value')]) == 'undefined') || ( groupingsNodesContainer[optNode.get('value')].length == 0)) {
							// it is expanded, take nodes from UI
							// This is a grouping, so instead of adding this item we actually need to add everything underneath it
							var sib = optNode.next(); // sib means sibling, as in, the next element in the DOM tree
							while (sib && sib.hasClass('nested') && sib.hasClass('group')) {
								// add sib
								selectedGroupsNode.append(sib.cloneNode(true));
								// go to next node
								sib = sib.next();
							}
						} else {
							// yes it IS collapsed, need to take the nodes from the container rather than from the UI
							groupingsNodesContainer[optNode.get('value')].forEach(function (underlyingGroupNode) {
								selectedGroupsNode.append(underlyingGroupNode.cloneNode(true));
							});
						}
					} else {
						selectedGroupsNode.append(optNode.cloneNode(true));
					}
                    if (limitAnswersSelectNode.get('value') == '1') {
                        updateLimitUIOfAllSelectedGroups();
                    }
				}

				function updateGroupLimit(e) {
					var selectedOptionsNodes = Y.all(SELECTORS.SELECTED_GRPS_SELECT + " option:checked");
					// get value of input box
					var limit = uiInputLimitNode.get('value');
					selectedOptionsNodes.each(function(optNode) {
						getInputLimitNodeOfSelectedGroupNode(optNode).set('value', limit);
                        updateLimitUIOfSelectedGroup(optNode);
					});
				}

				function collapseGrouping(groupingNode) {
					// Change the text of this <option> so that it is marked as collapsed:
					groupingNode.set('text', CHAR_SELECT_BULLET_COLLAPSED + groupingNode.get('text').substring(1));
					var sib = groupingNode.next(); // sib means sibling, as in, the next element in the DOM tree
					while (sib && sib.hasClass('nested') && sib.hasClass('group')) {
						// save this node somewhere first
						if (typeof groupingsNodesContainer[groupingNode.get('value')] == 'undefined') {
							groupingsNodesContainer[groupingNode.get('value')] = new Array();
						}
						groupingsNodesContainer[groupingNode.get('value')].push(sib.cloneNode(true));
						// save the next node before removing the current one
						var nextSibling = sib.next();
						sib.remove();
						// go to next node
						sib = nextSibling;
					}
				}

				function expandGrouping(groupingNode) {
					// Change the text of this <option> so that it is marked as collapsed:
					groupingNode.set('text', CHAR_SELECT_BULLET_EXPANDED + groupingNode.get('text').substring(1));
					var nextOpt = groupingNode.next();
					if (typeof groupingsNodesContainer[groupingNode.get('value')] != 'undefined') {
						groupingsNodesContainer[groupingNode.get('value')].forEach(function(underlyingGroupNode) {
							if (typeof nextOpt != 'undefined') {
								availableGroupsNode.insertBefore(underlyingGroupNode, nextOpt);
							} else {
								availableGroupsNode.appendChild(underlyingGroupNode);
							}
						});
						groupingsNodesContainer[groupingNode.get('value')] = new Array();
					}


				}

				function collapseAllGroupings() {
					var availableOptionsNodes = Y.all(SELECTORS.AVAILABLE_GRPS_SELECT + " option");
					availableOptionsNodes.each(function(optNode) {
						if (optNode.hasClass('grouping') == true) {
							collapseGrouping(optNode);
						}
					});
				}

				function expandAllGroupings() {
					var availableOptionsNodes = Y.all(SELECTORS.AVAILABLE_GRPS_SELECT + " option");
					availableOptionsNodes.each(function(optNode) {
						if (optNode.hasClass('grouping') == true) {
							expandGrouping(optNode);
						}
					});
				}

                function getGroupNameWithoutLimitText(groupNode) {
                    var indexOfLimitUIText = groupNode.get('text').indexOf(' ' + CHAR_LIMITUI_PAR_LEFT);
                    if (indexOfLimitUIText !== -1) {
                        return groupNode.get('text').substring(0, indexOfLimitUIText);
                    } else {
                        return groupNode.get('text');
                    }
                }
                function clearLimitUIFromSelectedGroup(groupNode) {
                	groupNode.set('text', getGroupNameWithoutLimitText(groupNode));
                }

                function updateLimitUIOfSelectedGroup(groupNode) {
                    groupNode.set('text', getGroupNameWithoutLimitText(groupNode) + ' ' + CHAR_LIMITUI_PAR_LEFT + getInputLimitNodeOfSelectedGroupNode(groupNode).get('value') + CHAR_LIMITUI_PAR_RIGHT);
                }

                function updateLimitUIOfAllSelectedGroups() {
                    Y.all(SELECTORS.SELECTED_GRPS_SELECT + " option").each(function(optNode) { updateLimitUIOfSelectedGroup(optNode); });
                }

                function clearLimitUIFromAllSelectedGroups() {
                    Y.all(SELECTORS.SELECTED_GRPS_SELECT + " option").each(function(optNode) { clearLimitUIFromSelectedGroup(optNode); });
                }

                function expandOrCollapseGrouping(groupingNode) {
					if (((typeof groupingsNodesContainer[groupingNode.get('value')]) == 'undefined') || ( groupingsNodesContainer[groupingNode.get('value')].length == 0)) {
						collapseGrouping(groupingNode);
						expandButtonNode.set('disabled', false);
					} else {
						expandGrouping(groupingNode);
						collapseButtonNode.set('disabled', false);
					}
                }

                getTextWidth = function(text, font) {
                	// Thanks for http://stackoverflow.com/a/21015393/3430277
                    // re-use canvas object for better performance
                    var canvas = getTextWidth.canvas || (getTextWidth.canvas = document.createElement("canvas"));
                    var context = canvas.getContext("2d");
                    context.font = font;
                    var metrics = context.measureText(text);
                    return metrics.width;
                };

                function wasFirstCharacterClicked(e, n) {
                	// Thanks for http://stackoverflow.com/a/21015393/3430277
                	// e is the event, n is the node to check
					var style = window.getComputedStyle(n.getDOMNode(), null).getPropertyValue('font');
					if ((e.pageX - e.currentTarget.getX()) <= getTextWidth(n.get('text').charAt(0),style)) {
						return true;
					}
					return false;
                }

				// --------------------------------
				// this code happens on form load
				// --------------------------------
				if (serializedSelectedGroupsListNode.get('value') != '') {
					var selectedGroups = serializedSelectedGroupsListNode.get('value').split(';');
					selectedGroups = selectedGroups.filter(function(n) {return n != '';});
					var availableOptionsNodes = Y.all(SELECTORS.AVAILABLE_GRPS_SELECT + " option");
					availableOptionsNodes.each(function(optNode) {
						selectedGroups.forEach(function (selectedGroup) {
							if (selectedGroup == optNode.get('value')) {
								addOptionNodeToSelectedGroupsList(optNode);
							}
						});
					});
					cleanSelectedGroupsList();
				}


				// Collapse all groupings on load
				collapseAllGroupings();
				expandButtonNode.set('disabled', false);
                // If necessary update their limit information
				if (limitAnswersSelectNode.get('value') == '1') { // limiting is enabled, show limit box
                    updateLimitUIOfAllSelectedGroups();
                }

				// -------------------------------
				// -------------------------------






				// ---------------------------------
				// Setup UI Bindings (on load)
				// ---------------------------------


				Y.one('#expandButton').on('click', function(e) {
					expandAllGroupings();
					expandButtonNode.set('disabled', true);
					collapseButtonNode.set('disabled', false);

				});
				Y.one('#collapseButton').on('click', function(e) {
					collapseAllGroupings();
					collapseButtonNode.set('disabled', true);
					expandButtonNode.set('disabled', false);

				});


				// On click fill in the limit in every field
				applyLimitToAllGroupsButtonNode.on('click', function (e) {
					// Get the value string
					var generalLimitValue = Y.one(SELECTORS.GLOBAL_LIMIT_INPUT).get('value');
					// Make sure we've got an integer value
					generalLimitValue = parseInt(generalLimitValue);
					if (!isNaN(generalLimitValue)) {
						var limitInputNodes = Y.all(SELECTORS.HIDDEN_LIMIT_INPUTS);
						limitInputNodes.each(function(n) { n.set('value', generalLimitValue); });
					} else {
						alert(M.util.get_string('the_value_you_entered_is_not_a_number', 'choicegroup'));
					}
                    updateLimitUIOfAllSelectedGroups();
				});




				formNode.on('submit', function(e) {
					var selectedOptionsNodes = Y.all(SELECTORS.SELECTED_GRPS_SELECT + " option");
					if (selectedOptionsNodes.size() < 1) {
						alert(M.util.get_string('pleaseselectonegroup', 'choicegroup'));
				        e.preventDefault();
				        e.stopPropagation();
					}
					var serializedSelection = '';
					selectedOptionsNodes.each(function(optNode) { serializedSelection += ';' + optNode.get('value'); });
					serializedSelectedGroupsListNode.set('value', serializedSelection);

				});


				availableGroupsNode.on('click', function(e) {
					var selectedOptionsNodes = Y.all(SELECTORS.AVAILABLE_GRPS_SELECT + " option:checked");
					if (selectedOptionsNodes.size() >= 2) {
						var allGroupings = true;
						selectedOptionsNodes.each(function(optNode){
							if (optNode.hasClass('grouping') == false) {
								allGroupings = false;
							}
						});
						if (allGroupings) {
							addGroupButtonNode.setContent(M.util.get_string('add_groupings', 'choicegroup'));
						} else {
							addGroupButtonNode.setContent(M.util.get_string('add_groups', 'choicegroup'));
						}
						addGroupButtonNode.set('disabled', false);

					} else if (selectedOptionsNodes.size() >= 1) {
						var firstNode = selectedOptionsNodes.item(0);
						if (firstNode.hasClass('grouping')) {
							addGroupButtonNode.setContent(M.util.get_string('add_grouping', 'choicegroup'));
							if (wasFirstCharacterClicked(e, firstNode)) {
								expandOrCollapseGrouping(firstNode);
							}

						} else {
							addGroupButtonNode.setContent(M.util.get_string('add_group', 'choicegroup'));
						}
						addGroupButtonNode.set('disabled', false);

					} else {
						addGroupButtonNode.set('disabled', true);
						addGroupButtonNode.setContent(M.util.get_string('add', 'choicegroup'));
					}

				});
				Y.delegate('dblclick', function(e) {
					if (e.currentTarget.hasClass('grouping') == true) {
						expandOrCollapseGrouping(e.currentTarget);
					} else {
						addOptionNodeToSelectedGroupsList(e.currentTarget);
						cleanSelectedGroupsList();
					}


				},  Y.config.doc, SELECTORS.AVAILABLE_GRPS_SELECT_OPTIONS, this);

				selectedGroupsNode.on('click', function(e) {
					var selectedOptionsNodes = Y.all(SELECTORS.SELECTED_GRPS_SELECT + " option:checked");
					if (selectedOptionsNodes.size() >= 2) {
						removeGroupButtonNode.setContent(M.util.get_string('del_groups', 'choicegroup'));
						removeGroupButtonNode.set('disabled', false);
						uiInputLimitNode.set('disabled', true);
						//uiInputLimitNode.set('value', 'multiple values');
						limitInputUIDIVNode.hide();

					} else if (selectedOptionsNodes.size() >= 1) {
						removeGroupButtonNode.setContent(M.util.get_string('del_group', 'choicegroup'));
						removeGroupButtonNode.set('disabled', false);
						uiInputLimitNode.set('disabled', false);
						uiInputLimitNode.set('value', getInputLimitNodeOfSelectedGroupNode(selectedOptionsNodes.item(0)).get('value'));
						Y.one(SELECTORS.LIMIT_UI_LABEL).set('text', M.util.get_string('set_limit_for_group', 'choicegroup') + getGroupNameWithoutLimitText(selectedOptionsNodes.item(0)) + ":");
						if (limitAnswersSelectNode.get('value') == '1') { // limiting is enabled, show limit box
							limitInputUIDIVNode.show();
						}


					} else {
						removeGroupButtonNode.set('disabled', true);
						removeGroupButtonNode.setContent(M.util.get_string('del', 'choicegroup'));
						uiInputLimitNode.set('disabled', true);
						limitInputUIDIVNode.hide();
					}

				});

				uiInputLimitNode.on('change', function(e) { updateGroupLimit(e); });
				uiInputLimitNode.on('blur', function(e) { updateGroupLimit(e); });


				addGroupButtonNode.on('click', function(e) {
					var selectedOptionsNodes = Y.all(SELECTORS.AVAILABLE_GRPS_SELECT + " option:checked");
					selectedOptionsNodes.each(function(optNode) { addOptionNodeToSelectedGroupsList(optNode); });
					cleanSelectedGroupsList();
				});
				removeGroupButtonNode.on('click', function(e) {
					var selectedOptionsNodes = Y.all(SELECTORS.SELECTED_GRPS_SELECT + " option:checked");
					selectedOptionsNodes.each(function(optNode) {
							optNode.remove();

					});
				});

				limitAnswersSelectNode.on('change', function(e) {
					if (limitAnswersSelectNode.get('value') == '1') { // limiting is enabled, show limit box
						var selectedOptionsNodes = Y.all(SELECTORS.SELECTED_GRPS_SELECT + " option:checked");
						if (selectedOptionsNodes.size() == 1) {
							limitInputUIDIVNode.show();
						}
                        updateLimitUIOfAllSelectedGroups();

					} else { // limiting is disabled
						limitInputUIDIVNode.hide();
                        clearLimitUIFromAllSelectedGroups();
					}

				});


			},


	};
}, '@VERSION@', {requires: ['node', 'event'] });
