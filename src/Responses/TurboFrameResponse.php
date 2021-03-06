<?php

namespace Eptic\Turbo\Responses;

use Eptic\Turbo\Exceptions\TurboStreamResponseFailedException;
use Eptic\Turbo\Turbo;
use Exception;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TurboFrameResponse implements Responsable
{
    private string $id;

    private ?string $useTarget = null;

    private ?string $partial = null;

    private string $template = '';

    public const TEMPLATES = [
        'generic' => 'turbo::turbo-frame',
    ];

    private function inserted(string $template, string $id, View $partial, string $target = null): self
    {
        $this->id = $id;
        $this->useTarget = $target;
        $this->partial = $partial->render();
        $this->template = $this::TEMPLATES[$template];

        return $this;
    }

    /**
     * Return a generic turbo frame response containing the rendered view partial provided wrapped with <<turbo-frame>>
     * tags appended with the provided target and id.
     *
     * Read the original Hotwired/Turbo documentation <a href="https://turbo.hotwired.dev/handbook/frames">here</a> for more information about the $id and $target.
     *
     * @param  string  $id  The target id where the content must be added
     * @param  View  $partial  The view that will be rendered inside the turbo-frame component
     * @param  string|null  $target The target of the frame, either '_top' or null
     * @return self
     */
    public function generic(string $id, View $partial, string $target = null): self
    {
        return $this->inserted('generic', $id, $partial, $target);
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     * @return Response
     */
    public function toResponse($request): Response
    {
        if (! $this->partial) {
            throw TurboStreamResponseFailedException::missingPartial();
        }

        return response(
            $this->render()
        );
    }

    private function render(): string
    {
        if (app()->environment('local')) {
            // Disable debug bar as it is not compatible with turbo-frames
            try {
                // @phpstan-ignore-next-line
                \Debugbar::disable();
            } catch (Exception $e) {
            }
        }

        return view($this->template, [
            'id' => $this->id,
            'target' => $this->useTarget,
            'partial' => $this->partial,
        ])->render();
    }
}
