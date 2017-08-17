<?php
/**
 * Description of Utils
 *
 * @author gregzorb
 */
require("Db.class.php");
require("Recaptcha.php");

class Utils extends DB
{

    public function searchForMarking()
    {
        $users = $this->showActive();

        $data = [];
        if (isset($_POST['term'])) {
            $term = trim(htmlentities(strip_tags($_POST['term'])));
            $term = stripslashes($term);
            if (strlen($term) > 0 && $this->validateAutocomplete($term)) {
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

    public function validateNewUser($row)
    {
        if (!$this->validateRecaptcha()) {
            return ['status' => 'fail', 'message' => 'Введена неправильна капча'];
        }

        $row = $this->filterUserData($row);
        if (!$row) {
            return ['status' => 'fail', 'message' => 'Введено некорректні/пусті данні'];
        }

        $row = $this->validateUser($row);
        if (!$row) {
            return ['status' => 'fail', 'message' => 'Введено некорректні данні'];
        }

        $row = $this->validateUserExists($row);
        if (!$row) {
            return ['status' => 'fail', 'message' => 'Такий користувач вже існує в системі'];
        }
        return [
            'status' => 'ok',
            'data' => $row,
        ];
    }

    //filter data
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

    public function validateAutocomplete($item)
    {
        if (!preg_match("/[0-9A-Za-zА-Яа-яЁёЇїІіЄєҐґ]/", $item)) {
            return FALSE;
        }
        return true;
    }

    protected function validateUser($row)
    {
        foreach ($row as $key => $r) {
            if (!preg_match("/[A-Za-zА-Яа-яЁёЇїІіЄєҐґ]/", $r)) {
                return FALSE;
            }
        }
        return $row;
    }

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
        return $row;
    }

    public function validateRecaptcha()
    {
        $captcha = $_POST['g-recaptcha-response'];
        if (!$captcha) {
            return false;
        }
        $secretKey    = "6LeY4SwUAAAAAHv1pvwaAqeUIjLdxp9y2Efn18Xb";
        $ip           = $_SERVER['REMOTE_ADDR'];
        $response     = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
        $responseKeys = json_decode($response, true);
        if (intval($responseKeys["success"]) !== 1) {
            //spammer
            return false;
        } else {
            return true;
        }
    }

    public function add($row)
    {
        $query = "INSERT INTO users
            (firstname, patronymic, surname, create_date, is_active) VALUES
            ('".$row['firstname']."',
            '".$row['patronymic']."',
            '".$row['surname']."',
            '".date("Y-m-d H:i:s")."', 1)";
        if ($this->query($query)) {
            return true;
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

    public function setActive($id)
    {
        if ($id > 0) {
            $query = "UPDATE users SET is_active = 1 WHERE  id = ".(int) $id;
            if ($this->query($query)) {
                return true;
            }
        }
        return false;
    }

    public function getFullName($u)
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

    function renderBlock($block)
    {
        $folder = __DIR__."/../blocks";
        switch ($block) {
            case "head":
                $filename = "/head.html";
                break;
            case "queue":
                $filename = "/queue.html";
                break;
            case "navbar":
                $filename = "/navbar.html";
                break;
            case "contacts":
                $filename = "/contacts.html";
                break;
            case "footer":
                $filename = "/footer.html";
                break;
            case "js":
                $filename = "/js.html";
                break;
            case "testmodal":
                $filename = "/testmodal.html";
                break;
        }

        $filename = $folder.$filename;

        $handle   = fopen($filename, "rb");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        return $contents;
    }
}