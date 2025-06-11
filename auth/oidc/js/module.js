/*global $, M, sessionStorage*/

M.auth_oidc = {};

M.auth_oidc.init = function(Y, idptype_ms, authmethodsecret, authmethodcertificate, authmethodcertificatetext) {
    var $idptype = $("#id_idptype");
    var $clientauthmethod = $("#id_clientauthmethod");
    var $clientsecret = $("#id_clientsecret");
    var $clientcert = $("#id_clientcert");
    var $clientprivatekey = $("#id_clientprivatekey");
    var $clientprivatekeyfile = $("#id_clientprivatekeyfile");
    var $clientcertfile = $("#id_clientcertfile");
    var $clientcertpassphrase = $("#id_clientcertpassphrase");
    var $clientcertsource = $("#id_clientcertsource");
    var $secretexpiryrecipients = $("#id_secretexpiryrecipients");

    $idptype.change(function() {
        if ($(this).val() != idptype_ms) {
            $("#id_clientauthmethod option[value='" + authmethodcertificate + "']").each(function() {
                $(this).remove();
            });
            $clientauthmethod.val(authmethodsecret);
            $clientsecret.prop('disabled', false);
            $clientcertsource.prop('disabled', true);
            $clientcert.prop('disabled', true);
            $clientprivatekey.prop('disabled', true);
            $clientprivatekeyfile.prop('disabled', true);
            $clientcertfile.prop('disabled', true);
            $clientcertpassphrase.prop('disabled', true);
            $secretexpiryrecipients.prop('disabled', false);
        } else {
            $clientauthmethod.append("<option value='" + authmethodcertificate + "'>" + authmethodcertificatetext + "</option>");
        }
    });

    $clientauthmethod.change(function() {
        if ($(this).val() == authmethodcertificate) {
            if ($clientcertsource.val() == 'file') {
                $clientcert.prop('disabled', true);
                $clientprivatekey.prop('disabled', true);
                $clientprivatekeyfile.prop('disabled', false);
                $clientcertfile.prop('disabled', false);
            } else {
                $clientcert.prop('disabled', false);
                $clientprivatekey.prop('disabled', false);
                $clientprivatekeyfile.prop('disabled', true);
                $clientcertfile.prop('disabled', true);
            }
            $clientcertpassphrase.prop('disabled', false);
            $clientcertsource.prop('disabled', false);
        } else {
            $secretexpiryrecipients.prop('disabled', false);
        }
    });
};
