<?php
    $value = old($field->name, $value);
?>
@foreach ($list as $item)
    <div class="checkbox">
        <label>
            <input
                @if ( (! empty($value) and in_array($item, $value)) or $field->default == $item)
                    checked="checked"
                @endif
                type="checkbox" name="{{ $field->name }}[]" value="{{ $item }}">
            {{ $item }}
        </label>
    </div>
@endforeach
<?php
    $has_other = false;
    $other_value = null;
    if ( ! empty($value)) {
        foreach ($value as $v) {
            if ( ! in_array($v, $list)) {
                $other_value = $v;
                $has_other = true;
            }
        }
    }
?>
@if ($field->other and $field->other != 'false')
    <div class="checkbox">
        <label>
            <input
                @if ($has_other)
                    checked="checked"
                @else
                    name="{{ $field->name }}[]"
                @endif
                type="checkbox" data-name="{{ $field->name }}[]" class="other">
            [其他]
        </label>
    </div>
@endif

@if ($has_other)
    <input type="text" class="form-control input-sm other-value" name="{{ $field->name }}" value="{{ $other_value }}" />
@endif