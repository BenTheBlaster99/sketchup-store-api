<x-mail::message>
# Hey {{ $user->name }}, new models just dropped!

We just added {{ $models->count() }} new model(s) to the library:

@foreach($models as $model)
**{{ $model->name }}** — {{ $model->category->name }}
@endforeach

<x-mail::button :url="config('app.frontend_url').'/dashboard'">
Browse the Library
</x-mail::button>

Thanks for being part of SketchLib!
</x-mail::message>
