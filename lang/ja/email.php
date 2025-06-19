<?php

return [
    'account_created' => [
        'subject' => 'アカウントが作成されました',
        'welcome_heading' => ':app へようこそ',
        'hello' => 'こんにちは :name さん',
        'account_created' => '**:app** にアカウントが作成されました。',
        'credentials' => '以下はあなたのログイン情報です:',
        'email' => 'メールアドレス',
        'phone' => '電話番号',
        'password' => 'パスワード',
        'login_button' => ':app にログイン',
        'security_notice' => 'セキュリティのため、**ログインしてすぐにパスワードを変更してください**。',
        'contact_admin' => 'ご不明な点がございましたら、管理者にお問い合わせください。',
        'thanks' => 'ありがとうございます。',
    ],
    'report_exported' => [
        'subject' => 'レポートのダウンロード準備ができました',
        'greeting' => 'こんにちは :name さん、',
        'message' => 'レポートが正常にエクスポートされました。添付ファイルをご確認ください。',
        'thanks' => 'ありがとうございます。',
    ],
    'phone_linked' => [
        'subject' => '電話番号がリンクされました',
        'greeting' => 'こんにちは :name さん、',
        'phone_linked' => '電話番号 :phone がメールアカウント :email にリンクされました。',
        'contact_admin' => 'このリンクに心当たりがない場合は、管理者にご連絡ください。',
        'thanks' => 'ありがとうございます。',
    ],
    'reset_password' => [
        'subject' => 'パスワードリセット通知',
        'greeting' => 'こんにちは :name さん、',
        'reset_password' => 'アカウントのパスワードリセット要求を受け取ったため、このメールをお送りしています。',
        'expire' => 'このリンクは60分後に無効になります。',
        'no_action' => 'パスワードのリセットを要求していない場合は、このメッセージを無視してください。',
        'thanks' => 'ありがとうございます。',
    ],
];
