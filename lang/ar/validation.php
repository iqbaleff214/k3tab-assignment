<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'يجب قبول :attribute',
    'accepted_if' => 'يجب قبول :attribute عندما يكون :other يساوي :value',
    'active_url' => ':attribute ليس عنوان URL صالحًا',
    'after' => 'يجب أن يكون :attribute تاريخًا بعد :date',
    'after_or_equal' => 'يجب أن يكون :attribute تاريخًا بعد أو يساوي :date',
    'alpha' => 'يجب أن يحتوي :attribute على أحرف فقط',
    'alpha_dash' => 'يجب أن يحتوي :attribute على أحرف وأرقام وشرطات فقط',
    'alpha_num' => 'يجب أن يحتوي :attribute على أحرف وأرقام فقط',
    'any_of' => ':attribute غير صالح',
    'array' => 'يجب أن يكون :attribute مصفوفة',
    'ascii' => 'يجب أن يحتوي :attribute على أحرف وأرقاف ASCII فقط',
    'before' => 'يجب أن يكون :attribute تاريخًا قبل :date',
    'before_or_equal' => 'يجب أن يكون :attribute تاريخًا قبل أو يساوي :date',
    'between' => [
        'array' => 'يجب أن يحتوي :attribute على عدد عناصر بين :min و :max',
        'file' => 'يجب أن يكون حجم :attribute بين :min و :max كيلوبايت',
        'numeric' => 'يجب أن يكون :attribute بين :min و :max',
        'string' => 'يجب أن يكون طول :attribute بين :min و :max أحرف',
    ],
    'boolean' => 'يجب أن يكون :attribute صحيحًا أو خاطئًا',
    'can' => 'يحتوي :attribute على قيمة غير مصرح بها',
    'confirmed' => 'تأكيد :attribute غير متطابق',
    'contains' => 'يجب أن يحتوي :attribute على قيمة مطلوبة',
    'current_password' => 'كلمة المرور غير صحيحة',
    'date' => ':attribute ليس تاريخًا صالحًا',
    'date_equals' => 'يجب أن يكون :attribute تاريخًا يساوي :date',
    'date_format' => 'يجب أن يتطابق :attribute مع الصيغة :format',
    'decimal' => 'يجب أن يحتوي :attribute على :decimal منازل عشرية',
    'declined' => 'يجب رفض :attribute',
    'declined_if' => 'يجب رفض :attribute عندما يكون :other يساوي :value',
    'different' => 'يجب أن يكون :attribute و :other مختلفين',
    'digits' => 'يجب أن يتكون :attribute من :digits أرقام',
    'digits_between' => 'يجب أن يتكون :attribute من :min إلى :max أرقام',
    'dimensions' => 'أبعاد صورة :attribute غير صالحة',
    'distinct' => 'يحتوي :attribute على قيمة مكررة',
    'doesnt_end_with' => 'يجب ألا ينتهي :attribute بأحد القيم التالية: :values',
    'doesnt_start_with' => 'يجب ألا يبدأ :attribute بأحد القيم التالية: :values',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالح',
    'ends_with' => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values',
    'enum' => ':attribute المحدد غير صالح',
    'exists' => ':attribute المحدد غير صالح',
    'extensions' => 'يجب أن يحتوي :attribute على أحد الامتدادات التالية: :values',
    'file' => 'يجب أن يكون :attribute ملفًا',
    'filled' => 'يجب أن يحتوي :attribute على قيمة',
    'gt' => [
        'array' => 'يجب أن يحتوي :attribute على أكثر من :value عناصر',
        'file' => 'يجب أن يكون حجم :attribute أكبر من :value كيلوبايت',
        'numeric' => 'يجب أن يكون :attribute أكبر من :value',
        'string' => 'يجب أن يكون طول :attribute أكبر من :value أحرف',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي :attribute على :value عناصر أو أكثر',
        'file' => 'يجب أن يكون حجم :attribute أكبر من أو يساوي :value كيلوبايت',
        'numeric' => 'يجب أن يكون :attribute أكبر من أو يساوي :value',
        'string' => 'يجب أن يكون طول :attribute أكبر من أو يساوي :value أحرف',
    ],
    'hex_color' => 'يجب أن يكون :attribute لونًا سداسيًا صالحًا',
    'image' => 'يجب أن يكون :attribute صورة',
    'in' => ':attribute المحدد غير صالح',
    'in_array' => 'يجب أن يوجد :attribute في :other',
    'integer' => 'يجب أن يكون :attribute عددًا صحيحًا',
    'ip' => 'يجب أن يكون :attribute عنوان IP صالحًا',
    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صالحًا',
    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صالحًا',
    'json' => 'يجب أن يكون :attribute نصًا JSON صالحًا',
    'list' => 'يجب أن يكون :attribute قائمة',
    'lowercase' => 'يجب أن يكون :attribute بأحرف صغيرة',
    'lt' => [
        'array' => 'يجب أن يحتوي :attribute على أقل من :value عناصر',
        'file' => 'يجب أن يكون حجم :attribute أقل من :value كيلوبايت',
        'numeric' => 'يجب أن يكون :attribute أقل من :value',
        'string' => 'يجب أن يكون طول :attribute أقل من :value أحرف',
    ],
    'lte' => [
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :value عناصر',
        'file' => 'يجب أن يكون حجم :attribute أقل من أو يساوي :value كيلوبايت',
        'numeric' => 'يجب أن يكون :attribute أقل من أو يساوي :value',
        'string' => 'يجب أن يكون طول :attribute أقل من أو يساوي :value أحرف',
    ],
    'mac_address' => 'يجب أن يكون :attribute عنوان MAC صالحًا',
    'max' => [
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :max عناصر',
        'file' => 'يجب ألا يتجاوز حجم :attribute :max كيلوبايت',
        'numeric' => 'يجب ألا يتجاوز :attribute :max',
        'string' => 'يجب ألا يتجاوز طول :attribute :max أحرف',
    ],
    'max_digits' => 'يجب ألا يحتوي :attribute على أكثر من :max أرقام',
    'mimes' => 'يجب أن يكون :attribute ملفًا من نوع: :values',
    'mimetypes' => 'يجب أن يكون :attribute ملفًا من نوع: :values',
    'min' => [
        'array' => 'يجب أن يحتوي :attribute على الأقل على :min عناصر',
        'file' => 'يجب أن يكون حجم :attribute على الأقل :min كيلوبايت',
        'numeric' => 'يجب أن يكون :attribute على الأقل :min',
        'string' => 'يجب أن يكون طول :attribute على الأقل :min أحرف',
    ],
    'min_digits' => 'يجب أن يحتوي :attribute على الأقل على :min أرقام',
    'missing' => 'يجب ألا يحتوي :attribute على قيمة',
    'missing_if' => 'يجب ألا يحتوي :attribute على قيمة عندما يكون :other يساوي :value',
    'missing_unless' => 'يجب ألا يحتوي :attribute على قيمة إلا إذا كان :other يساوي :value',
    'missing_with' => 'يجب ألا يحتوي :attribute على قيمة عندما يكون :values موجودًا',
    'missing_with_all' => 'يجب ألا يحتوي :attribute على قيمة عندما تكون :values موجودة',
    'multiple_of' => 'يجب أن يكون :attribute مضاعفًا لـ :value',
    'not_in' => ':attribute المحدد غير صالح',
    'not_regex' => 'صيغة :attribute غير صالحة',
    'numeric' => 'يجب أن يكون :attribute رقمًا',
    'password' => [
        'letters' => 'يجب أن يحتوي :attribute على حرف واحد على الأقل',
        'mixed' => 'يجب أن يحتوي :attribute على حرف كبير وحرف صغير على الأقل',
        'numbers' => 'يجب أن يحتوي :attribute على رقم واحد على الأقل',
        'symbols' => 'يجب أن يحتوي :attribute على رمز واحد على الأقل',
        'uncompromised' => 'ظهر :attribute في تسريب بيانات. يرجى اختيار :attribute آخر',
    ],
    'present' => 'يجب أن يكون :attribute موجودًا',
    'present_if' => 'يجب أن يكون :attribute موجودًا عندما يكون :other يساوي :value',
    'present_unless' => 'يجب أن يكون :attribute موجودًا إلا إذا كان :other يساوي :value',
    'present_with' => 'يجب أن يكون :attribute موجودًا عندما يكون :values موجودًا',
    'present_with_all' => 'يجب أن يكون :attribute موجودًا عندما تكون :values موجودة',
    'prohibited' => ':attribute محظور',
    'prohibited_if' => ':attribute محظور عندما يكون :other يساوي :value',
    'prohibited_if_accepted' => ':attribute محظور عندما يتم قبول :other',
    'prohibited_if_declined' => ':attribute محظور عندما يتم رفض :other',
    'prohibited_unless' => ':attribute محظور إلا إذا كان :other ضمن :values',
    'prohibits' => 'يمنع :attribute وجود :other',
    'regex' => 'صيغة :attribute غير صالحة',
    'required' => ':attribute مطلوب',
    'required_array_keys' => 'يجب أن يحتوي :attribute على مدخلات لـ: :values',
    'required_if' => ':attribute مطلوب عندما يكون :other يساوي :value',
    'required_if_accepted' => ':attribute مطلوب عندما يتم قبول :other',
    'required_if_declined' => ':attribute مطلوب عندما يتم رفض :other',
    'required_unless' => ':attribute مطلوب إلا إذا كان :other ضمن :values',
    'required_with' => ':attribute مطلوب عندما يكون :values موجودًا',
    'required_with_all' => ':attribute مطلوب عندما تكون :values موجودة',
    'required_without' => ':attribute مطلوب عندما لا يكون :values موجودًا',
    'required_without_all' => ':attribute مطلوب عندما لا تكون أي من :values موجودة',
    'same' => 'يجب أن يتطابق :attribute مع :other',
    'size' => [
        'array' => 'يجب أن يحتوي :attribute على :size عناصر',
        'file' => 'يجب أن يكون حجم :attribute :size كيلوبايت',
        'numeric' => 'يجب أن يكون :attribute يساوي :size',
        'string' => 'يجب أن يكون طول :attribute :size أحرف',
    ],
    'starts_with' => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values',
    'string' => 'يجب أن يكون :attribute نصًا',
    'timezone' => 'يجب أن يكون :attribute منطقة زمنية صالحة',
    'unique' => ':attribute مستخدم بالفعل',
    'uploaded' => 'فشل تحميل :attribute',
    'uppercase' => 'يجب أن يكون :attribute بأحرف كبيرة',
    'url' => 'يجب أن يكون :attribute عنوان URL صالحًا',
    'ulid' => 'يجب أن يكون :attribute ULID صالحًا',
    'uuid' => 'يجب أن يكون :attribute UUID صالحًا',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'رسالة مخصصة',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        "name" => "الاسم",
        "email" => "البريد الإلكتروني",
        "password" => "كلمة المرور",
        "password_confirmation" => "تأكيد كلمة المرور",
        "role" => "الدور",
        "roles" => "الدور",
        "current_password" => "كلمة المرور الحالية",
        "new_password" => "كلمة المرور الجديدة",
        "description" => "الوصف",
        "module" => "الوحدة",
        "created_at" => "تاريخ الإنشاء",
        "updated_at" => "تاريخ التحديث",
        "joined_at" => "تاريخ الانضمام",
        "permission" => "الإذن",
        "ip" => "عنوان IP",
        "ip_address" => "عنوان IP"
    ],

];
