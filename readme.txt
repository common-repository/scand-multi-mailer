=== MultiMailer ===
Contributors: scandltd
Tags: scandltd, smtp, contact form, email log, php mailer
Requires at least: 5.5.0
Tested up to: 6.6.2
Stable tag: 1.0.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Send data from one contact form to multiple email addresses or save data into log file.

== Description ==

This WordPress email plugin allows you to intercept an email that is sent by wp_mail() function and duplicates it as many times as you need. Then, upon your request, the plugin could process it in the following way:

* send the email to multiple recipients;
* differentiate data sets while sending it to multiple recipients;
* add the information about the emails sent to log;
* add some text before or after the message body;
* replace sender's text, name (field From Name), subject, and even the recipients if needed.

You can set the following options:

* Specify the form name and email address for the outgoing email.
* Specify the email(s) of recipient (s).
* Specify the text that could be prepended to the message body.
* Choose to send the mail by SMTP or PHP's mail() function.
* Specify the SMTP settings: host, port, username and password.
* Choose SSL / TLS encryption (different from STARTTLS).
* Choose to use of SMTP authentication or not (as it is by default).

== Installation ==

= WordPress installation =
1. Go to Plugins > Add New > search for "scand-multi-mailer"
1. Press "Install Now" button for the "MultiMailer" plugin
1. Press "Activate" button

= Manual installation =
1. Upload "scand-multi-mailer" directory to "/wp-content/plugins/" directory
1. Activate our WordPress SMTP plugin through the "Plugins" menu in WordPress

== Frequently Asked Questions ==

= My plugin still sends mail via the mail() function =
If other plugins that you're using are not designed to use the `wp_mail()` function and call PHP's `mail()` function directly, they will skip the settings of this plugin. As the matter of fact, you can edit other plugins and replace the "`mail(`" calls with "`wp_mail(`" (by adding wp_ in front) and this will work. We performed tests on a couple of plugins and it worked, but it may not work with all plugins.

= Will this plugin work with WordPress versions less than 2.7? =
No. The options page will only work on 2.7 version and higher.

= Can I use this plugin to send emails via Gmail / Google Apps =
Yes. Use the following settings:

* Mailer: SMTP
* SMTP Host: smtp.gmail.com
* SMTP Port: 587
* Encryption: TLS
* Authentication: Yes
* Username: your Gmail account name
* Password: your mail password

Note: adjust your Gmail account's security settings for it to enable Google service of sending emails via SMTP

== Screenshots ==

1. List of providers
2. General settings
3. Provider settings: PHP Mailer
4. Provider settings: Log file

== Changelog ==
= 1.0.4 (2024-10-18) =
Escaped for output in an HTML attributes to prevent XSS attacks.

= 1.0.3 (2021-01-05) =
External Library updates: PHPMailer

= 1.0.2 (2018-12-11) =
Fixed minor issues (testing 5.0)

= 1.0.1 (2018-01-18) =
Added functionality to send attachments.
Added Russian translation.
Changed way to work with provider settings.

= 1.0.0 (2017-02-20) =
Release of the plugin.