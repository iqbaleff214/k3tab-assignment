<?php

return [

    /*
    |--------------------------------------------------------------------------
    | バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | 以下の言語行は、バリデーションクラスにより使用されるデフォルトのエラー
    | メッセージです。sizeルールのようにいくつかのバリデーションでは異なる
    | バージョンのメッセージを必要とする場合があります。そうした場合には、
    | 必要なメッセージをここで調整してください。
    |
    */

    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeは有効なURLではありません。',
    'after' => ':attributeは:date以降の日付にしてください。',
    'after_or_equal' => ':attributeは:date以降か同じ日付にしてください。',
    'alpha' => ':attributeは英字のみ使用できます。',
    'alpha_dash' => ':attributeは英数字、ハイフン、アンダースコアのみ使用できます。',
    'alpha_num' => ':attributeは英数字のみ使用できます。',
    'any_of' => ':attributeの値が無効です。',
    'array' => ':attributeは配列にしてください。',
    'ascii' => ':attributeはASCII文字のみ使用できます。',
    'before' => ':attributeは:date以前の日付にしてください。',
    'before_or_equal' => ':attributeは:date以前か同じ日付にしてください。',
    'between' => [
        'array' => ':attributeは:min個から:max個にしてください。',
        'file' => ':attributeは:min～:max KBのファイルにしてください。',
        'numeric' => ':attributeは:min～:maxにしてください。',
        'string' => ':attributeは:min～:max文字にしてください。',
    ],
    'boolean' => ':attributeはtrueかfalseにしてください。',
    'can' => ':attributeに許可されていない値が含まれています。',
    'confirmed' => ':attributeの確認が一致しません。',
    'contains' => ':attributeに必要な値が含まれていません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attributeは有効な日付ではありません。',
    'date_equals' => ':attributeは:dateと同じ日付にしてください。',
    'date_format' => ':attributeは":format"形式にしてください。',
    'decimal' => ':attributeは小数点以下:decimal桁にしてください。',
    'declined' => ':attributeは拒否してください。',
    'declined_if' => ':otherが:valueの場合、:attributeは拒否してください。',
    'different' => ':attributeと:otherは異なる値にしてください。',
    'digits' => ':attributeは:digits桁にしてください。',
    'digits_between' => ':attributeは:min～:max桁にしてください。',
    'dimensions' => ':attributeの画像サイズが無効です。',
    'distinct' => ':attributeに重複した値があります。',
    'doesnt_end_with' => ':attributeは:valuesで終わらないようにしてください。',
    'doesnt_start_with' => ':attributeは:valuesで始まらないようにしてください。',
    'email' => ':attributeは有効なメールアドレス形式にしてください。',
    'ends_with' => ':attributeは:valuesのいずれかで終わるようにしてください。',
    'enum' => '選択された:attributeは無効です。',
    'exists' => '選択された:attributeは無効です。',
    'extensions' => ':attributeは:valuesのいずれかの拡張子にしてください。',
    'file' => ':attributeはファイルにしてください。',
    'filled' => ':attributeは必須項目です。',
    'gt' => [
        'array' => ':attributeは:value個より多くしてください。',
        'file' => ':attributeは:value KBより大きくしてください。',
        'numeric' => ':attributeは:valueより大きくしてください。',
        'string' => ':attributeは:value文字より多くしてください。',
    ],
    'gte' => [
        'array' => ':attributeは:value個以上にしてください。',
        'file' => ':attributeは:value KB以上にしてください。',
        'numeric' => ':attributeは:value以上にしてください。',
        'string' => ':attributeは:value文字以上にしてください。',
    ],
    'hex_color' => ':attributeは有効な16進数カラーコードにしてください。',
    'image' => ':attributeは画像にしてください。',
    'in' => '選択された:attributeは無効です。',
    'in_array' => ':attributeは:otherに存在しません。',
    'integer' => ':attributeは整数にしてください。',
    'ip' => ':attributeは有効なIPアドレスにしてください。',
    'ipv4' => ':attributeは有効なIPv4アドレスにしてください。',
    'ipv6' => ':attributeは有効なIPv6アドレスにしてください。',
    'json' => ':attributeは有効なJSON形式にしてください。',
    'list' => ':attributeはリスト形式にしてください。',
    'lowercase' => ':attributeは小文字にしてください。',
    'lt' => [
        'array' => ':attributeは:value個未満にしてください。',
        'file' => ':attributeは:value KB未満にしてください。',
        'numeric' => ':attributeは:value未満にしてください。',
        'string' => ':attributeは:value文字未満にしてください。',
    ],
    'lte' => [
        'array' => ':attributeは:value個以下にしてください。',
        'file' => ':attributeは:value KB以下にしてください。',
        'numeric' => ':attributeは:value以下にしてください。',
        'string' => ':attributeは:value文字以下にしてください。',
    ],
    'mac_address' => ':attributeは有効なMACアドレスにしてください。',
    'max' => [
        'array' => ':attributeは:max個以下にしてください。',
        'file' => ':attributeは:max KB以下のファイルにしてください。',
        'numeric' => ':attributeは:max以下にしてください。',
        'string' => ':attributeは:max文字以下にしてください。',
    ],
    'max_digits' => ':attributeは:max桁以下にしてください。',
    'mimes' => ':attributeは:valuesタイプのファイルにしてください。',
    'mimetypes' => ':attributeは:valuesタイプのファイルにしてください。',
    'min' => [
        'array' => ':attributeは:min個以上にしてください。',
        'file' => ':attributeは:min KB以上のファイルにしてください。',
        'numeric' => ':attributeは:min以上にしてください。',
        'string' => ':attributeは:min文字以上にしてください。',
    ],
    'min_digits' => ':attributeは:min桁以上にしてください。',
    'missing' => ':attributeは存在してはいけません。',
    'missing_if' => ':otherが:valueの場合、:attributeは存在してはいけません。',
    'missing_unless' => ':otherが:valueでない場合、:attributeは存在してはいけません。',
    'missing_with' => ':valuesが存在する場合、:attributeは存在してはいけません。',
    'missing_with_all' => ':valuesがすべて存在する場合、:attributeは存在してはいけません。',
    'multiple_of' => ':attributeは:valueの倍数にしてください。',
    'not_in' => '選択された:attributeは無効です。',
    'not_regex' => ':attributeの形式が無効です。',
    'numeric' => ':attributeは数値にしてください。',
    'password' => [
        'letters' => ':attributeには少なくとも1文字の英字を含めてください。',
        'mixed' => ':attributeには少なくとも1文字の大文字と1文字の小文字を含めてください。',
        'numbers' => ':attributeには少なくとも1文字の数字を含めてください。',
        'symbols' => ':attributeには少なくとも1文字の記号を含めてください。',
        'uncompromised' => 'この:attributeはデータ漏洩で見つかりました。別の:attributeを選択してください。',
    ],
    'present' => ':attributeが存在しません。',
    'present_if' => ':otherが:valueの場合、:attributeが存在しません。',
    'present_unless' => ':otherが:valueでない場合、:attributeが存在しません。',
    'present_with' => ':valuesが存在する場合、:attributeが存在しません。',
    'present_with_all' => ':valuesがすべて存在する場合、:attributeが存在しません。',
    'prohibited' => ':attributeは禁止されています。',
    'prohibited_if' => ':otherが:valueの場合、:attributeは禁止されています。',
    'prohibited_if_accepted' => ':otherが承認された場合、:attributeは禁止されています。',
    'prohibited_if_declined' => ':otherが拒否された場合、:attributeは禁止されています。',
    'prohibited_unless' => ':otherが:valuesでない場合、:attributeは禁止されています。',
    'prohibits' => ':attributeが存在すると:otherは禁止されます。',
    'regex' => ':attributeの形式が無効です。',
    'required' => ':attributeは必須項目です。',
    'required_array_keys' => ':attributeには:valuesのキーを含めてください。',
    'required_if' => ':otherが:valueの場合、:attributeは必須です。',
    'required_if_accepted' => ':otherが承認された場合、:attributeは必須です。',
    'required_if_declined' => ':otherが拒否された場合、:attributeは必須です。',
    'required_unless' => ':otherが:valuesでない場合、:attributeは必須です。',
    'required_with' => ':valuesが存在する場合、:attributeは必須です。',
    'required_with_all' => ':valuesがすべて存在する場合、:attributeは必須です。',
    'required_without' => ':valuesが存在しない場合、:attributeは必須です。',
    'required_without_all' => ':valuesがすべて存在しない場合、:attributeは必須です。',
    'same' => ':attributeと:otherが一致しません。',
    'size' => [
        'array' => ':attributeは:size個にしてください。',
        'file' => ':attributeは:size KBにしてください。',
        'numeric' => ':attributeは:sizeにしてください。',
        'string' => ':attributeは:size文字にしてください。',
    ],
    'starts_with' => ':attributeは:valuesのいずれかで始めてください。',
    'string' => ':attributeは文字列にしてください。',
    'timezone' => ':attributeは有効なタイムゾーンにしてください。',
    'unique' => 'この:attributeは既に使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'uppercase' => ':attributeは大文字にしてください。',
    'url' => ':attributeは有効なURL形式にしてください。',
    'ulid' => ':attributeは有効なULID形式にしてください。',
    'uuid' => ':attributeは有効なUUID形式にしてください。',

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション言語行
    |--------------------------------------------------------------------------
    |
    | "属性.ルール"の命名規則を使用して、特定のカスタムルールに対する
    | バリデーションメッセージをここで指定できます。これにより、特定の
    | 属性ルールに対するカスタムメッセージを簡単に指定できます。
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'カスタムメッセージ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション属性名
    |--------------------------------------------------------------------------
    |
    | 以下の言語行は、"email"のようなデフォルトのプレースホルダーを、
    | "メールアドレス"のようなよりユーザーフレンドリーな表現に置き換える
    | ために使用されます。これはメッセージをより明確にするのに役立ちます。
    |
    */

    'attributes' => [
        "name" => "名前",
        "email" => "メール",
        "password" => "パスワード",
        "password_confirmation" => "パスワード確認",
        "role" => "役割",
        "roles" => "役割",
        "current_password" => "現在のパスワード",
        "new_password" => "新しいパスワード",
        "description" => "説明",
        "module" => "モジュール",
        "created_at" => "作成日時",
        "updated_at" => "更新日時",
        "joined_at" => "参加日時",
        "permission" => "権限",
        "ip" => "IPアドレス",
        "ip_address" => "IPアドレス",
        "phone" => "電話番号",
        "has_whatsapp" => "WhatsApp",
        "email_or_phone" => "メールアドレスまたは電話番号",
        "subject" => "件名",
        "parent_id" => "マスター",
        "role_id" => "役職",
    ],

];
