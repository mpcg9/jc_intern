<div class="form-group">
    {{ Form::label($name, trans('form.' . $name)) }}
    {{ Form::number($name, $value, array_merge(['class' => 'form-control form-control-2d'], $attributes)) }}
</div>