<?php

/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * Scand_Multi_Mailer_MailStdSender class
 */

class Scand_Multi_Mailer_MailStdSender implements Scand_Multi_Mailer_ISender
{

    private $name = "mail()";

    public function get_name()
    {
        return $this->name;
    }

    public function get_form()
    {
        $alert = __( "Local smtp setting will be used", "scand-multi-mailer" );

        return $alert;
    }

    public function save_param($data)
    {
        //Mail don't any have param
    }

    public function send_message($to, $subject, $message, $from = null, $from_name = null)
    {
        $message = str_replace("\n.", "\n..", $message);

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=' . get_bloginfo('charset') . "\r\n";
        $to_usr = '';
        if( !empty( $to ) && is_array( $to ) ) {

            foreach( $to as $to_mail ) {
                $to_usr .= "<$to_mail>, ";
            }
        }

        $headers .= "From: $from_name <$from>" . "\r\n";

        return mail( $to_usr, $subject, $message, $headers );
    }

    public function get_param()
    {
        return array();
    }
}