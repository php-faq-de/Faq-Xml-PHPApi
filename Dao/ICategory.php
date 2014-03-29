<?php
/**
 * This file defines the DAO interface \FXP\Api\Dao\ICategory.
 *
 * @author     Ulf [UniKado] Kadner <ulfikado@gmail.com>
 * @license    LGPL v3
 * @package    FXP\Api
 * @subpackage DataAccessObjects
 * @version    0.1a
 */

namespace FXP\Api\Dao
{

    /**
     * Defines the interface  that every PHP-FAQ category must implement.
     *
     * @since    v0.1a
     */
    interface ICategory
    {

        /**
         * Returns the unique category alias
         *
         * @return string Alias
         */
        public function getAlias();
        /**
         * Return the category Name/Title.
         *
         * @return string Name/Title
         */
        public function getName();
        /**
         * Returns if there globaly already exists a faq with the defined faq-alias.
         *
         * @param string $faqAlias The faq alias to search for.
         * @return boolean
         */
        public function globalFaqAliasExists( $faqAlias );

    }

}
