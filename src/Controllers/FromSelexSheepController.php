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
    protected mixed $model_obj;
    protected $title;
    protected string $trans;
    protected array$all_columns;

    public function __construct()
    {
        $this->model = FromSelexSheep::class;
        $this->model_obj = new $this->model;                                                // Модель
        $this->trans = 'svr-raw-lang::raw'.'.';                                             // Переводы
        $this->title = trans($this->trans . 'raw_from_selex_sheep');                    // Заголовок
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
        // Определение порядка сортировки по убыванию
        // link to documentation https://open-admin.org/docs/en/model-grid-column#list-order-order-by
        $grid->model()->orderBy($grid->getKeyName(), 'desc');
        foreach ($this->all_columns as $column_name) {
            $value_label = strtoupper($column_name);
            $trans = trans(strtolower($this->trans . $column_name));
            match ($column_name) {
                // Индивидуальные настройки для отображения колонок:created_at, update_at, raw_from_selex_sheep_id
                $grid->getKeyName() => $grid->column($column_name, 'ID')->help($trans)->sortable(),

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

//         Отключение кнопки создания
        $grid->disableCreateButton();
//         Отключение "удаление" и редактирование у строк
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
        foreach ($this->all_columns as $column_name) {
            $value_label = $column_name;
            $trans = trans(strtolower($this->trans . $column_name));
            match ($column_name) {
                // Индивидуальные настройки для отображения полей:created_at, update_at, raw_from_selex_beef_id
                $this->model_obj->getCreatedAtColumn(), $this->model_obj->getUpdatedAtColumn() => $show
                    ->field($column_name, $value_label)
                    ->xx_datetime(),
                // Для отображения ключа
                $show->getModel()->getKeyName() => $show->field($column_name, $value_label)
                    ->xx_help(msg:$trans),
                // Отображение остальных полей
                default => $show->field($column_name, $value_label)
                    ->xx_help(msg:$trans),
            };
        }
        // Убрать кнопку "Удалить" и "редактировать"
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
                $tools->disableEdit();
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
        $model = $this->model_obj;
        $form = new Form($this->model_obj);
        $form->number('NANIMAL', __('NANIMAL'))
            ->help(trans($this->trans . 'nanimal'));
        $form->text('NANIMAL_TIME', __('NANIMAL_TIME'))
            ->help(trans($this->trans . 'nanimal_time'));
        $form->text('NINVLEFT', __('NINVLEFT'))
            ->help(trans($this->trans . 'ninvleft'));
        $form->text('NINVRIGHT', __('NINVRIGHT'))
            ->help(trans($this->trans . 'ninvright'));
        $form->text('NGOSREGISTER', __('NGOSREGISTER'))
            ->help(trans($this->trans . 'ngosregister'));
        $form->text('NINV3', __('NINV3'))
            ->help(trans($this->trans . 'ninv3'));
        $form->text('TATY', __('TATY'))
            ->help(trans($this->trans . 'taty'));
        $form->text('ANIMAL_VID', __('ANIMAL_VID'))
            ->help(trans($this->trans . 'animal_vid'));
        $form->select('ANIMAL_VID_COD', __('ANIMAL_VID_COD'))
            ->options([
                26 => 'Код 26 - селекс-овцы',
                17 => 'Код 17 - селекс-мясо',
                ])
            ->default(17)
            ->help(trans($this->trans . 'animal_vid_cod'));
        $form->text('KLICHKA', __('KLICHKA'))
            ->help(trans($this->trans . 'klichka'));
        $form->text('POL', __('POL'))
            ->help(trans($this->trans . 'pol'));
        $form->number('NPOL', __('NPOL'))
            ->help(trans($this->trans . 'npol'));
        $form->text('POR', __('POR'))
            ->help(trans($this->trans . 'por'));
        $form->number('NPOR', __('NPOR'))
            ->help(trans($this->trans . 'npor'));
        $form->text('OSN_OKRAS', __('OSN_OKRAS'))
            ->help(trans($this->trans . 'osn_okras'));
        $form->date('DATE_ROGD', __('DATE_ROGD'))
            ->help(trans($this->trans . 'date_rogd'));
        $form->date('DATE_POSTUPLN', __('DATE_POSTUPLN'))
            ->help(trans($this->trans . 'date_postupln'));
        $form->number('NHOZ_ROGD', __('NHOZ_ROGD'))
            ->help(trans($this->trans . 'nhoz_rogd'));
        $form->number('NHOZ', __('NHOZ'))
            ->help(trans($this->trans . 'nhoz'));
        $form->number('NOBL', __('NOBL'))
            ->help(trans($this->trans . 'nobl'));
        $form->number('NRN', __('NRN'))
            ->help(trans($this->trans . 'nrn'));
        $form->text('NIDENT', __('NIDENT'))
            ->help(trans($this->trans . 'nident'));
        $form->number('NSODERGANIE', __('NSODERGANIE'))
            ->help(trans($this->trans . 'nsoderganie'));
        $form->text('SODERGANIE_IM', __('SODERGANIE_IM'))
            ->help(trans($this->trans . 'soderganie_im'));
        $form->date('DATE_V', __('DATE_V'))
            ->help(trans($this->trans . 'date_v'));
        $form->text('PV', __('PV'))
            ->help(trans($this->trans . 'pv'));
        $form->text('RASHOD', __('RASHOD'))
            ->help(trans($this->trans . 'rashod'));
        $form->number('GM_V', __('GM_V'))
            ->help(trans($this->trans . 'gm_v'));
        $form->text('ISP', __('ISP'))
            ->help(trans($this->trans . 'isp'));
        $form->text('DATE_CHIP', __('DATE_CHIP'))
            ->help(trans($this->trans . 'date_chip'));
        $form->text('DATE_NINVRIGHT', __('DATE_NINVRIGHT'))
            ->help(trans($this->trans . 'date_ninvright'));
        $form->text('DATE_NINVLEFT', __('DATE_NINVLEFT'))
            ->help(trans($this->trans . 'date_ninvleft'));
        $form->text('DATE_NGOSREGISTER', __('DATE_NGOSREGISTER'))
            ->help(trans($this->trans . 'date_ngosregister'));
        $form->text('NINVRIGHT_OTCA', __('NINVRIGHT_OTCA'))
            ->help(trans($this->trans . 'ninvright_otca'));
        $form->text('NINVLEFT_OTCA', __('NINVLEFT_OTCA'))
            ->help(trans($this->trans . 'ninvleft_otca'));
        $form->text('NGOSREGISTER_OTCA', __('NGOSREGISTER_OTCA'))
            ->help(trans($this->trans . 'ngosregister_otca'));
        $form->text('NINVRIGHT_MATERI', __('NINVRIGHT_MATERI'))
            ->help(trans($this->trans . 'ninvright_materi'));
        $form->text('NINVLEFT_MATERI', __('NINVLEFT_MATERI'))
            ->help(trans($this->trans . 'ninvleft_materi'));
        $form->text('NGOSREGISTER_MATERI', __('NGOSREGISTER_MATERI'))
            ->help(trans($this->trans . 'ngosregister_materi'));
        $form->text('IMPORT_STATUS', __('IMPORT_STATUS'))
            ->options(ImportStatusEnum::get_option_list())
            ->help(trans($this->trans . 'import_status'))
            ->required()
            ->default('new');
        $form->number('TASK', __('TASK'))
            ->help(trans($this->trans . 'task'));
        $form->text('GUID_SVR', __('GUID_SVR'))
            ->help(trans($this->trans . 'guid_svr'));
        $form->textarea('ANIMALS_JSON', __('ANIMALS_JSON'))
            ->help(trans($this->trans . 'animals_json'));
        $form->display('created_at', __('created_at'))
            ->help(trans($this->trans . 'created_at'));
        $form->display('update_at', __('update_at'))
            ->help(trans($this->trans . 'updated_at'));
        // Отключить "Продолжить создание"
        $form->disableCreatingCheck();
        // Отключить "Удалить"
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });

        // обработка формы
        $form->saving(function (Form $form) use ($model)
        {
            // создается текущая страница формы.
            if ($form->isCreating())
            {
                $model->createRaw(request());
            }
            // обновляется текущая страница формы.
            if ($form->isEditing())
            {
                // The request contains dont have the primary key and value
                // The model contains the primary key and value
                // Add primary key to request
                $inputs = $this->getForm()->inputs();
                request()->request->add([$form->model()->getKeyName() => $form->model()->getKey()]);
                $model->updateRaw(request());
            }
            // Redirect to show page after save
            return redirect(admin_url($form->resource(0)));
        });
        return $form;
    }
}
