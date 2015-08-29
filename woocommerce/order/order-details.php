<?php
/**
 * Order details
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$order = wc_get_order( $order_id );

$customer_name=$order->billing_first_name;
if(!$customer_name) $customer_name = $order->billing_email;

?>

<ul class="order-payment-list list mb30">
    <?php
    if ( sizeof( $order->get_items() ) > 0 ) {

        foreach( $order->get_items() as $item_id => $item ) {
            $_product  = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
            $item_meta = new WC_Order_Item_Meta( $item['item_meta'], $_product );

            if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
                ?>
                <li class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
                    <div class="row">
                        <div class="col-xs-9">
                            <h5><i class="fa fa-plane"></i>
                                <?php
                                if ( $_product && ! $_product->is_visible() ) {
                                    echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );
                                } else {
                                    echo apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), $item['name'] ), $item );
                                }
                                ?>
                            </h5>
                        </div>
                        <div class="col-xs-3">
                            <p class="text-right"><span class="text-lg"><?php echo $order->get_formatted_line_subtotal( $item ); ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="product-name">
                        <?php

                        // Allow other plugins to add additional product information here
                        do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

                        $item_meta->display();

                        if ( $_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {

                            $download_files = $order->get_item_downloads( $item );
                            $i              = 0;
                            $links          = array();

                            foreach ( $download_files as $download_id => $file ) {
                                $i++;

                                $links[] = '<small><a href="' . esc_url( $file['download_url'] ) . '">' . sprintf( __( 'Download file%s', 'woocommerce' ), ( count( $download_files ) > 1 ? ' ' . $i . ': ' : ': ' ) ) . esc_html( $file['name'] ) . '</a></small>';
                            }

                            echo '<br/>' . implode( '<br/>', $links );
                        }

                        // Allow other plugins to add additional product information here
                        do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
                        ?>
                    </div>
                </li>
            <?php
            }

            if ( $order->has_status( array( 'completed', 'processing' ) ) && ( $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) ) ) {
                ?>
                <li class="product-purchase-note">
                    <div colspan="3"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></div>
                </li>
            <?php
            }
        }
    }

    do_action( 'woocommerce_order_items_table', $order );
    ?>
</ul>

<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

<header>
	<h2><?php _e( 'Customer details', 'woocommerce' ); ?></h2>
</header>
<table class="shop_table shop_table_responsive customer_details">
<?php
	if ( $order->billing_email ) {
		echo '<tr><th>' . __( 'Email:', 'woocommerce' ) . '</th><td data-title="' . __( 'Email', 'woocommerce' ) . '">' . $order->billing_email . '</td></tr>';
	}

	if ( $order->billing_phone ) {
		echo '<tr><th>' . __( 'Telephone:', 'woocommerce' ) . '</th><td data-title="' . __( 'Telephone', 'woocommerce' ) . '">' . $order->billing_phone . '</td></tr>';
	}

	// Additional customer details hook
	do_action( 'woocommerce_order_details_after_customer_details', $order );
?>
</table>

<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

<div class="col2-set addresses">

	<div class="col-1">

<?php endif; ?>

		<header class="title">
			<h3><?php _e( 'Billing Address', 'woocommerce' ); ?></h3>
		</header>
		<address>
			<?php
				if ( ! $order->get_formatted_billing_address() ) {
					_e( 'N/A', 'woocommerce' );
				} else {
					echo $order->get_formatted_billing_address();
				}
			?>
		</address>

<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

	</div><!-- /.col-1 -->

	<div class="col-2">

		<header class="title">
			<h3><?php _e( 'Shipping Address', 'woocommerce' ); ?></h3>
		</header>
		<address>
			<?php
				if ( ! $order->get_formatted_shipping_address() ) {
					_e( 'N/A', 'woocommerce' );
				} else {
					echo $order->get_formatted_shipping_address();
				}
			?>
		</address>

	</div><!-- /.col-2 -->

</div><!-- /.col2-set -->

<?php endif; ?>

<div class="clear"></div>
