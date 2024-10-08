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
use Svr\Raw\Models\FromSelexSheep;

class FromSelexSheepController extends AdminController
{
    protected $model;
    protected $model_obj;
    protected $title;
    protected $trans;
    protected $all_columns_obj;

    public function __construct()
    {
        $this->model = FromSelexSheep::class;
        $this->model_obj = new $this->model;                                                // Модель
        $this->trans = 'svr-raw-lang::raw'.'.';                                             // Переводы
        $this->title = trans($this->trans . 'raw_from_selex_sheep');                    // Заголовок
        $this->all_columns_obj = Schema::getColumns($this->model_obj->getTable());          // Все столбцы
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
        foreach ($this->all_columns_obj as $key => $value) {
            $value_name = $value['name'];
            $value_label = strtoupper($value_name);
            $trans = trans(strtolower($this->trans . $value_name));
            match ($value_name) {
                // Индивидуальные настройки для отображения колонок:created_at, update_at, raw_from_selex_sheep_id
                'raw_from_selex_sheep_id' => $grid->column($value_name, 'ID')->help($trans)->sortable(),

                $this->model_obj->getCreatedAtColumn(), $this->model_obj->getUpdatedAtColumn() => $grid
                    ->column($value_name, $value_label)
                    ->display(function ($value) {return Carbon::parse($value);})
                    ->xx_datetime()
                    ->help($trans)->sortable(),

                // Отображение остальных колонок
                default => $grid->column($value_name, $value_label)->help($trans),
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
     * Вывод всех полей таблицы.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(FromSelexSheep::findOrFail($id));
        foreach ($this->all_columns_obj as $key => $value) {
            $value_name = $value['name'];
            $value_label = $value_name;
            $trans = trans(strtolower($this->trans . $value_name));
            match ($value_name) {
                // Индивидуальные настройки для отображения полей:created_at, update_at, raw_from_selex_beef_id
                $this->model_obj->getCreatedAtColumn(), $this->model_obj->getUpdatedAtColumn() => $show
                    ->field($value_name, $value_label)
                    ->xx_datetime(),

                'raw_from_selex_sheep_id' => $show->field($value_name, $value_label)
                    ->xx_help(msg:$trans),

                // Отображение остальных полей
                default => $show->field($value_name, $value_label)
                    ->xx_help(msg:$trans),
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
        $form->text('NANIMAL_TIME', __('NANIMAL_TIME'))->help(trans($this->trans . 'nanimal_time'))->rules('max:128|nullable', ['max' => trans('svr-core-lang::validation.max')]);
        $form->text('NINVLEFT', __('NINVLEFT'))->help(trans($this->trans . 'ninvleft'))->rules('max:20|nullable', ['max' => trans('svr-core-lang::validation.max')]);
        $form->text('NINVRIGHT', __('NINVRIGHT'))->help(trans($this->trans . 'ninvright'))->rules('max:20|nullable', ['max' => trans('svr-core-lang::validation.max')]);
        $form->text('NGOSREGISTER', __('NGOSREGISTER'))->help(trans($this->trans . 'ngosregister'))->rules('max:50|nullable', ['max' => trans('svr-core-lang::validation.max')]);
        $form->text('NINV3', __('NINV3'))->help(trans($this->trans . 'ninv3'))->rules('max:20|nullable', ['max' => trans('svr-core-lang::validation.max')]);
        $form->text('TATY', __('TATY'))->help(trans($this->trans . 'taty'))->rules('max:12|nullable', ['max' => trans('svr-core-lang::validation.max')]);
        $form->text('ANIMAL_VID', __('ANIMAL_VID'))->help(trans($this->trans . 'animal_vid'))->rules('max:50|nullable', ['max' => trans('svr-core-lang::validation.max')]);

        $form->select('ANIMAL_VID_COD', __('ANIMAL_VID_COD'))->options([
            26 => 'Код 26 - селекс-овцы',
            17 => 'Код 17 - селекс-мясо',
        ])->default(17)->help(trans($this->trans . 'animal_vid_cod'))->rules('required|integer', ['integer' => trans('svr-core-lang::validation.integer')]);

        $form->xx_input('KLICHKA', __('KLICHKA'))->help(trans($this->trans . 'klichka'))->rules('max:50|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('POL', __('POL'))->help(trans($this->trans . 'pol'))->rules('max:30|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->number('NPOL', __('NPOL'))->help(trans($this->trans . 'npol'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('POR', __('POR'))->help(trans($this->trans . 'por'))->rules('max:30|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->number('NPOR', __('NPOR'))->help(trans($this->trans . 'npor'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('OSN_OKRAS', __('OSN_OKRAS'))->help(trans($this->trans . 'osn_okras'))->rules('max:30|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->date('DATE_ROGD', __('DATE_ROGD'))->help(trans($this->trans . 'date_rogd'))->rules('date: Y-m-d|nullable', ['date' => trans('svr-core-lang::validation.date')]);
        $form->date('DATE_POSTUPLN', __('DATE_POSTUPLN'))->help(trans($this->trans . 'date_postupln'))->rules('date: Y-m-d|nullable', ['date' => trans('svr-core-lang::validation.date')]);
        $form->number('NHOZ_ROGD', __('NHOZ_ROGD'))->help(trans($this->trans . 'nhoz_rogd'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->number('NHOZ', __('NHOZ'))->help(trans($this->trans . 'nhoz'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->number('NOBL', __('NOBL'))->help(trans($this->trans . 'nobl'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->number('NRN', __('NRN'))->help(trans($this->trans . 'nrn'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('NIDENT', __('NIDENT'))->help(trans($this->trans . 'nident'))->rules('max:30|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->number('NSODERGANIE', __('NSODERGANIE'))->help(trans($this->trans . 'nsoderganie'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('SODERGANIE_IM', __('SODERGANIE_IM'))->help(trans($this->trans . 'soderganie_im'))->rules('max:40|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->date('DATE_V', __('DATE_V'))->help(trans($this->trans . 'date_v'))->rules('date: Y-m-d|nullable', ['data' => trans('svr-core-lang::validation.date')]);
        $form->xx_input('PV', __('PV'))->help(trans($this->trans . 'pv'))->rules('max:60|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('RASHOD', __('RASHOD'))->help(trans($this->trans . 'rashod'))->rules('max:30|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->number('GM_V', __('GM_V'))->help(trans($this->trans . 'gm_v'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('ISP', __('ISP'))->help(trans($this->trans . 'isp'))->rules('max:20|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('DATE_CHIP', __('DATE_CHIP'))->help(trans($this->trans . 'date_chip'))->rules('date: Y-m-d|nullable', ['date' => trans('svr-core-lang::validation.date')])->valid_bootstrap();
        $form->xx_input('DATE_NINVRIGHT', __('DATE_NINVRIGHT'))->help(trans($this->trans . 'date_ninvright'))->rules('date: Y-m-d|nullable', ['date' => trans('svr-core-lang::validation.date')])->valid_bootstrap();
        $form->xx_input('DATE_NINVLEFT', __('DATE_NINVLEFT'))->help(trans($this->trans . 'date_ninvleft'))->rules('date: Y-m-d|nullable', ['date' => trans('svr-core-lang::validation.date')])->valid_bootstrap();
        $form->xx_input('DATE_NGOSREGISTER', __('DATE_NGOSREGISTER'))->help(trans($this->trans . 'date_ngosregister'))->rules('date: Y-m-d|nullable', ['date' => trans('svr-core-lang::validation.date')])->valid_bootstrap();
        $form->xx_input('NINVRIGHT_OTCA', __('NINVRIGHT_OTCA'))->help(trans($this->trans . 'ninvright_otca'))->rules('max:15|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('NINVLEFT_OTCA', __('NINVLEFT_OTCA'))->help(trans($this->trans . 'ninvleft_otca'))->rules('max:15|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('NGOSREGISTER_OTCA', __('NGOSREGISTER_OTCA'))->help(trans($this->trans . 'ngosregister_otca'))->rules('max:50|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('NINVRIGHT_MATERI', __('NINVRIGHT_MATERI'))->help(trans($this->trans . 'ninvright_materi'))->rules('max:15|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('NINVLEFT_MATERI', __('NINVLEFT_MATERI'))->help(trans($this->trans . 'ninvleft_materi'))->rules('max:15|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('NGOSREGISTER_MATERI', __('NGOSREGISTER_MATERI'))->help(trans($this->trans . 'ngosregister_materi'))->rules('max:50|nullable', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->xx_input('IMPORT_STATUS', __('IMPORT_STATUS'))
            ->options(ImportStatusEnum::get_option_list())
            ->help(trans($this->trans . 'import_status'))
            ->default('new')
            ->rules('required')->valid_bootstrap();

        $form->number('TASK', __('TASK'))->help(trans($this->trans . 'task'))->rules('integer|nullable', ['integer' => trans('svr-core-lang::validation.integer')]);
        $form->xx_input('GUID_SVR', __('GUID_SVR'))->help(trans($this->trans . 'guid_svr'))->rules('max:64', ['max' => trans('svr-core-lang::validation.max')])->valid_bootstrap();
        $form->textarea('ANIMALS_JSON', __('ANIMALS_JSON'))->help(trans($this->trans . 'animals_json'));
        $form->display('created_at', __('created_at'))->help(trans($this->trans . 'created_at'));
        $form->display('update_at', __('update_at'))->help(trans($this->trans . 'updated_at'));

        // Отключить "Продолжить создание"
        $form->disableCreatingCheck();
        // Отключить "Удалить"
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }
}
