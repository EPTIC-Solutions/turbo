{{-- This file should not be formatted! --}}
<turbo-frame id="{{ $id }}" @if(isset($target)) target="{{ $target }}" @endif>
    @include($partial, $partialData)
</turbo-frame>