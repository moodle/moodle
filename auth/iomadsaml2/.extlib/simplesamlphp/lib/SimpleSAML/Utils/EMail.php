<?php

declare(strict_types=1);

namespace SimpleSAML\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSAML\XHTML\Template;

/**
 * E-mailer class that can generate a formatted e-mail from array
 * input data.
 *
 * @author Jørn Åne de Jong, Uninett AS <jorn.dejong@uninett.no>
 * @package SimpleSAMLphp
 */

class EMail
{
    /** @var array Dictionary with multivalues */
    private $data = [];

    /** @var string Introduction text */
    private $text = '';

    /** @var PHPMailer The mailer instance */
    private $mail;


    /**
     * Constructor
     *
     * If $from or $to is not provided, the <code>technicalcontact_email</code>
     * from the configuration is used.
     *
     * @param string $subject The subject of the e-mail
     * @param string $from The from-address (both envelope and header)
     * @param string $to The recipient
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function __construct($subject, $from = null, $to = null)
    {
        $this->mail = new PHPMailer(true);
        $this->mail->Subject = $subject;
        $this->mail->setFrom($from ?: static::getDefaultMailAddress());
        $this->mail->addAddress($to ?: static::getDefaultMailAddress());

        static::initFromConfig($this);
    }


    /**
     * Get the default e-mail address from the configuration
     * This is used both as source and destination address
     * unless something else is provided at the constructor.
     *
     * It will refuse to return the SimpleSAMLphp default address,
     * which is na@example.org.
     *
     * @return string Default mail address
     */
    public static function getDefaultMailAddress()
    {
        $config = Configuration::getInstance();
        $address = $config->getString('technicalcontact_email', 'na@example.org');
        $address = preg_replace('/^mailto:/i', '', $address);
        if ('na@example.org' === $address) {
            throw new \Exception('technicalcontact_email must be changed from the default value');
        }
        return $address;
    }


    /**
     * Set the data that should be embedded in the e-mail body
     *
     * @param array $data The data that should be embedded in the e-mail body
     * @return void
     */
    public function setData(array $data)
    {
        /*
         * Convert every non-array value to an array with the original
         * as its only element. This guarantees that every value of $data
         * can be iterated over.
         */
        $this->data = array_map(
            /**
             * @param mixed $v
             * @return array
             */
            function ($v) {
                return is_array($v) ? $v : [$v];
            },
            $data
        );
    }


    /**
     * Set an introduction text for the e-mail
     *
     * @param string $text Introduction text
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
    }


    /**
     * Add a Reply-To address to the mail
     *
     * @param string $address Reply-To e-mail address
     * @return void
     */
    public function addReplyTo($address)
    {
        $this->mail->addReplyTo($address);
    }


    /**
     * Send the mail
     *
     * @param bool $plainTextOnly Do not send HTML payload
     * @return void
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send($plainTextOnly = false)
    {
        if ($plainTextOnly) {
            $this->mail->isHTML(false);
            $this->mail->Body = $this->generateBody('mailtxt.twig');
        } else {
            $this->mail->isHTML(true);
            $this->mail->Body = $this->generateBody('mailhtml.twig');
            $this->mail->AltBody = $this->generateBody('mailtxt.twig');
        }

        $this->mail->send();
    }

    /**
     * Sets the method by which the email will be sent.  Currently supports what
     * PHPMailer supports: sendmail, mail and smtp.
     *
     * @param string $transportMethod the transport method
     * @param array $transportOptions options for the transport method
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function setTransportMethod($transportMethod, array $transportOptions = [])
    {
        assert(is_string($transportMethod));

        switch (strtolower($transportMethod)) {
            // smtp transport method
            case 'smtp':
                $this->mail->isSMTP();

                // set the host (required)
                if (isset($transportOptions['host'])) {
                    $this->mail->Host = $transportOptions['host'];
                } else {
                    // throw an exception otherwise
                    throw new \InvalidArgumentException("Missing Required Email Transport Parameter 'host'");
                }

                // set the port (optional, assume standard SMTP port 25 if not provided)
                $this->mail->Port = (isset($transportOptions['port'])) ? (int)$transportOptions['port'] : 25;

                // smtp auth: enabled if username or password is set
                if (isset($transportOptions['username']) || isset($transportOptions['password'])) {
                    $this->mail->SMTPAuth = true;
                }

                // smtp auth: username
                if (isset($transportOptions['username'])) {
                    $this->mail->Username = $transportOptions['username'];
                }

                // smtp auth: password
                if (isset($transportOptions['password'])) {
                    $this->mail->Password = $transportOptions['password'];
                }

                // smtp security: encryption type
                if (isset($transportOptions['secure'])) {
                    $this->mail->SMTPSecure = $transportOptions['secure'];
                }

                // smtp security: enable or disable smtp auto tls
                if (isset($transportOptions['autotls'])) {
                    $this->mail->SMTPAutoTLS = (bool)$transportOptions['autotls'];
                }
                break;
            //mail transport method
            case 'mail':
                $this->mail->isMail();
                break;
            // sendmail transport method
            case 'sendmail':
                $this->mail->isSendmail();

                // override the default path of the sendmail executable
                if (isset($transportOptions['path'])) {
                    $this->mail->Sendmail = $transportOptions['path'];
                }
                break;
            default:
                throw new \InvalidArgumentException(
                    "Invalid Mail Transport Method - Check 'mail.transport.method' Configuration Option"
                );
        }
    }

    /**
     * Initializes the provided EMail object with the configuration provided from the SimpleSAMLphp configuration.
     *
     * @param EMail $EMail
     * @return EMail
     * @throws \Exception
     */
    public static function initFromConfig(EMail $EMail)
    {
        $config = Configuration::getInstance();
        $EMail->setTransportMethod(
            $config->getString('mail.transport.method', 'mail'),
            $config->getArrayize('mail.transport.options', [])
        );

        return $EMail;
    }


    /**
     * Generate the body of the e-mail
     *
     * @param string $template The name of the template to use
     *
     * @return string The body of the e-mail
     */
    public function generateBody($template)
    {
        $config = Configuration::getInstance();
        $newui = $config->getBoolean('usenewui', false);

        if ($newui === false) {
            $result = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>SimpleSAMLphp Email report</title>
	<style type="text/css">
pre, div.box {
	margin: .4em 2em .4em 1em;
	padding: 4px;
}
pre {
	background: #eee;
	border: 1px solid #aaa;
}
	</style>
</head>
<body>
<h1>' . htmlspecialchars($this->mail->Subject) . '</h1>
<div class="container" style="background: #fafafa; border: 1px solid #eee; margin: 2em; padding: .6em;">
<blockquote>"' . htmlspecialchars($this->text) . '"</blockquote>
</div>';
            foreach ($this->data as $name => $values) {
                $result .= '<h2>' . htmlspecialchars($name) . '</h2><ul>';
                foreach ($values as $value) {
                    $result .= '<li><pre>' . htmlspecialchars($value) . '</pre></li>';
                }
                $result .= '</ul>';
            }
        } else {
            $t = new Template($config, $template);
            $twig = $t->getTwig();
            if (!isset($twig)) {
                throw new \Exception(
                    'Even though we explicitly configure that we want Twig,'
                        . ' the Template class does not give us Twig. This is a bug.'
                );
            }
            $result = $twig->render($template, [
                'subject' => $this->mail->Subject,
                'text' => $this->text,
                'data' => $this->data
            ]);
        }
        return $result;
    }
}
