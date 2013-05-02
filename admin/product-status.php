<?php
/**
 * nrelate product status
 *
 * Identify all nrelate products that are active
 * 
 * @package nrelate
 * @subpackage Functions
 */

define( 'NRELATE_PRODUCT_STATUS', true );

// Define Admin constants
define( 'NRELATE_WEBSITE_FORUM_URL', 'http://nrelate.com/forum/' );
define( 'NRELATE_WEBSITE_AD_SIGNUP', 'https://partners.nrelate.com/register/');
define( 'NRELATE_ADMIN_IMAGES', NRELATE_ADMIN_URL . '/images' );

define( 'NRELATE_MIN_WP', '2.9' );
define( 'NRELATE_MIN_PHP', '5.0' );

define( 'NRELATE_ADMIN_VERSION', '0.05.3' );


// Pull the nrelate_products array
$product_status = get_option('nrelate_products');

// Get status of our products
$related_status = isset($product_status["related"]["status"]) ? $product_status["related"]["status"] : null;
$popular_status = isset($product_status["popular"]["status"]) ? $product_status["popular"]["status"] : null;
$flyout_status = isset($product_status["flyout"]["status"]) ? $product_status["flyout"]["status"] : null;
$nsquared_status = isset($product_status["nsquared"]["status"]) ? $product_status["nsquared"]["status"] : null;

// Set active products to active
if ($related_status == 1) { define( 'NRELATE_RELATED_ACTIVE', true ); }
if ($popular_status == 1) { define( 'NRELATE_POPULAR_ACTIVE', true ); }
if ($flyout_status == 1) { define( 'NRELATE_FLYOUT_ACTIVE', true ); }
if ($nsquared_status == 1) { define( 'NRELATE_NSQUARED_ACTIVE', true ); }

/**
* Try to find latest admin version.
* Default to NRELATE_ADMIN_VERSION from product-status.php if not found
*
* @since 0.50.0
*/
$latest_admin_version = NRELATE_ADMIN_VERSION;
if (isset($product_status) && is_array($product_status)) {
	foreach ($product_status as $pluginname => $plugininfo) {
	// Consider only active plugins
		if (isset($plugininfo['status']) && !$plugininfo['status']) continue;
		$latest_admin_version = max ( $latest_admin_version, isset($plugininfo['admin_version']) ? $plugininfo['admin_version'] : 0 );
	}
}
define('NRELATE_LATEST_ADMIN_VERSION', $latest_admin_version);

?>