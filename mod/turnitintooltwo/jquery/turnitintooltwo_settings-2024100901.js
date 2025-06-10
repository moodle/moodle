/*
 * This small piece of script is a workaround that's needed to add tabs in to
 * the settings page. They need to be removed from the settings form and placed
 * outside that containing fieldset.
 */
jQuery(document).ready(function($) {
    if ($('.settingsform fieldset div.formsettingheading').length > 0) {
        var tabmenu = $('.settingsform fieldset div.formsettingheading:first').html();
        if (tabmenu.indexOf("tabtree") >= 0) {
            $('.settingsform fieldset div.formsettingheading:first').remove();
            $('.settingsform h2:first').after(tabmenu);
        }
    }

    $('input[name="selectallcb"]').click(function() {
        if ($(this).prop('checked')) {
            $('.browser_checkbox').prop('checked', true);
            if ($('.browser_checkbox:checked').length > 0) {
                $('.create_checkboxes').slideDown();
            } else {
                $('.create_checkboxes').slideUp();
            }
        } else {
            $('.browser_checkbox').prop('checked', false);
            $('.create_checkboxes').slideUp();
        }
    });

    if ($('.test_connection').length > 0) {
        if ($('#id_s_turnitintooltwo_accountid').val() != '' || $('#id_s_turnitintooltwo_secretkey').val() != '') {
            $('.test_connection').show();
            $('#test_link').show();
        }

        $('#id_s_turnitintooltwo_accountid, #id_s_turnitintooltwo_secretkey, #id_s_turnitintooltwo_apiurl').keyup(function() {
            $('#testing_container').hide();

            var accountid = $('#id_s_turnitintooltwo_accountid').val();
            var accountshared = $('#id_s_turnitintooltwo_secretkey').val();

            // Make sure they aren't empty strings.
            accountid = accountid.trim();
            accountshared = accountshared.trim();
            if (accountid.length == 0 || accountshared.length == 0) {
                $('#test_result').hide();
                $('.test_connection').hide();
            } else {
                $('.test_connection').show();
                $('#test_link').show();
            }
        });

        $('#test_link').click(function() {
            $('#test_result').hide();
            $('input, #id_s_turnitintooltwo_apiurl').prop('disabled', true);
            $('#test_link').hide();
            $("#test_result").css('opacity', '');
            $('#test_result').removeClass('test_link_success test_link_fail');
            $('#testing_container').show();

            // Change Url depending on Settings page.
            var url = "ajax.php";
            if ($('.settingsform fieldset div.formsettingheading').length > 0) {
                url = "../mod/turnitintooltwo/ajax.php";
            }

            var accountid = $('#id_s_turnitintooltwo_accountid').val();
            var accountshared = $('#id_s_turnitintooltwo_secretkey').val();
            var accounturl = $('#id_s_turnitintooltwo_apiurl').val();

            $.ajax({
                type: "POST",
                url: url,
                dataType: "json",
                data: {action: "test_connection", sesskey: M.cfg.sesskey, accountid: accountid, accountshared: accountshared, url: accounturl},
                success: function(data) {
                    eval(data);

                    $('#testing_container').hide();

                    if (data.connection_status == "success") {
                        $('#test_result').addClass('test_link_success');
                    } else {
                        $('#test_result').addClass('test_link_fail');
                    }

                    $('#test_result').html(data.msg);
                    $('#test_result').show();
                    $('#test_link').show();
                    $('input, #id_s_turnitintooltwo_apiurl').prop('disabled', false);
                }
            });
        });
    }
});