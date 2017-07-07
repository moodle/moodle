<?php

/**
 * EmailLogInfo class to store the information for the logging
 * Contains the definiton of the email logging used by log4php
 * for rendnering with the LoggerLayoutSimple with a proper rendering class
 *
 * Initially created for the logging out of the moodle class message_output_email
 *
 * @package email_logging
 * @author Roger Barras
 * @date 10.03.2016
 */

class email_log_info {
    public $savedmessageid;
    public $targetmailadress;
    public $subject;
    public $smtptime;
}
