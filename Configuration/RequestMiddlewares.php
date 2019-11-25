<?php

return [
    'frontend' => [
        'peter-benke/pb-rel-nofollow/modify-content' => [
            'target' => \PeterBenke\PbRelNofollow\Middleware\ModifyContentMiddleware::class,
            'after' => [
                'typo3/cms-frontend/maintenance-mode',
            ],
        ]
    ]
];
