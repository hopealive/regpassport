<?php
/**
 * Description of Utils
 *
 * @author hopealive
 */
include("Db.class.php");
include("Validators.php");
include("Recaptcha.php");

class Utils extends DB
{

    /**
     * Routing
     * @return array
     */
    public function routing()
    {
        $viewParams = [];
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case "signup":
                    $viewParams = $this->action_signup();
                    break;
                case "mark":
                    $viewParams = $this->action_mark();
                    break;
            }
            $viewParams['action'] = $_POST['action'];
        } else {
            $viewParams['action'] = 'index';
        }
        return $viewParams;
    }
    //----------
    //Controller
    //----------

    /**
     * @return array
     */
    public function action_signup()
    {
        $row    = [
            'firstname' => $_POST['firstname'],
            'patronymic' => $_POST['patronymic'],
            'surname' => $_POST['surname'],
        ];
        $result = $this->validateNewUser($row);

        $message = 'Невідома помилка';

        //check if registered for last 30 minutes //TODO:

        if ($result['status'] == 'ok' && !empty($result['data'])) {
            $userId = $this->addNewUser($result['data']);
            if ($userId > 0) {
                $message = "Ви успішно зареєструвались. <br>Ваш номер: <br><h1>$userId</h1>";
                return [
                    'status' => 'ok',
                    'user-id' => $userId,
                    'message' => $message,
                ];
            } else {
                $message = "Помилка збереження користувача";
            }
        } elseif (isset($result['message'])) {
            $message = $result['message'];
        }

        return [
            'status' => 'fail',
            'message' => $message,
        ];
    }

    /**
     * @return array
     */
    public function action_mark()
    {
        $message = '';

        $userId = (int) $_POST['user-id'];

        //check if marked today //TODO:
        //check if marked another for last 30 minutes //TODO:
        //add mark
        if ($this->mark($userId)) {
            return [
                'status' => 'ok',
                'message' => 'Ви успішно відмітились',
            ];
        } else {
            $message = 'Помилка відмічення';
        }
        return [
            'status' => 'fail',
            'message' => $message,
        ];
    }

    //----------
    //DB actions
    //----------

    public function searchForMarking()
    {
        $users = $this->showActive();

        $data = [];
        if (isset($_POST['term'])) {
            $term       = trim(htmlentities(strip_tags($_POST['term'])));
            $term       = stripslashes($term);
            $Validators = new Validators();
            if (strlen($term) > 0 && $Validators->validateAutocomplete($term)) {
                //search in id
                foreach ($users as $u) {
                    $checkItem = [
                        $u['u__id'],
                        $u['u__firstname'],
                        $u['u__patronymic'],
                        $u['u__surname'],
                    ];
                    foreach ($checkItem as $item) {
                        if (strpos($item, $term) > -1) {
                            $data[] = [
                                "id" => $u['u__id'],
                                "name" => $this->getFullName($u),
                            ];
                        }
                    }
                }
            }
        }
        return json_encode($data);
    }

    public function getList()
    {
        $users = $this->showActive();
        if (!empty($users)) {
            $i = 1;
            foreach ($users as $u) {
                $marked = '<span class="badge badge-danger">Ні</span>';
                if (!is_null($u['m__id'])) {
                    $marked = '<span class="badge badge-success mark-this" style="cursor:pointer">Так</span>';
                }

                $items[] = [
                    "id" => $i,
                    "name" => $this->getFullName($u),
                    "marked" => $marked,
                ];
                ++$i;
            }
        }
        $data = ['data' => $items];
        return json_encode($data);
    }

    public function addNewUser($row)
    {
        $query = "INSERT INTO users
            (firstname, patronymic, surname, create_date, is_active) VALUES
            ('".$row['firstname']."',
            '".$row['patronymic']."',
            '".$row['surname']."',
            '".date("Y-m-d H:i:s")."', 1)";

        try {
            $this->beginTransaction();
            $this->query($query);
            $id = $this->lastInsertId();
            $this->executeTransaction();
            return $id;
        } catch (PDOExecption $e) {
            $this->rollBack();
        }
        return FALSE;
    }

    public function mark($id)
    {
        if (!is_numeric($id)) {
            return false;
        }
        $query = "INSERT INTO marks
            (user_id, create_date) VALUES
            ($id, '".date("Y-m-d H:i:s")."')";
        if ($this->query($query)) {
            return true;
        }
        return FALSE;
    }

    public function showActive()
    {
        $query = "SELECT
                    u.id as u__id,
                    u.firstname as u__firstname,
                    u.patronymic as u__patronymic,
                    u.surname as u__surname,
                    u.create_date as u__create_date,
                    m.id as m__id
            FROM users as u
            LEFT JOIN marks as m ON u.id = m.user_id
            and DATE(m.create_date) = CURDATE()
            WHERE u.is_active = 1
                and u.has_passport = 0
            ORDER BY u.create_date ASC";

        return $this->query($query);
    }
    //Toggle User: activate|disactivate

    /**
     * @param int $userId
     * @return boolean
     */
    public function activateUser($userId)
    {
        if ($userId > 0) {
            $query = "UPDATE users SET is_active = 1 WHERE  id = ".(int) $userId;
            if ($this->query($query)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param type $userId
     * @return boolean
     */
    public function disactivateUser($userId)
    {
        if ($userId > 0) {
            $query = "UPDATE users SET is_active = 0 WHERE  id = ".(int) $userId;
            if ($this->query($query)) {
                return true;
            }
        }
        return false;
    }

    public function validateNewUser($row)
    {
        //captcha
        $Recaptcha = new Recaptcha();
        if (!$Recaptcha->validateRecaptcha()) {
            return ['status' => 'fail', 'message' => 'Введена неправильна капча'];
        }

        //filter
        $row = $this->filterUserData($row);
        if (!$row) {
            return ['status' => 'fail', 'message' => 'Введено некорректні/пусті данні'];
        }

        //validate
        $Validators = new Validators();
        if (!$Validators->validateUser($row)) {
            return ['status' => 'fail', 'message' => 'Введено некорректні данні'];
        }

        if (!$this->validateUserExists($row)) {
            return ['status' => 'fail', 'message' => 'Такий користувач вже існує в системі'];
        }
        return [
            'status' => 'ok',
            'data' => $row,
        ];
    }

    public function validateSuperAdmin()
    {
        //check client info
        if (
            md5($_SERVER['HTTP_USER_AGENT']) == "93e8f6a8d4df3cb6af9902e296d15bc5"
        ) {
            return true;
        }
        return FALSE;
    }

    /**
     * Filter data
     * @param type array
     * @return array
     */
    public function filterUserData($row)
    {
        foreach ($row as $key => $r) {
            $r = trim(htmlentities(strip_tags($r)));
            $r = stripslashes($r);
            if (empty($r) OR strlen($r) < 3) {
                return false;
            }
            $row[$key] = $r;
        }
        return $row;
    }

    /**
     *
     * @param type array
     * @return boolean
     */
    public function validateUserExists($row)
    {
        //validate for exists
        $query = "SELECT * FROM users
            WHERE firstname = '".$row['firstname']."'
            AND patronymic = '".$row['patronymic']."'
            AND surname = '".$row['surname']."'
        ";

        $userExists = $this->query($query);
        if (!empty($userExists)) {
            return FALSE;
        }
        return true;
    }

    /**
     *   @void
     * 	Creates the log
     *
     */
    public function writeDbLog($message)
    {
        $query = "INSERT INTO logs (log, create_date) VALUES
            ( '$message', '".date('Y-m-d H:i:s')."' )";
        if ($this->query($query)) {
            return true;
        }
        return FALSE;
    }

    protected function getFullName($u)
    {
        $sn       = mb_strtoupper(mb_substr($u['u__surname'], 0, 1, 'UTF-8'));
        $sn       = $sn.mb_substr($u['u__surname'], 1, strlen($u['u__surname']),
                'UTF-8');
        $fullname = $sn
            ." "
            .strtoupper(mb_substr($u['u__firstname'], 0, 1, 'UTF-8'))
            .". "
            .strtoupper(mb_substr($u['u__patronymic'], 0, 1, 'UTF-8'))
            .". "
        ;
        return $fullname;
    }
}