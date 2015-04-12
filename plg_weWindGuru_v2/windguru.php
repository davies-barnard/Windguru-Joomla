<?php
/**
* Wind Guru Plugin
* Displays Wind Guru Forecast inside Joomla content item or article
* Using the a modified version of the Wind Guru Webmaster Plugin
* Author: Chris Davies-Barnard  @ Web Ethical
* Copyright (C) by web-ethical.co.uk - GNU GPL v2
* Website: http://web-ethical.co.uk
* v2.0 Jan 2013
*/

defined( '_JEXEC' ) or die( 'Direct Access not allowed.' );


jimport( 'joomla.plugin.plugin' );

class plgContentWindguru extends JPlugin {

	function plgContentWindguru( &$subject, $params ) {
		parent::__construct( $subject, $params );
 	}

	function onContentPrepare($context, &$row, &$params, $limitstart=0 ) {
		
		//global $mainframe;
		
		// simple performance check to determine whether bot should process further
        if ( JString::strpos( $row->text, 'windguru' ) === false ) {
        	return true;
        }
        
        //Expression to search for.
        $regex = '/{windguru\s*.*?}/i';
        
        // check whether plugin has been unpublished
        if ( !$this->params->get( 'enabled', 1 ) ) {
        	$row->text = preg_replace( $regex, '', $row->text );
            return true;
        }
        
        // find all instances of plugin and put in $matches
        preg_match_all( $regex, $row->text, $matches );
 
        // Number of plugins
        $count = count( $matches[0] );
		
		if($count) {
		
			//Add the Windguru Style sheet
			$document = & JFactory::getDocument();
			$document->addStyleSheet(JURI::base(). 'plugins/content/windguru/windguru-1.7/wg_images/wgstyle.css');

			//load the windguru plugin file.
			require_once JPATH_ROOT.DS.'plugins/content/windguru/windguru-1.7/windguru.inc.php'; 
		
			//for each of the matches
			foreach($matches[0] as $wparam ) {
			
				$tempParams = preg_replace('/windguru|\s|\{|\}/','',$wparam);
				$location =  preg_split('/:/',$tempParams,2);
					
				$output =  windguru_forecast($location[0],$location[1]); 
				$output = "<div class='windguru_wrapper'>".$output."</div>";
				
				$postoutput = str_replace('wg_images', JURI::base().'/plugins/content/windguru/windguru-1.7/wg_images',$output); 
				
				$row->text = str_replace($wparam, $postoutput, $row->text);
			}
		} // End Count
		
		return true;

	} //End onPrepareContent
	

} //End Windguru Plugin


/******  VERSION CHANGES *****//**
v2 - replace jimport line for general plugin file for the joomla lib.
v2 - changed the construct call to __construct from JPlugin.
v2 - updated paths to Vaclav's plugin location which will be pre installed inside our wrapper.
v2 - removed type=MyISAM from Vaclavs mysql script for the creation of database tables that might be a local
issue so it was left it for the published version.
v2 - major rewrite of code within onPrepareContent.
END CHANGES **/
?>
