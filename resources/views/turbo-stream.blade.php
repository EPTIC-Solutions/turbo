{{-- This file should not be formatted! --}}
<turbo-stream target="{{ $target }}" action="{{ $action }}">
    @if (isset($partial))
        <template>
            @include($partial, $partialData ?? [])
        </template>
    @endif
</turbo-stream>