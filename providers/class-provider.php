<?php

/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * Scand_Multi_Mailer_Provider class
 */

class Scand_Multi_Mailer_Provider
{
    const short_code_message = "[message]";
    private $id;
    private $name;
    /* @var Scand_Multi_Mailer_ISender */
    private $sender;
    private $send_to;
    private $send_from;
    private $send_from_name;
    private $message = self::short_code_message;
    private $update;
    private $subject;

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function get_subject()
    {
        return $this->subject;
    }

    public function set_subject($subject)
    {
        $this->subject = $subject;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_send_to()
    {
        return $this->send_to;
    }

    public function set_send_to($send_to)
    {
        $this->send_to = $send_to;
    }

    public function get_send_from_name()
    {
        return $this->send_from_name;
    }

    public function set_send_from_name($send_from_name)
    {
        $this->send_from_name = $send_from_name;
    }

    public function get_send_from()
    {
        return $this->send_from;
    }

    public function set_send_from($send_from)
    {
        $this->send_from = $send_from;
    }

    public function get_message()
    {
        return stripslashes( $this->message );
    }

    public function set_message($message)
    {
        $this->message = $message;
    }

    public function get_update()
    {
        return $this->update;
    }

    public function set_update($update)
    {
        $this->update = $update;
    }

    public function get_sender_data()
    {
        if( isset( $this->sender ) ) {
            return $this->sender->get_param();
        }

        return array();
    }

    public function send_test_message($to, $subject, $message)
    {
        $result = null;
        if( isset( $this->sender ) ) {
            $result = $this->sender->send_message(
                array($to),
                $subject,
                $this->transform_message($message),
                $this->send_from,
                $this->send_from_name
            );
        }

        return $result;
    }

    public function send_message($to, $subject, $message)
    {
        $result = null;
        if( isset( $this->sender ) ) {
            $result = $this->sender->send_message(
                !empty( $this->send_to ) ? $this->transformSendTo() : array($to),
                !empty( $this->subject ) ? $this->subject : $subject,
                $this->transform_message( $message),
                $this->send_from,
                $this->send_from_name
            );
        }

        return $result;
    }

    private function transform_message($message )
    {
        return str_replace( self::short_code_message, $message, self::get_message() );
    }

    private function transformSendTo()
    {
        if( !empty( $this->send_to ) ) {
            $to = explode( ', ', $this->send_to );

            return $to;
        }
        return array();
    }

    public function set_sender(Scand_Multi_Mailer_ISender $sender )
    {
        $this->sender = $sender;
    }

    public function get_form_sender()
    {
        return (isset( $this->sender)) ? $this->sender->get_form() : "";
    }

    public function set_sender_param($param )
    {
        if( isset( $this->sender ) ) {
            $this->sender->save_param( $param );
        }
    }

    public function get_sender()
    {
        return $this->sender;
    }

    public function get_sender_name()
    {
        return ( isset( $this->sender ) ) ? $this->sender->get_name() : "";
    }

}