@props(['status'])
<span class="bdg bdg-{{ $status->warna() }}">
    @if (method_exists($status, 'ikon'))<i class="bi {{ $status->ikon() }}"></i>@endif
    {{ $status->label() }}
</span>
