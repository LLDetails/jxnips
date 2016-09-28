<?php
    $value = old($field->name, $value);
?>
<select
    class="form-control input-sm
    @if ($field->other)
        other
    @endif
    " name="{{ $field->name }}" data-name="{{ $field->name }}">
    <option value="">{{ $field->prompt }}</option>
    @foreach ($list as $k => $item)
        <option
        @if ((empty($value) ? $field->default : $value) == $item)
            selected="selected"
            @endif
            value="{{ $item }}">{{ $item }}</option>
    @endforeach

    @if ($field->other and $field->other != 'false')
        <option @if ( ! empty($value) and ! in_array($value, $list)) selected="selected" @endif value="[other]">[其他]</option>
    @endif
</select>
@if ( ! empty($value) and ! in_array($value, $list))
    <input style="margin-top:5px;" type="text" value="{{ $value }}" class="form-control input-sm other-value" name="{{ $field->name }}" />
@endif