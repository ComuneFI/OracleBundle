<?php

namespace Fi\OracleBundle\DependencyInjection;

class FiOracle
{
    private $OracledbName;
    private $Oracleusername;
    private $Oraclepassword;
    private $dbOracle;

//  public function __construct()
    protected function connect($connessione, $utente, $password)
    {

        //$dbName,$username,$password
        $this->OracledbName = $connessione;
        $this->Oracleusername = $utente;
        $this->Oraclepassword = $password;
        /* Connessione oracle */
        $this->dbOracle = oci_connect($this->Oracleusername, $this->Oraclepassword, $this->OracledbName, 'AL32UTF8');
        // test connection
        if (!$this->dbOracle) {
            $err_description = oci_error();
            $code = $err_description['code'];
            $message = 'Impossibile stabilire una connessione con il server Oracle: '.
                    $this->OracledbName.htmlentities($err_description['message']);
            throw new Exception($message, $code);
        }

        return $this->dbOracle;
        /* Connessione oracle */
    }

    public function __destruct()
    {
        //echo 'Object was just destroyed <br>';
        //$this->disconnect();
    }

    protected function disconnect()
    {
        /* Disconnessione oracle */
        //OCILogoff($this->dbOracle);
        //oci_close($this->dbOracle);
        /* Disconnessione oracle */
    }
}
