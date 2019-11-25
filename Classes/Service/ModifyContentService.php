<?php
namespace PeterBenke\PbRelNofollow\Service;

use TYPO3\CMS\Core\SingletonInterface;

class ModifyContentService implements SingletonInterface
{

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * Sets the configuration
	 * @param $configuration
	 */
	private function setConfiguration($configuration)
	{
		$this->configuration = $configuration;
	}


	/**
	 * Clean the HTML with formatter
	 * @param string $content
	 * @param array $config Typoscript of this extension
	 * @return string
	 */
	public function clean($content, $config = [])
	{

		if (empty($config) || !isset($config['enable']) || (bool)$config['enable'] === false) {
			return $content;
		}
		$this->setConfiguration($config);

		$content = $this->modifyContent($content);
		return $content;

	}

	/**
	 * Modifies the content
	 * @param $content
	 * @return string|string[]|null
	 */
	private function modifyContent($content)
	{

		// $regExpression = '#<a(.*)>(.*)</a>#siU';
		$regExpression = '#<a\s+(.*)>(.*)</a>#siU';
		$content = preg_replace_callback($regExpression, 'self::setNoFollow', $content);
		return $content;

	}


	/*
	 * Adds the rel-nofollow-attribute
	 * @param array $match
	 * @return string the new link
	 */
	private function setNoFollow($match)
	{

		// Get only the link, because HTML-Entities inside of the a-tag can cause errors
		// $linkOnly = preg_replace('#<a(.*)>(.*)</a>#siU', '<a$1></a>', $match[0]);
		$linkOnly = preg_replace('#<a\s+(.*)>(.*)</a>#siU', '<a $1></a>', $match[0]);

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
	private function isInExcludeUrls($href)
	{

		$excludeUrls = $this->configuration['excludeUrls.'];
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
