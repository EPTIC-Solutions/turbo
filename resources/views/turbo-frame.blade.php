{{-- This file should not be formatted! --}}
<turbo-frame id="{{ $id }}" @if($target ?? false) :target="$target" @endif>
    @include($partial, $partialData)
</turbo-frame>