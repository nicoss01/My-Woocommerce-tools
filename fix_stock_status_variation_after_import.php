<?php
/*
 * Plugin Name: CodNex - WC Variations Fix Bug
 * Plugin URI: https://www.codnex.net
 * Description: Fix parent product stock status after import variations in woocommerce
 * Author: Nicolas Grillet
 * Version: 1.0
 */
function codnex_refresh_product_link(){
    add_menu_page( 
        __( 'Fix Variations', 'codnex' ),
        __( '// Fix Variations //', 'codnex' ),
        'manage_options',
        'codnex_refresh_product_page',
        'codnex_refresh_page_admin',
        'dashicons-update',
        6
    ); 
}
add_action( 'admin_menu', 'codnex_refresh_product_link' );
function codnex_refresh_page_admin(){
	echo "<h1>Update stock status after product/variation import</h1>";
    if($_GET['step']==2){
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1
		);
		$np=0;
		$nvp=0;
		$loop = new WP_Query( $args );
		if ( $loop->have_posts() ): while ( $loop->have_posts() ): $loop->the_post();
			global $product;
			//var_dump($product);
			echo "<p><strong>".$product->get_title()."(".get_post_meta($product->get_id(),'_stock_status', true).")</strong> :<br>\n";
			$np++;
			$variations = $product->get_available_variations();
			$in_stock=0;
			foreach ( $variations as $variation ) {
				$nvp++;
				$variation_obj = new WC_Product_variation($variation['variation_id']);
				$stock = $variation_obj->get_stock_quantity();
				$in_stock+=$stock;
				echo "<em><strong>&nbsp;- ".$variation_obj->get_title()." #".$variation['variation_id']."</strong></em> : <big style='color:green;font-weight:bold'>$stock</big><br>\n";
				wc_update_product_stock($variation['variation_id'],$stock);
				wc_delete_product_transients($variation['variation_id']);
			}
			if($in_stock>0){
				update_post_meta($product->get_id(),'_stock_status','instock');
			}
			echo "<hr><strong>After Update</strong> : <em>".get_post_meta($product->get_id(),'_stock_status', true)."</em></p>";
		endwhile; 
		echo "<p><big style='color:green;font-weight:bold'>$np</big> produits et <big style='color:green;font-weight:bold'>$nvp</big> variations</p>";
		endif; wp_reset_postdata();
	}else{
		echo "<p>This tool allows you to update stocks after importing new products and / or updating several products using import</p>";
		echo "<p><a href='".get_home_url()."/wp-admin/admin.php?page=codnex_refresh_product_page&step=2' onclick='return confirm(\"Are you sure to launch the update ?\")'>Start the inventory update</a></p>";
	}
}
?>