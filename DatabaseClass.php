<?php

class Database
{
    public $mysqli;
    public $name;
    public $columns;

    public function __construct(mysqli $mysqli, string $name, array $columns)
    {
        $this->mysqli = $mysqli;
        $this->name = $name;
        $this->columns = $columns;
    }

    public function getAll()
    {
        $result = $this->mysqli->query("SELECT * FROM " . $this->name);
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getOne($id)
    {
        $result = $this->mysqli->query("SELECT * FROM " . $this->name . " WHERE id = " . $id);
        $row = $result->fetch_assoc();
        return $row;
    }

    public function getWhere($whatGet = null, $data = null, $notbool = false): string|array|null
    {
        $columnsToGet = "";

        if ($whatGet == null || (is_array($whatGet) && count($whatGet) == 0)) {
            $columnsToGet = "*";
        } else if (!is_array($whatGet)) {
            $whatGet = array($whatGet);
            foreach ($whatGet as $what) {
                $columnsToGet .= $what . ", ";
            }
            $columnsToGet = substr($columnsToGet, 0, -2);
        } else {
            foreach ($whatGet as $what) {
                $columnsToGet .= $what . ", ";
            }
            $columnsToGet = substr($columnsToGet, 0, -2);
        }
        $result = null;
        if ($data == null) {
            $result = $this->mysqli->query("SELECT " . $columnsToGet . " FROM " . $this->name);
        } else {
            $columns = "";
            $values = "";
            $wherestr = "";
            // data is like array("username" => "test", "password" => "test"), i need a string like "username = test, password = test"
            foreach ($data as $key => $value) {
                if (is_int($value))
                    $wherestr .= $key . " = " . $value . " AND ";
                else {
                    $wherestr .= $key . " = '" . $value . "' AND ";
                }
            }
            $wherestr = substr($wherestr, 0, -5);

            $columns = substr($columns, 0, -2);
            $values = substr($values, 0, -2);
            $req = "";
            if ($notbool) {
                $req = "SELECT " . $columnsToGet . " FROM " . $this->name . " WHERE NOT " . $wherestr;
            } else {
                $req = "SELECT " . $columnsToGet . " FROM " . $this->name . " WHERE " . $wherestr;
            }
            //echo $req;
            $result = $this->mysqli->query($req);
        }
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        if (count($rows) == 0)
            return null;
        // if (count($rows) == 1 && count($whatGet) == 1) {
        //     return array_values($rows[0])[0];
        // }
        return $rows;
    }

    public function getWhereNot($whatGet = null, $data = null): string|array|null
    {
        return $this->getWhere($whatGet, $data, true);
    }

    // GET WHERE IN FUNCTION WILL BE RECEIVE IN $WHATGET ARRAY OF COLUMNS AND IN $DATA THE STRING WITH THE NAME TO CHECK IF IS INSIDE THE CONTAINER $CONTAINER, THE CONTAINER IS AN ARRAY OF VALUES LIKE THIS: array("value1", "value2", "value3")
    public function getWhereIn($whatGet = null, $data = null, $container = null)
    {
        $columnsToGet = "";

        if ($whatGet == null || (is_array($whatGet) && count($whatGet) == 0)) {
            $columnsToGet = "*";
        } else if (!is_array($whatGet)) {
            $whatGet = array($whatGet);
            foreach ($whatGet as $what) {
                $columnsToGet .= $what . ", ";
            }
            $columnsToGet = substr($columnsToGet, 0, -2);
        } else {
            foreach ($whatGet as $what) {
                $columnsToGet .= $what . ", ";
            }
            $columnsToGet = substr($columnsToGet, 0, -2);
        }
        $result = null;
        if ($data == null || $container == null) {
            $result = $this->mysqli->query("SELECT " . $columnsToGet . " FROM " . $this->name);
        } else {
            $containerString = "";
            foreach ($container as $value) {
                $containerString .= "'" . $value . "', ";
            }
            $containerString = substr($containerString, 0, -2);
            $result = $this->mysqli->query("SELECT " . $columnsToGet . " FROM " . $this->name . " WHERE " . $data . " IN (" . $containerString . ")");
        }
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        if (count($rows) == 0)
            return null;
        if (count($rows) == 1 && count($whatGet) == 1) {
            return array_values($rows[0])[0];
        }
        return $rows;
    }

    public function getAllColumn($columsName)
    {
        $columName = "";
        if (!is_array($columsName))
            $columsName = array($columsName);
        foreach ($columsName as $colum) {
            $columName .= $colum . ", ";
        }
        $columName = substr($columName, 0, -2);
        $result = $this->mysqli->query("SELECT " . $columName . " FROM " . $this->name);
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getOneColumn($id, $columName)
    {
        $result = $this->mysqli->query("SELECT " . $columName . " FROM " . $this->name . " WHERE id = " . $id);
        $row = $result->fetch_assoc();
        return $row;
    }

    public function insert($data)
    {
        $columns = "";
        $values = "";
        foreach ($data as $key => $value) {
            $columns .= $key . ", ";
            $values .= "'" . $value . "', ";
        }
        $columns = substr($columns, 0, -2);
        $values = substr($values, 0, -2);
        echo "INSERT INTO " . $this->name . " (" . $columns . ") VALUES (" . $values . ")";
        $this->mysqli->query("INSERT INTO " . $this->name . " (" . $columns . ") VALUES (" . $values . ")");
    }

    public function update($id, $data)
    {
        $values = "";
        foreach ($data as $key => $value) {
            $values .= $key . " = '" . $value . "', ";
        }
        $values = substr($values, 0, -2);
        //echo "UPDATE " . $this->name . " SET " . $values . " WHERE id = " . $id;
        $this->mysqli->query("UPDATE " . $this->name . " SET " . $values . " WHERE id = " . $id);
    }

    public function delete($id)
    {
        $this->mysqli->query("DELETE FROM " . $this->name . " WHERE id = " . $id);
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public static function UserDB(): Database
    {
        $mysqli = new mysqli("localhost", "root", "", "php_exam_db");
        $user = new Database($mysqli, "user", array("username", "password", "email"));
        return $user;
    }

    public static function ArticleDB(): Database
    {
        $mysqli = new mysqli("localhost", "root", "", "php_exam_db");
        $article = new Database($mysqli, "article", array("id", "name", "description", "price", "publication_date", "author_id", "image_link"));
        return $article;
    }

    public static function StockDB(): Database
    {
        $mysqli = new mysqli("localhost", "root", "", "php_exam_db");
        $stockdb = new Database($mysqli, "stock", array("id", "article_id", "quantity"));
        return $stockdb;
    }

    public static function CartDB(): Database
    {
        $mysqli = new mysqli("localhost", "root", "", "php_exam_db");
        $cartdb = new Database($mysqli, "cart", array("id", "user_id", "article_id"));
        return $cartdb;
    }

    public static function FactureDB(): Database
    {
        $mysqli = new mysqli("localhost", "root", "", "php_exam_db");
        $facturedb = new Database($mysqli, "invoice", array("id", "user_id", "transaction_date", "amount", 'billing_address', 'billing_city', 'billing_postal_code'));
        return $facturedb;
    }

    public static function deleteUserAndReferences($id)
    {
        $cartdb = Database::CartDB();
        $cartdb->deleteWhere(array("user_id" => $id));
        $facturedb = Database::FactureDB();
        $facturedb->deleteWhere(array("user_id" => $id));
        $articledb = Database::ArticleDB();
        $articledb->deleteWhere(array("author_id" => $id));
        $userdb = Database::UserDB();
        $userdb->delete($id);
    }
    
    public static function deleteArticleAndReferences($id)
    {
        $cartdb = Database::CartDB();
        $cartdb->deleteWhere(array("article_id" => $id));
        $stockdb = Database::StockDB();
        $stockdb->deleteWhere(array("article_id" => $id));
        $articledb = Database::ArticleDB();
        $articledb->delete($id);
    }

    public function deleteWhere($datas){
        $req = "DELETE FROM " . $this->name . " WHERE ";
        foreach ($datas as $key => $value) {
            $req .= $key . " = '" . $value . "' AND ";
        }
        $req = substr($req, 0, -5);
        $this->mysqli->query($req);

    }

    public function generalQuery($query)
    {
        $result = $this->mysqli->query($query);
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
}