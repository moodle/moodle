<?php

/**
 * Renderer class to format the email logging information
 * Implements the LoggerRenderer. How is the email_log_info writen in the logfile
 *
 * Initially created for the logging out of the moodle class message_output_email
 * in easy CSV-Format for analysing.
 *
 * @package email_logging
 * @author Roger Barras
 * @date 10.03.2016
 */

class email_log_renderer implements LoggerRenderer {
    public function render($maillog) {
        return ";{$maillog->smtptime};{$maillog->savedmessageid};{$maillog->targetmailadress};{$maillog->subject}";
    }
}
