<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <div class="docs-loading-indicator overflow-hidden" role="status" aria-live="polite" aria-label="Loading content">

        <svg
            class="docs-loading-indicator__svg"
            viewBox="0 0 100 8"
            clip-path="inset(0)"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            aria-hidden="true"
            preserveAspectRatio="none"
        >
            <rect
                class="docs-loading-indicator__track"
                x="0"
                y="1"
                width="100%"
                height="4"
                rx="3"
            />

            @php
                // generate 10 bars with different positions
                // every even bar has a different opacity to create a long-dash effect (docs-loading-indicator__bar--secondary)
                $bars = 10;
                $steps = range(-90, 90, 20);
            @endphp
            @foreach(range(1, $bars) as $i)
                <rect
                    class="docs-loading-indicator__bar {{ $i % 2 === 0 ? 'docs-loading-indicator__bar--secondary' : '' }}"
                    x="{{ $i * 10 }}"
                    y="1"
                    width="10%"
                    height="4"
                    rx="1"
                >
                    <animate attributeName="x" values="{{$steps[$i-1] }};{{ $steps[$i-1] +100 }}" dur="5s" repeatCount="indefinite" restart="always" />
                </rect>
            @endforeach
        </svg>
    </div>
</div>

<style>
    .docs-loading-indicator {
        width: 100%;
        color: var(--color-primary-400);
        pointer-events: none;
    }

    .dark .docs-loading-indicator {
        color: var(--color-primary-400);
    }

    .docs-loading-indicator__svg {
        display: block;
        width: 100%;
        height: 0.5rem;
        overflow: visible;
    }

    .docs-loading-indicator__track {
        fill: currentColor;
        opacity: 0.18;
    }

    .docs-loading-indicator__bar {
        fill: currentColor;
    }

    .docs-loading-indicator__bar--secondary {
        opacity: 0.45;
    }

    .docs-loading-indicator__sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
</style>

