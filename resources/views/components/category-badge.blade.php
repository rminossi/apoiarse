@props(['category'])

<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium text-white"
      style="background-color: {{ $category->color ?? '#059669' }}">
    {{ $category->name }}
</span>
