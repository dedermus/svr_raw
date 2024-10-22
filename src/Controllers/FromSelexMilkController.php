<?php

namespace Svr\Raw\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use OpenAdminCore\Admin\Controllers\AdminController;
use OpenAdminCore\Admin\Facades\Admin;
use OpenAdminCore\Admin\Form;
use OpenAdminCore\Admin\Grid;
use OpenAdminCore\Admin\Layout\Content;
use OpenAdminCore\Admin\Show;
use Svr\Core\Enums\ImportStatusEnum;
use Svr\Raw\Models\FromSelexMilk;

class FromSelexMilkController extends AdminController
{
    protected $model;
    protected mixed $model_obj;
    protected $title;
    protected string $trans;
    protected array$all_columns;

    public function __construct()
    {
        $this->model = FromSelexMilk::class;
        $this->model_obj = new $this->model;                                                // Модель
        $this->trans = 'svr-raw-lang::raw.';                                                // Переводы
        $this->title = trans($this->trans . 'raw_from_selex_milk');                    // Заголовок
        $this->all_columns = $this->model_obj->getFillable();                              // массив имён полей
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

            // Оформление подсказок (xx_help)
            ->css('.row .help-block {
                font-size: .9rem;
                color: #72777b
            }')

            ->body($this->detail($id));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        $grid = new Grid($this->model_obj);
        foreach ($this->all_columns as $column_name) {
            $value_label = strtoupper($column_name);
            $trans = trans(strtolower($this->trans . $column_name));
            match ($column_name) {
                // Индивидуальные настройки для отображения колонок:created_at, update_at, raw_from_selex_milk_id
                'raw_from_selex_milk_id' => $grid->column($column_name, 'ID')->help($trans)->sortable(),

                $this->model_obj->getCreatedAtColumn(), $this->model_obj->getUpdatedAtColumn() => $grid
                    ->column($column_name, $value_label)
                    ->display(function ($value) {return Carbon::parse($value);})
                    ->xx_datetime()
                    ->help($trans)->sortable(),

                // Отображение остальных колонок
                default => $grid->column($column_name, $value_label)->help($trans),
            };
        }
        //TODO: Реализовать
        // Настройки фильтров

        // Отключение кнопки создания
        $grid->disableCreateButton();
        // Отключение "удаление" и редактирование у строк
        $grid->actions(function (Grid\Displayers\Actions\Actions $actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show($this->model::findOrFail($id));
        foreach ($this->all_columns as $column_name) {
            $value_label = $column_name;
            $trans = trans(strtolower($this->trans . $column_name));
            match ($column_name) {
                // Индивидуальные настройки для отображения полей:created_at, update_at, raw_from_selex_beef_id
                $this->model_obj->getCreatedAtColumn(), $this->model_obj->getUpdatedAtColumn() => $show
                    ->field($column_name, $value_label)
                    ->xx_datetime(),

                'raw_from_selex_milk_id' => $show->field($column_name, $value_label)
                    ->xx_help(msg:$trans),

                // Отображение остальных полей
                default => $show->field($column_name, $value_label)->xx_help(msg:$trans),
            };
        }
        // Убрать кнопку "Удалить"
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
            });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form($this->model_obj);

        $form->number('NANIMAL', __('NANIMAL'))->help(trans($this->trans . 'nanimal'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('NANIMAL_TIME', __('NANIMAL_TIME'))->help(trans($this->trans . 'nanimal_time'))->rules('max:128|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('NINV', __('NINV'))->help(trans($this->trans . 'ninv'))->rules('max:15|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('KLICHKA', __('KLICHKA'))->help(trans($this->trans . 'klichka'))->rules('max:50|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('POL', __('POL'))->help(trans($this->trans . 'pol'))->rules('max:30|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->number('NPOL', __('NPOL'))->help(trans($this->trans . 'npol'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('NGOSREGISTER', __('NGOSREGISTER'))->help(trans($this->trans . 'ngosregister'))->rules('max:50|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('NINV1', __('NINV1'))->help(trans($this->trans . 'ninv1'))->rules('max:15|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('NINV3', __('NINV3'))->help(trans($this->trans . 'ninv3'))->rules('max:20|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('ANIMAL_VID', __('ANIMAL_VID'))->help(trans($this->trans . 'animal_vid'))->rules('max:50|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();

        $form->select('ANIMAL_VID_COD', __('ANIMAL_VID_COD'))->options([
            26 => 'Код 26 - селекс-овцы',
            17 => 'Код 17 - селекс-мясо',
        ])->default(17)->help(trans($this->trans . 'animal_vid_cod'))->rules('required|integer', ['integer' => trans('svr-core-lang::validation.integer')]);

        $form->xx_input('MAST', __('MAST'))->help(trans($this->trans . 'mast'))->rules('max:30|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->number('NMAST', __('NMAST'))->help(trans($this->trans . 'nmast'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('POR', __('POR'))->help(trans($this->trans . 'por'))->rules('max:30|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->number('NPOR', __('NPOR'))->help(trans($this->trans . 'npor'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->date('DATE_ROGD', __('DATE_ROGD'))->help(trans($this->trans . 'date_rogd'))->rules('date: Y-m-d|nullable', ['data' => trans('svr-core-lang::validation.data')]);
        $form->date('DATE_POSTUPLN', __('DATE_POSTUPLN'))->help(trans($this->trans . 'date_postupln'))->rules('date: Y-m-d|nullable', ['data' => trans('svr-core-lang::validation.data')]);
        $form->number('NHOZ_ROGD', __('NHOZ_ROGD'))->help(trans($this->trans . 'nhoz_rogd'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->number('NHOZ', __('NHOZ'))->help(trans($this->trans . 'nhoz'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->number('NOBL', __('NOBL'))->help(trans($this->trans . 'nobl'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->number('NRN', __('NRN'))->help(trans($this->trans . 'nrn'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('NIDENT', __('NIDENT'))->help(trans($this->trans . 'nident'))->rules('max:20|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('ROGD_HOZ', __('ROGD_HOZ'))->help(trans($this->trans . 'rogd_hoz'))->rules('max:50|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->date('DATE_V', __('DATE_V'))->help(trans($this->trans . 'date_v'))->rules('date: Y-m-d|nullable', ['data' => trans('svr-core-lang::validation.data')]);
        $form->xx_input('PV', __('PV'))->help(trans($this->trans . 'pv'))->rules('max:60|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('RASHOD', __('RASHOD'))->help(trans($this->trans . 'rashod'))->rules('max:30|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->number('GM_V', __('GM_V'))->help(trans($this->trans . 'gm_v'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('ISP', __('ISP'))->help(trans($this->trans . 'isp'))->rules('max:20|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->date('DATE_CHIP', __('DATE_CHIP'))->help(trans($this->trans . 'date_chip'))->rules('date: Y-m-d|nullable', ['data' => trans('svr-core-lang::validation.data')]);
        $form->date('DATE_NINV', __('DATE_NINV'))->help(trans($this->trans . 'date_ninv'))->rules('date: Y-m-d|nullable', ['data' => trans('svr-core-lang::validation.data')]);
        $form->date('DATE_NGOSREGISTER', __('DATE_NGOSREGISTER'))->help(trans($this->trans . 'date_ngosregister'))->rules('date: Y-m-d|nullable', ['data' => trans('svr-core-lang::validation.data')]);
        $form->xx_input('NINV_OTCA', __('NINV_OTCA'))->help(trans($this->trans . 'ninv_otca'))->rules('max:15|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('NGOSREGISTER_OTCA', __('NGOSREGISTER_OTCA'))->help(trans($this->trans . 'ngosregister_otca'))->rules('max:50|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('POR_OTCA', __('POR_OTCA'))->help(trans($this->trans . 'por_otca'))->rules('max:30|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->number('NPOR_OTCA', __('NPOR_OTCA'))->help(trans($this->trans . 'npor_otca'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->date('DATE_ROGD_OTCA', __('DATE_ROGD_OTCA'))->help(trans($this->trans . 'date_rogd_otca'))->rules('date: Y-m-d|nullable', ['data' => trans('svr-core-lang::validation.data')]);
        $form->xx_input('NINV_MATERI', __('NINV_MATERI'))->help(trans($this->trans . 'ninv_materi'))->rules('max:15|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('NGOSREGISTER_MATERI', __('NGOSREGISTER_MATERI'))->help(trans($this->trans . 'ngosregister_materi'))->rules('max:50|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('POR_MATERI', __('POR_MATERI'))->help(trans($this->trans . 'por_materi'))->rules('max:30|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->number('NPOR_MATERI', __('NPOR_MATERI'))->help(trans($this->trans . 'npor_materi'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->date('DATE_ROGD_MATERI', __('DATE_ROGD_MATERI'))->help(trans($this->trans . 'date_rogd_materi'))->rules('date: Y-m-d|nullable', ['data' => trans('svr-core-lang::validation.data')]);
        $form->select('IMPORT_STATUS', __('IMPORT_STATUS'))
            ->options(ImportStatusEnum::get_option_list())
            ->help(trans($this->trans . 'import_status'))
            ->default('new')
            ->rules('required');

        $form->number('TASK', __('TASK'))->help(trans($this->trans . 'task'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('GUID_SVR', __('GUID_SVR'))->help(trans($this->trans . 'guid_svr'))->rules('max:64|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->textarea('ANIMALS_JSON', __('ANIMALS_JSON'))->help(trans($this->trans . 'animals_json'));
        $form->display('created_at', __('created_at'))->help(trans('svr-core-lang::svr.created_at'));
        $form->display('update_at', __('update_at'))->help(trans('svr-core-lang::svr.updated_at'));

        // Отключить "Продолжить создание"
        $form->disableCreatingCheck();
        // Отключить "Удалить"
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }
}
