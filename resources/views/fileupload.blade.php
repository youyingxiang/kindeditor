<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        <div class="input-group">
            <input class="form-control" name="{{$name}}" value="{{ old($column, $value) }}"  placeholder="{{ $placeholder }}" {!! $attributes !!}>
            <span class="input-group-btn">
                <a href="{{ old($column, "/vendor/kindeditor/fileupload/no-image.png") }}" target="_blank" >
                    <img src="{{ old($column, "/vendor/kindeditor/fileupload/no-image.png") }}" style="height:34px; width:68px;" />
                </a>
                <button class="btn btn-success btn-flat up_img" type="button">
                    <i class="fa fa-cloud-upload"> 上传</i>
                </button>
                 <input type="file" class="file_img_up" style='display:none' name="imgFile" data-url="{{route('kindeditor.upload')}}?dir=image" multiple>
            </span>
        </div>
    </div>
</div>