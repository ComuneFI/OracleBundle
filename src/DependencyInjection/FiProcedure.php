<?php

namespace Fi\OracleBundle\DependencyInjection;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author manzolo
 */
class FiProcedure extends FiOracle
{

    private $resultset = array();
    private $numrows = 0;
    private $maxrows = -1;
    private $initialrow = 0;
    private $orasid;
    private $orausr;
    private $orapwd;

    public function __construct($parms)
    {
        $this->orasid = $parms['connessione'];
        $this->orausr = $parms['utente'];
        $this->orapwd = $parms['password'];
    }

    public function execute($sql, $parms = array())
    {
        if (!$sql) {
            trigger_error("Non è stata specificata l'istruzione sql da elaborare", E_USER_ERROR);
        }
        $dbOracle = $this->connect($this->orasid, $this->orausr, $this->orapwd);

        $sql_statement = oci_parse($dbOracle, $sql);

        $qParameters = (isset($parms['parameters']) ? $parms['parameters'] : array());

        foreach ($qParameters as $key => $value) {
            oci_bind_by_name($sql_statement, $key, $value);
        }

        $exitcode = oci_execute($sql_statement);
        if (!$exitcode) {
            $ex = oci_error($sql_statement);
            trigger_error('Impossibile eseguire la procedure: ' . $sql . '<br/>' . $ex['message'], E_USER_ERROR);
        }

        //Si libera la risorsa che conteneva lo statment sql
        oci_free_statement($sql_statement);
        return $exitcode;
    }

}
