@extends('layout.frame')

@section('main')
    <form class="form-horizontal">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">合同附件</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">合同</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static"><b>{{ $contract->title }}</b></p>
                                    <p class="form-control-static"><b>{{ $contract->code }}</b></p>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">附件</label>
                                <div class="col-sm-6">
                                    @if ( ! empty($attachments))
                                        @foreach ($attachments as $f)
                                            <?php list($id, $filename) = explode('/', $f) ?>
                                            <p class="bg-default file" style="padding: 0 10px; line-height: 30px; border:1px dashed #DCDCDC">
                                                <span class="fa fa-picture-o"></span> {{ $filename }}
                                                <button data-id="attachment" data-count="5" style="margin-top: 3px" type="button" class="close" aria-label="Close"></button>
                                                <input type="hidden" value="{{ $id.'/'.$filename }}" name="attachment[]" />
                                                <a target="_blank" href="{{ route('attachment.download', ['attachment' => $id]) }}">[下载附件]</a>
                                            </p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop