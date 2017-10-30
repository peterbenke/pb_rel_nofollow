<?php
namespace PeterBenke\PbRelNofollow\User;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2017 Peter Benke <info@typomotor.de>, TYPO motor
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
 * FrontendHook
 */
class FrontendHook implements \TYPO3\CMS\Core\SingletonInterface{

	/**
	 * @var array
	 */
	protected $conf = array();

	/**
	 * FrontendHook constructor
	 */
	public function __construct(){
		$this->conf = $GLOBALS['TSFE']->tmpl->setup['tx_pb_rel_nofollow.'];
	}

	/**
	 * Modify content, called after caching (USER_INT)
	 * @param array $parameters
	 * @return void
	 */
	public function modifyUncachedContent(&$parameters){

		if($this->conf['enable'] != '1'){
			return;
		}

		$tsfe = &$parameters['pObj'];
		if ($tsfe instanceof \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController) {
			if ($tsfe->isINTincScript() === true) {
				$this->modifyContent($tsfe->content);
			}
		}
	}

	/**
	 * Modify content, called before caching
	 * @param array $parameters
	 * @return void
	 */
	public function modifyCachedContent(&$parameters){

		if($this->conf['enable'] != '1'){
			return;
		}

		$tsfe = &$parameters['pObj'];
		if ($tsfe instanceof \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController) {
			if ($tsfe->isINTincScript() === false) {
				$this->modifyContent($tsfe->content);
			}
		}
	}

	/**
	 * Modifies the content
	 * @param $content
	 * @return void
	 */
	private function modifyContent(&$content){

		$regExpression = '#<a(.*)>(.*)</a>#siU';
		$content = preg_replace_callback($regExpression, 'self::setNoFollow', $content);

	}


	/*
	 * Adds the rel-nofollow-attribute
	 * @param array $match
	 * @return string the new link
	 */
	private function setNoFollow($match){

		// Get only the link, because HTML-Entities inside of the a-tag can cause errors
		$linkOnly = preg_replace('#<a(.*)>(.*)</a>#siU', '<a$1></a>', $match[0]);
		$xml = simplexml_load_string($linkOnly);

		if(!is_object($xml)){
			return $match[0];
		}

		// Get the link-attributes as an array
		$attr_object = $xml->attributes();
		$attr_array = (array)$attr_object;
		$attr_array = $attr_array['@attributes'];

		// Only links beginning with http(s) and not excluded URLs
		if (!preg_match('#^https?://#', $attr_array['href']) || $this->isInExcludeUrls($attr_array['href'])){
			return $match[0];
		}

		if(empty($attr_array['rel'])){
			$attr_array['rel'] = 'nofollow';
		}elseif(!preg_match('/nofollow/', $attr_array['rel'])){
			$attr_array['rel'].= ' nofollow';
		}

		$attr_string = '';
		foreach($attr_array as $attr => $value){
			$attr_string.= ' ' . $attr . '="' . $value . '"';
		}

		unset($xml);
		return '<a' . $attr_string . '>' . $match[2] . '</a>';

	}

	/**
	 * Checks whether a url is in the exclude-Array
	 * @return boolean
	 */
	private function isInExcludeUrls($href) {

		$excludeUrls = $this->conf['excludeUrls.'];
		$isInExcludeUrls = false;

		foreach($excludeUrls as $excludeUrl){

			// echo $href . '|' . $excludeUrl . "\n";

			if(preg_match('+^' . $excludeUrl . '+', $href)){
				$isInExcludeUrls = true;
				break;
			}
		}

		return $isInExcludeUrls;

	}

}