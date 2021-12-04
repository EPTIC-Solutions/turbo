<?php

namespace Eptic\Turbo\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NameResolver
{
    public static function resourceVariableName(Model $model): string
    {
        return Str::camel(Name::forModel($model)->singular);
    }

    public static function partialNameFor(Model $model): string
    {
        $name = Name::forModel($model);

        $resource = $name->plural;
        $partial = $name->element;

        return "pages.{$resource}._partials.{$partial}";
    }
}