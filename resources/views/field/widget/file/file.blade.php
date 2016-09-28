<button class="file-upload" id="{{ $field->name }}" data-prompt="{{ $field->prompt }}" data-filetype="{{ $field->filetype }}" data-file-ext="{{ implode('; ', config('addition.templates.file.type.'.$field->filetype.'.ext')) }}" data-size="{{ $field->size }}" data-count="{{ $field->count }}" data-display="{{ $field->display }}" data-name="{{ $field->name }}" type="button">选择文件</button>
<?php
    $value = old($field->name, $value);
?>
@if ( ! empty($value))
    @foreach ($value as $f)
        <?php list($id, $filename) = explode('/', $f) ?>
        <p class="bg-default file" style="padding: 0 10px; line-height: 30px; border:1px dashed #DCDCDC">
            @if ($field->filetype == 'image')<span class="fa fa-picture-o"></span> @else<span class="fa fa-file-text-o"></span> @endif{{ $filename }}
            <a href="{{ route('attachment.download', ['attachment' => $id]) }}">下载</a>
            <button data-id="{{ $field->name }}" data-count="{{ $field->count }}" style="margin-top: 3px" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <input type="hidden" value="{{ $id.'/'.$filename }}" name="{{ $field->name }}[]" />
        </p>
    @endforeach
@endif