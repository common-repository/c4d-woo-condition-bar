<?php
/*
Plugin Name: C4D Woocommerce Condition Bar 
Plugin URI: http://coffee4dev.com/
Description: So promotion for customer when they do something.
Author: Coffee4dev.com
Author URI: http://coffee4dev.com/
Text Domain: c4d-woo-cb
Version: 2.0.9
*/

define('C4DWCB_PLUGIN_URI', plugins_url('', __FILE__));

add_action( 'wp_enqueue_scripts', 'c4d_woo_cb_safely_add_stylesheet_to_frontsite');
add_action( 'woocommerce_single_product_summary', 'c4d_woo_cb_single_product_summary', 35 );
add_action( 'c4d-plugin-manager-section', 'c4d_woo_cb_section_options');
add_filter( 'plugin_row_meta', 'c4d_woo_cb_plugin_row_meta', 10, 2 );
add_shortcode('c4d-woo-cb', 'c4d_woo_cb_shortcode');

function c4d_woo_cb_plugin_row_meta( $links, $file ) {
    if ( strpos( $file, basename(__FILE__) ) !== false ) {
        $new_links = array(
            'visit' => '<a href="http://coffee4dev.com">Visit Plugin Site</<a>',
            'premium' => '<a href="http://coffee4dev.com">Premium Support</<a>'
        );
        $links = array_merge( $links, $new_links );
    }
    return $links;
}

function c4d_woo_cb_safely_add_stylesheet_to_frontsite( $page ) {
	wp_enqueue_style( 'c4d-woo-cb-frontsite-style', C4DWCB_PLUGIN_URI.'/assets/default.css' );
	wp_enqueue_script( 'c4d-woo-cb-frontsite-plugin-js', C4DWCB_PLUGIN_URI.'/assets/default.js', array( 'jquery' ), false, true );
	wp_localize_script( 'jquery', 'c4d_woo_cb',
        array( 
        	'ajax_url' => admin_url( 'admin-ajax.php' ),
        	'total' => WC()->cart->cart_contents_total,
        	'currency' => get_woocommerce_currency_symbol(),
        	'thousand' => get_option('woocommerce_price_thousand_sep'),
        	'decimal' => get_option('woocommerce_price_decimal_sep'),
        	'num_demical' => get_option('woocommerce_price_num_decimals')
        )
    );
}

function c4d_woo_cb_shortcode($atts = array()) {
	global $c4d_plugin_manager;
	$html = '';
	$default = array(
		'amount' => isset($c4d_plugin_manager['c4d-woo-cb-amount']) ? $c4d_plugin_manager['c4d-woo-cb-amount'] : 100,
		'currency' => get_woocommerce_currency_symbol(),
		'text' => isset($c4d_plugin_manager['c4d-woo-cb-promotion']) ? $c4d_plugin_manager['c4d-woo-cb-promotion'] : esc_html__('Get Free Standard Delivery On All Orders Over %s', 'c4d-woo-cb'),
		'achive' => isset($c4d_plugin_manager['c4d-woo-cb-achive']) ? $c4d_plugin_manager['c4d-woo-cb-achive'] :  esc_html__('Only %s away from free shipping', 'c4d-woo-cb'),
		'success' => isset($c4d_plugin_manager['c4d-woo-cb-success']) ? $c4d_plugin_manager['c4d-woo-cb-success'] : esc_html__('Congratulations! You\'ve got free shipping', 'c4d-woo-cb')
	);
	$atts = shortcode_atts($default, $atts);

	$html .= '<div class="c4d-woo-cb" data-amount="'.esc_attr($atts['amount']).'">';
	$html .= '<div class="c4d-woo-cb__promotion">'.sprintf($atts['text'], '<span class="c4d-woo-cb__promotion_amount">'.$atts['currency'].$atts['amount']. '</span>').'</div>';

	$html .= '<div class="c4d-woo-cb__achive">'.sprintf($atts['achive'],'<span class="c4d-woo-cb__achive_amount">'.$atts['currency'].$atts['amount'].'</span>').'</div>';

	$html .= '<div class="c4d-woo-cb__success">'.$atts['success'].'</div>';
	$html .= '</div>';
    $html .= '<script>
                jQuery(document).ready(function(){
                    setTimeout(function(){
                        c4d_woo_cb.update();    
                    }, 2000);
                });
            </script>';

	return $html;
}

function c4d_woo_cb_single_product_summary() {
    global $c4d_plugin_manager;
    if (isset($c4d_plugin_manager['c4d-woo-cb-single']) && $c4d_plugin_manager['c4d-woo-cb-single'] == 1) {
        echo c4d_woo_cb_shortcode();    
    }
}
function c4d_woo_cb_section_options(){
    $opt_name = 'c4d_plugin_manager';
    Redux::setSection( $opt_name, array(
        'title'            => esc_html__( 'Freeship Bar', 'c4d-woo-cb' ),
        'id'               => 'c4d-woo-cb',
        'desc'             => '',
        'customizer_width' => '400px',
        'icon'             => 'el el-home',
        'fields'           => array(
            array(
              'id'       => 'c4d-woo-cb-single',
              'type'     => 'button_set',
              'title'    => esc_html__('Show in Single Product', 'c4d-woo-cb'),
              'options' => array(
                  '1' => esc_html__('Yes', 'c4d-woo-cb'), 
                  '0' => esc_html__('No', 'c4d-woo-cb')
               ), 
              'default' => '1'
            ),
            array(
                'id'          => 'c4d-woo-cb-typo',
                'type'        => 'typography', 
                'title'       => esc_html__('Typo', 'c4d-woo-cb'),
                'output'      => array('.c4d-woo-cb'),
                'units'       =>'px',
                'text-align'  => false,
                'subsets'     => false,
                'color'     => false
            ),
            array(
                'id'       => 'c4d-woo-cb-amount',
                'type'     => 'text',
                'title'    => esc_html__('Amount', 'c4d-woo-cb'),
                'default'  => '100'
            ),
            array(
                'id'       => 'c4d-woo-cb-amount-color',
                'type'     => 'color',
                'title'    => esc_html__('Amount Color', 'c4d-woo-cb'), 
                'default'  => '',
                'transparent' => false,
                'validate' => 'color',
                'output'    => array(
                    'color' => '.c4d-woo-cb .c4d-woo-cb__promotion_amount'
                )
            ),
            array(
                'id'       => 'c4d-woo-cb-promotion',
                'type'     => 'text',
                'title'    => esc_html__('Promotion', 'c4d-woo-cb'),
                'default'  => 'Get Free Standard Delivery On All Orders Over %s'
            ),
            array(
                'id'       => 'c4d-woo-cb-promotion-color',
                'type'     => 'color',
                'title'    => esc_html__('Promotion Color', 'c4d-woo-cb'), 
                'default'  => '#7a7a7a',
                'transparent' => false,
                'validate' => 'color',
                'output'    => array(
                    'color' => '.c4d-woo-cb .c4d-woo-cb__promotion'
                )
            ),
            array(
                'id'       => 'c4d-woo-cb-promotion-background',
                'type'     => 'color',
                'title'    => esc_html__('Promotion Background Color', 'c4d-woo-cb'), 
                'default'  => '#f7f7f7',
                'transparent' => false,
                'validate' => 'color',
                'output'    => array(
                    'background-color' => '.c4d-woo-cb'
                )
            ),
            array(
                'id'       => 'c4d-woo-cb-achive',
                'type'     => 'text',
                'title'    => esc_html__('Achive', 'c4d-woo-cb'),
                'default'  => 'Only %s away from free shipping'
            ),
            array(
                'id'       => 'c4d-woo-cb-achive-color',
                'type'     => 'color',
                'title'    => esc_html__('Achive Color', 'c4d-woo-cb'), 
                'default'  => '#7a7a7a',
                'transparent' => false,
                'validate' => 'color',
                'output'    => array(
                    'color' => '.c4d-woo-cb .c4d-woo-cb__achive'
                )
            ),
            array(
                'id'       => 'c4d-woo-cb-achive-background',
                'type'     => 'color',
                'title'    => esc_html__('Achive Background Color', 'c4d-woo-cb'), 
                'default'  => '#f7f7f7',
                'transparent' => false,
                'validate' => 'color',
                'output'    => array(
                    'background-color' => '.c4d-woo-cb.achive'
                )
            ),
            array(
                'id'       => 'c4d-woo-cb-success',
                'type'     => 'text',
                'title'    => esc_html__('Success', 'c4d-woo-cb'),
                'default'  => 'Congratulations! You\'ve got free shipping'
            ),
            array(
                'id'       => 'c4d-woo-cb-success-color',
                'type'     => 'color',
                'title'    => esc_html__('Success Color', 'c4d-woo-cb'), 
                'default'  => '#111',
                'transparent' => false,
                'validate' => 'color',
                'output'    => array(
                    'color' => '.c4d-woo-cb .c4d-woo-cb__success'
                )
            ),
            array(
                'id'       => 'c4d-woo-cb-success-background',
                'type'     => 'color',
                'title'    => esc_html__('Success Background Color', 'c4d-woo-cb'), 
                'default'  => '#fed700',
                'transparent' => false,
                'validate' => 'color',
                'output'    => array(
                    'background-color' => '.c4d-woo-cb.success'
                )
            )
        )
    ));
}