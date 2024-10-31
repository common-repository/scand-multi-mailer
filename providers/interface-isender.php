<?php

/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * Scand_Multi_Mailer_ISender interface
 */
interface Scand_Multi_Mailer_ISender
{
    public function get_name();

    /* @return string */
    public function get_form();

    public function save_param( $data );

    public function get_param();

    /* @var $to String[] */
    public function send_message( $to, $subject, $message, $from = null, $from_name = null);

}