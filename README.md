Data Table (Grid) Builder for Laravel
=====================================

This package is a fork and improved version of the [Nayjest/Grids](https://github.com/Nayjest/Grids) package.

## Features
* Data providers (php array, Eloquent model, Doctrine DBAL query object)
* Themes support
* Individual views for UI components
* Twitter Bootstrap v3 used by default
* Caching
* Smart input handling allows to avoid conflicts with get parameters & easily place few interactive grids on same page
* Rich customization facilities
* Component architecture
* Declarative approach
* Constructing grids via strict object oriented API or configuration in php arrays
* Rich variety of components:
  - Excel and CSV export
  - _Records per page_ dropdown
  - Show/hide columns UI control
  - Sorting
  - Filtering
  - Totals calculation (sum, average value, records count, etc)
  - Pagination
  - etc

## Requirements

* PHP 8.2+
* Laravel 11+

## Installation

Run the following command in the root directory of your project to install the package using [Composer](https://getcomposer.org):

```bash
composer.phar require semaio/laravel-grids:^5.0
```

## Usage

#### Basic example

In example below grid is configured by php array using [Nayjest/Builder](https://github.com/Nayjest/Builder) package facilities.

```php
$cfg = [
    'src' => 'App\User',
    'columns' => [
        'id',
        'name',
        'email',
        'country'
    ]
];
echo Grids::make($cfg);
```

#### Advanced example

If you don't like plain arrays, you can construct grids using object oriented api:

##### Step 1. Instantiate & Configure Grid

See example below

```php
# Let's take a Eloquent query as data provider
# Some params may be predefined, other can be controlled using grid components
$query = (new User)
    ->newQuery()
    ->with('posts')
    ->where('role', '=', User::ROLE_AUTHOR);


# Instantiate & Configure Grid
$grid = new Grid(
    (new GridConfig)
        # Grids name used as html id, caching key, filtering GET params prefix, etc
        # If not specified, unique value based on file name & line of code will be generated
        ->setName('my_report')
        # See all supported data providers in sources
        ->setDataProvider(new EloquentDataProvider($query))
        # Setup caching, value in seconds, turned off in debug mode
        ->setCachingTime(300)
        # Setup table columns
        ->setColumns([
            # simple results numbering, not related to table PK or any obtained data
            new IdFieldConfig,
            (new FieldConfig)
                ->setName('login')
                # will be displayed in table header
                ->setLabel('Login')
                # That's all what you need for filtering.
                # It will create controls, process input
                # and filter results (in case of EloquentDataProvider -- modify SQL query)
                ->addFilter(
                    (new FilterConfig)
                        ->setName('login')
                        ->setOperator(FilterConfig::OPERATOR_LIKE)
                )
                # optional,
                # use to prettify output in table cell
                # or print any data located not in results field matching column name
                ->setCallback(function ($val, ObjectDataRow $row) {
                    if ($val) {
                        $icon  = "<span class='glyphicon glyphicon-user'></span>&nbsp;";
                        $user = $row->getSrc();
                        return $icon . HTML::linkRoute('users.profile', $val, [$user->id]);
                    }
                })
                # sorting buttons will be added to header, DB query will be modified
                ->setSortable(true)
            ,
            (new FieldConfig)
                ->setName('status')
                ->setLabel('Status')
                ->addFilter(
                    (new SelectFilterConfig)
                        ->setOptions(User::getStatuses())
                )
            ,
            (new FieldConfig)
                ->setName('country')
                ->setLabel('Country')
                ->addFilter(
                    (new SelectFilterConfig)
                        ->setName('country')
                        ->setOptions(get_countries_list())
                )
            ,
            (new FieldConfig)
                ->setName('registration_date')
                ->setLabel('Registration date')
                ->setSortable(true)
            ,
            (new FieldConfig)
                ->setName('comments_count')
                ->setLabel('Comments')
                ->setSortable(true)
            ,
            (new FieldConfig)
                ->setName('posts_count')
                ->setLabel('Posts')
                ->setSortable(true)
            ,
        ])
        # Setup additional grid components
        ->setComponents([
            # Renders table header (table>thead)
            (new THead)
                # Setup inherited components
                ->setComponents([
                    # Add this if you have filters for automatic placing to this row
                    new FiltersRow,
                    # Row with additional controls
                    (new OneCellRow)
                        ->setComponents([
                            # Control for specifying quantity of records displayed on page
                            (new RecordsPerPage)
                                ->setVariants([
                                    50,
                                    100,
                                    1000
                                ])
                            ,
                            # Control to show/hide rows in table
                            (new ColumnsHider)
                                ->setHiddenByDefault([
                                    'activated_at',
                                    'updated_at',
                                    'registration_ip',
                                ])
                            ,
                            # Submit button for filters.
                            # Place it anywhere in the grid (grid is rendered inside form by default).
                            (new HtmlTag)
                                ->setTagName('button')
                                ->setAttributes([
                                    'type' => 'submit',
                                    # Some bootstrap classes
                                    'class' => 'btn btn-primary'
                                ])
                                ->setContent('Filter')
                        ])
                        # Components may have some placeholders for rendering children there.
                        ->setRenderSection(THead::SECTION_BEGIN)
                ])
            ,
            # Renders table footer (table>tfoot)
            (new TFoot)
                ->addComponent(
                    # TotalsRow component calculates totals on current page
                    # (max, min, sum, average value, etc)
                    # and renders results as table row.
                    # By default there is a sum.
                    new TotalsRow([
                        'comments',
                        'posts',
                    ])
                )
                ->addComponent(
                    # Renders row containing one cell
                    # with colspan attribute equal to the table columns count
                    (new OneCellRow)
                        # Pagination control
                        ->addComponent(new Pager)
                )
        ])
);
```

#### Step 2. Render Grid
```
<?php echo $grid->render(); ?>

# Example below will also work as Grid class implements __toString method.
# Note that you can't forward Exceptions through __toString method on account of PHP limitations.
# Therefore you can preliminarily render grid in debug reasons and then pass resutls to view.
<?php echo $grid; ?>

# or shorter
<?= $grid ?>
# or using blade syntax (Laravel 5)
{!! $grid !!}
```

#### Notes
* Class names in example code used without namespaces therefore you must import it before
* Grids does not includes Twitter Bootstrap css/js files to your layout. You need to do it manually
Quick links:
```html
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
```
* Nayjest\Grids\Components\Pager component works only with Laravel 4.X, for Laravel 5 use Nayjest\Grids\Components\Laravel5\Pager

##### Working with related Eloquent models

If you need to render data from related Eloquent models, the recommendation is to use joins
instead of fetching data from related models becouse in this case filters/sorting will not work.
Grids sorting and filters changes Laravel query object, but Laravel makes additional queries to get data for related models, so it's impossible to use filters/sorting with related models.

Following example demonstrates, how to construct grid that displays data from Customer model and related Country model.

```php
// building query with join
$query = Customer
    ::leftJoin('countries', 'customers.country_id', '=','countries.id' )
    ->select('customers.*')
    // Column alias 'country_name' used to avoid naming conflicts, suggest that customers table also has 'name' column.
    ->addSelect('countries.name as country_name')
...
///   "Country" column config:
	(new FieldConfig)
	        /// Grid column displaying country name must be named according to SQl alias: column_name
		->setName('country_name')
		->setLabel('Country')
		// If you use MySQL, grid filters for column_name in this case may not work,
		// becouse MySQL don't allows to specify column aliases in WHERE SQL section.
		// To fix filtering for aliased columns, you need to override
		// filtering function to use 'countries.name' in SQL instead of 'country_name'
		->addFilter(
			(new FilterConfig)
				->setOperator(FilterConfig::OPERATOR_EQ)
				->setFilteringFunc(function($val, EloquentDataProvider $provider) {
					$provider->getBuilder()->where('countries.name', '=', $val);
				})
		)
		// Sorting will work by default becouse MySQL allows to use column aliases in ORDER BY SQL section.
		->setSortable(true)
	,
...
```

## Upgrade Guide

### From 1.X to 2.X

* Support for Laravel 4 has been dropped.
* New requires Laravel 5.7+
* `Nayjest\Grids\Components\Laravel5\Pager` has been removed, use `Nayjest\Grids\Components\Pager` instead.

### From 0.9.X to 1.X

There are full backward compatibility between 0.9.X and 1.X branches.

### From 0.8.X to 0.9.X

Grids starting from v 0.9.0 uses "laravelcollective\html" instead of outdated "illuminate\html".

You may continue to use illuminate\html, but it's recommended to replace it to laravelcollective\html.

1. Replace illuminate\html to laravelcollective\html in composer.json

2. Replace class aliases section in config/app.php ('Illuminate\Html\HtmlFacade' to 'Collective\Html\FormFacade' and 'Illuminate\Html\HtmlFacade' to 'Collective\Html\HtmlFacade')

3. Replace 'Illuminate\Html\HtmlServiceProvider' to 'Collective\Html\HtmlServiceProvider'

4. Run composer update

### From 0.3.X to 0.4.X

1. Use THead & TFoot instead of Header & Footer components
2. If you have customized grid view (grid.php), refactor it using changes in default view
3. Some components became default, so you don't need to add it to configuration

Default components hierarchy:
```
- GridConfig
    - THead
        - ColumnHeadersRow
        - FiltersRow
    - TFoot
        - OneCellRow
            - Pager

```
For adding child components to default one, resolve it by name and use addComponent / addComponents methods.

Example:
```php
...
(new GridConfig)
    ->setDataProvider($provider)
    ->getComponentByName(THead::NAME)
        ->getComponentByName(FiltersRow::NAME)
            ->addComponent(
                (new HtmlTag)
                    ->setTagName('button')
                    ->setContent('Filter')
                    ->setAttributes([
                        'type' => 'submit',
                        'class' => 'btn btn-success btn-sm'
                    ])
                    ->setRenderSection('filters_row_column_Actions')
            )
            ->getParent()
        ->getParent()
    ->setColumns([
...
```

Note that setComponents method rewrites components structure provided by defaults.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email (see [composer.json](composer.json)) instead of using the issue tracker.

## License

© 2014&mdash;2019 Vitalii Stepanenko, Rouven Alexander Rieker

Licensed under the MIT License. Please see [License File](LICENSE) for more information.
