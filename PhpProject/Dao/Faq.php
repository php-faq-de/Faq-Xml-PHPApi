<?php
/**
 * This file defines the DAO class \FXP\Api\Faq.
 *
 * @author     Ulf [UniKado] Kadner <ulfikado@gmail.com>
 * @license    LGPL v3
 * @package    FXP\Api
 * @subpackage DateAccessObjects
 * @version    0.1a
 */

namespace FXP\Api\Dao
{

    /**
     * Define a DAO object representing a FAQ item.
     *
     * @property-read ICategory $category The owning category.
     * @property string $alias The unique Alias of this FAQ item ^[A-Za-z0-0_.-]+$
     * @property string $author The name of the initial faq author
     * @property int $created Optional unix timestamp of the creation date for current faq.
     * @property string $keywords Comma separated list of all faq keywords
     * @property string $question The Question string of this faq.
     * @property array $editors A array with elements of type {@see \FXP\Api\Dao\Editor}
     * @since    v0.1a
     */
    class Faq
    {

        # <editor-fold defaultstate="collapsed" desc="- - - -   P R I V A T E   F I E L D S   - - - - - - - - - - - - - - - - - - - - -">

        private $data;

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - >   C O N S T R U C T O R   - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Inits a new instance.
         *
         * @param \FXP\Api\Dao\ICategory $category The owning category.
         * @param string $alias The unique Alias of this FAQ item ^[A-Za-z0-0_.-]+$
         * @param string $author The name of the initial faq author
         * @param int $created Optional unix timestamp of the creation date for current faq.
         * @param string $keywords Comma separated list of all faq keywords
         * @param string $question The Question string of this faq.
         * @param array $editors A array with elements of type {@see \FXP\Api\Dao\Editor}
         */
        protected function __construct(
            \FXP\Api\Dao\ICategory $category, $alias, $author, $created,
            $keywords, $question, array $editors )
        {
            $this->data['alias']    = $alias;
            $this->data['author']   = $author;
            $this->data['created']  = $created;
            $this->data['keywords'] = $keywords;
            $this->data['question'] = $question;
            $this->data['editors']  = $editors;
            $this->data['category'] = $category;
        }


        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   G E T T E R S   - - - - - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * The magic get method.
         *
         * @param string $name
         * @return mixed
         */
        public function __get( $name )
        {
            $lowerName = \strtolower( $name );
            if ( ! \in_array( $lowerName, $this->data ) ) { return false; }
            return $this->data[$lowerName];
        }
        /**
         * Returns the unique alias of the faq.
         *
         * @return string
         */
        public function getAlias()
        {
            return $this->data['alias'];
        }
        /**
         * Returns the name of the initial faq author.
         *
         * @return string
         */
        public function getAuthor()
        {
            return $this->data['author'];
        }
        /**
         * Returns the optional unix timestamp of the creation date for current faq.
         *
         * @return int|null
         * @see \FXP\Api\Dao\Faq::hasCreationDate()
         */
        public function getCreationDate()
        {
            return $this->data['created'];
        }
        /**
         * Returns a comma separated list of all faq keywords.
         *
         * @return string
         */
        public function getKeyWords()
        {
            return $this->data['keywords'];
        }
        /**
         * Returns the Question string of this faq.
         *
         * @return string
         */
        public function getQuestion()
        {
            return $this->data['question'];
        }
        /**
         * Returns a array with elements of type {@see \FXP\Api\Dao\Editor}
         *
         * @return array
         */
        public function getEditors()
        {
            return $this->data['editors'];
        }
        /**
         * Returns the editor at defined index.
         *
         * @param int $index
         * @return \FXP\Api\Dao\Editor
         */
        public function getEditor( $index )
        {
            return $this->data['editors'][$index];
        }
        /**
         * Returns the FAQ Answer HTML. Remember its not a integral member
         * of this class! It will be loaded from the FAQ related answer.html
         * file.
         *
         * @param string $xmlFolder The 'xml' folder, where the whole PHP-FAQ is stored.
         * @return string
         */
        public function getAnswer( $xmlFolder )
        {
            $file = \rtrim( $xmlFolder, \DIRECTORY_SEPARATOR ) . '/' .
                    $this->category->getAlias() . '/' . $this->alias . '/answer.html';
            if ( !\file_exists( $file ) ) { return ''; }
            return file_get_contents( $file );
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   S E T T E R S   - - - - - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * The magic set method.
         *
         * @param string $name
         * @param mixed  $value
         */
        public function __set( $name, $value )
        {
            switch ( \strtolower($name ) )
            {
                case 'alias':
                    $this->setAlias( $value );      break;
                case 'created':
                    $this->setCreateDate( $value ); break;
                case 'author':
                    $this->setAuthor( $value );     break;
                case 'keywords':
                    $this->setKeywords( $value );   break;
                case 'question':
                    $this->setQuestion( $value );   break;
                case 'editors':
                    $this->setEditors( $value );    break;
            }
        }
        /**
         * Sets a new alias an returns if setting is successfull.
         *
         * @param string $value The new alias
         * @return boolean
         */
        public function setAlias( $value )
        {
            if ( $this->data['alias'] === $value )  { return true; }
            if ( $this->category->containsFaqAlias( $value ) ) { return false; }
            if ( !\FXP\Api\Dao\Faq::IsValidAlias( $value ) ) { return false; }
            $this->data['alias'] = $value;
            return true;
        }
        /**
         * Sets a new author name.
         *
         * @param string $value
         */
        public function setAuthor( $value )
        {
            $this->data['author'] = $value;
        }
        /**
         * Sets a creation date.
         *
         * @param integer|string|null $value
         */
        public function setCreateDate( $value )
        {
            if ( empty( $value ) ) { $this->data['created'] = null; }
            else if ( \is_int( $value ) ) { $this->data['created'] = $value; }
            else if ( \is_string( $value ) ) {
                $this->data['created'] = \strtotime( $value ); }
            else { $this->data['created'] = null; }
        }
        /**
         * Set the keywords. (separated by comma)
         *
         * @param string $value
         */
        public function setKeywords( $value )
        {
            $this->data['keywords'] = $value;
        }
        /**
         * Sets the question string.
         *
         * @param string $value
         */
        public function setQuestion( $value )
        {
            $this->data['question'] = $value;
        }
        /**
         * Sets all editors (editing persons) of the faq.
         *
         * @param array $value A array of {@see \FXP\Api\Dao\Editor} elements.
         * @return boolean
         */
        public function setEditors( array $value )
        {
            if ( \count( $value ) > 0 ) {
                foreach ( $value as $editor ) {
                    if ( ! ( $editor instanceof \FXP\Api\Dao\Editor ) ) {
                        return false; } }
            }
            $this->data['editors'] = $value;
            return true;
        }
        /**
         * Sets the current FAQ depending answer HTML.
         *
         * @param string $value The answer HTML
         * @param string $xmlFolder The 'xml' folder, where the whole PHP-FAQ is stored.
         */
        public function setAnswer( $value, $xmlFolder)
        {
            $file = \rtrim( $xmlFolder, \DIRECTORY_SEPARATOR ) . '/' .
                    $this->category->getAlias() . '/' . $this->alias . '/answer.html';
            \file_put_contents( $file, $value );
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   P U B L I C   M E T H O D S   - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Returns if a valid creation date is defined.
         *
         * @return boolean
         */
        public function hasCreationDate()
        {
            return !empty( $this->data['created'] );
        }
        /**
         * Return the count of all defined editors.
         *
         * @return int
         */
        public function countEditors()
        {
            return \count( $this->data['editors'] );
        }
        /**
         * Return the index of the editor with defined name.
         *
         * @param string $editorName
         * @return int Return the Index, or -1 if no editor with the name exists.
         */
        public function indexOfEditor( $editorName )
        {
            for ( $i = 0; $i < \count( $this->data['editors'] ); ++$i ) {
                if ( $this->data['editors'][$i]->name === $editorName ) {
                    return $i; } }
            return -1;
        }
        /**
         * Saves the current faq data to associated faq.xml.
         *
         * @param string $xmlFolder The 'xml' folder, where the whole PHP-FAQ is stored.
         */
        public function save( $xmlFolder )
        {
            $writer = new \XMLWriter();
            $file = \rtrim( $xmlFolder, \DIRECTORY_SEPARATOR ) . '/' .
                    $this->category->getAlias() . '/' . $this->alias . '/faq.xml';
            $writer->openUri( $file );
            $writer->setIndent( true ); $writer->setIndentString( '  ' );
            $writer->startElement( 'Faq' );
            $writer->writeAttribute( 'alias', $this->data['alias'] );
            $writer->writeAttribute( 'author', $this->data['author'] );
            if ( \is_int( $this->data['created'] ) ) { $writer->writeAttribute(
                'created', \strftime('%Y-%m-%d 00:00:00', $this->data['created']) ); }
            $writer->writeElement( 'Question', $this->data['question'] );
            $writer->writeElement( 'Keywords', $this->data['keywords'] );
            if ( $this->countEditors() > 0 ) {
                $writer->startElement( 'Editors' );
                foreach ( $this->data['editors'] as $editor ) {
                    $editor->writeXml( $writer ); }
                $writer->endElement(); }
            $writer->endElement();
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   P U B L I C   S T A T I C   M E T H O D S   - - - - - - - - - - - - - -">

        /**
         * Checks if the defined Alias uses a valid format.
         *
         * @param  string $alias The alias to check.
         * @return boolean
         */
        public static function IsValidAlias( $alias )
        {
            return (bool) \preg_match( '~^[a-z][a-z0-9_.-]+$~', $alias );
        }
        /**
         * Loads a {@see \FXP\Api\Dao\Faq} instance from file system.
         *
         * @param string $xmlFolder The 'xml' folder, where the whole PHP-FAQ is stored.
         * @param \FXP\Api\Dao\ICategory $category The owning category.
         * @param string $alias THe faq alias
         * @return \FXP\Api\Dao\Faq Returns the Faq, or (boolean)FALSE.
         */
        public static function Load(
            $xmlFolder, \FXP\Api\Dao\ICategory $category, $alias )
        {
            $file = \rtrim( $xmlFolder, \DIRECTORY_SEPARATOR ) . '/' .
                    $category->getAlias() . '/' . $alias . '/faq.xml';
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

        # <editor-fold defaultstate="collapsed" desc="- - - -   P R I V A T E   S T A T I C   M E T H O D S   - - - - - - - - - - - - -">

        private static function readEditors(\SimpleXMLElement $xml)
        {
            $editors = array();
            if ( isset( $xml->Editors ) && isset( $xml->Editors->Editor ) )
            {
                foreach ( $xml->Editors->Editor as $element ) {
                    if ( FALSE === ( $editor =
                         \FXP\Api\Dao\Editor::ParseXml($element) ) ) {
                         continue; }
                    $editors[] = $editor;
                }
            }
            return $editors;
        }

        # </editor-fold>

    }

}
