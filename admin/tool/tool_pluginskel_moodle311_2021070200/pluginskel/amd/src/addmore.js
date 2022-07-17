define(['jquery'], function($) {

    var addElements = {

        /**
         * Deletes a an array template variable value.
         *
         * @param {Array} templateVars The template variables.
         * @param {String} variableNames The recipe hierarchy for the variable.
         * @param {Boolean} isComponentFeature If the elements are a component feature.
         */
        deleteElements: function(templateVars, variableNames, isComponentFeature) {

            var variableName;
            var idPrefix;
            var i;

            if (isComponentFeature) {
                var componentFeatures = variableNames[0];
                variableName = variableNames[1];
                idPrefix = componentFeatures + '_' + variableName;
            } else {
                variableName = variableNames[0];
                idPrefix = variableName;
            }

            var countName = idPrefix + 'count';
            var variableCount = parseInt($('[name="' + countName + '"]').val());

            var templateVariable;
            for (i in templateVars) {
                if (templateVars[i].name == variableName) {
                    templateVariable = templateVars[i];
                    break;
                }
            }

            if (variableCount == 1) {
                var elementIdPrefix = 'id_' + idPrefix + '_0';
                addElements.removeInputFromNode(templateVariable, elementIdPrefix);
                return;
            }

            var lastIndex = variableCount - 1;

            // Removing the newline before the element that will be deleted.
            $('#id_' + idPrefix + ' br').last().remove();

            for (i in templateVariable.values) {
                var fieldVariable = templateVariable.values[i];
                var lastElementDiv = 'fitem_id_' + idPrefix + '_' + lastIndex + '_' + fieldVariable.name;

                $('#' + lastElementDiv).remove();

                if (fieldVariable.type === 'numeric-array') {

                    var nestedCountName = idPrefix + '_' + lastIndex + '_' + fieldVariable.name + 'count';
                    var nestedCount = parseInt($("[name='" + nestedCountName + "']").val());

                    // Removing the newlines between the nested array elements.
                    if (nestedCount >= 2) {
                        $('#id_' + idPrefix + ' br').slice(-(nestedCount - 1)).remove();
                    }

                    for (var j in fieldVariable.values) {

                        var nestedVariable = fieldVariable.values[j];

                        for (var k = 0; k < nestedCount; k++) {
                            var nestedDiv = lastElementDiv + '_' + k + '_' + nestedVariable.name;
                            $('#' + nestedDiv).remove();
                        }
                    }

                    // Removing the 'Add more ..' button.
                    var buttonDivId = 'fitem_id_addmore_' + idPrefix + '_' + lastIndex + '_' + fieldVariable.name;
                    $('#' + buttonDivId).remove();

                    // Remove the number of nested variables.
                    $("[name='" + nestedCountName + "']").remove();
                }
            }

            // Decrement the number of variable elements.
            $('[name="' + countName + '"]').val(variableCount - 1);
        },

        /**
         * Adds more elements to a fieldset located at the root of the document.
         *
         * @param {Array} templateVars The template variables.
         * @param {String} variableNames The recipe hierarchy for the variable.
         * @param {Boolean} isComponentFeature If the elements are a component feature.
         */
        addMoreRootElements: function(templateVars, variableNames, isComponentFeature) {

            var variableName;
            var namePrefix;
            var idPrefix;
            var i;

            if (isComponentFeature) {
                var componentFeatures = variableNames[0];
                variableName = variableNames[1];
                namePrefix = componentFeatures + '[' + variableName + ']';
                idPrefix = componentFeatures + '_' + variableName;
            } else {
                variableName = variableNames[0];
                namePrefix = variableName;
                idPrefix = variableName;
            }

            var countName = idPrefix + 'count';
            var variableCount = parseInt($('[name="' + countName + '"]').val());

            var templateVariable;
            for (i in templateVars) {
                if (templateVars[i].name == variableName) {
                    templateVariable = templateVars[i];
                    break;
                }
            }

            var prevIndex = variableCount - 1;
            var elementIdPrefix;
            var newElements = '<br/>';

            for (i in templateVariable.values) {
                var fieldVariable = templateVariable.values[i];

                var newElementName = namePrefix + '[' + variableCount + '][' + fieldVariable.name + ']';
                var newElementId = 'id_' + idPrefix + '_' + variableCount + '_' + fieldVariable.name;

                var prevElementName = namePrefix + '[' + prevIndex + '][' + fieldVariable.name + ']';
                var prevElementId = 'id_' + idPrefix + '_' + prevIndex + '_' + fieldVariable.name;

                if (fieldVariable.type == 'numeric-array') {

                    // Cloning the static label.
                    var prevElementClasses = $('#fitem_' + prevElementId).attr('class');
                    newElements += '<div id="fitem_' + newElementId + '" class="' + prevElementClasses + '">';
                    newElements += $('#fitem_' + prevElementId).html();
                    newElements += '</div>';

                    for (var j in fieldVariable.values) {
                        var nestedVariable = fieldVariable.values[j];

                        var newNestedElementName = newElementName + '[0][' + nestedVariable.name + ']';
                        var newNestedElementId = newElementId + '_0_' + nestedVariable.name;

                        var prevNestedElementName = prevElementName + '[0][' + nestedVariable.name + ']';
                        var prevNestedElementId = prevElementId + '_0_' + nestedVariable.name;

                        var prevNestedIndex = prevIndex + '.0';
                        var newNestedIndex = variableCount + '.0';
                        newElements += addElements.copyElement(prevNestedElementName, prevNestedElementId,
                                                               newNestedElementName, newNestedElementId,
                                                               prevNestedIndex, newNestedIndex);
                    }

                    // Cloning the 'Add more ..' button.
                    var newButtonId = 'id_addmore_' + idPrefix + '_' + variableCount + '_' + fieldVariable.name;
                    var newButtonName = 'addmore_' + namePrefix + '[' + variableCount + '][' + fieldVariable.name + ']';

                    var prevButtonId = 'id_addmore_' + idPrefix + '_' + prevIndex + '_' + fieldVariable.name;
                    var prevButtonName = 'addmore_' + namePrefix + '[' + prevIndex + '][' + fieldVariable.name + ']';

                    newElements += addElements.copyElement(prevButtonName, prevButtonId, newButtonName, newButtonId,
                                                           null, null);

                    // Adding the hidden count field.
                    var prevCountName = idPrefix + '_' + prevIndex + '_' + fieldVariable.name + 'count';
                    var newCountName = idPrefix + '_' + variableCount + '_' + fieldVariable.name + 'count';
                    var newCountHtml = '<input name="' + newCountName + '" value="1" type="hidden"></input>';
                    $('[name="' + prevCountName + '"]').after(newCountHtml);

                } else {
                    newElements += addElements.copyElement(prevElementName, prevElementId, newElementName, newElementId,
                                                           prevIndex, variableCount);
                }
            }

            $('#fgroup_id_buttons_' + idPrefix).before(newElements);

            elementIdPrefix = 'id_' + idPrefix + '_' + variableCount;
            addElements.removeInputFromNode(templateVariable, elementIdPrefix);

            // Increment the number of variable elements.
            $('[name="' + countName + '"]').val(variableCount + 1);
        },

        /**
         * Adds more elements nested inside a fieldset element.
         *
         * @param {Array} templateVars The template variables.
         * @param {String} variableNames The recipe hierarchy for the variable.
         * @param {Boolean} isComponentFeature If the elements are a component feature.
         * @param {Boolean} isPartOfAssocArray If the elements to add are part of an associative array variable.
         */
        addMoreNestedElements: function(templateVars, variableNames, isComponentFeature, isPartOfAssocArray) {

            var namePrefix;
            var idPrefix;
            var parentVariableName;
            var variableName;
            var topIndex;
            var i;

            if (isPartOfAssocArray) {
                if (isComponentFeature) {
                    // Componenenttype_features[<parent associative array>][<numeric array>].
                    namePrefix = variableNames[0] + '[' + variableNames[1] + '][' + variableNames[2] + ']';
                    idPrefix = variableNames[0] + '_' + variableNames[1] + '_' + variableNames[2];
                    parentVariableName = variableNames[1];
                    variableName = variableNames[2];
                } else {
                    // Parent associative array[numeric array].
                    namePrefix = variableNames[0] + '[' + variableNames[1] + ']';
                    idPrefix = variableNames[0] + '_' + variableNames[1];
                    parentVariableName = variableNames[0];
                    variableName = variableNames[1];
                }
                topIndex = null;
            } else {
                if (isComponentFeature) {
                    // Componenenttype_features[<parent numeric array>][<index>][<numeric array>].
                    namePrefix = variableNames[0] + '[' + variableNames[1] + '][' + variableNames[2] + ']';
                    namePrefix = namePrefix + '[' + variableNames[3] + ']';
                    idPrefix = variableNames[0] + '_' + variableNames[1] + '_' + variableNames[2] + '_' + variableNames[3];
                    parentVariableName = variableNames[1];
                    variableName = variableNames[3];
                    topIndex = variableNames[2];
                } else {
                    // Parent numeric array[<index>][<numeric array>].
                    namePrefix = variableNames[0] + '[' + variableNames[1] + '][' + variableNames[2] + ']';
                    idPrefix = variableNames[0] + '_' + variableNames[1] + '_' + variableNames[2];
                    parentVariableName = variableNames[0];
                    variableName = variableNames[2];
                    topIndex = variableNames[1];
                }
            }

            var countName = idPrefix + 'count';
            var variableCount = parseInt($('[name="' + countName + '"]').val());

            var templateVariable;
            for (i in templateVars) {
                if (templateVars[i].name == parentVariableName) {
                    for (var j in templateVars[i].values) {
                        if (templateVars[i].values[j].name == variableName) {
                            templateVariable = templateVars[i].values[j];
                            break;
                        }
                    }
                }
            }

            var prevIndex = variableCount - 1;
            var newElements = '<br/>';
            for (i in templateVariable.values) {

                var fieldVariable = templateVariable.values[i];

                var newElementName = namePrefix + '[' + variableCount + '][' + fieldVariable.name + ']';
                var newElementId = 'id_' + idPrefix + '_' + variableCount + '_' + fieldVariable.name;

                var prevElementName = namePrefix + '[' + prevIndex + '][' + fieldVariable.name + ']';
                var prevElementId = 'id_' + idPrefix + '_' + prevIndex + '_' + fieldVariable.name;

                var prevIndexPrefix;
                var newIndexPrefix;
                if (topIndex === null) {
                    prevIndexPrefix = prevIndex;
                    newIndexPrefix = variableCount;
                } else {
                    prevIndexPrefix = topIndex + '.' + prevIndex;
                    newIndexPrefix = topIndex + '.' + variableCount;
                }
                newElements += addElements.copyElement(prevElementName, prevElementId,
                                                        newElementName, newElementId,
                                                        prevIndexPrefix, newIndexPrefix);
            }

            $('#fitem_id_addmore_' + idPrefix).before(newElements);

            var elementIdPrefix = 'id_' + idPrefix + '_' + variableCount;
            addElements.removeInputFromNode(templateVariable, elementIdPrefix);

            // Increment the number of variable elements.
            $('[name="' + countName + '"]').val(variableCount + 1);

        },

        /**
         * Removes previous input from a node.
         *
         * @param {Array} templateVariable The variable from which the added elements were created.
         * @param {String} elementIdPrefix The prefix for the elements' id.
         */
        removeInputFromNode: function(templateVariable, elementIdPrefix) {

            var i;

            for (i in templateVariable.values) {

                var fieldVariable = templateVariable.values[i];
                var newElementId = elementIdPrefix + '_' + fieldVariable.name;

                if (fieldVariable.type == 'text' || fieldVariable.type == 'int') {
                    $('#' + newElementId).val('');
                }

                // Non required boolean variables are represented as a select element.
                if (fieldVariable.type == 'multiple-options' || fieldVariable.type == 'boolean') {
                    var isRequired = ('required' in fieldVariable) && fieldVariable.required === true;
                    if (!isRequired) {
                        $('#' + newElementId + ' option[value="undefined"]').prop('selected', true).change();
                    }
                }

                if (fieldVariable.type == 'numeric-array') {
                    var countName = newElementId.replace('id_', '') + 'count';
                    var variableCount = parseInt($('[name="' + countName + '"]').val());

                    for (var j = 0; j < variableCount; j++) {
                        var newIdPrefix = newElementId + '_' + j;
                        addElements.removeInputFromNode(fieldVariable, newIdPrefix);
                    }
                }
            }
        },

        /**
         * Creates a clone of a previous element.
         *
         * @param {String} prevElementName
         * @param {String} prevElementId
         * @param {String} newElementName
         * @param {String} newElementId
         * @param {String} prevIndex
         * @param {String} newIndex
         * @returns {String}
         */
        copyElement: function(prevElementName, prevElementId, newElementName, newElementId, prevIndex, newIndex) {

            var prevElementClasses = $('#fitem_' + prevElementId).attr('class');
            var newElementHtml = $('#fitem_' + prevElementId).html();

            while (newElementHtml.indexOf(prevElementId) != -1) {
                newElementHtml = newElementHtml.replace(prevElementId, newElementId);
            }

            while (newElementHtml.indexOf(prevElementName) != -1) {
                newElementHtml = newElementHtml.replace(prevElementName, newElementName);
            }

            if (prevIndex !== null) {
                prevIndex = prevIndex + '. ';
                newIndex = newIndex + '. ';
                newElementHtml = newElementHtml.replace('label for="' + newElementId + '">' + prevIndex,
                                                        'label for="' + newElementId + '">' + newIndex);
            }

            var newElement = '<div id="fitem_' + newElementId + '" class="' + prevElementClasses + '">';
            newElement += newElementHtml;
            newElement += '</div>';

            return newElement;
        },

        /**
         * Adds or remove fields from an array template variable.
         */
        addMore: function() {

            $(document).ready(function() {
                $('.mform').on('click', ':button', function(event) {
                    var buttonName = event.target.name;

                    if (buttonName.indexOf('addmore_') !== -1 || buttonName.indexOf('delete_') !== -1) {
                        var templateVars = $.parseJSON($('[name="templatevars"]').val());
                        var componentType = $('[name="componenttype1"]').val();
                        var componentFeatures = componentType + '_features';

                        var shouldAdd;
                        var variableName;
                        if (buttonName.indexOf('addmore_') !== -1) {
                            shouldAdd = true;
                            variableName = buttonName.replace('addmore_', '');
                        } else {
                            variableName = buttonName.replace('delete_', '');
                            shouldAdd = false;
                        }

                        var variableNames = variableName.split(/\]\[|\[|\]/gi);
                        // Removing last empty element added by split.
                        if (variableNames[variableNames.length - 1].length === 0) {
                            variableNames.pop();
                        }

                        var isComponentFeature;
                        if (variableNames[0] === componentFeatures) {
                            isComponentFeature = true;
                        } else {
                            isComponentFeature = false;
                        }

                        var isNested = false;
                        for (var i in variableNames) {
                            if ($.isNumeric(variableNames[i])) {
                                isNested = true;
                                break;
                            }
                        }

                        var isPartOfAssocArray = false;
                        if (!isNested) {
                            if (isComponentFeature && variableNames.length === 3) {
                                isNested = true;
                                isPartOfAssocArray = true;
                            }

                            if (!isComponentFeature && variableNames.length === 2) {
                                isNested = true;
                                isPartOfAssocArray = true;
                            }
                        }

                        if (shouldAdd) {
                            if (isNested) {
                                addElements.addMoreNestedElements(templateVars, variableNames, isComponentFeature,
                                                                  isPartOfAssocArray);
                            } else {
                                addElements.addMoreRootElements(templateVars, variableNames, isComponentFeature);
                            }
                        } else {
                            addElements.deleteElements(templateVars, variableNames, isComponentFeature);
                        }
                    }
                });
            });
        },
    };

    return addElements;
});
