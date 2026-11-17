<?php

return [

    /*
    | 1 日の歩数目標（ホームの進捗表示に使う）
    */
    'daily_goal' => 8000,

    /*
    | 1 日あたり・1 レコードあたりの歩数の上限
    */
    'max_steps' => 100000,

    /*
    | 1 リクエストで受け付けるレコード数の上限
    */
    'max_records_per_request' => 31,

    /*
    | 何日前まで遡って保存を受け付けるか（今日を 0 日前とする）
    */
    'max_past_days' => 31,

    /*
    | ホーム・履歴・週集計の対象日数（今日を含む）
    */
    'history_days' => 7,

    /*
    | レート制限
    */
    'rate' => [
        'api_user_per_hour' => 60,
        'api_ip_per_minute' => 120,
        'group_join_per_minute' => 10,
        'group_join_ip_per_minute' => 30,
        'register_ip_per_hour' => 20,
        // D-024：ログインの IP 単位の失敗上限（IPv6 は /64 単位）
        'login_ip_max_failures' => 20,
        'login_ip_decay_minutes' => 10,
        // D-031：ログイン後の書き込み操作（ユーザー単位）
        'group_create_per_hour' => 10,
        'token_issue_per_hour' => 10,
        'manual_steps_per_minute' => 30,
    ],

];
