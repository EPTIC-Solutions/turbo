<?php

namespace Eptic\Turbo\Responses;

use Eptic\Turbo\Exceptions\TurboStreamResponseFailedException;
use Eptic\Turbo\Turbo;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TurboStreamResponse implements Responsable
{
    private string $useTarget;
    private string $useAction;
    private ?string $partialView = null;
    private array $partialData = [];

    public static function create(string $target, ?string $action, View $view): self
    {
        $builder = new self();

        // We're treating soft-deleted models as they were deleted. In other words, we
        // will render the deleted Turbo Stream. If you need to treat a soft-deleted
        // model differently, you can do that on your deleted Turbo Stream view.

        return $builder->inserted($target, $action ?: 'replace', $view);
    }

    /**
     * Remove the DOM element with the provided ID from the DOM.
     *
     * @param  string  $target Target DOM ID that needs to be removed.
     * @return self
     */
    public function remove(string $target): self
    {
        $this->useAction = 'remove';
        $this->useTarget = $target;

        return $this;
    }

    private function inserted(string $target, string $action, View $partial): self
    {
        $this->useTarget = $target;
        $this->useAction = $action;
        $this->partialView = $partial->name();
        $this->partialData = array_merge($partial->getData(), ['isTurboStream' => true]);

        return $this;
    }

    /**
     * Append the DOM element present in the provided partial inside the DOM element with the provided DOM ID.
     *
     * @param  string  $target  Target DOM ID in which we will append data.
     * @param  View  $view  The view that needs to be rendered
     * @return self
     */
    public function append(string $target, View $view): self
    {
        return $this->inserted($target, 'append', $view);
    }

    /**
     * Prepend the DOM element present in the provided partial inside the DOM element with the provided DOM ID.
     *
     * @param  string  $target  Target DOM ID in which we will prepend data.
     * @param  View  $view  The view that needs to be rendered
     * @return self
     */
    public function prepend(string $target, View $view): self
    {
        return $this->inserted($target, 'prepend', $view);
    }

    /**
     * Add the DOM element(s) present in the provided partial before the DOM element with the provided DOM ID.
     *
     * @param  string  $target  Target DOM ID before which we will add data.
     * @param  View  $view  The view that needs to be rendered
     * @return self
     */
    public function before(string $target, View $view): self
    {
        return $this->inserted($target, 'before', $view);
    }

    /**
     * Add the DOM element(s) present in the provided partial after the DOM element with the provided DOM ID.
     *
     * @param  string  $target  Target DOM ID after which we will add data.
     * @param  View  $view  The view that needs to be rendered
     * @return self
     */
    public function after(string $target, View $view): self
    {
        return $this->inserted($target, 'after', $view);
    }

    /**
     * Update the target with the provided DOM ID with the data present in the provided partial.
     * Any handlers bound to the element *$target* would be retained.
     *
     * @param  string  $target  Target DOM ID which will be updated
     * @param  View  $view  The view that needs to be rendered
     * @return self
     */
    public function update(string $target, View $view): self
    {
        return $this->inserted($target, 'update', $view);
    }

    /**
     * Replace the target with the provided DOM ID with the data present in the provided partial.
     *
     * @param  string  $target  Target DOM ID which will be replaced
     * @param  View  $view  The view that needs to be rendered
     * @return self
     */
    public function replace(string $target, View $view): self
    {
        return $this->inserted($target, 'replace', $view);
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     * @return Response
     */
    public function toResponse($request): Response
    {
        if ($this->useAction !== 'remove' && ! $this->partialView) {
            throw TurboStreamResponseFailedException::missingPartial();
        }

        return Turbo::makeStream(
            $this->render()
        );
    }

    private function render(): string
    {
        return view('turbo::turbo-stream', [
            'target' => $this->useTarget,
            'action' => $this->useAction,
            'partial' => $this->partialView,
            'partialData' => $this->partialData,
        ])->render();
    }
}
