<?php
/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * @var Scand_Multi_Mailer_Provider_Service $providerService
 * @var $sender_field[ 'hidden_sender_field' ]
 * @var $sender_field[ 'provider_form' ]
 * @var $sender_field[ 'sender_key' ]
 * @var $action
 * @var $tab
 */

$button_text = "Save Sender";
if( self::is_action_edit( $action ) ) {
    $button_text = "Update Sender";
}

?>

<div class="wrap">
<?php
    if( isset( $answer ) )
    {
        if( $answer ) {
            echo '<div class="updated"><p><strong>' . __('Save SMTP Setting.', SCAND_MULTI_MAILER_TEXTDOMAIN) . '</strong></p></div>';
        } else {
            echo '<div class="error"><p><strong>' . __('Something Wrong.', SCAND_MULTI_MAILER_TEXTDOMAIN) . '</strong></p></div>';
        }
    }

    $form_sender = $providerService->get_form_sender();
?>
    <h2><?php _e( 'Add Provider', SCAND_MULTI_MAILER_TEXTDOMAIN ); ?></h2>

    <form name="create_provider"
          method="post"
          action="<?php
                echo $_SERVER[ "PHP_SELF" ]. "?page=" . Scand_Multi_Mailer::PAGE .
                "&action=$action&tab=$tab&id={$providerService->get_provider_id()}"; ?>">
        <input type="hidden" name="<?php echo $sender_field[ 'hidden_sender_field' ]; ?>">
        <table class="form-table">
        <tr>
            <th>
                <?php _e("Select what do you want to use:", SCAND_MULTI_MAILER_TEXTDOMAIN ); ?>
            </th>
            <td>
                <?php
                foreach($providerService->get_senders() as $key=> $sender) { ?>
                    <label>
                        <input onclick="this.form.submit()"
                               type="radio"
                               name="<?php echo $sender_field[ 'sender_key' ]; ?>"
                               value="<?php echo $key; ?>"
                                <?php if( $providerService->get_sender_name() == $sender->get_name() ) {
                                    echo 'checked="checked"';
                                    //use sender key for equal selected sender and current sender
                                    $sender_key = $key;
                                } ?>
                        >
                        <span><?php _e( $sender->get_name(), SCAND_MULTI_MAILER_TEXTDOMAIN ); ?></span>
                    </label>
                <?php } ?>
            </td>
        </tr>
        </table>
        <?php
        if( isset( $form_sender ) && !empty( $form_sender ) ) :
            echo $form_sender;
            echo "<input type='hidden' name='{$sender_field[ 'provider_form' ]}' value='". (isset( $sender_key ) ? $sender_key : '') ."'>";
            echo " <p class=\"submit\">
                         <input type=\"submit\" class=\"button button-primary\" name=\"Submit\" value=\"". __( $button_text, SCAND_MULTI_MAILER_TEXTDOMAIN ) ."\" />
                   </p>";
        endif; ?>
    </form>
<hr />
</div>
