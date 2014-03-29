<?php
/**
 * This file defines the DAO class \FXP\Api\Dao\Category.
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
     * Description of Category
     *
     * @property-read string $alias The unique category alias
     * @property string $name The category name.
     * @property string $keywords All keywords for current category, separated by comma.
     * @property array $faqs All FAQs of this category as {@see \FXP\Api\Dao\Faq} array.
     * @since    v0.1a
     */
    class Category implements ICategory
    {

        # <editor-fold defaultstate="collapsed" desc="- - - -   P R I V A T E   F I E L D S   - - - - - - - - - - - - - - - - - - - - -">

        private $data;

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - >   C O N S T R U C T O R   - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Inits a new Instance.
         *
         * @param \FXP\Api\Dao\IUniqueCategoryCollection $owner The collection, owning this category.
         * @param string $alias The unique, readonly category alias
         * @param string $name The category name
         * @param string $keywords All keywords for category, separated by comma.
         */
        public function __construct( \FXP\Api\Dao\IUniqueCategoryCollection $owner, $alias, $name, $keywords )
        {
            $this->data = array(
                'alias' => $alias,
                'name' => $name,
                'keywords' => $keywords,
                'owner' => $owner,
                'faqs' => array()
            );
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   G E T T E R S   - - - - - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Overrides the magic getter.
         *
         * @param string $name
         * @return mixed (boolean)FALSE on error.
         */
        public function __get ( $name )
        {
            $lowerName = \strtolower( $name );
            switch ( $lowerName )
            {
                case 'name':
                case 'alias':
                case 'keywords':
                case 'faqs': return $this->data[$lowerName];
                default:
                    $matches = null;
                    if ( !\preg_match( '~^faq[_-]([a-z][a-z0-9_.-]*)$~', $lowerName, $matches ) ) { return false; }
                    $idx = $this->indexOfFaqAlias( $matches[1] );
                    if ( $idx < 0 ) { return false; }
                    return $this->data['faqs'][$idx];
            }
        }

        /**
         * Returns the unique category alias
         *
         * @return string Alias
         */
        public function getAlias()
        {
            return $this->data['alias'];
        }

        /**
         * Return the category Name/Title.
         *
         * @return string Name/Title
         */
        public function getName()
        {
            return $this->data['name'];
        }

        /**
         * Return the category keywords (separated by comma)
         *
         * @return string Name/Title
         */
        public function getKeywords()
        {
            return $this->data['keywords'];
        }

        /**
         * Returns the Faq at defined index.
         *
         * @param  int $index
         * @return \FXP\Api\Dao\Faq or (boolean)FALSE
         */
        public function getFaq( $index )
        {
            if ( $index < 0 || $index >= \count( $this->data['faqs'] ) ) { return false; }
            return $this->data['faqs'][$index];
        }

        /**
         * Returns the Faq from current category, with defined alias.
         *
         * @param  string $faqAlias The alias of the required FAQ.
         * @return \FXP\Api\Dao\Faq or (boolean)FALSE
         */
        public function getFaqByAlias ( $faqAlias )
        {
            $index = $this->indexOfFaqAlias( $faqAlias );
            if ( $index < 0 ) { return false; }
            return $this->data['faqs'][$index];
        }

        /**
         * Returns all currently defined faqs inside this category.
         *
         * @return array All FAQs of this category as {@see \FXP\Api\Dao\Faq} array.
         */
        public function getFaqs()
        {
            return $this->data['faqs'];
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   S E T T E R S   - - - - - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Overrides the magic setter.
         *
         * @param string $name
         * @param string|array $value
         */
        public function __set( $name, $value )
        {
            $lowerName = \strtolower( $name );
            switch ( $lowerName )
            {
                case 'name':
                    $this->setName( $value );
                    break;
                case 'keywords':
                    $this->setKeywords( $value );
                    break;
                case 'faqs':
                    if ( \is_array( $value ) ) { $this->setFaqs( $value ); }
                    break;
            }
        }

        /**
         * Sets a new category name.
         *
         * @param string $value The category name
         */
        public function setName( $value )
        {
            $this->data['name'] = $value;
        }

        /**
         * Sets the category keywords. (separated by comma)
         *
         * @param string $value The category keywords
         */
        public function setKeywords( $value )
        {
            $this->data['keywords'] = $value;
        }

        /**
         * Sets all faqs of this category.
         *
         * @param array $value A {@see \FXP\Api\Dao\Faq} array.
         */
        public function setFaqs( array $value )
        {
            $this->data['faqs'] = $value;
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   P U B L I C   M E T H O D S   - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Returns the index of the faq (for this category) with the defined faq alias.
         *
         * @param  string $faqAlias The FAQ alias
         * @return int Returns the index, or -1 if no faq with defined alias exists for this category.
         */
        public function indexOfFaqAlias( $faqAlias )
        {
            for ( $i = 0; $i < \count($this->data['faqs']); ++$i )
            {
                if ( $this->data['faqs'][$i]->getAlias() === $faqAlias ) return $i;
            }
            return -1;
        }

        /**
         * Returns if there exists already a faq with the defined faq-alias.
         *
         * @param string $faqAlias The faq alias to search for.
         * @return boolean
         */
        public function globalFaqAliasExists( $faqAlias )
        {
            if ( $this->containsFaqAlias( $faqAlias ) ) { return true; }
            return $this->data['owner']->containsFaqAlias( $faqAlias );
        }

        /**
         * Returns if a faq with defined alias already exists for this category.
         *
         * @param  string $faqAlias
         * @return boolean
         */
        public function containsFaqAlias( $faqAlias )
        {
            foreach ( $this->data['faqs'] as $faq )
            {
                if ( $faq->alias === $faqAlias ) { return true; }
            }
            return false;
        }

        /**
         * Saves the category and all contained FAQs.
         *
         * @param string $xmlFolder The 'xml' folder, where the whole PHP-FAQ is stored.
         */
        public function save( $xmlFolder )
        {
            $folder = \rtrim( $xmlFolder, \DIRECTORY_SEPARATOR ) . \DIRECTORY_SEPARATOR . $this->getAlias();
            if ( !\is_dir( $folder ) ) { \mkdir ( $folder, 0755, true ); }
            $writer = new \XMLWriter();
            $file = $folder . \DIRECTORY_SEPARATOR . 'category.xml';
            $writer->openUri( $file );
            $writer->setIndent( true ); $writer->setIndentString( '  ' );
            $writer->startDocument( '1.0', 'utf-8' );
            $this->writeXml( $writer );
            $writer->endDocument();
            $writer->flush();
            $this->saveFaqs( $xmlFolder );
            # Remove folders of unknown faqs
            $this->removeUnknownFaqFolders( $folder );
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   P R O T E C T E D   M E T H O D S   - - - - - - - - - - - - - - - - - -">

        protected function writeXml( \XMLWriter $writer )
        {
            $writer->startElement( 'Category' );
            $writer->writeAttribute( 'alias', $this->data['alias'] );
            $writer->writeAttribute( 'name', $this->data['name'] );
            $writer->writeAttribute( 'keywords', $this->data['keywords'] );
            $writer->writeComment( 'Die FAQs sind hier noch mal redundant aufgelistet um die Reihenfolge vorzugeben.' );
            if ( \count( $this->data['faqs'] ) > 0 ) {
                $writer->startElement( 'Faqs' );
                foreach ( $this->data['faqs'] as $faq) {
                    $writer->startElement( 'Faq' );
                    $writer->writeAttribute( 'alias', $faq->alias );
                    $writer->endElement();
                }
                $writer->endElement();
            }
            $writer->endElement();
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   P R I V A T E   M E T H O D S   - - - - - - - - - - - - - - - - - - - -">

        // Saves all FAQ in they folders
        private function saveFaqs( $xmlFolder )
        {
            if ( \count( $this->data['faqs'] ) > 0 ) {
                foreach ( $this->data['faqs'] as $faq) {
                    $faq->save( $xmlFolder );
                }
            }
        }

        // Remove folders of unknown faqs
        private function removeUnknownFaqFolders( $categoryFolder )
        {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator( $categoryFolder ),
                \RecursiveIteratorIterator::SELF_FIRST);
            foreach ( $iterator as $item ) {
                if ( !$item->isDir() ) { continue; }
                $alias = $item->getFilename();
                $faqFolder = $item->getRealpath();
                if ( $this->containsFaqAlias( $alias ) ) { continue; }
                \FXP\Api\Helpers\deleteFolder( $faqFolder );
            }
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   P U B L I C   S T A T I C   M E T H O D S   - - - - - - - - - - - - - -">

        /**
         * Loads a {@see \FXP\Api\Dao\Faq} instance from file system.
         *
         * @param string $xmlFolder The 'xml' folder, where the whole PHP-FAQ is stored.
         * @param \FXP\Api\Dao\ICategory $category The owning category.
         * @param string $alias THe faq alias
         * @return \FXP\Api\Dao\Faq Returns the Faq, or (boolean)FALSE.
         */
        public static function Load(
            $xmlFolder, \FXP\Api\Dao\CategoryCollection $categories, $alias )
        {
            $file = \rtrim( $xmlFolder, \DIRECTORY_SEPARATOR ) . '/' .
                    $alias . 'category.xml';
            if ( !\file_exists( $file ) ) { return false; }
            $xml = \simplexml_load_file( $file );
            if ( \is_null( $xml['alias'] ) || \is_null( $xml['author'] ) ||
                 !isset($xml->Question) || !isset($xml->Keywords) ) { return false; }
            $alias  = (string) $xml['alias']; $author = (string) $xml['author'];
            $question = (string) $xml->Question;
            $keywords = (string) $xml->Keywords;
            $editors = self::readEditors($xml);
            $created = null;
            if ( !\is_null( $xml['created'] ) ) {
                $created = \strtotime( (string) $xml['created'] ); }
            $result = new \FXP\Api\Dao\Faq(
                $category, $alias, $author, $created, $keywords, $question,
                $editors );
            return $result;
        }

        # </editor-fold>

    }

}