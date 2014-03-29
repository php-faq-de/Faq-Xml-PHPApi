<?php
/**
 * This file defines the DAO interface \FXP\Api\Dao\IUniqueCategoryCollection.
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
     * The Interface, that must be implemented by every category collection.
     *
     * @since    v0.1a
     */
    interface IUniqueCategoryCollection
    {

        /**
         * Returns, if a category with the defined alias already exists in current collection
         *
         * @return bool
         */
        public function containsAlias( $categoryAlias );

        /**
         * Returns, if a faq with the defined alias already exists in current collection.
         *
         * @return bool
         */
        public function containsFaqAlias( $faqAlias );

    }

}