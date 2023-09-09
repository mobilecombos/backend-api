<?php

namespace App\JsonApi\V1\Resources;

use App\JsonApi\V1\Auth;
use App\Models\CapabilitySet;
use App\Models\Combo;
use App\Models\DeviceFirmware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tobyz\JsonApiServer\Context;
use Tobyz\JsonApiServer\Laravel\EloquentResource;
use Tobyz\JsonApiServer\Schema\Field;
use Tobyz\JsonApiServer\Endpoint;
use Tobyz\JsonApiServer\Laravel\Filter\Scope;
use Tobyz\JsonApiServer\Laravel\Filter\Where;
use Tobyz\JsonApiServer\Laravel\Sort\SortColumn;
use Tobyz\JsonApiServer\Resource\Creatable;
use Tobyz\JsonApiServer\Schema\Sort;

use function Tobyz\JsonApiServer\Laravel\rules;

class CombosResource extends EloquentResource implements Creatable
{
    public function type(): string
    {
        return 'combos';
    }

    public function newModel(Context $context): object
    {
        return new Combo();
    }

    public function endpoints(): array
    {
        return [
            Endpoint\Show::make(),
            // Endpoint\Index::make(),
        ];
    }

    public function fields(): array
    {
        return [
            Field\Str::make('comboString'),
            Field\Attribute::make('bandwidthCombinationSetEutra'),
            Field\Attribute::make('bandwidthCombinationSetNr'),
            Field\Attribute::make('bandwidthCombinationSetIntraEndc'),
            Field\DateTime::make('createdAt'),
            Field\DateTime::make('updatedAt'),

            Field\ToOne::make('capabilitySet')->type('capability-sets')->includable()->withoutLinkage(),
            Field\ToMany::make('lteComponents')->type('lte-components')->includable()->withoutLinkage(),
            Field\ToMany::make('nrComponents')->type('nr-components')->includable()->withoutLinkage(),
        ];
    }

    public function filters(): array
    {
        return [
            Where::make('comboString'),
        ];
    }

    public function sorts(): array
    {
        return [
            SortColumn::make('comboString'),
        ];
    }
}
