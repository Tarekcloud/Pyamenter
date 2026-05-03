<div class="prose dark:prose-invert max-w-full bg-background-secondary border border-neutral p-6 rounded-lg">
    @if($page->as_html)
    {!! $page->content !!}
    @else
    <h1 class="mb-2">{{ $page->title }}</h1>
    <div>{!! $page->content !!}</div>
    @endif
</div>