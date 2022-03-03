<?php

// No direct access
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
class CrBc_Plugins_Payment_Concordpay extends JTable
{
	/**
	 * Primary Key.
	 *
	 * @var integer
	 * @since 3.8.0
	 */
	public $identity = null;

	/**
	 * @var string
	 * @since 3.8.0
	 */
	public $info = null;

	/**
	 * Constructor.
	 *
	 * @param   object $table Database connector object.
	 * @since 3.8.0
	 */
	public function __construct($table)
	{
		parent::__construct($table, 'identity', JFactory::getDBO());
	}
}

