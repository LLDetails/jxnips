<?php

return [
    'templates' => [
        'text'        => ['view' => 'field.text', 'name' => '单行文本'],
        'select'      => ['view' => 'field.select', 'name' => '选项'],
        'file'        => [
            'view' => 'field.file',
            'name' => '文件上传',
            'type' => [
                'image' => [
                    'ext'       => ['*.jpg', '*.jpeg', '*.png', '*.gif'],
                    'mime_type' => ['image/png', 'image/jpeg', 'image/gif']
                ],
                'document' => [
                    'ext'       => ['*.doc', '*.docx', '*.pdf', '*.rtf', '*.wps', '*odt'],
                    'mime_type' => ['application/msword', 'application/vnd.oasis.opendocument.text',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/rtf', 'application/pdf', 'application/kswps']
                ],
                'mixed' => [
                    'ext'       => ['*.doc', '*.docx', '*.pdf', '*.rtf', '*.wps', '*odt', '*.jpg', '*.jpeg', '*.png', '*.gif'],
                    'mime_type' => ['application/msword', 'application/vnd.oasis.opendocument.text',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/rtf', 'application/pdf', 'application/kswps', 'image/png', 'image/jpeg', 'image/gif']
                ]
            ]
        ],
        'textarea'    => ['view' => 'field.textarea', 'name' => '多行文本']
    ]
];