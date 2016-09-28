@if ($field->rule == 'date')
    <div class="input-group input-group-sm">
        <input type="text" class="form-control input-sm datepick" name="{{ $field->name }}" value="{{ old($field->name, $value) }}" placeholder="{{ $field->prompt }}">
        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
    </div>
@else
    <input type="text" class="form-control input-sm" name="{{ $field->name }}" value="{{ old($field->name, $value) }}" placeholder="{{ $field->prompt }}">
@endif