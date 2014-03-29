<?php
/**
 * This file defines the class \FXP\Api\Dao\CategoryCollection.
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
     * Description of \FXP\Api\Dao\CategoryCollection
     *
     * @since v0.1a
     */
    class CategoryCollection extends \ArrayObject implements \FXP\Api\Dao\IUniqueCategoryCollection
    {

        # <editor-fold defaultstate="collapsed" desc="- - - -   P R I V A T E   F I E L D S   - - - - - - - - - - - - - - - - - - - - -">

        private $xmlFolder;

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - >   C O N S T R U C T O R   - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Inits a new instance
         *
         * @param string $xmlFolder The 'xml' folder, where the whole PHP-FAQ is stored.
         * @param array $array The initial array with {@see \FXP\Api\Dao\Category} elements.
         */
        protected function __construct( $xmlFolder, array $array = array() )
        {
            parent::__construct( array() );
            $this->xmlFolder = $xmlFolder;
            foreach ( $array as $category )
            {
                if ( ! ( $category instanceof \FXP\Api\Dao\Category ) ) { continue; }
                $this[] = $category;
            }
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   G E T T E R S   - - - - - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Returns the category from defined index or alias.
         *
         * @param int|string $index Index or category alias
         * @return \FXP\Api\Dao\Category Description
         */
        public function offsetGet( $index )
        {
            if ( \is_string( $index ) )
            {
                $index = $this->indexOf( $index );
                if ( $index < 0 ) { return null; }
            }
            parent::offsetGet( $index );
        }

        /**
         * Overrides the magic setter and serves access to categories by all category aliases,
         * like <code>$this->category_alias</code> or <code>$this->{category-alias}</code>
         *
         * @param string $name THe category alias
         * @return \FXP\Api\Dao\Category or NULL
         */
        public function __get( $name )
        {
            $idx = $this->indexOf( $name );
            if ( $idx < 0 ) { return null; }
            return $this->offsetGet( $idx );
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   S E T T E R S   - - - - - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Sets a new Categoy at defined index.
         *
         * @param  int $index The Index
         * @param  \FXP\Api\Dao\Category $newval The category to set
         */
        public function offsetSet( $index, $newval )
        {
            if ( \is_null( $newval ) )
            {
                parent::offsetSet( $index, $newval );
                return;
            }
            if ( ! ( $newval instanceof \FXP\Api\Dao\Category ) ) { return; }
            parent::offsetSet( $index, $newval );
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   P U B L I C   M E T H O D S   - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Returns the index of the category with the defined alias.
         *
         * @param  string $categoryAlias Alias of the requested category.
         * @return int Index or -1 if no category exists for defined alias.
         */
        public function indexOf( $categoryAlias )
        {
            for ( $i = 0; $i < count( $this ); $i++ ) {
                if ( $this->offsetGet( $i )->getAlias() === $categoryAlias ) { return $i; }
            }
            return -1;
        }

        /**
         * Moves the category at index $index one position up (1 to 0, 2 to 1, etc.).)
         *
         * @param  int $index
         * @return boolean
         */
        public function moveCategoryUp( $index )
        {
            if ( $index < 1 || $index >= $this->count() ) { return false; }
            $array = $this->getArrayCopy();
            $out = \array_splice( $array, $index, 1 );
            \array_splice( $array, $index - 1, 0, $out );
            $this->exchangeArray( $array );
            return true;
        }

        /**
         * Moves the category at index $index one position down (0 to 1, 1 to 2, etc
         *
         * @param  int $index
         * @return boolean
         */
        public function moveCategoryDown( $index )
        {
            if ( $index < 0 || $index >= $this->count() - 1 ) { return false; }
            $array = $this->getArrayCopy();
            $out = \array_splice( $array, $index, 1 );
            \array_splice( $array, $index + 1, 0, $out );
            $this->exchangeArray( $array );
            return true;
        }

        /**
         * Saves all defined categories with the Faqs in defined 'xml' folder of php-faq-de/FAQ-Daten project.
         * Old, deleted categories or FAQs will be also deleted at file system
         *
         * @param string $xmlFolder The 'xml' folder, where the whole PHP-FAQ is stored.
         * @return boolean
         */
        public function save( $xmlFolder = null )
        {
            if ( !empty( $xmlFolder ) && \is_dir( $xmlFolder ) ) { $this->xmlFolder = $xmlFolder; }
            if ( empty( $this->xmlFolder ) ) { return false; }
            $file = \rtrim( $this->xmlFolder, \DIRECTORY_SEPARATOR ) . \DIRECTORY_SEPARATOR . 'categories.xml';
            $writer = new \XMLWriter();
            $writer->openUri( $file );
            $writer->setIndent( true ); $writer->setIndentString( '  ' );
            $writer->startDocument( '1.0', 'utf-8' );
            $writer->startElement( 'Categories' );
            if ( $this->count() > 0 ) {
                foreach ( $this as $category ) {
                    $writer->startElement( 'Category' );
                    $writer->writeAttribute( 'alias', $category->alias );
                    $writer->endElement();
                    $category->save( $this->xmlFolder );
                }
            }
            $writer->endElement();
            $writer->endDocument(); $writer->flush();
            # Remove folders of unknown categories
            $this->removeUnknownCategoryFolders();
            return true;
        }

        /**
         * Returns, if a category with the defined alias already exists in current collection
         *
         * @return bool
         */
        public function containsAlias( $categoryAlias )
        {
            return $this->indexOf( $categoryAlias ) > -1;
        }

        /**
         * Returns, if a faq with the defined alias already exists in current collection.
         *
         * @return bool
         */
        public function containsFaqAlias( $faqAlias )
        {
            foreach ( $this as $category )
            {
                if ( $category->containsFaqAlias( $faqAlias ) ) { return true; }
            }
            return false;
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   P R I V A T E   M E T H O D S   - - - - - - - - - - - - - - - - - - - -">

        // Remove folders of unknown categories
        private function removeUnknownCategoryFolders()
        {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator( $this->xmlFolder ),
                \RecursiveIteratorIterator::SELF_FIRST);
            foreach ( $iterator as $item ) {
                if ( !$item->isDir() ) { continue; }
                $alias = $item->getFilename();
                $categoryFolder = $item->getRealpath();
                if ( $this->containsAlias( $alias ) ) { continue; }
                \FXP\Api\Helpers\deleteFolder( $categoryFolder );
            }
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   P U B L I C   S T A T I C   M E T H O D S   - - - - - - - - - - - - - -">

        public static function Load( $xmlFolder )
        {
            $result = new \FXP\Api\Dao\CategoryCollection( $xmlFolder );
            if ( empty( $xmlFolder ) || !\is_dir( $xmlFolder ) ) { return $result; }
            $file = \rtrim( $xmlFolder, \DIRECTORY_SEPARATOR ) . \DIRECTORY_SEPARATOR . 'categories.xml';
        }

        # </editor-fold>

    }

}

