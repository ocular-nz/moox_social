<?php
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
 
$extensionClassesPath = t3lib_extMgm::extPath('moox_social') . 'Classes/';

return array(
	'tx_mooxsocial_facebookgettask' => $extensionClassesPath . 'Tasks/FacebookGetTask.php',
	'tx_mooxsocial_facebookgettask_additionalfieldprovider' => $extensionClassesPath . 'Tasks/FacebookGetTaskAdditionalFieldProvider.php',
        'tx_mooxsocial_twittergettask' => $extensionClassesPath . 'Tasks/TwitterGetTask.php',
	'tx_mooxsocial_twittergettask_additionalfieldprovider' => $extensionClassesPath . 'Tasks/TwitterGetTaskAdditionalFieldProvider.php',
        'tx_mooxsocial_youtubegettask' => $extensionClassesPath . 'Tasks/YoutubeGetTask.php',
	'tx_mooxsocial_youtubegettask_additionalfieldprovider' => $extensionClassesPath . 'Tasks/YoutubeGetTaskAdditionalFieldProvider.php',
        'tx_mooxsocial_flickrgettask' => $extensionClassesPath . 'Tasks/FlickrGetTask.php',
	'tx_mooxsocial_flickrgettask_additionalfieldprovider' => $extensionClassesPath . 'Tasks/FlickrGetTaskAdditionalFieldProvider.php',
        'tx_mooxsocial_slidesharegettask' => $extensionClassesPath . 'Tasks/SlideshareGetTask.php',
	'tx_mooxsocial_slidesharegettask_additionalfieldprovider' => $extensionClassesPath . 'Tasks/SlideshareGetTaskAdditionalFieldProvider.php'
);

?>