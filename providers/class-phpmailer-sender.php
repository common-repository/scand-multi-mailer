<?php

/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * Scand_Multi_Mailer_PHPMailerSender
 */
require_once( 'class-password-encryption.php' );

class Scand_Multi_Mailer_PHPMailerSender implements Scand_Multi_Mailer_ISender
{

    private $name = "PHP Mailer";

    private $param_host = "host";
    private $param_port = "port";
    private $param_smtp_auth = "smtp_auth";
    private $param_username = "username";
    private $param_password = "password";
    private $param_smtp_secure = "smtp_secure";
    private $param_is_obfuscated = "is_obfuscated";

    public function get_name()
    {
        return $this->name;
    }

    public function get_form()
    {
        if ( ! empty($_POST) && (int)$_POST['is_provider_form'] == 0 ) {
            if ( ! array_key_exists('smtp_secure', $_POST) ) {
                $_POST['smtp_secure'] = '';
            }
        }

        $error = isset($GLOBALS['scand_error']) ? true : false;
        unset( $GLOBALS['scand_error'] );

        $host = ( $error && isset($_POST['host']) ) ? $_POST['host'] : $this->{$this->param_host};
        $port = ( $error && isset($_POST['port']) ) ? $_POST['port'] : $this->{$this->param_port};
        $username = ( $error && isset($_POST['username']) ) ? $_POST['username'] : $this->{$this->param_username};
        $password = ( $error && isset($_POST['password']) ) ? $_POST['password'] : $this->{$this->param_password};
        $smtp_auth = ( $error && isset($_POST['smtp_auth']) ) ? $_POST['smtp_auth'] : $this->{$this->param_smtp_auth};
        $smtp_secure = ( $error && isset($_POST['smtp_secure']) ) ? $_POST['smtp_secure'] : $this->{$this->param_smtp_secure};

        $form = "
        <table class=\"form-table\">
            <tr>
                <th>
                    ". __('Host:', SCAND_MULTI_MAILER_TEXTDOMAIN ) ."
                </th>
                <td>
                    <input type=\"text\" name=\"{$this->param_host}\" value=\"". esc_attr($host). "\" size='50'>
                </td>
            </tr>
            <tr>
                <th>
                    ". __('Port:', SCAND_MULTI_MAILER_TEXTDOMAIN ) ."
                </th>
                <td>
                    <input type=\"text\" name=\"{$this->param_port}\" value=\"". esc_attr($port). "\" size='50'>
                </td>
            </tr>
            <tr>
                <th>
                    ". __('SMTPAuth:', SCAND_MULTI_MAILER_TEXTDOMAIN ) ."
                </th>
                <td>
                    <input type=\"checkbox\" name=\"{$this->param_smtp_auth}\" " . ($smtp_auth ? 'checked' : '' ) . " size='50'>
                </td>
            </tr>
            <tr>
                <th>
                    ". __('Username:', SCAND_MULTI_MAILER_TEXTDOMAIN ) ."
                </th>
                <td>
                    <input type=\"text\" name=\"{$this->param_username}\" value=\"" . $username ."\" size='50'>
                </td>
            </tr>
            <tr>
                <th>
                    ". __('Password:', SCAND_MULTI_MAILER_TEXTDOMAIN ) ."
                </th>
                <td>
                    <input type=\"password\" name=\"{$this->param_password}\" value=\"" . $password . "\" size='50'>
                </td>
            </tr>
            <tr>
                <th>
                     ". __('SMTPSecure:', SCAND_MULTI_MAILER_TEXTDOMAIN ) ."
                </th>
                <td>
                    <label>
                        <input type=\"radio\" name=\"{$this->param_smtp_secure}\" value=\"tls\" " .
            ( $smtp_secure === 'tls' ? 'checked="checked"' : '' ) . ">
                        TLS
                    </label>
                    <label>
                        <input type=\"radio\" name=\"{$this->param_smtp_secure}\" value=\"ssl\" " .
            ( $smtp_secure === 'ssl' ? 'checked="checked"' : '' ) . ">
                        SSL
                    </label>
                </td>
            </tr>
        </table>";

        return  $form;
    }

    public function send_message($to, $subject, $message, $from = null, $from_name = null)
    {
        require_once ABSPATH . WPINC . '/class-phpmailer.php';
        require_once ABSPATH . WPINC . '/class-smtp.php';
        //Php mailer smtp setting
        $phpmailer = new PHPMailer( true );
        $phpmailer->Host = $this->{$this->param_host};
        $phpmailer->Port = $this->{$this->param_port};
        $phpmailer->SMTPAuth = $this->{$this->param_smtp_auth};
        $phpmailer->SMTPSecure = $this->{$this->param_smtp_secure};
        if ($this->{$this->param_smtp_auth}) {
            $phpmailer->Username = $this->{$this->param_username};
            $phpmailer->Password = $this->{$this->param_password};
        }
        $phpmailer->isSMTP();
        $phpmailer->isHTML(true);
        //$phpmailer->SMTPDebug = 2;
        $phpmailer->CharSet = get_bloginfo('charset');
        //email setting
        $phpmailer->FromName   = $from_name;
        $phpmailer->From       = $from;
        $phpmailer->Subject    = $subject;
        $phpmailer->Body       = $message;

        if( !is_array( $to ) ) {
            $to[] = $to;
        }

        foreach( $to as $to_mail ) {
            $phpmailer->AddAddress( $to_mail );
        }

        $answer = false;
        try {
            $answer = $phpmailer->Send();
        }
        catch (Exception $ex) {
            echo $ex->getMessage();
            error_log( print_r($ex, true).PHP_EOL );
        }

        return $answer;
    }

    public function save_param($data)
    {
        $this->{$this->param_host} = isset($data[$this->param_host]) ? $data[$this->param_host] : '';
        $this->{$this->param_port} = isset($data[$this->param_port]) ? $data[$this->param_port] : '';
        $this->{$this->param_smtp_secure} = isset($data[$this->param_smtp_secure]) ? $data[$this->param_smtp_secure] : '';
       // $this->{$this->param_smtp_auth} = isset($data[$this->param_smtp_auth]) ? true : false;
        $this->{$this->param_smtp_auth} = (bool) $data[$this->param_smtp_auth];
        if ($this->{$this->param_smtp_auth}) {
            $this->{$this->param_username} = isset($data[$this->param_username]) ? $data[$this->param_username] : '';
            $this->{$this->param_password} = isset($data[$this->param_password]) ? $data[$this->param_password] : '';
            $this->{$this->param_is_obfuscated} = isset($data[$this->param_is_obfuscated]) ? $data[$this->param_is_obfuscated] : '';

            //password recovering
            if ($this->{$this->param_is_obfuscated} == 1) {
                $key = hash('sha512', sha1(md5( $this->{$this->param_username} )));
                $oEncrypt = new Encryption_password();
                $decrypt_result = $oEncrypt->decrypt($key, $this->{$this->param_password});
                if (count($oEncrypt->errors) > 0){
                    error_log( print_r($oEncrypt->errors, true) );
                    $str = '';
                    foreach ($oEncrypt->errors as $error) {
                        $str .= $error.'<br>';
                    }
                    echo $str;
                }
                else {
                    $this->{$this->param_password} = $decrypt_result;
                }
            }
        }
    }

    public function get_param()
    {
        $data[ $this->param_host ] = isset( $this->{$this->param_host} ) ? $this->{$this->param_host} : '';
        $data[ $this->param_port ] = isset( $this->{$this->param_port} ) ? $this->{$this->param_port} : '';
        $data[ $this->param_smtp_secure ] = isset( $this->{$this->param_smtp_secure} ) ? $this->{$this->param_smtp_secure} : '';
        $data[ $this->param_smtp_auth ] = isset( $this->{$this->param_smtp_auth} ) ? $this->{$this->param_smtp_auth} : false;
        if( $data[ $this->param_smtp_auth ] ) {
            $data[ $this->param_username ] = isset( $this->{$this->param_username} ) ? $this->{$this->param_username} : '';
            $data[ $this->param_password ] = isset( $this->{$this->param_password} ) ? $this->{$this->param_password} : '';

            //password obfuscation
            $key = hash('sha512', sha1(md5( $data[ $this->param_username ] )));
            $oEncrypt = new Encryption_password();
            $encrypt_result = $oEncrypt->encrypt($key, $data[ $this->param_password ], strlen($data[ $this->param_password ]));
            if (count($oEncrypt->errors) > 0){
                error_log( print_r($oEncrypt->errors, true) );
                $data[ $this->param_is_obfuscated ] = 0;
                $str = '';
                foreach ($oEncrypt->errors as $error) {
                    $str .= $error.'<br>';
                }
                echo $str;
            }
            else {
                $data[ $this->param_password ] = $encrypt_result;
                $data[ $this->param_is_obfuscated ] = 1;
            }
        }

        return $data;
    }
}