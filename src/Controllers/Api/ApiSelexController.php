<?php

namespace Svr\Raw\Controllers\Api;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Svr\Core\Enums\SystemStatusDeleteEnum;
use Svr\Core\Enums\SystemStatusEnum;
use Svr\Core\Models\SystemRoles;
use Svr\Core\Models\SystemUsers;
use Svr\Core\Models\SystemUsersRoles;
use Svr\Core\Models\SystemUsersToken;
use Svr\Data\Models\DataCompanies;
use Svr\Data\Models\DataUsersParticipations;
use Symfony\Component\Mailer\Exception\InvalidArgumentException;

class ApiSelexController extends Controller
{
    /**
     * Авторизация
     */
    public function selexLogin(Request $request): JsonResponse
    {

        // НАЧАЛО валидация данных запроса
        $model = new SystemUsers();
        $current_user = collect(); // переменная для пользователя
        // - готовим список необходимых полей для валидации
        $filterKeys = ['user_email', 'user_password', 'user_base_index'];
        // - собираем набор правил и сообщений
        $rules = $model->getFilterValidationRules($request, $filterKeys);
        $messages = $model->getFilterValidationMessages($filterKeys);
        // - переопределяем ключи и правила
        $rules['base_index'] = $rules['user_base_index'];
        $rules['user_base_index'] = "required|".$rules['user_base_index'];
        $messages['base_index'] = $messages['user_base_index'];
        // ... TODO - подумать об оптимизации данного костыля
        // - удаляем ненужные ключи
        unset($rules['user_base_index']);
        unset($messages['user_base_index']);
        // - проверяем запрос на валидацию
        $request->validate($rules, $messages);
        // КОНЕЦ валидации данных запроса

        // Проверить существование пользователя, который активный и не удален
        /** @var SystemUsers $users */
        $users = SystemUsers::where([
            ['user_email', '=', $request['user_email']],
            ['user_status', '=', SystemStatusEnum::ENABLED->value],
            ['user_status_delete', '=', SystemStatusDeleteEnum::ACTIVE->value],
        ])->get();


        // Если получен список пользователей с одним email
        if (!is_null($users)) {
            // переберем пользователей
            foreach ($users as $item) {
                // если email и password совпали
                if ($item
                    && Hash::check($request['user_password'],
                        $item->user_password)
                ) {
                    $current_user = $item;
                    break;  // выйдем из перебора
                }
            }
        }

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
            'user_id'          => $current_user['user_id'],
            'participation_id' => $participation_id,
            'token_value'      => $new_token,
            'token_client_ip'  => $request->ip()
        ]);
        // роль "Ветеринарный врач хозяйства"
        /** @var Collection $role_data */
        $role_data = $this->user_role_data($current_user['user_id'],
            'doctor_company');
        // проверка роли "Ветеринарный врач хозяйства"
        if ($role_data->isEmpty()) {
            return response()->json(['error' => 'У пользователя отсутствует роль "Ветеринарный врач хозяйства"'],
                401);
        }
        // Проверка наличия у пользователя в списке переданного хозяйства и получение его ссылочного ключа company_location_id
        $participation_item_id
            = $this->check_base_index_from_user(collect($current_user));  // ссылочный ключ на хозяйства, который назначен хозяйству
        $participation_type
            = 'company';                                    // тип участника - компания

        if ($participation_item_id === false) {
            throw new InvalidArgumentException('Пользователь не привязан к указанному хозяйству',
                401);
        }

        // добавляем в контейнер данных из переданного запроса новые поля и значения
        $data['participation_item_id'] = $participation_item_id;
        $data['participation_item_type'] = $participation_type;

        // правила валидации
         // валидация запроса
        $model = new DataUsersParticipations();
        // - готовим список необходимых полей для валидации
        $filterKeys = ['participation_item_id', 'participation_item_type'];
        // - собираем набор правил и сообщений
        $rules = $model->getFilterValidationRules($request, $filterKeys);
        $messages = $model->getFilterValidationMessages($filterKeys);

        // - переопределяем ключи и правила
        $rules['participation_item_id'] = "required|".$rules['participation_item_id'];
        // - проверяем запрос на валидацию
        $valid_data = Validator::make($data, $rules, $messages)->validate();

        // переключаем пользователя на работу с указанным хозяйством
        $auth = new DataUsersParticipations();
        $auth_set_result = $auth->api_set_v1($valid_data, [
            'user_id'  => $this->USER('user_id'),
            'token_id' => $this->USER('token_id')
        ]);

        if ($auth_set_result) {
            $data = $this->response_data();
            $user_token = (isset($data['user_token'])) ? $data['user_token']
                : '';
            $this->response_clear();
            $this->response_data('user_token', $user_token);
            $this->response_message('Выдан токен');
            return true;
        } else {
            $this->response_message($auth->response_message('Ошибка переключаем пользователя на работу с указанным хозяйством'));
        }

        // Заглушка
        return response()->json(['test' => 'test'], 200);
    }

    /**
     * Передача данных в СВР со стороны модуля обмена
     */
    public function selexSendAnimals(Request $request): JsonResponse
    {

        return response()->json();
    }

    /**
     * Получение данных из СВР со стороны модуля обмена
     */
    public function selexCheckAnimals(Request $request): JsonResponse
    {

        return response()->json(
            [
                "status"        => true,
                "data"          => [
                    "list_animals" => [
                        [
                            "nanimal"      => 1188540001223,
                            "nanimal_time" => "1188540001223",
                            "guid_svr"     => "0eb6679a-cc33-458a-9fdf-d179b0ccb602",
                            "duble"        => true,
                            "status"       => false
                        ],
                        [
                            "nanimal"      => "1188540001242",
                            "nanimal_time" => "1184540001523",
                            "guid_svr"     => "e8103cb0-c77f-469c-af86-cf26fa525fba",
                            "duble"        => true,
                            "status"       => true
                        ],
                        [
                            "nanimal"      => "1188540001243",
                            "nanimal_time" => "1184540001523",
                            "guid_svr"     => "118efde1-58e1-426d-a5d0-bed0340c3458",
                            "duble"        => false,
                            "status"       => true
                        ]
                    ]
                ],
                "message"       => "Операция выполнена",
                "notifications" => [
                    "count_new"   => 3,
                    "count_total" => 12
                ],
                "pagination"    => [
                    "total_records" => 0,
                    "max_page"      => 1,
                    "cur_page"      => 1,
                    "per_page"      => 100
                ]
            ]
        );

    }


    /**
     * @param  integer  $user_id
     * @param  string  $role_slug
     *
     * @return Collection
     */
    private function user_role_data($user_id, $role_slug): Collection
    {
        $result = collect(DB::table(SystemUsersRoles::getTableName().' as ur')
            ->select(
                [
                    'ur.*',
                    'r.role_id',
                    'r.role_name_long',
                    'r.role_name_short',
                    'r.role_slug'
                ]
            )
            ->leftjoin(SystemRoles::getTableName().' AS r', 'r.role_slug', '=',
                'ur.role_slug')
            ->where(
                [
                    ['ur.user_id', '=', $user_id],
                    ['ur.role_slug', '=', $role_slug],
                    ['r.role_status', '=', SystemStatusEnum::ENABLED->value],
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
     * @param  Collection  $current_user  - данные текущего пользователя
     */
    private function check_base_index_from_user(Collection $current_user)
    {
        // получаем базовый индекс хозяйства
        $company_base_index = (isset($current_user['user_base_index'])) ? $current_user['user_base_index']
            : false;

        // получаем данные из справочника (Установка справочника ответа)
//        $data = $this->response_dictionary();

        // подготовим для перебора необходимую область (ключ) из справочника (список подключенных хозяйств)
        $user_companies_locations_list
            = DataUsersParticipations::userCompaniesLocationsList($current_user['user_id'])
            ->all();
        // получаем данные о компании по ее базовому индексу

        $company_data = $this->company_data(false, false, $company_base_index);
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


    /**
     * Метод получения данных компании по company_id (фермерской хозяйств) из
     * БД получаем данные о компании по ее базовому индексу Таблица:
     * data.data_companies
     *
     * @param  false|int  $company_id
     * @param  false|string  $guid_vetis
     * @param  false|string  $base_index
     *
     * @return Collection|false
     */
    private function company_data(
        false|int        $company_id = false,
        false|string     $guid_vetis = false,
        false|string     $base_index = false
    ): Collection|false {

        if ((bool) $company_id === false && (bool) $guid_vetis === false
            && (bool) $base_index === false
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

        $result = collect(DataCompanies::where([$where])->first());
//        $query = 'SELECT * FROM ' . SCHEMA_DATA . '.' . TBL_COMPANIES . ' WHERE ' . $where . ' LIMIT 1';
//        return $this->get_data(DB_MAIN, $query, $query_data, 'row');
        return $result;
    }


}
