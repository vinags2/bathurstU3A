@if (empty($noLink))
    @php
        $base = "https://www.google.com/maps/search/?api=1&query=";
        $queryParameter = $address['address'];
        if (!empty($address['suburb'])) {
            $queryParameter .= ','.$address['suburb'];
        }
        if (!empty($address['postcode'])) {
            $queryParameter .= ','.$address['postcode'];
        }
        $url = $base.urlencode($queryParameter);
    @endphp
    <a target="_blank" href="{{ $url }}">
@endif
{{ $address['address'] }}
@if (!empty($includeSuburbAndPostcode))
    <br>
    {{ !empty($address['suburb']) ? $address['suburb'] : '' }}
    {{ !empty($address['postcode']) ? $address['postcode'] : '' }}
@endif
@if (empty($noLink))
</a>
@endif