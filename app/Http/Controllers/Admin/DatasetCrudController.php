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
        CRUD::column('jenis_penyakit')
            ->label('Jenis Penyakit')
            ->type('select')
            ->entity('jenispenyakit')
            ->model(\App\Models\JenisPenyakit::class)
            ->attribute('name');
        $this->crud->addButtonFromView('top', 'kmeans', 'kmeans', 'end');
        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
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
            'type' => 'select_from_array',
            'options' => [
                1 => '0-7 hari',
                2 => '8-28 hari',
                3 => '1-11 bulan',
                4 => '1-4 tahun',
                5 => '5-9 tahun',
                6 => '10-14 tahun',
                7 => '15-19 tahun',
                8 => '20-44 tahun',
                9 => '45-59 tahun',
                10 => '>59 tahun',
            ]
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
}
