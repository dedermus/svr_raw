<?php

namespace Svr\Raw\Controllers;

use App\ImportStatusEnum;
use Svr\Raw\Models\FromSelexSheep;
use Illuminate\Support\Facades\Schema;
use OpenAdminCore\Admin\Facades\Admin;
use OpenAdminCore\Admin\Controllers\AdminController;
use OpenAdminCore\Admin\Form;
use OpenAdminCore\Admin\Grid;
use OpenAdminCore\Admin\Show;
use OpenAdminCore\Admin\Layout\Content;
use Illuminate\Support\Carbon;

class FromSelexSheepController extends AdminController
{
    protected $model;
    protected $title;
    protected $all_columns_obj;

    public function __construct()
    {
        $this->model = new FromSelexSheep();
        $this->title = trans('svr.raw_from_selex_sheep');
        $this->all_columns_obj = Schema::getColumns($this->model->getTable());
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header($this->title);
            $content->description(trans('admin.description'));
            $content->body($this->grid());
        });
    }

    /**
     * Create interface.
     *
     * @param Content $content
     */
    public function create(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header($this->title);
            $content->description(trans('admin.create'));
            $content->body($this->form());
        });
    }

    /**
     * Edit interface.
     *
     * @param string $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->title($this->title)
            ->description(trans('admin.edit'))
            ->row($this->form()->edit($id));
    }

    /**
     * Show interface.
     *
     * @param string $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->title($this->title)
            ->description(trans('admin.show'))
            ->body($this->detail($id));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        $grid = new Grid($this->model);
        foreach ($this->all_columns_obj as $key => $value) {
            $value = strtoupper($value['name']);
            match ($value) {
                // Индивидуальные настройки для отображения колонок:created_at, update_at, raw_from_selex_beef_id
                strtoupper('raw_from_selex_sheep_id') => $grid->column($value, __(trans('ID'))),
                strtoupper('created_at'), strtoupper('update_at') => $grid->column($value, $value)->display(function ($value) {
                    return Carbon::parse($value)->format('Y-m-d / H:m:s');
                })->help(trans('svr.' . strtolower($value))),
                // Отображение остальных колонок
                default => $grid->column($value, $value)->help(trans('svr.' . strtolower($value))),
            };
        }

        return $grid;
    }

    /**
     * Вывод всех полей таблицы.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(FromSelexSheep::findOrFail($id));
        foreach ($this->all_columns_obj as $key => $value) {
            match ($value['name']) {
                // Индивидуальные настройки для отображения полей:created_at, update_at, raw_from_selex_beef_id
                'created_at', 'update_at' => $show
                    ->field($value['name'], $value['name'])
                    ->as(function ($value) {
                        return $value->format('Y-m-d / H:m:s');
                    }),
                'raw_from_selex_sheep_id' => $show->field($value['name'],
                    __(trans('svr.id')))
                    ->as(function ($value) {
                        return $value;
                    }),
                // Отображение остальных полей
                default => $show->field($value['name'], $value['name']),
            };
        }

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new FromSelexSheep());

        $form->number('NANIMAL', __('NANIMAL'))->help(trans('svr.nanimal'))->rules('integer|nullable', ['integer' => trans('validation.integer')]);
        $form->text('NANIMAL_TIME', __('NANIMAL_TIME'))->help(trans('svr.nanimal_time'))->rules('max:128|nullable', ['max' => trans('validation.max')]);
        $form->text('NINVLEFT', __('NINVLEFT'))->help(trans('svr.ninvleft'))->rules('max:20|nullable', ['max' => trans('validation.max')]);
        $form->text('NINVRIGHT', __('NINVRIGHT'))->help(trans('svr.ninvright'))->rules('max:20|nullable', ['max' => trans('validation.max')]);
        $form->text('NGOSREGISTER', __('NGOSREGISTER'))->help(trans('svr.ngosregister'))->rules('max:50|nullable', ['max' => trans('validation.max')]);
        $form->text('NINV3', __('NINV3'))->help(trans('svr.ninv3'))->rules('max:20|nullable', ['max' => trans('validation.max')]);
        $form->text('TATY', __('TATY'))->help(trans('svr.taty'))->rules('max:12|nullable', ['max' => trans('validation.max')]);
        $form->text('ANIMAL_VID', __('ANIMAL_VID'))->help(trans('svr.animal_vid'))->rules('max:50|nullable', ['max' => trans('validation.max')]);

        $form->select('ANIMAL_VID_COD', __('ANIMAL_VID_COD'))->options([
            26 => 'Код 26 - селекс-овцы',
            17 => 'Код 17 - селекс-мясо',
        ])->default(17)->help(trans('svr.animal_vid_cod'))->rules('required|integer', ['integer' => trans('validation.integer')]);

        $form->text('KLICHKA', __('KLICHKA'))->help(trans('svr.klichka'))->rules('max:50|nullable', ['max' => trans('validation.max')]);
        $form->text('POL', __('POL'))->help(trans('svr.pol'))->rules('max:30|nullable', ['max' => trans('validation.max')]);
        $form->number('NPOL', __('NPOL'))->help(trans('svr.npol'))->rules('integer|nullable', ['integer' => trans('validation.integer')]);
        $form->text('POR', __('POR'))->help(trans('svr.por'))->rules('max:30|nullable', ['max' => trans('validation.max')]);
        $form->number('NPOR', __('NPOR'))->help(trans('svr.npor'))->rules('integer|nullable', ['integer' => trans('validation.integer')]);
        $form->text('OSN_OKRAS', __('OSN_OKRAS'))->help(trans('svr.osn_okras'))->rules('max:30|nullable', ['max' => trans('validation.max')]);
        $form->date('DATE_ROGD', __('DATE_ROGD'))->help(trans('svr.date_rogd'))->rules('date: Y-m-d|nullable', ['date' => trans('validation.date')]);
        $form->date('DATE_POSTUPLN', __('DATE_POSTUPLN'))->help(trans('svr.date_postupln'))->rules('date: Y-m-d|nullable', ['date' => trans('validation.date')]);
        $form->number('NHOZ_ROGD', __('NHOZ_ROGD'))->help(trans('svr.nhoz_rogd'))->rules('integer|nullable', ['integer' => trans('validation.integer')]);
        $form->number('NHOZ', __('NHOZ'))->help(trans('svr.nhoz'))->rules('integer|nullable', ['integer' => trans('validation.integer')]);
        $form->number('NOBL', __('NOBL'))->help(trans('svr.nobl'))->rules('integer|nullable', ['integer' => trans('validation.integer')]);
        $form->number('NRN', __('NRN'))->help(trans('svr.nrn'))->rules('integer|nullable', ['integer' => trans('validation.integer')]);
        $form->text('NIDENT', __('NIDENT'))->help(trans('svr.nident'))->rules('max:30|nullable', ['max' => trans('validation.max')]);
        $form->number('NSODERGANIE', __('NSODERGANIE'))->help(trans('svr.nsoderganie'))->rules('integer|nullable', ['integer' => trans('validation.integer')]);
        $form->text('SODERGANIE_IM', __('SODERGANIE_IM'))->help(trans('svr.soderganie_im'))->rules('max:40|nullable', ['max' => trans('validation.max')]);
        $form->date('DATE_V', __('DATE_V'))->help(trans('svr.date_v'))->rules('date: Y-m-d|nullable', ['data' => trans('validation.date')]);
        $form->text('PV', __('PV'))->help(trans('svr.pv'))->rules('max:60|nullable', ['max' => trans('validation.max')]);
        $form->text('RASHOD', __('RASHOD'))->help(trans('svr.rashod'))->rules('max:30|nullable', ['max' => trans('validation.max')]);
        $form->number('GM_V', __('GM_V'))->help(trans('svr.gm_v'))->rules('integer|nullable', ['integer' => trans('validation.integer')]);
        $form->text('ISP', __('ISP'))->help(trans('svr.isp'))->rules('max:20|nullable', ['max' => trans('validation.max')]);
        $form->text('DATE_CHIP', __('DATE_CHIP'))->help(trans('svr.date_chip'))->rules('date: Y-m-d|nullable', ['date' => trans('validation.date')]);
        $form->text('DATE_NINVRIGHT', __('DATE_NINVRIGHT'))->help(trans('svr.date_ninvright'))->rules('date: Y-m-d|nullable', ['date' => trans('validation.date')]);
        $form->text('DATE_NINVLEFT', __('DATE_NINVLEFT'))->help(trans('svr.date_ninvleft'))->rules('date: Y-m-d|nullable', ['date' => trans('validation.date')]);
        $form->text('DATE_NGOSREGISTER', __('DATE_NGOSREGISTER'))->help(trans('svr.date_ngosregister'))->rules('date: Y-m-d|nullable', ['date' => trans('validation.date')]);
        $form->text('NINVRIGHT_OTCA', __('NINVRIGHT_OTCA'))->help(trans('svr.ninvright_otca'))->rules('max:15|nullable', ['max' => trans('validation.max')]);
        $form->text('NINVLEFT_OTCA', __('NINVLEFT_OTCA'))->help(trans('svr.ninvleft_otca'))->rules('max:15|nullable', ['max' => trans('validation.max')]);
        $form->text('NGOSREGISTER_OTCA', __('NGOSREGISTER_OTCA'))->help(trans('svr.ngosregister_otca'))->rules('max:50|nullable', ['max' => trans('validation.max')]);
        $form->text('NINVRIGHT_MATERI', __('NINVRIGHT_MATERI'))->help(trans('svr.ninvright_materi'))->rules('max:15|nullable', ['max' => trans('validation.max')]);
        $form->text('NINVLEFT_MATERI', __('NINVLEFT_MATERI'))->help(trans('svr.ninvleft_materi'))->rules('max:15|nullable', ['max' => trans('validation.max')]);
        $form->text('NGOSREGISTER_MATERI', __('NGOSREGISTER_MATERI'))->help(trans('svr.ngosregister_materi'))->rules('max:50|nullable', ['max' => trans('validation.max')]);
        $form->text('IMPORT_STATUS', __('IMPORT_STATUS'))
            ->options(ImportStatusEnum::get_option_list())
            ->help(trans('svr.import_status'))
            ->default('new')
            ->rules('required');

        $form->number('TASK', __('TASK'))->help(trans('svr.task'))->rules('integer|nullable', ['integer' => trans('validation.integer')]);
        $form->text('GUID_SVR', __('GUID_SVR'))->help(trans('svr.guid_svr'))->rules('max:64', ['max' => trans('validation.max')]);
        $form->textarea('ANIMALS_JSON', __('ANIMALS_JSON'))->help(trans('svr.animals_json'));
        $form->hidden('created_at', __('created_at'))->help(trans('svr.created_at'));
        $form->hidden('update_at', __('update_at'))->help(trans('svr.updated_at'));

        return $form;
    }
}
