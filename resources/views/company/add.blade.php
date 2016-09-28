@extends('layout.frame')

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">添加分公司</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            @if ($errors->has('form'))
                                <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
                            @endif
                            <div class="form-group
                                @if ($errors->has('name'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">公司名称</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="name" value="{{ old('name') }}" placeholder="公司名称">
                                </div>
                                @if ($errors->has('name'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('name') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('code'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">合同代号</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="code" value="{{ old('code') }}" placeholder="公司合同代号">
                                </div>
                                @if ($errors->has('code'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('code') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('area_id'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">归属地区</label>
                                <div class="col-sm-6">
                                    <select class="form-control input-sm select2" name="area_id">
                                        <option value="">请选择地区</option>
                                        @foreach ($areas as $area)
                                            <option @if(old('area_id') == $area->id) selected @endif value="{{ $area->id }}">{{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('area_id'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('area_id') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('delivery_address'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">公司地址</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="delivery_address" value="{{ old('delivery_address') }}" placeholder="合同交付地址">
                                </div>
                                @if ($errors->has('delivery_address'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('delivery_address') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('contract_contact'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">合同联系人</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="contract_contact" value="{{ old('contract_contact') }}" placeholder="合同联系人">
                                </div>
                                @if ($errors->has('contract_contact'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('contract_contact') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('contract_tel'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">合同电话</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="contract_tel" value="{{ old('contract_tel') }}" placeholder="合同电话">
                                </div>
                                @if ($errors->has('contract_tel'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('contract_tel') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('contract_fax'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">合同传真</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="contract_fax" value="{{ old('contract_fax') }}" placeholder="合同传真">
                                </div>
                                @if ($errors->has('contract_fax'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('contract_fax') }}</p>
                                @endif
                            </div>
                            @foreach ($addition as $field)
                                <div class="form-group
                                @if ($errors->has($field->name))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">{{ $field->display }}</label>
                                    <div class="col-sm-6">
                                        @if ($field->tpl == 'select')
                                            @include('field.widget.'.$field->tpl.'.'.$field->widget, ['list'=> array_map('trim', explode("\n", $field->list)), 'value' => ''])
                                        @elseif($field->tpl == 'text')
                                            @include('field.widget.'.$field->tpl.'.'.$field->widget, ['value' => ''])
                                        @elseif ($field->tpl == 'file')
                                            @include('field.widget.file.file', ['value' => ''])
                                        @else
                                            @include('field.widget.textarea.textarea', ['value' => ''])
                                        @endif
                                    </div>
                                    @if ($errors->has($field->name))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first($field->name) }}</p>
                                    @endif
                                </div>
                            @endforeach
                            <div class="form-group">
                                <label class="col-sm-2 control-label"> </label>
                                <div class="col-sm-6">
                                    <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在提交..." type="submit"><span class="fa fa-plus-circle"></span> 添加分公司</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop