<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Mdsupport
 * @subpackage Mdsupport/admin/partials
 */
require_once ABSPATH .'wp-admin/includes/template.php';

class WPSE_139269_Walker_Category_Radio_Checklist extends Walker_Category_Checklist
{

    function walk($elements, $max_depth, $args = array())
    {
        $output = parent::walk($elements, $max_depth, $args);
        $output = str_replace(
            array('type="checkbox"', "type='checkbox'"), array('type="radio"', "type='radio'"), $output
        );

        return $output;
    }


}