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

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://www.intelliboard.net/
 */


define(['jquery', 'core/ajax', 'core/log'], function($, ajax, log) {

    const ASValidator = {
        required: function(input) {
            return input.value.length > 0;
        },
        email: function(input) {
            var pattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
            return pattern.test(input.value);
        },
        tel: function(input) {
            var pattern = /^[+]*[(]{0,1}[0-9]{1,3}[)]{0,1}[-\s\./0-9]*$/;
            return pattern.test(input.value);
        },
        validate: function(input) {
            if (input.required) {
                if (typeof ASValidator[input.type] === "function") {
                    return ASValidator[input.type](input);
                } else {
                    return ASValidator.required(input);
                }
            }
        }
    };

    var AccountSetup = {
        forms: ["getstartedform", "accountform", "accounttypeform", "usertypeform"],

        validator: ASValidator,
        usertype: [],

        init: function(setup) {
            if (setup == true) {
                AccountSetup.forms = ["getstartedform", "thanksform"];
            } else {
                AccountSetup.initFormValidation("accountform");
                AccountSetup.initFormValidation("accounttypeform");
                AccountSetup.initAccountTypeSelection();
                AccountSetup.initUserTypeSelection();
                AccountSetup.setupSubmitAction();
            }
            AccountSetup.initNextAction();
            AccountSetup.initPrevAction();
        },
        getPrevForm: function(currentForm) {
            var pos = AccountSetup.forms.indexOf(currentForm);
            var prevForm = AccountSetup.forms[pos - 1];
            return prevForm.length ? prevForm : false;
        },
        getNextForm: function(currentForm) {
            var pos = AccountSetup.forms.indexOf(currentForm);
            var nextForm = AccountSetup.forms[pos + 1];
            return nextForm.length ? nextForm : false;

        },
        toggleForms: function(showForm, hideForm) {
            document.getElementById(hideForm).classList.add("intelliboard-hide");
            document.getElementById(showForm).classList.remove("intelliboard-hide");
        },
        getFormInputs: function(formId) {
            var form = document.getElementById(formId);
            return form.querySelectorAll('input, select');
        },
        setValidationState: function(input, valid) {
            if (valid) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }
        },
        initValidationAction: function(inputs) {
            inputs.forEach(function(input) {
                if (input.required) {
                    var event = 'input';
                    if (input.type === 'select') {
                        event = 'change';
                    }
                    input.addEventListener(event, function() {
                            AccountSetup.setValidationState(input, AccountSetup.validator.validate(input));
                    });
                }
            });
        },
        setHelpTextState: function(formId, visible) {
            var helptext = document.getElementById(formId).getElementsByClassName('form-help-text')[0];
            if (helptext) {
                if (visible) {
                    helptext.classList.replace('invisible', 'visible');
                } else {
                    helptext.classList.replace('visible', 'invisible');
                }
            }
        },
        setNextButtonState: function(formId, disabled) {
            var nextbutton = document.getElementById(formId).getElementsByClassName('next-btn')[0];
            if (nextbutton) {
                nextbutton.disabled = disabled;
                AccountSetup.setHelpTextState(formId, disabled);
            }
        },

        isFormValid: function(formId) {
            var formInputs = AccountSetup.getFormInputs(formId);
            for (var i = 0; i < formInputs.length; i++) {
                if (formInputs[i].required && !AccountSetup.validator.validate(formInputs[i])) {
                    return false;
                }
            }
            return true;
        },
        initFormValidation: function(formId) {
            var formInputs = AccountSetup.getFormInputs(formId);
            AccountSetup.initValidationAction(formInputs);
            for (var i = 0; i < formInputs.length; i++)  {
                var event = 'input';
                if (formInputs[i].type === 'select') {
                    event = 'change';
                }
                formInputs[i].addEventListener(event, function(e) {
                    AccountSetup.setNextButtonState(formId, !AccountSetup.isFormValid(formId));
                });
            }
        },
        initNextAction: function() {
            Array.from(document.getElementsByClassName("next-btn")).forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        var currentform =  e.currentTarget.getAttribute('data-form');
                        var nextform = AccountSetup.getNextForm(currentform);
                        AccountSetup.toggleForms(nextform, currentform);
                        var inputs = AccountSetup.getFormInputs(nextform);
                        if (inputs[0]) {
                            inputs[0].dispatchEvent(new Event("input"));
                        }
                        return false;
                    }, false);
            });

        },
        initPrevAction: function() {
            Array.from(document.getElementsByClassName("prev-btn")).forEach(function(button) {
                button.addEventListener('click', function(e) {
                    var currentform = e.currentTarget.getAttribute('data-form');
                    var prevform = AccountSetup.getPrevForm(currentform);
                    AccountSetup.toggleForms(prevform, currentform);
                    return false;
                }, false);
            });
        },
        initAccountTypeSelection: function() {
            var accounttypes = document.getElementsByClassName("accounttype");
            for (var i = 0; i < accounttypes.length; i++) {
                accounttypes[i].addEventListener('click', function(e) {
                    Array.from(accounttypes).forEach(function(element) {
                        element.classList.remove('active');
                        element.setAttribute('aria-pressed', 'false');
                    });
                    e.currentTarget.classList.add('active');
                    e.currentTarget.setAttribute('aria-pressed', 'true');
                    var accounttype = e.currentTarget.getAttribute('data-accounttype');
                    var input = document.getElementById('accounttype');
                    if (input.value != accounttype) {
                        AccountSetup.setupUserTypeForm(accounttype);
                    }
                    input.value = accounttype;
                    input.dispatchEvent(new Event("input"));
                    return false;
                }, false);
            }
        },
        setupUserTypeForm: function(accounttype) {
                AccountSetup.usertype = [];
                Array.from(document.getElementsByClassName("usertype")).forEach(function(ut) {
                    ut.classList.remove('active');
                    ut.setAttribute('aria-pressed', 'false');
                });
                document.getElementById('submitdata').disabled = true;
                Array.from(document.getElementsByClassName('intelliboard-user-types')).forEach(function(form) {
                    if (form.id === accounttype) {
                        form.classList.remove("intelliboard-hide");
                    } else {
                        form.classList.add("intelliboard-hide");
                    }
                });
        },
        initUserTypeSelection: function() {
            var usertypes = document.getElementsByClassName("usertype");
            for (var i = 0; i < usertypes.length; i++) {
                usertypes[i].addEventListener('click', function(e) {
                    var el = e.currentTarget;
                    var val = el.getAttribute('data-usertype');
                    if (AccountSetup.usertype.indexOf(val) >= 0) {
                        AccountSetup.usertype.splice(AccountSetup.usertype.indexOf(val), 1);
                        el.classList.remove('active');
                        el.setAttribute('aria-pressed', 'false');
                    } else {
                        AccountSetup.usertype.push(val);
                        el.classList.add('active');
                        el.setAttribute('aria-pressed', 'true');
                    }
                    var isFormValid = AccountSetup.usertype.length > 0;
                    document.getElementById('submitdata').disabled = !isFormValid;
                    AccountSetup.setHelpTextState('usertypeform', !isFormValid);
                    return false;
                }, false);
            }
        },
        setupSubmitAction: function() {
            var submit = document.getElementById('submitdata');
            submit.addEventListener("click", function(e) {
                var forms = document.getElementsByClassName('intelliboard-splash-page');
                var data = {};
                data.usertype = AccountSetup.usertype.toString();
                Array.from(forms).forEach(function(form) {
                    form.querySelectorAll('input, select').forEach(function(input) {
                        data[input.name] = input.value;
                    });
                    if (form.id === 'thanksform') {
                        form.classList.remove("intelliboard-hide");
                    } else {
                        form.classList.add("intelliboard-hide");
                    }
                });
                AccountSetup.sendData(data);
            }, false);
        },
        sendData: function(data) {
            ajax.call([{
                methodname: 'local_intelliboard_account_setup',
                args: {
                    params: data
                }
            }]);
        }
    };

    return AccountSetup;

});