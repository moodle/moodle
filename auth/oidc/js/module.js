/*global $, M, sessionStorage*/

M.auth_oidc = {};

M.auth_oidc.init = function(Y, idptype_ms, authmethodsecret, authmethodcertificate, authmethodcertificatetext) {
    var $idptype = $("#id_idptype");
    var $clientauthmethod = $("#id_clientauthmethod");
    var $clientsecret = $("#id_clientsecret");
    var $clientcert = $("#id_clientcert");
    var $clientprivatekey = $("#id_clientprivatekey");

    $idptype.change(function() {
        if ($(this).val() != idptype_ms) {
            $("#id_clientauthmethod option[value='" + authmethodcertificate + "']").each(function() {
                $(this).remove();
            });
            $clientauthmethod.val(authmethodsecret);
            $clientsecret.prop('disabled', false);
            $clientcert.prop('disabled', true);
            $clientprivatekey.prop('disabled', true);
        } else {
            $clientauthmethod.append("<option value='" + authmethodcertificate + "'>" + authmethodcertificatetext + "</option>");
        }
    });

    $clientauthmethod.change(function() {
        if ($(this).val() == authmethodcertificate) {
            $clientcert.prop('disabled', false);
            $clientprivatekey.prop('disabled', false);
        }
    });
};
