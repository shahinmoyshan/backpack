<?php

namespace Backpack\Lib;

use Hyper\Template;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Helper class for sending emails.
 *
 * Provides a wrapper around PHPMailer, with pre-configured settings from the
 * environment and a simpler API for sending emails.
 *
 * @package Backpack
 */
class Mailer
{
    /**
     * PHPMailer instance that is used to send emails.
     * @var PHPMailer
     *
     * @see https://github.com/PHPMailer/PHPMailer
     */
    private PHPMailer $mailer;

    /**
     * Constructor for the mailer class.
     *
     * Initializes the mailer instance with the specified configuration.
     *
     * The configuration array should contain the following keys:
     *
     *   - `sender`: An array with the sender email address and name.
     *   - `reply`: An array with the reply email address and name.
     *   - `smtp`: An array with SMTP configuration options.
     *
     * The SMTP configuration options should contain the following keys:
     *
     *   - `enabled`: A boolean indicating whether SMTP is enabled.
     *   - `host`: The SMTP host.
     *   - `port`: The SMTP port.
     *   - `username`: The SMTP username.
     *   - `password`: The SMTP password.
     *   - `encryption`: The encryption type (either 'tls' or 'ssl').
     *
     * @param array $config Optional configuration array for merging with mail env config.
     */
    public function __construct(private array $config = [])
    {
        $this->mailer = new PHPMailer();

        // Merger mail config with env config
        $this->config = array_merge(env('mail', []), $config);

        // Set SMTP configuration
        if (isset($this->config['smtp']['enabled']) && $this->config['smtp']['enabled']) {
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp']['host'];
            $this->mailer->Port = $this->config['smtp']['port'];

            // Enable SMTP authentication
            if (isset($this->config['smtp']['username']) && isset($this->config['smtp']['password'])) {
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = $this->config['smtp']['username'];
                $this->mailer->Password = $this->config['smtp']['password'];
            }

            // Set encryption type
            $this->mailer->SMTPSecure = (isset($this->config['smtp']['encryption']) && strtolower($this->config['smtp']['encryption']) === 'tls') ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        }
    }

    /**
     * Configure the mailer instance using a callback.
     *
     * This method takes a callback function that is given the PHPMailer
     * instance as its argument. The callback function is then free to
     * configure the mailer with any additional settings that are needed.
     *
     * @param callable $callback A callback function that takes a PHPMailer
     *                            instance as its argument.
     *
     * @return self
     */
    public function configure(callable $callback): self
    {
        $callback($this->mailer);
        return $this;
    }

    /**
     * Set the email body content using a template.
     *
     * This method allows you to set the email body content using a template.
     * The template is rendered using the Hyper\template engine.
     * The engine is configured to look for the template file in the mail
     * directory of the application.
     *
     * @param string $template The name of the template file to render.
     * @param array $data An associative array of data to pass to the template.
     * @return self Returns the instance of the class for method chaining.
     */
    public function template(string $template, array $data = []): self
    {
        return $this->content(
            get(Template::class)->render($template, $data),
            true
        );
    }

    /**
     * Set the email body content directly.
     *
     * This method allows you to set the email body content directly.
     * The second parameter determines whether the content is HTML or plain text.
     *
     * @param string $content The content of the email.
     * @param bool $isHtml Whether the content is HTML or plain text.
     * @return self Returns the instance of the class for method chaining.
     */
    public function content(string $content, bool $isHtml = false): self
    {
        $this->mailer->isHTML($isHtml);
        $this->mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $this->mailer->Body = $content;

        return $this;
    }

    /**
     * Send an email using the configured mailer instance.
     *
     * This method takes an associative array as its argument. The array
     * should contain the following elements:
     *
     *   - email: The email address of the recipient.
     *   - name: The name of the recipient.
     *   - subject: The subject of the email.
     *   - Optional - cc_address: An array of email addresses to add as CC.
     *   - Optional - bcc_address: An array of email addresses to add as BCC.
     *   - Optional - attachments: An array of paths to files to attach to the email.
     *
     * @param array $recipient An associative array of recipient information.
     *
     * @return bool Whether the email was sent successfully.
     */
    public function send(array $recipient): bool
    {
        // Set recipient address and subject
        $this->mailer->addAddress($recipient['email'], $recipient['name'] ?? '');
        $this->mailer->Subject = $recipient['subject'] ?? '';

        // Set sender address
        if (isset($recipient['render']['email'])) {
            $this->mailer->setFrom($recipient['sender']['email'], $recipient['sender']['name'] ?? '');
        } elseif (isset($this->config['sender']['email'])) {
            $this->mailer->setFrom($this->config['sender']['email'], $this->config['sender']['name'] ?? '');
        }

        // Set reply address
        if (isset($recipient['reply']['email'])) {
            $this->mailer->addReplyTo($recipient['reply']['email'], $recipient['reply']['name'] ?? '');
        } elseif (isset($this->config['reply']['email'])) {
            $this->mailer->addReplyTo($this->config['reply']['email'], $this->config['reply']['name'] ?? '');
        }

        // Set CC Address
        if (isset($recipient['cc_address']) && is_array($recipient['cc_address'])) {
            foreach ($recipient['cc_address'] as $cc) {
                if (is_array($cc)) {
                    $this->mailer->addCC($cc['email'], $cc['name'] ?? '');
                } else {
                    $this->mailer->addCC($cc);
                }
            }
        }

        // Set BCC Address
        if (isset($recipient['bcc_address']) && is_array($recipient['bcc_address'])) {
            foreach ($recipient['bcc_address'] as $bcc) {
                if (is_array($bcc)) {
                    $this->mailer->addBCC($bcc['email'], $bcc['name'] ?? '');
                } else {
                    $this->mailer->addBCC($bcc);
                }
            }
        }

        // Set attachments
        if (isset($recipient['attachments'])) {
            foreach ((array) $recipient['attachments'] ?? [] as $attachment) {
                if (is_array($attachment)) {
                    $this->mailer->addAttachment($attachment['path'], $attachment['name'] ?? '');
                } else {
                    $this->mailer->addAttachment($attachment);
                }
            }
        }

        return $this->mailer->send();
    }
}