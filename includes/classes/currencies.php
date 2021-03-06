<?php
/**
 * currencies Class.
 *
 * @package classes
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: DrByte  Sun Oct 18 03:20:05 2015 -0400 Modified in v1.5.5 $
 */
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}
/**
 * currencies Class.
 * Class to handle currencies
 *
 * @package classes
 */
class currencies extends base
{
    public $currencies;

    public function __construct()
    {
        global $db;
        $this->currencies = array();
        $currencies_query = "select code, title, symbol_left, symbol_right, decimal_point, thousands_point, decimal_places, value
                         from " . TABLE_CURRENCIES;
        $currencies = $db->Execute($currencies_query);

        while (!$currencies->EOF) {
            $this->currencies[$currencies->fields['code']] = array(
            'title' => $currencies->fields['title'],
            'symbol_left' => $currencies->fields['symbol_left'],
            'symbol_right' => $currencies->fields['symbol_right'],
            'decimal_point' => $currencies->fields['decimal_point'],
            'thousands_point' => $currencies->fields['thousands_point'],
            'decimal_places' => (int)$currencies->fields['decimal_places'],
            'value' => $currencies->fields['value']);
            $currencies->MoveNext();
        }
    }

    // class methods
    public function format($number, $calculate_currency_value = true, $currency_type = '', $currency_value = '')
    {
        if (empty($currency_type)) {
            $currency_type = (isset($_SESSION['currency']) ? $_SESSION['currency'] : DEFAULT_CURRENCY);
        }

        if ($calculate_currency_value == true) {
            $rate = (zen_not_null($currency_value)) ? $currency_value : $this->currencies[$currency_type]['value'];
            $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format(zen_round($number * $rate, $this->currencies[$currency_type]['decimal_places']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $this->currencies[$currency_type]['symbol_right'];

            // Special Case: if the selected currency is in the european euro-conversion and the default currency is euro,
            // then the currency will displayed in both the national currency and euro currency
            if ((DEFAULT_CURRENCY == 'EUR') && ($currency_type == 'DEM' || $currency_type == 'BEF' || $currency_type == 'LUF' || $currency_type == 'ESP' || $currency_type == 'FRF' || $currency_type == 'IEP' || $currency_type == 'ITL' || $currency_type == 'NLG' || $currency_type == 'ATS' || $currency_type == 'PTE' || $currency_type == 'FIM' || $currency_type == 'GRD')) {
                $format_string .= ' <small>[' . $this->format($number, true, 'EUR') . ']</small>';
            }
        } else {
            $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format(zen_round($number, $this->currencies[$currency_type]['decimal_places']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $this->currencies[$currency_type]['symbol_right'];
        }

        if (IS_ADMIN_FLAG === false && (DOWN_FOR_MAINTENANCE=='true' and DOWN_FOR_MAINTENANCE_PRICES_OFF=='true') and (!strstr(EXCLUDE_ADMIN_IP_FOR_MAINTENANCE, $_SERVER['REMOTE_ADDR']))) {
            $format_string= '';
        }

        return $format_string;
    }

    public function rateAdjusted($number, $calculate_currency_value = true, $currency_type = '', $currency_value = '')
    {
        if (empty($currency_type)) {
            $currency_type = (isset($_SESSION['currency']) ? $_SESSION['currency'] : DEFAULT_CURRENCY);
        }

        if ($calculate_currency_value == true) {
            $rate = (zen_not_null($currency_value)) ? $currency_value : $this->currencies[$currency_type]['value'];
            $result = zen_round($number * $rate, $this->currencies[$currency_type]['decimal_places']);
        } else {
            $result = zen_round($number, $this->currencies[$currency_type]['decimal_places']);
        }
        return $result;
    }

    public function value($number, $calculate_currency_value = true, $currency_type = '', $currency_value = '')
    {
        if (empty($currency_type)) {
            $currency_type = (isset($_SESSION['currency']) ? $_SESSION['currency'] : DEFAULT_CURRENCY);
        }

        if ($calculate_currency_value == true) {
            if ($currency_type == DEFAULT_CURRENCY) {
                $rate = (zen_not_null($currency_value)) ? $currency_value : 1/$this->currencies[$_SESSION['currency']]['value'];
            } else {
                $rate = (zen_not_null($currency_value)) ? $currency_value : $this->currencies[$currency_type]['value'];
            }
            $currency_value = zen_round($number * $rate, $this->currencies[$currency_type]['decimal_places']);
        } else {
            $currency_value = zen_round($number, $this->currencies[$currency_type]['decimal_places']);
        }

        return $currency_value;
    }

    public function normalizeValue($valueIn, $currencyType = null)
    {
        if (!isset($currencyType)) {
            $currencyType = (isset($_SESSION['currency']) ? $_SESSION['currency'] : DEFAULT_CURRENCY);
        }
        $value = str_replace($this->currencies[$currencyType]['decimal_point'], '.', $valueIn);
        return $value;
    }

    public function is_set($code)
    {
        if (isset($this->currencies[$code]) && zen_not_null($this->currencies[$code])) {
            return true;
        } else {
            return false;
        }
    }

    public function get_value($code)
    {
        return $this->currencies[$code]['value'];
    }

    public function get_decimal_places($code)
    {
        return $this->currencies[$code]['decimal_places'];
    }

    public function display_price($products_price, $products_tax, $quantity = 1)
    {
        return $this->format(zen_add_tax($products_price, $products_tax) * $quantity);
    }
}
