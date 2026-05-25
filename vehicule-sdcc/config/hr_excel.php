<?php

return [
    // Use a shared disk in production when running multiple app instances.
    'disk' => env('HR_EXCEL_DISK', 'local'),
    'json_path' => env('HR_EXCEL_JSON_PATH', 'hr/main_requests.json'),
    'excel_path' => env('HR_EXCEL_FILE_PATH', 'hr/main_requests.xlsx'),
    'download_name' => env('HR_EXCEL_DOWNLOAD_NAME', 'main_requests.xlsx'),
];
