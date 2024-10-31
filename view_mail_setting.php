<?php
/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * @var $providerService Scand_Multi_Mailer_Provider_Service
 * @var $mail_field[ 'hidden_mail_field' ]
 * @var $mail_field [ 'from' ]
 * @var $mail_field[ 'from_name' ]
 * @var $mail_field[ 'to' ]
 * @var $mail_field[ 'subject' ]
 * @var $mail_field [ 'message' ]
 * @var $action
 * @var $tab
 */

$button_text = 'Save Mail Template';
if( self::is_action_edit( $action ) ) {
    $button_text = 'Update Mail Template';
}

$provider = $providerService->get_provider();
if( isset( $answer ) )
{
    if( $answer ) {
        echo '<div class="updated"><p><strong>' . __('Save Mail Setting.', SCAND_MULTI_MAILER_TEXTDOMAIN) . '</strong></p></div>';
    } else {
        echo '<div class="error"><p><strong>' . __('Something Wrong.', SCAND_MULTI_MAILER_TEXTDOMAIN) . '</strong></p></div>';
    }
}
?>
<div class="wrap">
    <h1><?php _e('Mail Setting', 'mt_'); ?></h1>
    <form method="post"
          action="<?php echo $_SERVER[ "PHP_SELF" ]. "?page=" . Scand_Multi_Mailer::PAGE .
              "&action=$action&tab=$tab&id={$providerService->get_provider_id()}"; ?>">
        <input type="hidden" name="<?php echo $mail_field[ 'hidden_mail_field' ] ?>" value="true">
        <table class="form-table">
            <tr>
                <th>
                    <?php _e("From Email:", SCAND_MULTI_MAILER_TEXTDOMAIN ); ?>
                </th>
                <td>
                    <input name="<?php echo $mail_field[ 'from' ]; ?>"
                           value="<?php if (isset($_POST[$mail_field['from']]) ) echo $_POST[$mail_field['from']];
                            else echo $provider->get_send_from(); ?>"
                           placeholder="<?php _e('Default email', 'mt_') ?>" size="50">
                    <span>
                        <?php _e( 'Please read this first: ', SCAND_MULTI_MAILER_TEXTDOMAIN ); ?>
                        <a href="https://support.google.com/mail/answer/22370">
                            <?php _e( 'Add another user in gmail', SCAND_MULTI_MAILER_TEXTDOMAIN ); ?>
                        </a>
                    </span>
                </td>
            </tr>
            <tr>
                <th>
                    <?php _e("From Name:", SCAND_MULTI_MAILER_TEXTDOMAIN ); ?>
                </th>
                <td>
                    <input name="<?php echo $mail_field[ 'from_name' ]; ?>"
                           value="<?php if (isset($_POST[$mail_field['from_name']]) ) echo $_POST[$mail_field['from_name']];
                                else echo $provider->get_send_from_name(); ?>"
                           placeholder="<?php _e('Default name', 'mt_') ?>" size="50">
                </td>
            </tr>
            <tr>
                <th>
                    <?php _e("To:", SCAND_MULTI_MAILER_TEXTDOMAIN ); ?>
                </th>
                <td>
                    <input name="<?php echo $mail_field[ 'to' ]; ?>"
                           value="<?php if (isset($_POST[$mail_field['to']]) ) echo $_POST[$mail_field['to']];
                                else echo $provider->get_send_to(); ?>"
                           placeholder="<?php _e('Default user', 'mt_') ?>"
                           size="50">
                    <span class="description">
                        <?php _e( "user@mail.com, user1@mail.com", SCAND_MULTI_MAILER_TEXTDOMAIN); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>
                    <?php _e("Subject:", SCAND_MULTI_MAILER_TEXTDOMAIN ); ?>
                </th>
                <td>
                    <input name="<?php echo $mail_field[ 'subject' ]; ?>"
                           value="<?php if (isset($_POST[$mail_field['subject']]) ) echo $_POST[$mail_field['subject']];
                                else echo $provider->get_subject(); ?>"
                           placeholder="<?php _e('Default subject', 'mt_') ?>" size="50">
                </td>
            </tr>
            <tr>
                <th>
                    <?php _e("Message:", SCAND_MULTI_MAILER_TEXTDOMAIN ); ?>
                </th>
                <td>
                    <?php
                    wp_editor($provider->get_message(), 'editor_id', array(
                        'media_buttons' => 0,
                        'textarea_name' => $mail_field [ 'message' ],
                        'textarea_rows' => 10,
                        'tabindex'      => null,
                        'editor_css'    => '',
                        'editor_class'  => '',
                        'dfw'           => 0,
                        'teeny'         => 0,
                        'tinymce'       => 1,
                        'wpautop'       => 0,
                        'quicktags'     => 1,
                        'drag_drop_upload' => false
                    ) );
                    ?>
                    <span class="description">
                        <?php _e( "[message]-is required shortcode for default text message", SCAND_MULTI_MAILER_TEXTDOMAIN); ?>
                    </span>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button button-primary" name="Submit" value="<?php _e( $button_text, SCAND_MULTI_MAILER_TEXTDOMAIN ); ?>" />
        </p>
    </form>
    <hr/>
</div>
