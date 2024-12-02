<?php

namespace Svr\Raw\Controllers\Api;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Svr\Core\Enums\SystemParticipationsTypesEnum;
use Svr\Core\Enums\SystemStatusDeleteEnum;
use Svr\Core\Enums\SystemStatusEnum;
use Svr\Core\Models\SystemRoles;
use Svr\Core\Models\SystemUsers;
use Svr\Core\Models\SystemUsersRoles;
use Svr\Core\Models\SystemUsersToken;
use Svr\Data\Models\DataCompanies;
use Svr\Data\Models\DataCompaniesLocations;
use Svr\Data\Models\DataUsersParticipations;
use Svr\Raw\Models\FromSelexBeef;
use Svr\Raw\Models\FromSelexMilk;
use Svr\Raw\Models\FromSelexSheep;
use Symfony\Component\Mailer\Exception\InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

class ApiSelexController extends Controller
{
    /**
     * Авторизация
     */
    public function selexLogin(Request $request): JsonResponse
    {

        // НАЧАЛО валидация данных запроса
        $model = new SystemUsers();
        // - готовим список необходимых полей для валидации
        $filterKeys = ['user_email', 'user_password', 'user_base_index'];
        // - собираем набор правил и сообщений
        $rules = $model->getFilterValidationRules($request, $filterKeys);
        $messages = $model->getFilterValidationMessages($filterKeys);
        // - переопределяем ключи и правила
        $rules['base_index'] = $rules['user_base_index'];
        $rules['user_base_index'] = "required|" . $rules['user_base_index'];
        $messages['base_index'] = $messages['user_base_index'];
        // ... TODO - подумать об оптимизации данного костыля
        // - удаляем ненужные ключи
        unset($rules['user_base_index']);
        unset($messages['user_base_index']);
        // - проверяем запрос на валидацию
        Validator::make($request->all(), $rules, $messages)->validate();
        // КОНЕЦ валидации данных запроса

        $current_user = $this->get_current_user($request['user_email'], $request['user_password']);

        // Если пользователь не найден
        if (is_null($current_user)) { // TODO переписать на нормальный структурированный вид после того как сделаем нормальный конструктор вывода
            // вызваем ошибку авторизации (неправильный логин или пароль) Exception
            throw new AuthenticationException('Пользователь не найден. Неправильный логин или пароль');
            // return response()->json(['error' => 'Неправильный логин или пароль'], 401);
        }

        // Создаём токен пользователю
        $new_token = $current_user->createToken('auth_token')->plainTextToken;

        // Последний токен пользователя
        $last_token
            = SystemUsersToken::userLastTokenData($current_user->user_id);

        if ($last_token) {
            // $last_token = $last_token->toArray();
            /** @var int|null $participation_id */
            $participation_id = $last_token->participation_id ?? null;
        } else {
            // получаем связку пользователя с хозяйствами/регионами/районами
            $participation_last = DataUsersParticipations::where(
                [
                    ['user_id', '=', $current_user['user_id']],
                    [
                        'participation_status', '=',
                        SystemStatusEnum::ENABLED->value
                    ]
                ]
            )
                ->latest('updated_at')
                ->first();
            // если привязка есть
            if (!is_null($participation_last)) {
                $participation_id = $participation_last['participation_id'];
            } else {
                throw new AuthenticationException('Пользователь не привязан ни к одному хозяйству/району/региону');
                // return response()->json(['error' => 'Пользователь не привязан ни к одному хозяйству/району/региону'], 401);
            }
        }

        // Создали запись в таблице токенов
        (new SystemUsersToken())->userTokenStore([
            'user_id' => $current_user['user_id'],
            'participation_id' => $participation_id,
            'token_value' => $new_token,
            'token_client_ip' => $request->ip()
        ]);

        // проверка роли "Ветеринарный врач хозяйства"
        /** @var Collection $role_data */
        $role_data = $this->user_role_data($current_user['user_id'], 'doctor_company');
        if ($role_data->isEmpty()) {
            throw new InvalidArgumentException('У пользователя отсутствует роль "Ветеринарный врач хозяйства"',
                401);
        }
        // Проверка наличия у пользователя в списке переданного хозяйства и получение его ссылочного ключа company_location_id
        // ссылочный ключ на хозяйства, который назначен хозяйству
        $participation_item_id
            = $this->check_base_index_from_user(collect($current_user));

        if ($participation_item_id === false) {
            throw new InvalidArgumentException('Пользователь не привязан к указанному хозяйству',
                401);
        }

        // добавляем в контейнер данных из переданного запроса новые поля и значения
        $data['participation_item_id'] = $participation_item_id;
        // тип участника - компания
        $data['participation_item_type'] = 'company';

        // валидация данных
        $model_data_users_participations = new DataUsersParticipations();
        // - готовим список необходимых полей для валидации
        $filterKeys = ['participation_item_id', 'participation_item_type'];
        // - собираем набор правил и сообщений
        $rules
            = $model_data_users_participations->getFilterValidationRules($request,
            $filterKeys);
        $messages
            = $model_data_users_participations->getFilterValidationMessages($filterKeys);
        // - переопределяем ключи и правила
        $rules['participation_item_id'] = "required|"
            . $rules['participation_item_id'];
        // - Валидируем
        $valid_data = Validator::make($data, $rules, $messages)->validate();

        // преобразуем $valid_data в объект Request
        // $valid_data = new Request($valid_data);

        // переключаем пользователя на работу с указанным хозяйством
        $res = $this->setUsersParticipations($current_user['user_id'], $new_token, $participation_item_id, SystemParticipationsTypesEnum::COMPANY);

        return response()->json(['test' => 'test'], 200);
    }

    /**
     * Передача данных в СВР со стороны модуля обмена
     * (Закачка животных из Селекс в таблицы с сырыми данными)
     */
    public function selexSendAnimals(Request $request): JsonResponse
    {
        $current_user = auth()->user();
        // получаем данные по компании по данным пользователя
        $company_data_location = DataCompaniesLocations::where('company_location_id', '=', $current_user['company_location_id'])->first();
        $company_data = DataCompanies::where('company_id', '=', $company_data_location['company_id'])->first();
        // получаем базовый индекс компании $company_base_index
        $company_base_index = $company_data['company_base_index'];

        // получаем базовый индекс из входящих данных (Request)
        $base_index = $request['main']['base_index'];

        // сравниваем базовые индексы: полученные из $company_id пользователя и переданные из секции MAIN
        if ((string)$base_index !== (string)$company_base_index) {
            throw new InvalidArgumentException('Переданный базовый индекс в секции MAIN не сопоставлен с Токеном',
                401);
        }

        /** @var array $exclude_fields - список полей, которые не входят в список обязательных полей для анализа */
        $exclude_fields = [
            'raw_from_selex_milk_id',   // инкремент молочных коров КРС
            'raw_from_selex_beef_id',   // инкремент мясных коров КРС
            'raw_from_selex_sheep_id',  // инкремент овец МРС
            'animals_json',             // сырые данные из Селекс
            'import_status',            // ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)
            'created_at',               // Дата и время создания
            'update_at',                // Дата и время модификации
            'task',                     // код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы)
            'status',                   // статус обработки записи
        ];

        // код задачи из секции MAIN
        $table_name = $this->switch_table($request['main']['task']);

        // получаем список полей из таблицы
        $table_columns = Schema::getColumns($table_name);
        if (empty($table_columns)) {
            // Не определена таблица для записи животных
            throw new InvalidArgumentException('Таблица ' . $table_name . ' не существует',
                500);
        }

        // на основе списка полей получаем правила валидации
        $rules = $this->get_rules_by_columns($table_columns, $exclude_fields);

        // проверяем статус, задачу, секции MAIN и поля в секции ANIMALS
        // проверка через стандартный validator. Поля таблицы из БД, правила валидации из модели
        $this->validate_structure_request($request, $rules);

        // сохраняем данные по животным
        $res = $this->add_animals($request, $table_name);

        return response()->json(['test' => 'test'], 200);
    }


    /**
     * Добавить животное в схему RAW таблицу с холодными данными
     * Все переменные должны быть предварительно проверены перед передачей в метод
     *
     * @param Request|Collection $data - контейнер, содержащий параметры для записи данных
     * @param string $table_name - имя таблицы
     */
    private function add_animals(Request|Collection $data, string $table_name): JsonResponse
    {
        // Если $data является объектом типа Request, преобразовать в коллекцию
        if ($data instanceof Request) {
            $data = collect($data->all());
        }

        // В свр в data
        //     'task'          => $task,           // код задачи
        //     'base_index'    => $base_index,     // базовый индекс компании               $data['main']['base_index']
        //     'list_fields'   => $list_fields,    // поля в таблице для заполнения
        //     'list_animals'  => $list_animals,   // список животных                       $data['animals']
        //     'table_name'    => $table_name,     // имя таблицы                           $table_name


        $list_date_field = [
            'date_rogd',
            'date_postupln',
            'date_v',
            'date_chip',
            'date_ninv',
            'date_ngosregister',
            'date_rogd_materi',
            'date_rogd_otca',
            'date_ninvright',
            'date_ninvleft'
        ];
        // результат обработки по каждому из переданных животных
        $result_animals = [];
        // флаг обработки животных
        $result = false;
        // список животных
        $list_animals = (isset($data['animals'])) ? $data['animals'] : false;

        if ($list_animals && $table_name) {

            $nanimals_list = array_column($list_animals, 'NANIMAL_TIME');
            $nanimals_data = DB::table($table_name)
                ->select('GUID_SVR', 'NANIMAL', 'NANIMAL_TIME')
                ->where(
                    [
                        ['NHOZ', '=', $data['main']['base_index']],
                        ['NANIMAL_TIME', '!=', ''],
                        ['NANIMAL_TIME', '!=', null]
                    ]
                )
                ->whereIn('NANIMAL_TIME', $nanimals_list)->get()->toArray();

            // $nanimals_data = $this->get_data(DB_MAIN,
            //     'SELECT guid_svr, nanimal, nanimal_time FROM ' . SCHEMA_RAW . '.' . $table_name . ' WHERE nanimal_time IN (\'' . implode('\',\'', $nanimals_list) . '\') AND nhoz = :nhoz',
            //     ['nhoz' => $data['base_index']], 'rows', 'nanimal_time');

            foreach ($list_animals as $animal) {
                $animal = array_change_key_case($animal);

                if (isset($nanimals_data[$animal['nanimal_time']])) {
                    $result_animals [] = [
                        'nanimal' => (isset($nanimals_data[$animal['nanimal_time']]['nanimal'])) ? $nanimals_data[$animal['nanimal_time']]['nanimal'] : null,
                        'nanimal_time' => (isset($nanimals_data[$animal['nanimal_time']]['nanimal_time'])) ? $nanimals_data[$animal['nanimal_time']]['nanimal_time'] : null,
                        'guid_svr' => (isset($nanimals_data[$animal['nanimal_time']]['guid_svr'])) ? $nanimals_data[$animal['nanimal_time']]['guid_svr'] : null,
                        'status' => true,
                        'double' => true
                    ];
                    $result = true;
                    continue;
                }

                // формируем JSONB
                $jsonB = json_encode(array_change_key_case($animal), JSON_UNESCAPED_UNICODE);

                $guid_svr = (isset($animal['guid_svr'])) ? $animal['guid_svr'] : false;

                if (empty($guid_svr)) {
                    // уберем все значения с NULL
                    $temp_animal = [];

                    foreach ($animal as $key => $n) {
                        if (!empty(trim($n))) {
                            $temp_animal[$key] = $n;
                        }
                    }

                    $animal = $temp_animal;

                    // Генерация GUID v4 'UUID4'
                    $animal['guid_svr'] = Uuid::v4()->toString();

                    // форматируем даты
                    foreach ($list_date_field as $date_field) {
                        if (isset($animal[$date_field])) {
                            $animal[$date_field] = strtotime($animal[$date_field]) ? date('Y-m-d', strtotime($animal[$date_field])) : null;
                        }
                    }

                    // формируем JSONB
                    $animal['animals_json'] = $jsonB;
                    // добавляем животное. Но нужно добавить проверки.
                    // !!! Может сделать возможность передавать и Request и Collection ??? Минимальные изменения
                    // В проверке использовать Validator::make()
                    // array в Request Request::create()->replace($animal)
                    (new FromSelexMilk)->createRaw(Request::create()->replace(
                        $animal
                    ));

                    // DB::table($table_name)->insert(
                    //     $animal
                    // );

                    // $this->insert(DB_MAIN, SCHEMA_RAW . '.' . $table_name, $animal);

                    $result_animals [] = [
                        'nanimal' => (isset($animal['nanimal'])) ? $animal['nanimal'] : null,
                        'nanimal_time' => (isset($animal['nanimal_time'])) ? $animal['nanimal_time'] : null,
                        'guid_svr' => (isset($animal['guid_svr'])) ? $animal['guid_svr'] : null,
                        'status' => true,
                        'double' => false
                    ];
                    $result = true;
                } else {
                    $result_animals [] = [
                        'nanimal' => (isset($animal['nanimal'])) ? $animal['nanimal'] : null,
                        'nanimal_time' => (isset($animal['nanimal_time'])) ? $animal['nanimal_time'] : null,
                        'guid_svr' => (isset($animal['guid_svr'])) ? $animal['guid_svr'] : null,
                        'status' => false,
                        'double' => false
                    ];
                    $result = true;
                }
            }
        } else {
            throw new Exception(message: 'Переданные данные по животным не сохранены',
                code: 500);
        }

        return response()->json($result_animals);
    }


    /**
     * Метод получения правил валидации из списка полей таблицы и списка исключений
     *
     * @param array $table_columns - список полей таблицы
     * @param array $exclude_fields - список полей для исключения. Поля не входят в список полей для анализа
     */
    private function get_rules_by_columns(array $table_columns, array $exclude_fields): array
    {
        $list_fields = [];
        // проходим по всем полям. Создаем правила валидации
        foreach ($table_columns as $item) {
            // dump($item);
            // если поле из таблицы не равно списку исключения, то добавим его в список полей
            if (!in_array(strtolower($item['name']), $exclude_fields)) {
                $rule = 'present';  // present - обязательное поле и оно может быть null в отличие от required
                $list_fields['animals.*.' . $item['name']] = $rule;
            }
        }
        return $list_fields;
    }


    /**
     * Получение данных из СВР со стороны модуля обмена
     */
    public function selexCheckAnimals(Request $request): JsonResponse
    {

        return response()->json(
        // [
        // "status"        => true,
        // "data"          => [
        //     "list_animals" => [
        //         [
        //             "nanimal"      => 1188540001223,
        //             "nanimal_time" => "1188540001223",
        //             "guid_svr"     => "0eb6679a-cc33-458a-9fdf-d179b0ccb602",
        //             "duble"        => true,
        //             "status"       => false
        //         ],
        //         [
        //             "nanimal"      => "1188540001242",
        //             "nanimal_time" => "1184540001523",
        //             "guid_svr"     => "e8103cb0-c77f-469c-af86-cf26fa525fba",
        //             "duble"        => true,
        //             "status"       => true
        //         ],
        //         [
        //             "nanimal"      => "1188540001243",
        //             "nanimal_time" => "1184540001523",
        //             "guid_svr"     => "118efde1-58e1-426d-a5d0-bed0340c3458",
        //             "duble"        => false,
        //             "status"       => true
        //         ]
        //     ]
        // ],
        // "message"       => "Операция выполнена",
        // "notifications" => [
        //     "count_new"   => 3,
        //     "count_total" => 12
        // ],
        // "pagination"    => [
        //     "total_records" => 0,
        //     "max_page"      => 1,
        //     "cur_page"      => 1,
        //     "per_page"      => 100
        // ]
        // ]
        );

    }

    /**
     * Переключаем пользователя на работу с указанным хозяйством
     * @param int $user_id
     * @param int $token
     * @param int $participation_item_id
     * @param SystemParticipationsTypesEnum $participation_type
     */
    private function setUsersParticipations(int $user_id, int $token_id, int $participation_item_id, SystemParticipationsTypesEnum $participation_type)
    {

        // # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

        if ($participation_type == SystemParticipationsTypesEnum::ADMIN) {
            // Проверим, назначена ли пользователю роль администратора
            $role_data = $this->user_role_data($user_id, $participation_type->value);
            if ($role_data->isEmpty()) {
                throw new AuthenticationException('У пользователя отсутствует роль администратора');
            }

            // $this->set_admin();

            $participation_data = DataUsersParticipations::where([
                ['user_id' . '=' . $user_id],
                ['participation_item_type' . '=' . SystemParticipationsTypesEnum::ADMIN->value]
            ])->first();

            if ($participation_data) {
                $participation_id = $participation_item_id;
            } else {
                $participation_id = DataUsersParticipations::create([
                    'user_id' => $user_id,
                    'participation_item_type' => SystemParticipationsTypesEnum::ADMIN->value,
                    'role_id' => 1
                ])->participation_id;
            }


            // $this->update(DB_MAIN, SCHEMA_SYSTEM.'.'.TBL_USERS_TOKENS,
            //     ['participation_id' => $participation_id],
            //     ['token_id' => $this->USER('token_id')]);

            //  END  #   set admin


        } else {
            $user_participation_data = collect($this->user_participation_data($user_id, $participation_item_id, $participation_type));

            if ($user_participation_data->isNotEmpty()) {
                $res = SystemUsersToken::where('token_id', $token_id)->update(['participation_id' => $user_participation_data['participation_id']])->returning('token_id');
                $this->update(
                    DB_MAIN, SCHEMA_SYSTEM . '.' . TBL_USERS_TOKENS,
                    ['participation_id' => $user_participation_data['participation_id']],   // это данные на обновление???
                    ['token_id' => $token_id]                                               // это условие where для поиска строки которую будем обновлять?
                );

                $this->response_message('Привязка успешно установлена');

                $user_data_extend = $this->user_data_extend(['user_id' => $user_id]);

                $company_location_id = false;
                $region_id = false;
                $district_id = false;

                if (!empty($user_participation_data['participation_item_type'])) {
                    switch ($user_participation_data['participation_item_type']) {
                        case SystemParticipationsTypesEnum::COMPANY->value:
                            $company_location_id = $user_participation_data['participation_item_id'];
                            break;
                        case SystemParticipationsTypesEnum::REGION->value:
                            $region_id = $user_participation_data['participation_item_id'];
                            break;
                        case SystemParticipationsTypesEnum::DISTRICT->value:
                            $district_id = $user_participation_data['participation_item_id'];
                            break;
                    }
                }

                $this->response_dictionary('user_companies_locations_list', module_Users::widgets_list_user_company_location($user_data_extend['user_companies_locations_list'], 'simple', $company_location_id));
                $this->response_dictionary('user_roles_list', module_Users::widgets_list_user_roles($user_data_extend['user_roles_list'], 'simple', $user_participation_data['role_id']));
                $this->response_dictionary('user_districts_list', module_Users::widgets_list_user_districts($user_data_extend['user_districts_list'], 'simple', $district_id));
                $this->response_dictionary('user_regions_list', module_Users::widgets_list_user_regions($user_data_extend['user_regions_list'], 'simple', $region_id));
            }
        }
    }

    public function user_participation_data(int $user_id, int $participation_item_id, SystemParticipationsTypesEnum $participation_item_type)
    {
        return DB::table(DataUsersParticipations::getTableName())->where('user_id', $user_id)
            ->where('participation_item_id', $participation_item_id)
            ->where('participation_item_type', $participation_item_type->value)
            ->first();
    }


    /**
     * @param integer $user_id
     * @param string $role_slug
     *
     * @return Collection
     */
    private function user_role_data($user_id, $role_slug): Collection
    {
        $result = collect(DB::table(SystemUsersRoles::getTableName()
            . ' as ur')
            ->select(
                [
                    'ur.*',
                    'r.role_id',
                    'r.role_name_long',
                    'r.role_name_short',
                    'r.role_slug'
                ]
            )
            ->leftjoin(SystemRoles::getTableName() . ' AS r',
                'r.role_slug', '=',
                'ur.role_slug')
            ->where(
                [
                    ['ur.user_id', '=', $user_id],
                    ['ur.role_slug', '=', $role_slug],
                    [
                        'r.role_status', '=',
                        SystemStatusEnum::ENABLED->value
                    ],
                    [
                        'r.role_status_delete', '=',
                        SystemStatusDeleteEnum::ACTIVE->value
                    ]
                ]
            )
            ->first());
        return $result;
    }

    /**
     * Проверка наличия у пользователя в списке переданного хозяйства
     * и получение его ссылочного ключа company_location_id
     *
     * @param Collection $current_user - данные текущего пользователя
     */
    private function check_base_index_from_user(Collection $current_user)
    {
        // получаем базовый индекс хозяйства
        $company_base_index = (isset($current_user['user_base_index']))
            ? $current_user['user_base_index']
            : false;


        // подготовим для перебора необходимую область (ключ) из справочника (список подключенных хозяйств)
        $user_companies_locations_list
            = DataUsersParticipations::userCompaniesLocationsList($current_user['user_id'])
            ->all();
        // получаем данные о компании по ее базовому индексу

        $company_data = $this->company_data(false, false,
            $company_base_index);
        if ($company_data === false || $company_data->isEmpty()) {
            throw new AuthenticationException('Не найдены компании по базовому индексу хозяйства');
        }
        $company_id = (isset($company_data['company_id']))
            ? $company_data['company_id'] : false;

        // получаем связь компании с пользователем
        // - введем переменную локации со значением по умолчанию - false;
        $company_location_id = false;

        // - сделаем перебор списка хозяйств, назначенных пользователю
        foreach ($user_companies_locations_list as $item) {
            // проверим, есть ли компания, которая нас интересует
            $company_location_id = ($item->company_id === $company_id)
                ? $item->company_location_id
                : $company_location_id;
        }

        // если пользователю не назначено хозяйство или не найдено
        if ($company_location_id === false) {
            throw new AuthenticationException('Переданный базовый индекс хозяйства не привязан к учетной записи пользователя.');
        }

        // возвращаем флаг проверки
        return $company_location_id;
    }

    /** Вернуть текущего пользователя */
    private function get_current_user($user_email, $user_password)
    {

        // Проверить существование пользователя, который активный и не удален
        /** @var SystemUsers $users */
        $users = SystemUsers::where([
            ['user_email', '=', $user_email],
            ['user_status', '=', SystemStatusEnum::ENABLED->value],
            ['user_status_delete', '=', SystemStatusDeleteEnum::ACTIVE->value],
        ])->get();


        // Если получен список пользователей с одним email
        if (!is_null($users)) {
            // переберем пользователей
            foreach ($users as $item) {
                // если email и password совпали
                if ($item
                    && Hash::check($user_password,
                        $item->user_password)
                ) {
                    $current_user = $item;
                    break;  // выйдем из перебора
                }
            }
        }

        return $current_user;
    }

    /**
     * Метод получения данных компании по company_id (фермерской хозяйств) из
     * БД получаем данные о компании по ее базовому индексу Таблица:
     * data.data_companies
     *
     * @param false|int $company_id
     * @param false|string $guid_vetis
     * @param false|string $base_index
     *
     * @return Collection|false
     */
    private function company_data(
        false|int    $company_id = false,
        false|string $guid_vetis = false,
        false|string $base_index = false
    ): Collection|false
    {

        if ((bool)$company_id === false && (bool)$guid_vetis === false
            && (bool)$base_index === false
        ) {
            return false;
        }

        if ($company_id) {
            $where = ['company_id', '=', $company_id];
        }

        if ($guid_vetis) {
            $where = ['company_guid_vetis', '=', $guid_vetis];
        }

        if ($base_index) {
            $where = ['company_base_index', '=', $base_index];
        }

        return collect(DataCompanies::where([$where])->first());
    }

    /**
     * Проверка структуры переданных данных
     * - Наличие всех обязательных полей
     * - Наличие полей согласно таблице БД
     *
     * @param Request $request - Входные данные в метод selexSendAnimals со стороны модуля обмена
     * @param array $rules - Правила валидации на основе полей таблицы БД
     * @return void
     */
    private function validate_structure_request(Request $request, array $rules): void
    {
        // Валидация данных
        Validator::make(
            data: $request->all(),
            rules: [
                'main.base_index' => 'required|numeric',
                'main.task' => ['present', 'numeric', 'in:1,4,6'],
                'animals' => 'present',
                // проверка полей в секции ANIMALS
                ...$rules
            ],
            messages: [
                'main.base_index.required' => 'Не передан базовый индекс в секции MAIN',
                'main.task.in' => 'Переданный код задачи :input не сопоставлен ни с одним из типов известных задач (1 – молоко / 6- мясо / 4 - овцы).',
                'animals.required' => 'Отсутствует секция ANIMALS или она пустая',
                '*.*.*.present' => 'Атрибут :attribute в секции ANIMALS обязателен.',
            ]
        )
            ->validate();
    }


    /**
     * Получить имя таблицы по коду задачи
     *
     * @param $task - код задачи, берется из секции MAIN, атрибута TASK (1 – молоко / 6- мясо / 4 - овцы)
     * @return string|false     - имя таблицы
     * @example "raw.raw_from_selex_milk"
     */
    private static function switch_table($task): false|string
    {
        switch ($task) {
            case 1:
                return FromSelexMilk::getTableName();   // сырые данные из Селекс для мясных коров
            case 6:
                return FromSelexBeef::getTableName();   // сырые данные из Селекс для молочных коров
            case 4:
                return FromSelexSheep::getTableName();  // сырые данные из Селекс для овец
            default:
                return false;
        }
    }


}