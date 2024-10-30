<?php

declare(strict_types=1);

namespace PeterBenke\PbRelNofollow\Service;

/**
 * TYPO3
 */

use TYPO3\CMS\Core\SingletonInterface;

/**
 * ModifyContentService
 */
class ModifyContentService implements SingletonInterface
{

    /**
     * @var array|null
     */
    protected ?array $configuration;

    /**
     * Sets the configuration
     * @param array|null $configuration
     * @return void
     */
    private function setConfiguration(?array $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * Clean the HTML with formatter
     * @param string|null $content
     * @param array|null $config Typoscript of this extension
     * @return array|string|string[]|null
     */
    public function clean(?string $content, ?array $config = []): array|string|null
    {

        if (empty($config) || !isset($config['enable']) || (bool)$config['enable'] === false) {
            return $content;
        }
        $this->setConfiguration($config);
        return $this->modifyContent($content);

    }

    /**
     * Modifies the content
     * @param string|null $content
     * @return string|string[]|null
     */
    private function modifyContent(?string $content): array|string|null
    {
        $regExpression = '#<a\s+(.*)>(.*)</a>#siU';
        return preg_replace_callback($regExpression, 'self::setNoFollow', $content);
    }

    /**
     * Adds the rel-nofollow-attribute
     * @param array|null $match
     * @return string
     */
    private function setNoFollow(?array $match): string
    {

        // Get only the link, because HTML-Entities inside the a-tag can cause errors
        // $linkOnly = preg_replace('#<a(.*)>(.*)</a>#siU', '<a$1></a>', $match[0]);
        $linkOnly = preg_replace('#<a\s+(.*)>(.*)</a>#siU', '<a $1></a>', $match[0]);

        $xml = simplexml_load_string($linkOnly);

        if (!is_object($xml)) {
            return $match[0];
        }

        // Get the link-attributes as an array
        $attr_object = $xml->attributes();
        $attr_array = (array)$attr_object;
        $attr_array = $attr_array['@attributes'];

        // Only links beginning with http(s) and not excluded URLs
        if (!preg_match('#^https?://#', $attr_array['href']) || $this->isInExcludeUrls($attr_array['href'])) {
            return $match[0];
        }

        if (empty($attr_array['rel'])) {
            $attr_array['rel'] = 'nofollow';
        } elseif (!preg_match('/nofollow/', $attr_array['rel'])) {
            $attr_array['rel'] .= ' nofollow';
        }

        $attr_string = '';
        foreach ($attr_array as $attr => $value) {
            $attr_string .= ' ' . $attr . '="' . $value . '"';
        }

        unset($xml);
        return '<a' . $attr_string . '>' . $match[2] . '</a>';

    }

    /**
     * Checks whether an url is in the exclude-Array
     * @param string|null $href
     * @return bool
     */
    private function isInExcludeUrls(?string $href): bool
    {

        $excludeUrls = $this->configuration['excludeUrls.'];
        $isInExcludeUrls = false;

        foreach ($excludeUrls as $excludeUrl) {

            // echo $href . '|' . $excludeUrl . "\n";

            if (preg_match('+^' . $excludeUrl . '+', $href)) {
                $isInExcludeUrls = true;
                break;
            }
        }

        return $isInExcludeUrls;

    }

}
