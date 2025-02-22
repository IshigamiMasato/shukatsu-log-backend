<?php

return [
    'event_types' => [
        'company_information_session' => 1, // 説明会
        'casual_interview' => 2, // リクルータ面談
        'document_submission' => 3, // 書類提出
        'aptitude_test' => 4, // 適性検査
        'interview' => 5, // 面接
        'job_offer_related' => 6, // 内定関連
    ],

    'apply_status' => [
        'document_selection' => 1, // 書類選考中
        'exam_selection' => 2, // 筆記試験選考中
        'interview_selection' => 3, // 面接選考中
        'offer' => 4, // 内定
        'final' => 5, // 選考終了
    ],

    'applies' => [
        'status' => [
            'document_selection' => 1,
            'exam_selection' => 2,
            'interview_selection' => 3,
            'offer' => 4,
            'final' => 5,
        ]
    ],

    'final_results' => [
        'status' => [
            'passed' => 1,
            'rejected' => 2,
            'decline' => 3,
        ]
    ]
];
