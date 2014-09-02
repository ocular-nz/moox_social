<?php

namespace TYPO3\MooxSocial\Tests;
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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \TYPO3\MooxSocial\Domain\Model\Facebook.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage MOOX social
 *
 * @author Dominic Martin <dm@dcn.de>
 */
class FacebookTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {
	/**
	 * @var \TYPO3\MooxSocial\Domain\Model\Facebook
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new \TYPO3\MooxSocial\Domain\Model\Facebook();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getTitleReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle() { 
		$this->fixture->setTitle('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getTitle()
		);
	}
	
	/**
	 * @test
	 */
	public function getDescriptionReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setDescriptionForStringSetsDescription() { 
		$this->fixture->setDescription('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getDescription()
		);
	}
	
	/**
	 * @test
	 */
	public function getYearReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setYearForStringSetsYear() { 
		$this->fixture->setYear('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getYear()
		);
	}
	
	/**
	 * @test
	 */
	public function getImagesReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setImagesForStringSetsImages() { 
		$this->fixture->setImages('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getImages()
		);
	}
	
	/**
	 * @test
	 */
	public function getCaptionReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setCaptionForStringSetsCaption() { 
		$this->fixture->setCaption('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getCaption()
		);
	}
	
	/**
	 * @test
	 */
	public function getLinkReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setLinkForStringSetsLink() { 
		$this->fixture->setLink('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getLink()
		);
	}			
}
?>