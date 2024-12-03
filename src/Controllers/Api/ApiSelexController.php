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
use Svr\Core\Resources\SvrApiResponseResource;
use Svr\Data\Models\DataAnimals;
use Svr\Data\Models\DataApplicationsAnimals;
use Svr\Data\Models\DataCompanies;
use Svr\Data\Models\DataCompaniesLocations;
use Svr\Data\Models\DataUsersParticipations;
use Svr\Raw\Models\FromSelexBeef;
use Svr\Raw\Models\FromSelexMilk;
use Svr\Raw\Models\FromSelexSheep;
use Svr\Raw\Resources\SelexSendAnimalsCollection;
use Svr\Raw\Resources\SelexSendAnimalsResource;
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

        // // Создаём токен пользователю
        $new_token = $current_user->createToken('auth_token')->plainTextToken;


        // Создали запись в таблице токенов
        $token_data = (new SystemUsersToken())->userTokenStore([
            'user_id' => $current_user['user_id'],
            'participation_id' => null,
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
        $data['participation_item_type'] = SystemParticipationsTypesEnum::COMPANY->value;

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
        Validator::make($data, $rules, $messages)->validate();

        // преобразуем $valid_data в объект Request
        // $valid_data = new Request($valid_data);

        // переключаем пользователя на работу с указанным хозяйством
        $result = $this->setUsersParticipations($current_user['user_id'], $token_data['token_id'], $participation_item_id, SystemParticipationsTypesEnum::COMPANY);

        return response()->json(['data' => ['user_token' => $new_token]], 200);
    }

    /**
     * Передача данных в СВР со стороны модуля обмена
     * (Закачка животных из Селекс в таблицы с сырыми данными)
     */
    public function selexSendAnimals(Request $request): SvrApiResponseResource
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
            'ANIMALS_JSON',             // сырые данные из Селекс
            'IMPORT_STATUS',            // ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)
            'created_at',               // Дата и время создания
            'update_at',                // Дата и время модификации
            'TASK',                     // код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы)
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
        $result_animals = $this->add_animals($request, $table_name);

        $data = collect([

            'status' => true,
            'result_animals' => $result_animals,
            'message' => 'Операция выполнена',
            'user_id' => $current_user['user_id'],
            'response_resource_data' => SelexSendAnimalsCollection::class,
            'response_resource_dictionary' => false,
            'pagination' => [
                'total_records' => 1,
                'cur_page' => 1,
                'per_page' => 1
            ],
        ]);

        return new SvrApiResponseResource($data);

    }


    /**
     * Добавить животное в схему RAW таблицу с холодными данными
     * Все переменные должны быть предварительно проверены перед передачей в метод
     *
     * @param Request|Collection $data - контейнер, содержащий параметры для записи данных
     * @param string $table_name - имя таблицы
     */
    private function add_animals(Request|Collection $data, string $table_name): array
    {
        // Если $data является объектом типа Request, преобразовать в коллекцию
        if ($data instanceof Request) {
            $data = collect($data->all());
        }

        $list_date_field = [
            'DATE_ROGD',
            'DATE_POSTUPLN',
            'DATE_V',
            'DATE_CHIP',
            'DATE_NINV',
            'DATE_NGOSREGISTER',
            'DATE_ROGD_MATERI',
            'DATE_ROGD_OTCA',
            'DATE_NINVRIGHT',
            'DATE_NINVLEFT'
        ];
        // результат обработки по каждому из переданных животных
        $result_animals = [];
        // флаг обработки животных
        $result = false;
        // список животных
        $list_animals = (isset($data['animals'])) ? $data['animals'] : false;
        // TODO? Думаю нужно убрать, т.к. $table_name обязательный параметр на входе метода. animals проверяется в validate_structure_request
        if ($list_animals && $table_name) {

            // получаем список животных из БД СВР
            $nanimals_list = array_column($list_animals, 'NANIMAL_TIME');
            $nanimals_data = DB::table($table_name)
                ->select(
                    'GUID_SVR',
                    'NANIMAL',
                    'NANIMAL_TIME'
                )
                ->where('NHOZ', '=', $data['main']['base_index'])
                ->where('NANIMAL_TIME', '!=', '')
                ->whereNotNull('NANIMAL_TIME')
                ->whereIn('NANIMAL_TIME', $nanimals_list)
                // Сортировка нужна для корректной работы метода distinct
                // https://postgrespro.ru/docs/postgrespro/9.5/sql-select#SQL-DISTINCT
                ->orderBy('NANIMAL_TIME')
                ->orderBy('raw_from_selex_milk_id', 'desc')
                ->distinct('NANIMAL_TIME')
                ->get()
                // используем keyBy для преобразования коллекции в хешь-таблицу
                ->keyBy('NANIMAL_TIME');

            // Преобразуем коллекцию $nanimals_data которая содержит объекты stdClass в массивы
            $nanimals_data = $nanimals_data->map(function ($item) {
                return (array)$item;
            });

            foreach ($list_animals as $animal) {

                if (isset($nanimals_data[$animal['NANIMAL_TIME']])) {
                    $result_animals [] = SelexSendAnimalsResource::make(
                        [
                            'nanimal' => $nanimals_data[$animal['NANIMAL_TIME']]['NANIMAL'] ?? null,
                            'nanimal_time' => $nanimals_data[$animal['NANIMAL_TIME']]['NANIMAL_TIME'] ?? null,
                            'guid_svr' => $nanimals_data[$animal['NANIMAL_TIME']]['GUID_SVR'] ?? null,
                            'status' => true,
                            'double' => true
                        ]
                    );
                    continue;
                }

                $guid_svr = (isset($animal['GUID_SVR'])) ? $animal['GUID_SVR'] : false;

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
                    $animal['GUID_SVR'] = Uuid::v4()->toString();

                    // форматируем даты. Переводим формат даты с разделителем '-'. Формат 'Y-m-d'
                    foreach ($list_date_field as $date_field) {
                        if (isset($animal[$date_field])) {
                            $animal[$date_field] = strtotime($animal[$date_field]) ? date('Y-m-d', strtotime($animal[$date_field])) : null;
                        }
                    }

                    // формируем JSONB для поля ANIMALS_JSON
                    $animal['ANIMALS_JSON'] = json_encode(array_change_key_case($animal), JSON_UNESCAPED_UNICODE);

                    // Модель в зависимости от номера task
                    if ($table_name === FromSelexMilk::getTableName()) {
                        $table = new FromSelexMilk;
                    } elseif ($table_name === FromSelexBeef::getTableName()) {
                        $table = new FromSelexBeef;
                    } elseif ($table_name === FromSelexSheep::getTableName()) {
                        $table = new FromSelexSheep;
                    }

                    // добавляем животное в таблицу
                    $table->createRaw($animal);

                    $result_animals [] = SelexSendAnimalsResource::make(
                        [
                            'nanimal' => $animal['NANIMAL'] ?? null,
                            'nanimal_time' => $animal['NANIMAL_TIME'] ?? null,
                            'guid_svr' => $animal['GUID_SVR'] ?? null,
                            'status' => true,
                            'double' => false
                        ]
                    );

                } else {

                    $result_animals [] = SelexSendAnimalsResource::make(
                        [
                            'nanimal' => $animal['NANIMAL'] ?? null,
                            'nanimal_time' => $animal['NANIMAL_TIME'] ?? null,
                            'guid_svr' => $animal['GUID_SVR'] ?? null,
                            'status' => false,
                            'double' => false
                        ]
                    );
                }
            }
        } else {
            throw new Exception(message: 'Переданные данные по животным не сохранены',
                code: 500);
        }
        return $result_animals;
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
            // если поле name из таблицы не найдено в списке исключения, то добавим его в список полей.
            // Сравнение регистронезависимое
            if (!in_array($item['name'], $exclude_fields)) {
                $rule = 'present';  // present - обязательное поле и оно может быть null в отличие от required
                $list_fields['animals.*.' . $item['name']] = $rule;
            }
        }
        return $list_fields;
    }


    /**
     * Получение данных из СВР со стороны модуля обмена
     */
    public function selexGetAnimals(Request $request): SvrApiResponseResource
    {
        // Проверка входящих данных
        $request->validate([
            'list_animals_guid_svr' => 'required|array',
        ], [
            'list_animals_guid_svr.required' => 'Поле list_animals_guid_svr обязательно для заполнения',
            'list_animals_guid_svr.array' => 'Поле list_animals_guid_svr должно быть массивом',
        ]);

        $current_user = auth()->user();
        // получаем данные по компании по данным пользователя
        $company_location_id = $current_user['company_location_id'];

        $ttt = $this->get_animals_list_from_different_list_guid_svr($request, $company_location_id);

        $data = [
            'status' => true,
            'message' => 'Операция выполнена',
            'response_resource_data' => $ttt,
            'user_id' => $current_user['user_id'],
            'response_resource_dictionary' => false,
            'pagination' => [
                'total_records' => 1,
                'cur_page' => 1,
                'per_page' => 1
            ],
        ];
        dd($data);
        // return SvrApiResponseResource::make($data);

    }


    /**
     * Получить локацию компании для выборки по животным
     *
     * @return false|mixed
     */
    private function get_animals_list_from_different_list_guid_svr($data, $company_location_id)
    {

        $result = [];                                                        // конечный результат
        $list_application_animal = [
            'added' => 'Добавлено в систему СВР',                                // добавили в систему. животное из СЕЛЭКС попало в СВР'
            'in_application' => 'Добавлено в заявку',                                    // 'добавили в заявку. животное добавили в заявку, но заявка пока в статусе "Создана"',
            'sent' => 'Отправлено на регистрацию',                            // 'отправили на регистрацию. заявку перевели в статус "Сформирована" и затем отправили в Хорриот.
            'registered' => 'Животное зарегистрировано. Присвоен guid из Хорриот',    //'зарегистрировали. получили ответ от Хорриот, все ок, и животное имеет номер.',
            'rejected' => 'Отказ в регистрации',                                    //'отказ. по какой-то причине отказали, вет. врач или система Хорриот.',
        ];


        // Подзапрос для получения последнего application_id для каждого animal_id с использованием ROW_NUMBER()
        $subQuery = DB::table(DataApplicationsAnimals::getTableName() . ' as t_application_animal_temp')
            ->select('animal_id') // Выбираем animal_id
            ->selectRaw('application_id') // Выбираем application_id
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY animal_id ORDER BY application_id DESC) as rn') // Используем ROW_NUMBER() для нумерации строк в порядке убывания application_id для каждого animal_id
            ->toSql(); // Преобразуем запрос в строку SQL

        // Основной запрос
        $list_animals = DB::table(DataAnimals::getTableName() . ' as t_animal')
            ->select(
                't_animal.*', // Выбираем все поля из таблицы t_animal
                't_application_animal.application_animal_status' // Выбираем поле application_animal_status из таблицы t_application_animal
            )
            ->leftJoin(DB::raw("($subQuery) as t_application_animal_temp"), function ($join) {
                $join->on('t_application_animal_temp.animal_id', '=', 't_animal.animal_id') // Соединяем по animal_id
                ->where('t_application_animal_temp.rn', '=', 1); // Фильтруем, чтобы получить только строки с rn = 1 (последние application_id для каждого animal_id)
            })
            ->leftJoin(DataApplicationsAnimals::getTableName() . ' as t_application_animal', function ($join) {
                $join->on('t_application_animal.animal_id', '=', 't_animal.animal_id') // Соединяем по animal_id
                ->on('t_application_animal.application_id', '=', 't_application_animal_temp.application_id'); // Соединяем по application_id, чтобы получить соответствующие записи из t_application_animal
            })
            ->where('t_animal.company_location_id', '=', $company_location_id) // Фильтруем по company_location_id
            ->whereIn('t_animal.animal_guid_self', $data['list_animals_guid_svr']) // Фильтруем по массиву list_animals_guid_svr
            ->get() // Выполняем запрос и получаем результаты
            ->keyBy('animal_guid_self'); // Устанавливаем ключи массива результатов по полю animal_guid_self

        // dd($list_animals);



        foreach ($data['list_animals_guid_svr'] as $guid_svr) {
            if (!isset($list_animals[$guid_svr])) {
                $result[$guid_svr] = [
                    'guid_svr' => $guid_svr,
                    'guid_horriot' => null,
                    'number_horriot' => null,
                    'message' => 'Животное не найдено',
                ];

                continue;
            }

            $animal_data = (array)$list_animals[$guid_svr];

            $animal_message = match ($animal_data['application_animal_status']) {
                'added', 'in_application', 'sent', 'registered', 'rejected' => $list_application_animal[$animal_data['application_animal_status']],
                default => 'Животное еще не добавлено в заявку',
            };
            // dd($animal_message);
            $result[$guid_svr] = [
                'guid_svr' => $guid_svr,
                'guid_horriot' => (!empty($animal_data['animal_guid_horriot'])) ? $animal_data['animal_guid_horriot'] : null,
                'number_horriot' => (!empty($animal_data['animal_number_horriot'])) ? $animal_data['animal_number_horriot'] : null,
                'message' => $animal_message,
            ];
        }

        return $result;
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
        $user_participation_data = collect(DataUsersParticipations::where([
            ['user_id', '=', $user_id],
            ['participation_item_id', '=', $participation_item_id],
            ['participation_item_type', '=', $participation_type->value]
        ])->first());

        if ($user_participation_data->isNotEmpty()) {
            SystemUsersToken::where('token_id', $token_id)
                ->update(
                    [
                        'participation_id' => $user_participation_data['participation_id']
                    ]
                );


            // $this->response_message('Привязка успешно установлена');

            // $user_data_extend = $this->user_data_extend(['user_id' => $user_id]);

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

            // $this->response_dictionary('user_companies_list', module_Users::widgets_list_user_companies($user_data_extend['user_companies_list'], 'simple', $company_location_id));
            // $this->response_dictionary('user_companies_locations_list', module_Users::widgets_list_user_company_location($user_data_extend['user_companies_locations_list'], 'simple', $company_location_id));
            // $this->response_dictionary('user_roles_list', module_Users::widgets_list_user_roles($user_data_extend['user_roles_list'], 'simple', $user_participation_data['role_id']));
            // $this->response_dictionary('user_districts_list', module_Users::widgets_list_user_districts($user_data_extend['user_districts_list'], 'simple', $district_id));
            // $this->response_dictionary('user_regions_list', module_Users::widgets_list_user_regions($user_data_extend['user_regions_list'], 'simple', $region_id));
            return [
                'company_location_id' => $company_location_id,
                'region_id' => $region_id,
                'district_id' => $district_id,
            ];
        } else {
            throw new Exception(message: 'Привязка пользователя на хозяйство не найдена', code: 404);
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
