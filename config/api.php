<?php

return [
    "response" => [
        "code" => [
            /* 400 */
            "bad_request" => "BAD_REQUEST",

            /* 401 */
            "unauthorized" => "UNAUTHORIZED",
            "invalid_refresh_token" => "INVALID_REFRESH_TOKEN",
            "expired_token" => "EXPIRED_TOKEN",

            /* 404 */
            "not_found" => "NOT_FOUND",
            "user_not_found" => "USER_NOT_FOUND",
            "event_not_found" => "EVENT_NOT_FOUND",
            "company_not_found" => "COMPANY_NOT_FOUND",
            "apply_not_found" => "APPLY_NOT_FOUND",

            /* 500 */
            "internal_server_error" => "INTERNAL_SERVER_ERROR",
        ]
    ]
];
