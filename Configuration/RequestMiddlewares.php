<?php

declare(strict_types=1);

/**
 * PbRelNofollow
 */

use PeterBenke\PbRelNofollow\Middleware\ModifyContentMiddleware;

return [
    'frontend' => [
        'peter-benke/pb-rel-nofollow/modify-content' => [
            'target' => ModifyContentMiddleware::class,
            'after' => [
                'typo3/cms-frontend/maintenance-mode',
            ],
        ]
    ]
];
