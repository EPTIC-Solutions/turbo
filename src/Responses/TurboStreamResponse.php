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

    private function inserted(string $target, string $action, View $partial): self
    {
        $this->useTarget = $target;
        $this->useAction = $action;
        $this->partialView = $partial->name();
        $this->partialData = array_merge($partial->getData(), ['isTurboStream' => true]);

        return $this;
    }

    /**
     * Append the DOM element present in the provided partial inside the DOM element with the provided QuerySelector.
     *
     * @param  string  $target  Target QuerySelector in which we will append data.
     * @param  View  $partial  The view partial that will be rendered inside the <template> tag.
     * @return self
     */
    public function append(string $target, View $partial): self
    {
        return $this->inserted($target, 'append', $partial);
    }

    /**
     * Prepend the DOM element present in the provided partial inside the DOM element with the provided QuerySelector.
     *
     * @param  string  $target  Target QuerySelector in which we will prepend data.
     * @param  View  $partial  The view partial that will be rendered inside the <template> tag.
     * @return self
     */
    public function prepend(string $target, View $partial): self
    {
        return $this->inserted($target, 'prepend', $partial);
    }

    /**
     * Add the DOM element(s) present in the provided partial before the DOM element with the provided QuerySelector.
     *
     * @param  string  $target  Target QuerySelector before which we will add data.
     * @param  View  $partial  The view partial that will be rendered inside the <template> tag.
     * @return self
     */
    public function before(string $target, View $partial): self
    {
        return $this->inserted($target, 'before', $partial);
    }

    /**
     * Add the DOM element(s) present in the provided partial after the DOM element with the provided QuerySelector.
     *
     * @param  string  $target  Target QuerySelector after which we will add data.
     * @param  View  $partial  The view partial that will be rendered inside the <template> tag.
     * @return self
     */
    public function after(string $target, View $partial): self
    {
        return $this->inserted($target, 'after', $partial);
    }

    /**
     * Update the target with the provided QuerySelector with the data present in the provided partial.
     * Any handlers bound to the element *$target* would be retained.
     *
     * @param  string  $target  Target QuerySelector which will be updated
     * @param  View  $partial  The view partial that will be rendered inside the <template> tag.
     * @return self
     */
    public function update(string $target, View $partial): self
    {
        return $this->inserted($target, 'update', $partial);
    }

    /**
     * Replace the target with the provided QuerySelector with the HTML present in the provided partial.
     *
     * @param  string  $target  Target QuerySelector which will be replaced
     * @param  View  $partial  The view partial that will be rendered inside the <template> tag.
     * @return self
     */
    public function replace(string $target, View $partial): self
    {
        return $this->inserted($target, 'replace', $partial);
    }

    /**
     * Remove the DOM element with the provided QuerySelector from the DOM.
     *
     * @param  string  $target Target QuerySelector that needs to be removed.
     * @return self
     */
    public function remove(string $target): self
    {
        $this->useAction = 'remove';
        $this->useTarget = $target;

        return $this;
    }

    /**
     * Create a turbo stream response from a view.
     * This will make sure the rendered response contains the correct headers.
     *
     * @param  View|string  $view A view instance or the view name
     * @param  array|null  $data The data to pass to the view, only needed if you don't provide a view instance
     * @return Response
     */
    public function view(View|string $view, ?array $data = []): Response
    {
        if (! $view instanceof View) {
            $view = view($view, $data);
        }

        return Turbo::makeStream($view->render());
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
