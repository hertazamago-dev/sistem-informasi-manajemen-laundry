@props(['ikon', 'label', 'nilai', 'warna' => 'biru'])
<div class="cardx stat">
    <div class="stat-ikon {{ $warna }}"><i class="bi {{ $ikon }}"></i></div>
    <div>
        <div class="stat-nilai">{{ $nilai }}</div>
        <div class="stat-label">{{ $label }}</div>
    </div>
</div>
