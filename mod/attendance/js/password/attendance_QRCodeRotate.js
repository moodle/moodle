/**
 *
 * @copyright  2019 Maksud R
 * @package   mod_attendance
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

class attendance_QRCodeRotate {

    constructor() {
        this.sessionId = 0;
        this.password = "";
        this.qrCodeInstance = "";
        this.qrCodeHTMLElement = "";
    }

    start(sessionId, qrCodeHTMLElement, textPasswordHTMLElement, timerHTMLElement) {
        this.sessionId = sessionId;
        this.qrCodeHTMLElement = qrCodeHTMLElement;
        this.textPasswordHTMLElement = textPasswordHTMLElement;
        this.timerHTMLElement = timerHTMLElement;
        this.fetchAndRotate();
    }

    qrCodeSetUp() {
        this.qrCodeInstance = new QRCode(this.qrCodeHTMLElement, {
            text: '',
            width: 328,
            height: 328,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    }

    changeQRCode(password) {
        var qrcodeurl = document.URL.substr(0,document.URL.lastIndexOf('/')) + '/attendance.php?qrpass=' + password + '&sessid=' + this.sessionId;
        this.qrCodeInstance.clear();
        this.qrCodeInstance.makeCode(qrcodeurl);
        // display new password
        this.textPasswordHTMLElement.innerHTML = '<h2>'+password+'</h2>';
    }

    updateTimer(timeLeft) {
        this.timerHTMLElement.innerHTML = '<h3>Time left: '+timeLeft+'</h3>';
    }

    startRotating() {
        var parent = this;

        setInterval(function() {
            var found = Object.values(parent.password).find(function(element) {

                if (element.expirytime > Math.round(new Date().getTime() / 1000)) {
                    return element;
                }
            });

            if (found == undefined) {
                location.reload(true);
            } else {
                parent.changeQRCode(found.password);
                parent.updateTimer(found.expirytime - Math.round(new Date().getTime() / 1000));

            }

        }, 1000);

    }

    fetchAndRotate() {
        var parent = this;

        fetch('password.php?session='+this.sessionId+'&returnpasswords=1', {
                headers: {
                    'Content-Type': 'application/json; charset=utf-8'
                }
            })
            .then((resp) => resp.json()) // Transform the data into json
            .then(function(data) {
                parent.password = data;
                parent.qrCodeSetUp();
                // this.changeQRCode( password );
                parent.startRotating();
            }).catch(err => {
                console.error("Error fetching QR passwords from API.");
        });
    }
}