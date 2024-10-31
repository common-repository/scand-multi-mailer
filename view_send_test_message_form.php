<?php
/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * @var Scand_Multi_Mailer_Provider_Service $providerService
 */
$hiddenField = "send_test_message";
$send_to = "test_send_message_to";
$send_subject = "test_send_message_subject";
$send_text = "test_send_message_text";
if ( isset($_POST[ $hiddenField ]) )
{
    $provider = $providerService->get_provider();
    if ( (is_null($provider->get_send_from()) && is_null($provider->get_send_to()))
        || is_null($provider->get_sender()) ) {
        echo '<div class="error"><p><strong>' . __('Provider is not defined.', SCAND_MULTI_MAILER_TEXTDOMAIN) . '</strong></p></div>';
    }
    else if ( ! is_email($_POST[$send_to]) ) {
        echo '<div class="error"><p><strong>' . __('Invalid email.', SCAND_MULTI_MAILER_TEXTDOMAIN) . '</strong></p></div>';
    }
    else {
        $arPOST = sanitize_post($_POST);
        $answer = $providerService->send_test_message($arPOST[$send_to], $arPOST[$send_subject], $arPOST[$send_text]);
        if ($answer === true) {
            echo '<div class="updated"><p><strong>' . __('Success send test message.', SCAND_MULTI_MAILER_TEXTDOMAIN) . '</strong></p></div>';
        } else {
            echo '<div class="error"><p><strong>' . __('Something Wrong.', SCAND_MULTI_MAILER_TEXTDOMAIN) . '</strong></p></div>';
        }
    }
}

?>

<div class="wrap">
    <h1><?php _e('Send test message', SCAND_MULTI_MAILER_TEXTDOMAIN); ?></h1>
    <form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="<?php echo $hiddenField ?>">
        <table class="form-table">
            <tr>
                <th>
                    <?php _e("Send message to", SCAND_MULTI_MAILER_TEXTDOMAIN); ?>
                </th>
                <td>
                    <input name="<?php echo $send_to ?>" value="<?php if ( isset($_POST[$send_to])) echo esc_attr($_POST[$send_to]) ?>" >
                </td>
            </tr>
            <tr>
                <th>
                    <?php _e("Subject for test message", SCAND_MULTI_MAILER_TEXTDOMAIN); ?>
                </th>
                <td>
                    <input name="<?php echo $send_subject ?>" value="<?php if ( isset($_POST[$send_subject]) ) echo esc_attr($_POST[$send_subject]);
                    else _e("It`s test subject", SCAND_MULTI_MAILER_TEXTDOMAIN); ?>" >
                </td>
            </tr>
            <tr>
                <th>
                    <?php _e("Text for test message", SCAND_MULTI_MAILER_TEXTDOMAIN); ?>
                </th>
                <td>
                    <textarea name="<?php echo $send_text ?>"><?php if (isset($_POST[$send_text])) echo esc_textarea($_POST[$send_text]);
                        else _e("It`s test message", SCAND_MULTI_MAILER_TEXTDOMAIN); ?></textarea>
                </td>
            </tr>
        </table>
        <button type="submit" class="button button-primary"><?php _e('Send message', SCAND_MULTI_MAILER_TEXTDOMAIN); ?></button>
    </form>
    <hr/>
</div>
