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
        'document_selection' => 1, // 書類選考
        'exam_selection' => 2, // 筆記試験等
        'interview_arrangement' => 3, // 面接調整
        'interview_selection' => 4, // 面接
        'offer' => 5, // 内定
        'final' => 6, // 選考終了
    ],
];
