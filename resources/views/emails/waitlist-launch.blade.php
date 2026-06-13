<x-mail::message>
# Hey {{ $entry->name ?? 'there' }}, SketchLib is live!

The library is now open. Browse and download curated 3D furniture
models directly inside SketchUp — or from the web.

<x-mail::button :url="config('app.frontend_url').'/pricing'">
Browse the library
</x-mail::button>

Thanks for waiting,<br>
The SketchLib team
</x-mail::message>
