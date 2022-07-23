{{-- This file should not be formatted! --}}
<turbo-stream target="{{ $target }}" action="{{ $action }}">
@if (isset($partial))    <template>
        {!! $partial !!}    </template>@endif

</turbo-stream>
