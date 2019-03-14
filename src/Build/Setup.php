<?php namespace Nayjest\Grids\Build;

use Closure;
use Illuminate\Support\Facades\DB;
use LogicException;
use Nayjest\Builder\Blueprint;
use Nayjest\Builder\BlueprintsCollection;
use Nayjest\Builder\Builder;
use Nayjest\Builder\Env;
use Nayjest\Builder\Instructions\Base\Instruction;
use Nayjest\Builder\Instructions\Mapping\Build;
use Nayjest\Builder\Instructions\Mapping\BuildChildren;
use Nayjest\Builder\Instructions\CustomInstruction;
use Nayjest\Builder\Instructions\Mapping\CallMethodWith;
use Nayjest\Builder\Instructions\Mapping\CustomMapping;
use Nayjest\Builder\Instructions\Mapping\Rename;
use Nayjest\Builder\Instructions\SimpleValueAsField;
use Nayjest\Builder\Scaffold;
use Nayjest\Grids\Build\Instructions\BuildDataProvider;
use Nayjest\Grids\EloquentDataProvider;

/**
 * Class Setup
 *
 * This class prepares environment for nayjest/builder package for usage with grids.
 * Integration with nayjest/builder package allows to construct grids from configuration in form of php array.
 *
 * @See \Grids::make
 *
 * @internal
 * @package Nayjest\Grids\Build
 */
class Setup
{
    const COLUMN_CLASS = 'Nayjest\Grids\FieldConfig';
    const COMPONENT_CLASS = 'Nayjest\Grids\Components\Base\ComponentInterface';
    const GRID_CLASS = 'Nayjest\Grids\GridConfig';
    const FILTER_CLASS = 'Nayjest\Grids\FilterConfig';

    /**
     * @var BlueprintsCollection
     */
    protected $blueprints;

    /**
     * Creates blueprints required to construct grids from configuration.
     *
     * @return Builder
     */
    public function run()
    {
        $this->blueprints = Env::instance()->blueprints();
        $this->blueprints
            ->add($this->makeFilterBlueprint())
            ->add($this->makeFieldBlueprint())
            ->add($this->makeComponentBlueprint())
            ->add($configBlueprint = $this->makeConfigBlueprint());

        return new Builder($configBlueprint);
    }

    /**
     * Creates main blueprint of grid configuration.
     *
     * @return Blueprint
     */
    protected function makeConfigBlueprint()
    {
        $componentBlueprint = $this->blueprints->getFor(self::COMPONENT_CLASS);
        if (!$componentBlueprint) {
            throw new LogicException(
                'Blueprint for grid components must be created before main blueprint.'
            );
        }

        $columnBlueprint = $this->blueprints->getFor(self::COLUMN_CLASS);
        if (!$columnBlueprint) {
            throw new LogicException(
                'Blueprint for grid columns must be created before main blueprint.'
            );
        }

        $blueprint = new Blueprint(self::GRID_CLASS, [
            new BuildDataProvider(),
            new CustomInstruction(function (Scaffold $scaffold) {
                /** @var EloquentDataProvider $provider */
                $provider = $scaffold->getInput('data_provider');
                $isEloquent = $provider instanceof EloquentDataProvider;
                if ($isEloquent && !$scaffold->getInput('columns')) {
                    $table = $provider->getBuilder()->getModel()->getTable();
                    $columns = DB::connection()->getSchemaBuilder()->getColumnListing($table);
                    $scaffold->input['columns'] = $columns;
                }
            }, Instruction::PHASE_PRE_INST),
            new BuildChildren(
                'components',
                $componentBlueprint
            ),
            new Build('row_component', $componentBlueprint),
            new BuildChildren(
                'columns',
                $columnBlueprint
            ),
        ]);

        return $blueprint;
    }

    /**
     * Creates blueprint for grid components.
     *
     * @return Blueprint
     */
    protected function makeComponentBlueprint()
    {
        $blueprint = new Blueprint(self::COMPONENT_CLASS, [

            new CustomInstruction(function (Scaffold $scaffold) {
                if ($scaffold->input instanceof Closure) {
                    $scaffold->class = 'Nayjest\Grids\Components\RenderFunc';
                    $scaffold->constructorArguments = [$scaffold->input];
                    $scaffold->input = [];
                } elseif (is_string($scaffold->input)) {
                    $scaffold->class = 'Nayjest\Grids\Components\RenderFunc';
                    $out = $scaffold->input;
                    $scaffold->constructorArguments = [function () use ($out) {
                        return $out;
                    }];
                    $scaffold->input = [];
                }
            }, Instruction::PHASE_PRE_INST),
            new CustomMapping('type', function ($type, Scaffold $scaffold) {
                if (strpos($type, '\\') !== false) {
                    $scaffold->class = $type;
                } else {
                    $scaffold->class = 'Nayjest\Grids\Components\\' . str_replace(
                        ' ',
                        '',
                        ucwords(str_replace(array('-', '_'), ' ', $type))
                    );
                }
            }, null, Instruction::PHASE_PRE_INST),
        ]);
        $blueprint->add(new BuildChildren('components', $blueprint));
        $blueprint->add(new Rename('component', 'add_component'));
        $blueprint->add(new Build('add_component', $blueprint));
        $blueprint->add(new CallMethodWith('add_component', 'addComponent'));

        return $blueprint;
    }

    /**
     * Creates blueprint for filters.
     *
     * @return Blueprint
     */
    protected function makeFilterBlueprint()
    {
        return new Blueprint(self::FILTER_CLASS, [
            new SimpleValueAsField('name'),
            new CustomMapping('type', function ($type, Scaffold $scaffold) {
                switch ($type) {
                    case 'select':
                        $scaffold->class = 'Nayjest\Grids\SelectFilterConfig';
                        break;
                    default:
                        break;
                }
            }, null, Instruction::PHASE_PRE_INST),
            new Rename(0, 'name'),
            new Rename(1, 'operator'),
        ]);
    }

    /**
     * Creates blueprint for grid columns.
     *
     * @return Blueprint
     */
    protected function makeFieldBlueprint()
    {
        $filterBlueprint = $this->blueprints->getFor(self::FILTER_CLASS);
        if (!$filterBlueprint) {
            throw new LogicException(
                'Blueprint for grid filters must be created before grid columns blueprint.'
            );
        }

        return new Blueprint(self::COLUMN_CLASS, [
            new SimpleValueAsField('name'),
            new Rename(0, 'name'),
            new BuildChildren('filters', $filterBlueprint),
            new Rename('filter', 'add_filter'),
            new Build('add_filter', $filterBlueprint),
            new CallMethodWith('add_filter', 'addFilter'),
        ]);
    }
}
