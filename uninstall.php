<?php

// No direct access.
defined('_JEXEC') or die('Restricted access');


/**
 * PHP version 7.2.34
 *
 * @category   Class
 * @package    BreezingCommerce
 * @subpackage BreezingCommerce
 * @author     MustPay <info@mustpay.tech>
 * @copyright  2022 ConcordPay
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://concordpay.concord.ua
 * @since      3.8.0
 */
class CrBcUninstallation extends CrBcUninstaller
{
	/**
	 * @var string
	 * @since 3.8.0
	 */
	public $type = 'payment';

	/**
	 * @var string
	 * @since 3.8.0
	 */
	public $name = 'concordpay';

	/**
	 * @return void
	 * @since 3.8.0
	 */
	public function uninstall(): void
	{
		$db = JFactory::getDBO();
		$db->setQuery("DROP TABLE IF EXISTS `#__breezingcommerce_plugin_payment_concordpay`");
		$db->query();
	}
}
