<?php
/*
*Refer to  PDO CRUD
*
*/
class db
{

//    var $db;
//	var $dsn=DSN;//db server; 
//	var $username = USERNAME;//db username; 
//	var $password =PASSWORD;//db password; 

    var $db;
	var $dsn="mysql:host=localhost;dbname=lergerator";//db server; 
	var $username ="root";//db username; 
	var $password ="asdfjkl";//db password;


    /**
     *
     * Set variables
     *
     */
    public function __set($name, $value)
    {
        switch($name)
        {
            case 'username':
            $this->username = $value;
            break;

            case 'password':
            $this->password = $value;
            break;

            case 'dsn':
            $this->dsn = $value;
            break;

            default:
            throw new Exception("$name is invalid");
        }
    }

    /**
     *
     * @check variables have default value
     *
     */
    public function __isset($name)
    {
        switch($name)
        {
            case 'username':
            $this->username = null;
            break;

            case 'password':
            $this->password = null;
            break;
        }
    }

        /**
         *
         * @Connect to the database and set the error mode to Exception
         *
         * @Throws PDOException on failure
         *
         */
        public function conn()
        {
            isset($this->username);
            isset($this->password);
            if (!$this->db instanceof PDO)
            {
                $this->db = new PDO($this->dsn, $this->username, $this->password);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        }


        /***
         *
         * @select values from table
         *
         * @access public
         *
         * @param string $table The name of the table
         *
         * @param string $fieldname
         *
         * @param string $id
         *
         * @return array on success or throw PDOException on failure
         *
         */
        public function dbSelect($table, $fieldname=null, $id=null)
        {
            $this->conn();
            $sql = "SELECT * FROM `$table` WHERE `$fieldname`=:id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

		/*add   by david 2011-12-12
		*function:count amount of  records
		*/
		 public function getCnt($table, $fieldname=null, $id=null)
        {
            $this->conn();
            $sql = "SELECT count(*) as cnt FROM `$table` WHERE `$fieldname`=:id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result[0]['cnt'];
        }


        /**
         *
         * @execute a raw query
         *
         * @access public
         *
         * @param string $sql
         *
         * @return array
         *
         */
        public function rawQuery($sql)
        {
            $this->conn();
            $records=$this->db->query($sql);
            return  $records->fetchAll(PDO::FETCH_ASSOC);
        }


        /**
         *
         * @Insert a value into a table
         *
         * @acces public
         *
         * @param string $table
         *
         * @param array $values
         *
         * @return int The last Insert Id on success or throw PDOexeption on failure
         *array(array('tf_num_string'=>$prefix,'supported'=>$supported))
         */
        public function dbInsert($table, $values)
        {
            $this->conn();
            /*** snarg the field names from the first array member ***/
            $fieldnames = array_keys($values[0]);
            /*** now build the query ***/
            $size = sizeof($fieldnames);
            $i = 1;
            $sql = "INSERT INTO $table";
            /*** set the field names ***/
            $fields = '( ' . implode(' ,', $fieldnames) . ' )';
            /*** set the placeholders ***/
            $bound = '(:' . implode(', :', $fieldnames) . ' )';
            /*** put the query together ***/
            $sql .= $fields.' VALUES '.$bound;


            /*** prepare and execute ***/
            $stmt = $this->db->prepare($sql);
            foreach($values as $vals)
            {
                $stmt->execute($vals);
            }
        }


        public function insert($table, $values)
        {
            $this->conn();
            /*** snarg the field names from the first array member ***/
            $fieldnames = array_keys($values[0]);
            /*** now build the query ***/

            $sql = "INSERT INTO $table";
            /*** set the field names ***/
            $fields = '( `' . implode('` ,`', $fieldnames) . '`)';
            /*** set the placeholders ***/
//            $bound = '(:' . implode(', :', $fieldnames) . ' )';
            $placeholder = '( ' . substr(str_repeat('?,',count($fieldnames)),0,-1) . ' )';
            /*** put the query together ***/
            $sql .= $fields.' VALUES '.$placeholder;


            /*** prepare and execute ***/
            $stmt = $this->db->prepare($sql);

            foreach($values as $vals)
            {
                $stmt->execute(array_values($vals));
            }
        }

		/**
		*Insert a record into a table
		*@insertSql  Insert sql statement.
		*/
		 public function dbAdd($insertSql)
        {
            $this->conn();
			$cnt=$this->db->exec($insertSql);
			return $cnt;
        }

        public function query($Sql)
        {
            $this->conn();
            $cnt=$this->db->exec($Sql);
            return $cnt;
        }
        /**
         *
         * @Update a value in a table
         *
         * @access public
         *
         * @param string $table
         *
         * @param string $fieldname, The field to be updated
         *
         * @param string $value The new value
         *
         * @param string $pk The primary key
         *
         * @param string $id The id
         *
         * @throws PDOException on failure
         *
         */
        public function dbUpdateOne($table, $fieldname, $value, $pk, $id)
        {
            $this->conn();
            $sql = "UPDATE `$table` SET `$fieldname`='{$value}' WHERE `$pk` = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();
        }

        public function dbUpdateMultiple($table,$condition=array(),$values=array())
        {
            $this->conn();
            $set = '';
            foreach ($values as $fieldname => $value) {
                $set .= "`$fieldname`=?,";
            }
            $set = substr_replace($set, "", -1 );

            $where = ' WHERE ';
            foreach ($condition as $fieldname => $value) {
                $where .= "`$fieldname`='$value' AND";
            }
            $where = substr_replace($where, "", -3 );

            $sql ="UPDATE `$table` SET " .$set.$where;
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($values));


        }


		  public function dbUpdate($updateSql)
        {
            $this->conn();

            $this->db->exec($updateSql);
        }


        /**
         *
         * @Delete a record from a table
         *
         * @access public
         *
         * @param string $table
         *
         * @param string $fieldname
         *
         * @param string $id
         *
         * @throws PDOexception on failure
         *
         */
        public function dbDelete($table, $fieldname, $id)
        {
            $this->conn();
            $sql = "DELETE FROM `$table` WHERE `$fieldname` = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

