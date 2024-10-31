<?php

/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * Scand_Multi_Mailer class
 */

class Scand_Multi_Mailer
{
    CONST PAGE = 'scand-multi-mailer';
    private static $providerService;

    public static function init_for_user()
    {
        self::init_hook_for_email();
    }

    public static function init_for_admin()
    {
        add_action('admin_init', array( __CLASS__, 'load_my_textdomain'));
        add_action('admin_menu', array( __CLASS__, 'add_setting_page' ));
        self::init_hook_for_email();
    }

    public static function load_my_textdomain() {
        load_plugin_textdomain(
            SCAND_MULTI_MAILER_TEXTDOMAIN,
            false,
            dirname( plugin_basename( __DIR__ ) )  . DIRECTORY_SEPARATOR . 'languages'
        );
    }

    private static function init_provider_service()
    {
        self::$providerService = new Scand_Multi_Mailer_Provider_Service();
        self::$providerService->register_sender(new Scand_Multi_Mailer_PHPMailerSender());
        self::$providerService->register_sender(new Scand_Multi_Mailer_MailStdSender());
        self::$providerService->register_sender(new Scand_Multi_Mailer_LogFileSender());
    }

    private static function init_hook_for_email()
    {
        add_filter( 'wp_mail', array( __CLASS__, 'send_message_hook' ) );
    }

    public static function add_setting_page()
    {
        add_options_page(
            __( 'Multi Mailer', SCAND_MULTI_MAILER_TEXTDOMAIN ),
            __( 'Multi Mailer', SCAND_MULTI_MAILER_TEXTDOMAIN ),
            'manage_options',
            self::PAGE,
            array( __CLASS__, 'multi_mailer_page')
        );
    }

    public static function plugin_activation()
    {
		self::required_files();
        self::$providerService = new Scand_Multi_Mailer_Provider_Service();
        self::$providerService->init_table();
    }

    public static function plugin_uninstall()
    {
		self::required_files();
        self::$providerService = new Scand_Multi_Mailer_Provider_Service();
        self::$providerService->uninstall();
    }

    public static function multi_mailer_page()
    {
        self::required_files();
        self::init_provider_service();
        self::init_css();

        echo '<div class="wrap">
            <h1 class="plugin-title">'. __( 'Multi Mailer', SCAND_MULTI_MAILER_TEXTDOMAIN ).
            '<a href="?page='.self::PAGE.'&action=add" class="page-title-action">'.__("Add New", SCAND_MULTI_MAILER_TEXTDOMAIN ).'</a></h1>';

        $action = isset( $_GET[ "action" ] ) ? $_GET[ "action" ] : '';

        switch( $action ) {
            case "add":
            case "edit":
                self::show_provider_form( $action );
                break;
            case "delete":
                if( isset( $_GET["id"] ) ) {
                    $provider_id = $_GET["id"];
                    self::$providerService->delete_by_id( $provider_id );
                }
            default:
                self::show_list_providers();
                break;

        }
        echo '</div>';
    }

    protected static function filterEmailFields($arFields)
    {
        $strError = '';
        if ( ! is_email( $arFields['email_from'] ) ) {
            $strError .= __('Invalid email format', SCAND_MULTI_MAILER_TEXTDOMAIN) . '<br>';
        }

        if (strlen($arFields['email_to']) > 0 ) {
            $flag = 0;
            $arTo = explode(", ", $arFields['email_to']);
            foreach ( $arTo as $email) {
                if ( ! is_email($email) ) {
                    $flag = 1;
                }
            }
            if ($flag) {
                $strError .= __('Invalid email in TO field', SCAND_MULTI_MAILER_TEXTDOMAIN) . '<br>';
            }
        }
        else {
            $strError .= __('Empty TO field', SCAND_MULTI_MAILER_TEXTDOMAIN) . '<br>';
        }

        return $strError;
    }

    protected static function validateSMTPData($postData)
    {
        $strError = '';
        if ( ! preg_match("/^[a-z.-0-9]{3,}+$/", $postData['host'])) {
            $strError .= __( 'Invalid hostname', SCAND_MULTI_MAILER_TEXTDOMAIN ). '<br>';
        }
        if ( ! preg_match("/\d+/", $postData['port']) || (int)$postData['port'] > 65535) {
            $strError .= __( 'Invalid port number', SCAND_MULTI_MAILER_TEXTDOMAIN ) . '<br>';
        }

        if ( array_key_exists('smtp_auth', $postData) ) {
            if (strlen($postData['username']) > 0) {
                if (!is_email($postData['username'])) {
                    $strError .= __('Invalid SMTP username', SCAND_MULTI_MAILER_TEXTDOMAIN) . '<br>';
                }
            } else {
                $strError .= __('Please fill in username field', SCAND_MULTI_MAILER_TEXTDOMAIN) . '<br>';
            }

            if (strlen($postData['password']) < 1) {
                $strError .= __('Please fill in password field', SCAND_MULTI_MAILER_TEXTDOMAIN) . '<br>';
            }
        }
        return $strError;
    }

    public static function show_provider_form($action )
    {
        $mail_field = self::get_mail_template_field();
        $sender_field = self::get_sender_field();
        $provider_id = 0;
        if( isset( $_GET["id"] ) ) {
            $provider_id = (int) $_GET["id"];
        }
        self::$providerService->init_provider_by_id( $provider_id );
        $answer = null;
        if ( isset($_POST[ $mail_field[ 'hidden_mail_field' ] ] ) ) {
            //validate data
            $postData = array(
                'email_from' =>  $_POST[ $mail_field['from'] ],
                'email_to' => $_POST[ $mail_field['to'] ]
            );
            $error = self::filterEmailFields( $postData );
            if ( strlen($error) > 0 ) {
                echo '<div class="error"><p><strong>' . $error . '</strong></p></div>';
            }
            else {
                $answer = self::$providerService->save_mail_template(
                    sanitize_text_field( $_POST[ $mail_field[ 'from' ] ] ),
                    sanitize_text_field( $_POST[ $mail_field[ 'from_name' ] ] ),
                    sanitize_text_field( $_POST[ $mail_field[ 'subject' ] ] ),
                    sanitize_text_field( $_POST[ $mail_field[ 'to' ] ] ),
                    sanitize_text_field( $_POST[ $mail_field[ 'message' ] ] )
                );
            }
        }

        if ( isset($_POST[ $sender_field[ 'hidden_sender_field' ] ] ) ) {
            $sender_key = $_POST[ $sender_field[ 'sender_key' ] ];
            self::$providerService->set_sender_by_key( $sender_key );
            if ( isset($_POST[ $sender_field[ 'provider_form' ] ] ) && $_POST[ $sender_field[ 'provider_form' ] ] == $sender_key )
            {
                $error = null;
                if ( (int)$_POST['is_provider_form'] == 0) {
                    $error = self::validateSMTPData($_POST);
                }
                if ( (int)$_POST['is_provider_form'] == 2 && ! array_key_exists('period', $_POST) ) {
                    $error = __( 'Please select period value', SCAND_MULTI_MAILER_TEXTDOMAIN );
                }
                if ( strlen($error) > 0 ) {
                    echo '<div class="error"><p><strong>' . $error . '</strong></p></div>';
                    $GLOBALS['scand_error'] = 1;
                }
                else {
                    $answer = self::$providerService->save_sender(sanitize_post($_POST));
                }
            }
        }

        self::provider_fame_form( $action );

        $current_tab = isset($_GET["tab"]) ? $_GET["tab"] : null;
        self::tabs( $current_tab, $action );

        switch ($current_tab) {
            case 'test-email' :
                self::test_message_form( $provider_id );
                break;
            case 'smtp_setting' :
                self::setting_sender_form( $provider_id, $answer, $action, $current_tab );
                break;
            case 'email-setting':
            default :
                self::setting_email_form( $provider_id, $answer, $action, $current_tab );
                break;
        }

    }

    public static function tabs( $current_tab, $action )
    {
        if( !$current_tab ) {
            $current_tab = 'email-setting';
        }
        $tabs = array(
            'email-setting' => __( 'Email Setting', SCAND_MULTI_MAILER_TEXTDOMAIN ),
            'smtp_setting' => __( 'SMTP Setting', SCAND_MULTI_MAILER_TEXTDOMAIN ),
            'test-email' => __( 'Test Message', SCAND_MULTI_MAILER_TEXTDOMAIN ),
        );
        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $tabs as $tab => $name ) {
            $class = ( $tab == $current_tab ) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=".self::PAGE."&action=$action&id="
                .self::$providerService->get_provider_id()."&tab=$tab'>$name</a>";
        }
        echo '</h2>';
    }

    public static function provider_fame_form($action )
    {
        $error = 0;
        $form = null;
        $field_name = "name_provider";
        if( isset($_POST[ $field_name ]) ) {
            if ( ! preg_match("/^[a-zA-Z0-9-.\s_]+$/", $_POST[ $field_name ]) ) {
                echo '<div class="error"><p><strong>' . __('Invalid provider name.', SCAND_MULTI_MAILER_TEXTDOMAIN) . '</strong></p></div>';
                $error = 1;
            }
            else {
                $answer = self::$providerService->set_provider_name( $_POST[ $field_name ] );
                if( $answer ) {
                    echo '<div class="updated"><p><strong>' . __('Save.', SCAND_MULTI_MAILER_TEXTDOMAIN) . '</strong></p></div>';
                } else {
                    echo '<div class="error"><p><strong>' . __('Something Wrong.', SCAND_MULTI_MAILER_TEXTDOMAIN) . '</strong></p></div>';
                }
            }
        }

        if( self::is_action_edit( $action ) ) {
            $button_text = 'Update Name';
        } else {
            $button_text = 'Save Name';
        }

        if ($error) {
            $form = "<form method='post' action='". $_SERVER[ 'PHP_SELF' ]. "?page=" . self::PAGE .
                "&action=$action&id=0'>
                    <div id='titlediv'>
                        <input type='text' id='title'
                            value='". esc_attr( $_POST[ $field_name ] )."'
                            placeholder='". __('Input Name Provider')."' required name='$field_name'>
                        <p>
                            <button type=\"submit\" class=\"button button-primary\">" . __( $button_text, SCAND_MULTI_MAILER_TEXTDOMAIN ) . "</button>
                        </p>
                    </div>
                </form>";
        }
        else {
            $form = "<form method='post' action='" . $_SERVER['PHP_SELF'] . "?page=" . self::PAGE .
                "&action=$action&id=" . self::$providerService->get_provider_id() . "'>
            <div id='titlediv'>
                <input type='text' id='title'
                    value='" . esc_attr( self::$providerService->get_provider_name() ) . "'
                    placeholder='" . __('Input Name Provider', SCAND_MULTI_MAILER_TEXTDOMAIN) . "' required name='$field_name'>
                <p>
                    <button type=\"submit\" class=\"button button-primary\">" . __( $button_text, SCAND_MULTI_MAILER_TEXTDOMAIN ) . "</button>
                </p>
            </div>
        </form>";
        }
        echo $form;
    }

    private static function is_action_edit($action ) {
        $answer = false;
        if( $action == 'edit' ) {
            $answer = true;
        }

        return $answer;
    }

    public static function show_list_providers()
    {
        //use in file without self::
        $providerService = self::$providerService;
        require_once (SCAND_MULTI_MAILER_DIR.'view-providers-table.php');
    }

    public static function test_message_form($id )
    {
        //use in file without self::
        $providerService = self::$providerService;
        require_once (SCAND_MULTI_MAILER_DIR.'view_send_test_message_form.php');
    }

    public static function setting_sender_form($id, $answer, $action, $tab )
    {
        $sender_field = self::get_sender_field();
        $providerService = self::$providerService;
        require_once (SCAND_MULTI_MAILER_DIR.'view_sender_setting.php');
    }

    public static function setting_email_form($id, $answer, $action, $tab )
    {
        $mail_field = self::get_mail_template_field();
        $providerService = self::$providerService;
        require_once (SCAND_MULTI_MAILER_DIR.'view_mail_setting.php');
    }

    public static function send_message_hook( $mail_data )
    {
        self::required_files();
        self::init_provider_service();
        self::$providerService->send_message( $mail_data['to'], $mail_data['subject'], $mail_data['message'] );

        return $mail_data;
    }

    public static function init_css()
    {
        echo "
        <style>
            h1.plugin-title { margin-bottom: 5px; }
            h1.plugin-title > a.page-title-action { margin-left: 10px; }
            #titlediv { transition: 3s; }
            #titlediv.hide { display: none; opacity: 0;}
            input[type=radio]:disabled:checked:before { opacity: 1; }
        </style>
        ";
    }

    private static function get_mail_template_field()
    {
        $field[ 'hidden_mail_field' ] = "mail_form";
        $field[ 'from' ] = "email_from";
        $field[ 'from_name' ] = "email_from_name";
        $field[ 'to' ] = "email_to";
        $field[ 'subject' ] = "email_subject";
        $field[ 'message' ] = "email_message";

        return $field;
    }

    private static function get_sender_field()
    {
        $field[ 'hidden_sender_field'] = 'is_select_provider_form';
        $field[ 'sender_key' ] = "sender_key";
        $field[ 'provider_form' ] = 'is_provider_form';

        return $field;
    }

    private static function required_files()
    {
        require_once(SCAND_MULTI_MAILER_DIR . "providers/interface-isender.php");
        foreach (glob(SCAND_MULTI_MAILER_DIR.'providers/class-*.php') as $filename)
        {
            require_once $filename;
        }
    }

}