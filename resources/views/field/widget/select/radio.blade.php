<?php
    $value = old($field->name, $value);
?>
@foreach ($list as $item)
    <div class="radio">
        <label>
            <input @if ($field->other) class="except-other" @endif
                @if ((empty($value) ? $field->default : $value) == $item)
                    checked="checked"
                @endif
                type="radio" name="{{ $field->name }}" value="{{ $item }}">
            {{ $item }}
        </label>
    </div>
@endforeach
@if ($field->other and $field->other != 'false')
    <div >
        <label class="radio">
            <input
                @if ( ! empty($value) and ! in_array($value, $list))
                    checked="checked"
                @else
                    name="{{ $field->name }}"
                @endif
                type="radio" class="other">
            [其他]
        </label>
    </div>
@endif
@if ( ! empty($value) and ! in_array($value, $list))
    <input type="text" class="form-control input-sm other-value" name="{{ $field->name }}" value="{{ $value }}" />
@endif