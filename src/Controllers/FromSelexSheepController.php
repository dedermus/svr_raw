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
            $value_label = $column_name;
            $trans = trans($this->trans . $column_name);
            match ($column_name) {
                // Индивидуальные настройки для отображения колонок:created_at, updated_at, raw_from_selex_sheep_id
                $grid->getKeyName() => $grid->column($column_name, 'id')->help($trans)->sortable(),

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
            $trans = trans($this->trans . $column_name);
            match ($column_name) {
                // Индивидуальные настройки для отображения полей:created_at, updated_at, raw_from_selex_beef_id
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
        $form->number('nanimal', __('nanimal'))
            ->help(trans($this->trans . 'nanimal'));
        $form->text('nanimal_time', __('nanimal_time'))
            ->help(trans($this->trans . 'nanimal_time'));
        $form->text('ninvleft', __('ninvleft'))
            ->help(trans($this->trans . 'ninvleft'));
        $form->text('ninvright', __('ninvright'))
            ->help(trans($this->trans . 'ninvright'));
        $form->text('ngosregister', __('ngosregister'))
            ->help(trans($this->trans . 'ngosregister'));
        $form->text('ninv3', __('ninv3'))
            ->help(trans($this->trans . 'ninv3'));
        $form->text('taty', __('taty'))
            ->help(trans($this->trans . 'taty'));
        $form->text('animal_vid', __('animal_vid'))
            ->help(trans($this->trans . 'animal_vid'));
        $form->select('animal_vid_cod', __('animal_vid_cod'))
            ->options([
                26 => 'Код 26 - селекс-овцы',
                17 => 'Код 17 - селекс-мясо',
                ])
            ->default(17)
            ->help(trans($this->trans . 'animal_vid_cod'));
        $form->text('klichka', __('klichka'))
            ->help(trans($this->trans . 'klichka'));
        $form->text('pol', __('pol'))
            ->help(trans($this->trans . 'pol'));
        $form->number('npol', __('npol'))
            ->help(trans($this->trans . 'npol'));
        $form->text('por', __('por'))
            ->help(trans($this->trans . 'por'));
        $form->number('npor', __('npor'))
            ->help(trans($this->trans . 'npor'));
        $form->text('osn_okras', __('osn_okras'))
            ->help(trans($this->trans . 'osn_okras'));
        $form->date('date_rogd', __('date_rogd'))
            ->help(trans($this->trans . 'date_rogd'));
        $form->date('date_postupln', __('date_postupln'))
            ->help(trans($this->trans . 'date_postupln'));
        $form->number('nhoz_rogd', __('nhoz_rogd'))
            ->help(trans($this->trans . 'nhoz_rogd'));
        $form->number('nhoz', __('nhoz'))
            ->help(trans($this->trans . 'nhoz'));
        $form->number('nobl', __('nobl'))
            ->help(trans($this->trans . 'nobl'));
        $form->number('nrn', __('nrn'))
            ->help(trans($this->trans . 'nrn'));
        $form->text('nident', __('nident'))
            ->help(trans($this->trans . 'nident'));
        $form->number('nsoderganie', __('nsoderganie'))
            ->help(trans($this->trans . 'nsoderganie'));
        $form->text('soderganie_im', __('soderganie_im'))
            ->help(trans($this->trans . 'soderganie_im'));
        $form->date('date_v', __('date_v'))
            ->help(trans($this->trans . 'date_v'));
        $form->text('pv', __('pv'))
            ->help(trans($this->trans . 'pv'));
        $form->text('rashod', __('rashod'))
            ->help(trans($this->trans . 'rashod'));
        $form->number('gm_v', __('gm_v'))
            ->help(trans($this->trans . 'gm_v'));
        $form->text('isp', __('isp'))
            ->help(trans($this->trans . 'isp'));
        $form->text('date_chip', __('date_chip'))
            ->help(trans($this->trans . 'date_chip'));
        $form->text('date_ninvright', __('date_ninvright'))
            ->help(trans($this->trans . 'date_ninvright'));
        $form->text('date_ninvleft', __('date_ninvleft'))
            ->help(trans($this->trans . 'date_ninvleft'));
        $form->text('date_ngosregister', __('date_ngosregister'))
            ->help(trans($this->trans . 'date_ngosregister'));
        $form->text('ninvright_otca', __('ninvright_otca'))
            ->help(trans($this->trans . 'ninvright_otca'));
        $form->text('ninvleft_otca', __('ninvleft_otca'))
            ->help(trans($this->trans . 'ninvleft_otca'));
        $form->text('ngosregister_otca', __('ngosregister_otca'))
            ->help(trans($this->trans . 'ngosregister_otca'));
        $form->text('ninvright_materi', __('ninvright_materi'))
            ->help(trans($this->trans . 'ninvright_materi'));
        $form->text('ninvleft_materi', __('ninvleft_materi'))
            ->help(trans($this->trans . 'ninvleft_materi'));
        $form->text('ngosregister_materi', __('ngosregister_materi'))
            ->help(trans($this->trans . 'ngosregister_materi'));
        $form->text('import_status', __('import_status'))
            ->options(ImportStatusEnum::get_option_list())
            ->help(trans($this->trans . 'import_status'))
            ->required()
            ->default('new');
        $form->number('task', __('task'))
            ->help(trans($this->trans . 'task'));
        $form->text('guid_svr', __('guid_svr'))
            ->help(trans($this->trans . 'guid_svr'));
        $form->textarea('animals_json', __('animals_json'))
            ->help(trans($this->trans . 'animals_json'));
        $form->display('created_at', __('created_at'))
            ->help(trans($this->trans . 'created_at'));
        $form->display('updated_at', __('updated_at'))
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
