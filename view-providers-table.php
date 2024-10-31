<?php
/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 *
 * @var $providerService Scand_Multi_Mailer_Provider_Service
 */

$orderby = isset( $_GET[ "orderby" ] ) ? $_GET[ "orderby" ] : 'date';
$order = isset( $_GET[ "order" ] ) ? $_GET[ "order" ] : 'desc';
$listProviders = $providerService->sort_providers( $orderby, $order );

?>
<table class="wp-list-table widefat fixed striped pages">
    <thead>
        <tr>
            <?php
            if( $orderby == 'name' ) {
                echo "<th class=\"sortable $order\">";
            } else {
                echo "<th class=\"sortable desc\">";
            }
            ?>

                <a href="<?php
                        echo "?page=".Scand_Multi_Mailer::PAGE."&orderby=name&order=".
                        ( $orderby == 'name' && $order == 'asc' ? 'desc' : 'asc' ); ?>">
                    <span><?php _e("Name Provider", SCAND_MULTI_MAILER_TEXTDOMAIN); ?></span>
                    <span class="sorting-indicator"></span>
                </a>

            </th>
            <?php
            if( $orderby == 'date' ) {
                echo "<th width=\"20%\" class=\"sortable $order\">";
            } else {
                echo "<th width=\"20%\" class=\"sortable desc\">";
            }
            ?>
                <a href="<?php
                        echo "?page=".Scand_Multi_Mailer::PAGE."&orderby=date&order=".
                        ( $orderby == 'date' && $order == 'asc' ? 'desc' : 'asc' ); ?>">
                    <span><?php _e("Last Update", SCAND_MULTI_MAILER_TEXTDOMAIN); ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th width="5%">
                <?php _e("Edit", SCAND_MULTI_MAILER_TEXTDOMAIN); ?>
            </th>
            <th width="5%">
                <?php _e("Delete", SCAND_MULTI_MAILER_TEXTDOMAIN); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $listProviders as $provider) : ?>
            <tr>
                <td>
                    <strong>
                        <?php echo "<a href='?page=".Scand_Multi_Mailer::PAGE."&action=edit&id={$provider->get_id()}'>{$provider->get_name()}</a>"; ?>
                    </strong>
                    <div class="row-actions">
                        <span class="edit">
                            <?php echo "<a href='?page=".Scand_Multi_Mailer::PAGE."&action=edit&id={$provider->get_id()}'>".__( "Edit" , SCAND_MULTI_MAILER_TEXTDOMAIN)."</a>"; ?>
                        </span>
                        <span class="trash">
                            <?php echo "<a href='?page=".Scand_Multi_Mailer::PAGE."&action=delete&id={$provider->get_id()}'>".__( "Delete", SCAND_MULTI_MAILER_TEXTDOMAIN )."</a>"; ?>
                        </span>
                    </div>
                </td>
                <td>
                    <span><?php echo $provider->get_update(); ?></span>
                </td>
                <td>
                    <?php echo "<a href='?page=".Scand_Multi_Mailer::PAGE."&action=edit&id={$provider->get_id()}'>".__( "Edit", SCAND_MULTI_MAILER_TEXTDOMAIN )."</a>"; ?>
                </td>
                <td class="trash">
                    <?php echo "<a href='?page=".Scand_Multi_Mailer::PAGE."&action=delete&id={$provider->get_id()}'>".__( "Delete", SCAND_MULTI_MAILER_TEXTDOMAIN )."</a>"; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
