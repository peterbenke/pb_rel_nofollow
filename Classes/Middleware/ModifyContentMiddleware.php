<?php

namespace PeterBenke\PbRelNofollow\Middleware;

/**
 * PbRelNofollow
 */

use PeterBenke\PbRelNofollow\Service\ModifyContentService;

/**
 * Psr
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * TYPO3
 */

use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ModifyContentMiddleware
 * @package PeterBenke\PbRelNofollow\Middleware
 */
class ModifyContentMiddleware implements MiddlewareInterface
{


    protected ModifyContentService $modifyContentService;

    /**
     * Did not work?
     * ModifyContentMiddleware constructor
     */
    #public function __construct(protected ModifyContentService $modifyContentService)
    #{
    #}

    public function __construct()
    {
        $this->modifyContentService = GeneralUtility::makeInstance(ModifyContentService::class);
    }

    /**
     * Modify the content
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $response = $handler->handle($request);

        if (
            $request->getAttribute('frontend.typoscript') instanceof FrontendTypoScript
        ) {

            $modifiedHtml = $this->modifyContentService->clean(
                $response->getBody()->__toString(),
                $request->getAttribute('frontend.typoscript')->getConfigArray()['pb_rel_nofollow.'] ?? []
            );

            $responseBody = new Stream('php://temp', 'rw');
            $responseBody->write($modifiedHtml);

            $response = $response->withBody($responseBody);

        }

        return $response;

    }

}