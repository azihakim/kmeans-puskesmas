<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DatasetRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DatasetCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DatasetCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Dataset::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/dataset');
        CRUD::setEntityNameStrings('dataset', 'datasets');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.
        // CRUD::column('kelompok_usia')
        //     ->label('Kelompok Usia')
        //     ->type('select')
        //     ->entity('kelompokusia')
        //     ->model(\App\Models\KelompokUsia::class)
        //     ->attribute('name');

        // CRUD::column('jenis_kelamin')
        //     ->label('Jenis Kelamin')
        //     ->type('select_from_array')
        //     ->options([
        //         1 => 'Laki-laki',
        //         2 => 'Perempuan',
        //     ]);

        // CRUD::column('jenis_penyakit')
        //     ->label('Jenis Penyakit')
        //     ->type('select')
        //     ->entity('jenispenyakit')
        //     ->model(\App\Models\JenisPenyakit::class)
        //     ->attribute('name');
        $this->crud->addButtonFromView('top', 'kmeans', 'kmeans', 'end');
        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
        $this->crud->addButtonFromView('top', 'import_excell', 'import_excell');
        $this->crud->addButtonFromView('top', 'empty_dataset', 'empty_dataset');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(DatasetRequest::class);
        CRUD::setFromDb(); // set fields from db columns.
        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        CRUD::addField([
            'name' => 'kelompok_usia',
            'label' => 'Usia',
            'type' => 'select',
            'entity' => 'kelompokusia',
            'model' => \App\Models\KelompokUsia::class,
            'attribute' => 'name',
            'options' => (function ($query) {
                return $query->orderByRaw('LEFT(name, 1)')->orderBy('name')->get();
            }),
        ]);
        CRUD::addField([
            'name' => 'jenis_kelamin',
            'label' => 'Jenis kelamin',
            'type' => 'select_from_array',
            'options' => [
                1 => 'Laki-laki',
                2 => 'Perempuan',
            ]
        ]);
        CRUD::addField([
            'name' => 'jenis_penyakit',
            'label' => 'Jenis Pentakit',
            'type' => 'select',
            'entity' => 'jenispenyakit',
            'model' => \App\Models\JenisPenyakit::class,
            'attribute' => 'name',
            'options' => (function ($query) {
                return $query->orderByRaw('LEFT(name, 1)')->orderBy('name')->get();
            }),
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function emptyDataset()
    {
        \App\Models\Dataset::truncate();
        return redirect()->back()
            ->with('success', 'Dataset berhasil dikosongkan');
    }
}
