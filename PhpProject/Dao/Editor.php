<?php
/**
 * This file defines the class \FXP\Api\Editor.
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
     * Defines a object for holding some informations of a FAQ Editor.
     *
     * @property string $name The Name of the current editor.
     * @property int $lastEdit The PHP timestamp of the last FAQ item change by this editor.
     * @since    v0.1a
     */
    class Editor
    {

        # <editor-fold defaultstate="collapsed" desc="- - - -   P R I V A T E   F I E L D S   - - - - - - - - - - - - - - - - - - - - -">

        private $data;

        #</editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - >   C O N S T R U C T O R   - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Inits a new Instance.
         *
         * @param string $name The Name of the current editor.
         * @param string|int $lastEdit The PHP timestamp of the last FAQ item change by this editor.
         */
        public function __construct( $name, $lastEdit )
        {
            $this->data = array( 'name' => $name );
            if ( \is_string( $lastEdit ) ) {
                $this->data['lastedit'] = \strtotime( $lastEdit ); }
            else { $this->data['lastedit'] = \intval( $lastEdit ); }
        }

        #</editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   G E T T E R S   - - - - - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Magic getter method override.
         *
         * @param string $name
         * @return mixed
         */
        public function __get( $name )
        {
            $lowerName = \strtolower( $name );
            if ( !\in_array( $lowerName, $this->data) ) { return false; }
            return $this->data[$lowerName];
        }
        /**
         * Returns the Name of the current Editor person.
         *
         * @return string
         */
        public function getName()
        {
            return $this->data['name'];
        }
        /**
         * Returns the Unix timestamp of last editing by current editor.
         *
         * @return integer
         */
        public function getLastEdit()
        {
            return $this->data['lastedit'];
        }

        #</editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   S E T T E R S   - - - - - - - - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Magic setter method override.
         *
         * @param string $name The Name of the field to set a new value for. (name|lastedit caseless)
         * @param mixed  $value THe new value to set.
         */
        public function __set( $name, $value )
        {
            switch ( \strtolower( $name ) )
            {
                case 'name':
                    $this->data['name'] = $value;
                    break;
                case 'lastedit':
                    if ( \is_int( $value ) ) {
                        $this->data['lastedit'] = $value; }
                    else if ( \is_string( $value ) ) {
                        $this->data['lastedit'] = \strtotime( $value ); }
                    break;
            }
        }
        /**
         * Sets the name of the current editor person.
         * 
         * @param string $name The Name to set.
         */
        public function setName( $name )
        {
            $this->data['name'] = $name;
        }
        /**
         * Sets the last editing datetime for current editor.
         * 
         * @param int $timestamp The last editing datetime for current editor.
         */
        public function setLastEdit( $timestamp )
        {
            $this->lastEdit = $timestamp;
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   P U B L I C   M E T H O D S   - - - - - - - - - - - - - - - - - - - - -">

        /**
         * Writes the data of current editor, as a XML element with the name
         * 'Editor' and the 2 attributes 'name' and 'lastedit'.
         *
         * @param \XMLWriter $writer Here the XML element will be written.
         */
        public function writeXml( \XMLWriter $writer )
        {
            $writer->startElement( 'Editor' );
            $writer->writeAttribute( 'name', $this->data['name'] );
            $writer->writeAttribute(
                'lastedit',
                \strftime( '%Y-%m-%d 00:00:00', $this->data['lastedit'] ) );
            $writer->endElement();
        }

        # </editor-fold>

        # <editor-fold defaultstate="collapsed" desc="- - - -   P U B L I C   S T A T I C   M E T H O D S   - - - - - - - - - - - - - -">

        /**
         * Extracts a {@see \FXP\Api\Dao\Editor} Instance from defined
         * SimpleXMLElement if it is a valid Element. (Requires the attributes
         * name and lastedit)
         *
         * @param \SimpleXMLElement $element Element with the Editor data.
         * @return \FXP\Api\Dao\Editor Returns the Editor, or (bool)FALSE
         */
        public static function ParseXml(\SimpleXMLElement $element)
        {
            if ( \is_null( $element['name'] ) ) { return false; }
            if ( \is_null( $element['lastedit'] ) ) { return false; }
            $name     = \strval( $element['name'] );
            $lastedit = \strtotime( $element['lastedit'] );
            return new \FXP\Api\Dao\Editor( $name, $lastedit );
        }

        # </editor-fold>

    }

}
