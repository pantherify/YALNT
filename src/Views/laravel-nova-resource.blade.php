namespace {{ $model["space"] }};

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
@foreach($model['fields'] as $field)
use Laravel\Nova\Fields\{{$field}};
@endforeach
use Laravel\Nova\Http\Requests\NovaRequest;

class {{ $model["name"] }} extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \{{ $model["namespace"] }}::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',

/**
* You can add any of this to your Laravel Nova Search
@foreach($model['attributes'] as $key => $attribute)
*    '{{$attribute['name']}}',
@endforeach
*/
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            @foreach($model['attributes'] as $key => $attribute)
{{$attribute['type']}}::make(__('{{Str::upper($attribute['name'])}}'),'{{$attribute['name']}}'),
            @endforeach
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
