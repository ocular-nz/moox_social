<?php
namespace TYPO3\MooxSocial\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Dominic Martin <dm@dcn.de>, DCN GmbH
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package moox_social
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Facebook extends \TYPO3\MooxSocial\Domain\Model\Post {
	
	/**
	 * Url Dummy for requesting profile image from graph url
	 *
	 * @var string
	 */
	public $graphApiProfileImageUrl = "http://graph.facebook.com/###id###/picture?type=###type###";
	
	/**
	 * Story
	 *
	 * @var \string
	 */
	protected $story;
	
	/**
	 * Message
	 *
	 * @var \string
	 */
	protected $message;
	
	/**
	 * Autor-Bild
	 *
	 * @var \string
	 */
	protected $authorImageUrl;
	
	/**
	 * Autor-Bild (groß)
	 *
	 * @var \string
	 */
	protected $authorImageLargeUrl;
	
	/**
	 * Returns the default post action as Facebook story
	 *
	 * @return \string $story
	 */
	public function getStory() {
		return $this->action;
	}	
	
	/**
	 * Returns the default post text as Facebook message
	 *
	 * @return \string $message
	 */
	public function getMessage() {
		//return preg_replace("#(\r|\n)#", '##br##', trim($this->text));
		return nl2br(trim($this->text));
	}
	
	/**
	 * Returns url of default author profile image
	 *
	 * @return \string $authorImageUrl
	 */
	public function getAuthorImageUrl() {
		
		return $this->prepareProfileImageUrl();
	}		
	
	/**
	 * Returns url of default author profile image (large)
	 *
	 * @return \string $authorImageLargeUrl
	 */
	public function getAuthorImageLargeUrl() {
		
		return $this->prepareProfileImageUrl("large");
	}
	
	/**
	 * Get url of profile image from graph api
	 *
	 * @return \string $url
	 */
	public function prepareProfileImageUrl($type = "square") {
		
		// http://graph.facebook.com/###id###/?fields=picture.width(720).height(720)
		
		$url = $this->graphApiProfileImageUrl;
		
		$url = str_replace("###id###",$this->authorId,$url);
		
		$url = str_replace("###type###",$type,$url);
				
		return $url;
	}
	
}
?>