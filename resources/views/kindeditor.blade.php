<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    @include('admin::form.error')
    <div class="{{$viewClass['field']}}">
        <textarea class="form-control {{$class}}" id="{{$id}}"  style="resize:none;height:155px;" name="{{$name}}" placeholder="{{ $placeholder }}"{!! $attributes !!}>{{ old($column, $value) }}</textarea>
    </div>
    @include('admin::form.help-block')

</div>