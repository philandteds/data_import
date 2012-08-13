<?php

class XMLMultiLanguages extends XmlHandlerPHP
{
	var $handlerTitle = 'Multi Languages Handler';

	var $current_loc_info = array();

	var $logfile = 'multilanguages_import.log';

	var $remoteID = "";

	const REMOTE_IDENTIFIER = 'xmlmultilanguage_';	

	function MultiLanguagesHandler()
	{}

	function writeLog( $message, $newlogfile = '')
	{
		if($newlogfile)
			$logfile = $newlogfile;
		else
			$logfile = $this->logfile;
		
		$this->logger->write( self::REMOTE_IDENTIFIER.$this->current_row->getAttribute('id').': '.$message , $logfile );
	}
	
	// mapping for xml field name and attribute name in ez publish
	function geteZAttributeIdentifierFromField()
	{
		$field_name = $this->current_field->getAttribute('name');
		return $field_name; 
	}
	
	// handles xml fields before storing them in ez publish
	function getValueFromField()
	{
		switch( $this->current_field->getAttribute('name') )
		{
			case 'publishdate':
			{
				$return_unix_ts = time();
				
				$us_formated_date = $this->current_field->nodeValue;
				$parts = explode('/', $us_formated_date );
				
				if( count( $parts ) == 3)
				{
					$return_unix_ts = mktime( 0,0,0, $parts[0], $parts[1] , $parts[2] );
				}
				return $return_unix_ts;
				
				break;
			}
			
			default:
			{
				return $this->current_field->nodeValue;
			}
		}
	}
	
	// logic where to place the current content node into the content tree
	function getParentNodeId()
	{
		$parent_id = 2; // fallback is the root node
		
		return $this->current_row->getAttribute('parent_id');

		if( $parent_remote_id )
		{
			$eZ_object = eZContentObject::fetchByNodeID( $parent_remote_id );

			if( $eZ_object )
			{
				$parent_id = $eZ_object->attribute('main_node_id');
			}
		}

		return $parent_id;
	}

	function getDataRowId()
	{
		return $this->current_row->getAttribute('remote_id');
	}

	function getTargetLanguage()
	{
		return $this->current_row->getAttribute( 'language' );
	}

	function getTargetContentClass()
	{
		return $this->current_row->getAttribute( 'type' );
	}

	function readData()
	{
		return $this->parse_xml_document( 'extension/ezxmlexport/exports/xml/support_section7/support_section7.transformed.xml', 'all' );
	}

	function post_publish_handling( $eZ_object, $force_exit )
	{
	    $force_exit = false;		
		return true;
	}

}

?>