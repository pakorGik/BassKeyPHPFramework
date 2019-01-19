<?php

namespace BassKey\Components\Database\Controllers;

use \PDO;

class DB_MS_SQL
{
    # @object, The PDO object
    private $pdo;

    # @object, PDO statement object
    private $sQuery;

    # @array,  The database settings
    private $settings;

    # @bool ,  Connected to the database
    private $bConnected = false;

    # @object, Object for logging exceptions
    private $log;

    # @array, The parameters of the SQL query
    private $parameters;

    /**
     *   Default Constructor
     *
     *	1. Instantiate Log class.
     *	2. Connect to database.
     *	3. Creates the parameter array.
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->log = new DatabaseLog();
        $this->Connect();
        $this->parameters = array();
    }

    /**
     *	This method makes connection to the database.
     *
     *	1. Reads the database settings from a ini file.
     *	2. Puts  the ini content into the settings array.
     *	3. Tries to connect to the database.
     *	4. If connection failed, exception is displayed and a log file gets created.
     */
    private function Connect()
    {
        $dsn = 'sqlsrv:Server='.$this->settings["host"].';Database='.$this->settings["dbname"].'';
        try
        {
            # Read settings from INI file, set UTF8
            $this->pdo = new PDO($dsn, $this->settings["user"], $this->settings["password"]);
            $this->pdo->exec("set names utf8");

            # We can now log any exceptions on Fatal error.
            //$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            # Disable emulation of prepared statements, use REAL prepared statements instead.
            //$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            # Connection succeeded, set the boolean to true.
            $this->bConnected = true;
        }
        catch (PDOException $e)
        {
            # Write into log
            echo $this->ExceptionLog($e->getMessage());
            die();
        }
    }
    /*
     *   You can use this little method if you want to close the PDO connection
     *
     */
    public function CloseConnection()
    {
        # Set the PDO object to null to close the connection
        # http://www.php.net/manual/en/pdo.connections.php
        $this->pdo = null;
    }

    /**
     *	Every method which needs to execute a SQL query uses this method.
     *
     *	1. If not connected, connect to the database.
     *	2. Prepare Query.
     *	3. Parameterize Query.
     *	4. Execute Query.
     *	5. On exception : Write Exception into the log + SQL query.
     *	6. Reset the Parameters.
     */
    private function Init($query,$parameters = "")
    {
        # Connect to database
        if(!$this->bConnected) { $this->Connect(); }
        try {
            # Prepare query
            $this->sQuery = $this->pdo->prepare($query);

            # Add parameters to the parameter array
            $this->bindMore($parameters);

            # Bind parameters
            if(!empty($this->parameters)) {
                foreach($this->parameters as $param)
                {
                    $parameters = explode("\x7F",$param);
                    $this->sQuery->bindParam($parameters[0],$parameters[1]);
                }
            }

            # Execute SQL
            $this->succes 	= $this->sQuery->execute();
        }
        catch(PDOException $e)
        {
            # Write into log and display Exception
            echo $this->ExceptionLog($e->getMessage(), $query );
            die();
        }

        # Reset the parameters
        $this->parameters = array();
    }

    /**
     *	@void
     *
     *	Add the parameter to the parameter array
     *	@param string $para
     *	@param string $value
     */
    public function bind($para, $value)
    {
        $this->parameters[sizeof($this->parameters)] = ":" . $para . "\x7F" . $value;
    }
    /**
     *	@void
     *
     *	Add more parameters to the parameter array
     *	@param array $parray
     */
    public function bindMore($parray)
    {
        if(empty($this->parameters) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach($columns as $i => &$column)	{
                $this->bind($column, $parray[$column]);
            }
        }
    }
    /**
     *   	If the SQL query  contains a SELECT or SHOW statement it returns an array containing all of the result set row
     *	If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
     *
     *   	@param  string $query
     *	@param  array  $params
     *	@param  int    $fetchmode
     *	@return mixed
     */
    public function query($query,$params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $query = trim($query);

        $this->Init($query,$params);

        $rawStatement = explode(" ", $query);

        # Which SQL statement is used
        $statement = strtolower($rawStatement[0]);

        if ($statement === 'select' || $statement === 'show') {
            return $this->sQuery->fetchAll($fetchmode);
        }
        elseif ( $statement === 'insert' ||  $statement === 'update' || $statement === 'delete' ) {
            return $this->sQuery->rowCount();
        }
        else {
            return NULL;
        }
    }

    public function querySelect($query,$params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $query = trim($query);

        $this->Init($query,$params);

        $rawStatement = explode(" ", $query);

        # Which SQL statement is used
        $statement = strtolower($rawStatement[0]);

        if ($statement === 'select' || $statement === 'show') {
            return $this->sQuery->fetchAll($fetchmode);
        }
        else {
            return NULL;
        }
    }

    /**
     * queryTransactions
     * @param array(array("", array()), array("", array()), ...) $query
     */
    public function queryTransactions($query)
    {
        if(!isset($query) || empty($query) || !is_array($query))
        {
            return false;
        }

        try
        {
            $this->beginTransaction();

            foreach($query as $sql)
            {
                if(!isset($sql[0], $sql[1]) || empty($sql[0]))
                {
                    continue;
                }

                $this->query($sql[0], $sql[1]);
            }

            $this->commit();
            return array("status" => "success");
        }
        catch (\Exception $e)
        {
            $this->rollBack();
            return array("status" => "error", "errors" => array("Error"));
        }
    }

    public function commit ()
    {
        $this->pdo->commit();
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function rollBack()
    {
        $this->pdo->rollBack();
    }

    /**
     *  Returns the last inserted id.
     *  @return string
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     *	Returns an array which represents a column from the result set
     *
     *	@param  string $query
     *	@param  array  $params
     *	@return array
     */
    public function column($query,$params = null)
    {
        $this->Init($query,$params);
        $Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);

        $column = null;

        foreach($Columns as $cells) {
            $column[] = $cells[0];
        }

        return $column;

    }
    /**
     *	Returns an array which represents a row from the result set
     *
     *	@param  string $query
     *	@param  array  $params
     *   	@param  int    $fetchmode
     *	@return array
     */
    public function row($query,$params = null,$fetchmode = PDO::FETCH_ASSOC)
    {
        $this->Init($query,$params);
        return $this->sQuery->fetch($fetchmode);
    }
    /**
     *	Returns the value of one single field/column
     *
     *	@param  string $query
     *	@param  array  $params
     *	@return string
     */
    public function single($query,$params = null)
    {
        $this->Init($query,$params);
        return $this->sQuery->fetchColumn();
    }
    /**
     * Writes the log and returns the exception
     *
     * @param  string $message
     * @param  string $sql
     * @return string
     */
    protected function ExceptionLog($message , $sql = "")
    {
        $exception  = 'Unhandled Exception. <br />';
        $exception .= $message;
        $exception .= "<br /> You can find the error back in the log.";

        if(!empty($sql)) {
            # Add the Raw SQL to the Log
            $message .= "\r\nRaw SQL : "  . $sql;
        }
        # Write into log
        $this->log->write($message);

        return $exception;
    }

    /**
     * @param mixed $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param DatabaseLog $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }
}