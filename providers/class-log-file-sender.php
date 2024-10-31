<?php

/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * Scand_Multi_Mailer_LogFileSender class
 */

class Scand_Multi_Mailer_LogFileSender implements Scand_Multi_Mailer_ISender
{
    private $name = "LogFile";
    private $field_period = "period";
    private $path;
    private $always = '';
    private $day = "Y-m-d";
    private $week = "Y-W";
    private $month = "Y-m";

    function __construct()
    {
        $this->path = SCAND_MULTI_MAILER_DIR.'logs/';
    }

    public function get_name()
    {
        return $this->name;
    }

    /* @return string */
    public function get_form()
    {
        $form = "<table class=\"form-table\">
            <tr>
                <th>
                    ". __('Save into one file ', SCAND_MULTI_MAILER_TEXTDOMAIN ) ."
                </th>
                <td>
                    <label>
                        <input type='radio' name='{$this->field_period}' value='always' " .
                            ( $this->{$this->field_period} == 'always' ? 'checked="checked"' : '' ) . ">
                        ". __('Always', SCAND_MULTI_MAILER_TEXTDOMAIN ) ."
                    </label>
                    <label>
                        <input type='radio' name='{$this->field_period}' value='day' " .
                            ( $this->{$this->field_period} == 'day' ? 'checked="checked"' : '' ) . ">
                        ". __('Every Day', SCAND_MULTI_MAILER_TEXTDOMAIN ) ."
                    </label>
                    <label>
                        <input type='radio' name='{$this->field_period}' value='week' " .
                            ( $this->{$this->field_period} == 'week' ? 'checked="checked"' : '' ) . ">
                        ". __('Every Week', SCAND_MULTI_MAILER_TEXTDOMAIN ) ."
                    </label>
                    <label>
                        <input type='radio' name='{$this->field_period}' value='month' " .
                            ( $this->{$this->field_period} == 'month' ? 'checked="checked"' : '' ) . ">
                        ". __('Every Month', SCAND_MULTI_MAILER_TEXTDOMAIN ) ."
                    </label>
                </td>
            </tr>
        </table>";

        if( !empty( $this->{$this->field_period} ) && self::is_file_exist() ) {
            $form .= "<a class='button button-primary' href='" . plugins_url() . "/" . SCAND_MULTI_MAILER_FOLDER_NAME . "/logs/" . $this->get_filename() . "' download>Download Log File</a>";
        }

        return $form;
    }

    public function save_param($data)
    {
        $this->{$this->field_period} = isset( $data[ $this->field_period ] ) ? $data[ $this->field_period ] : '';
    }

    public function get_param()
    {
        $data[ $this->field_period ] = isset( $this->{$this->field_period} ) ? $this->{$this->field_period} : '';

        return $data;
    }

    /* @var $to String[] */
    public function send_message($to, $subject, $message, $from = null, $from_name = null)
    {
        $handle = self::open_file();
        $answer = false;
        if( !empty( $handle ) ) {
            if( self::write_str( $handle, "To: " . implode( ', ', $to ) ) &&
                self::write_str( $handle, "From: " . $from_name . " <" . $from . ">" ) &&
                self::write_str( $handle, "Subject: " . $subject ) &&
                self::write_str( $handle, "Message: \r\n" . $message ) )
                $answer = true;

            fclose( $handle );
        } else {
            error_log( "Cannot open file" );
        }

        return $answer;
    }

    private function write_str($fs, $string )
    {
        $time = date( 'Y.m.d H:i:s' );
        $answer = false;
        if( fwrite( $fs, $time . " ** " . $string . "\r\n" ) !== false ) {
            $answer = true;
        }

        return $answer;
    }

    private function open_file()
    {
        if( !is_dir( $this->path) ) {
            mkdir( $this->path );
        }
        $filename = $this->path . $this->get_filename();
        $handle = @fopen( $filename, 'a' );

        return $handle;
    }

    private function get_filename()
    {
        $postfix = date( $this->{$this->{$this->field_period}} );

        return "log_". $postfix . ".txt";
    }

    private function is_file_exist()
    {
        $answer = false;
        if( file_exists( $this->path . self::get_filename() )  ) {
            $answer = true;
        }

        return $answer;
    }
}