<?php

namespace Fi\OracleBundle\DependencyInjection;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author manzolo
 */
class FiQuery extends FiOracle
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

    public function executeSelectQuery($sql, $parms = array())
    {
        $this->resultset = array();
        $this->numrows = 0;
        if (!$sql) {
            trigger_error("Non è stata specificata l'istruzione sql da elaborare", E_USER_ERROR);
        }
        $dbOracle = $this->connect($this->orasid, $this->orausr, $this->orapwd);

        $sql_statement = oci_parse($dbOracle, $sql);

        $qParameters = (isset($parms['query_parameters']) ? $parms['query_parameters'] : array());

        foreach ($qParameters as $key => $value) {
            oci_bind_by_name($sql_statement, $key, $value);
        }

        $exitcode = oci_execute($sql_statement);
        if (!$exitcode) {
            $ex = oci_error($sql_statement);
            trigger_error('Impossibile eseguire la query: '.$sql.'<br/>'.$ex['message'], E_USER_ERROR);
        }

        if (isset($parms['fetch_by_column']) && $parms['fetch_by_column']) {
            $fetchMode = OCI_FETCHSTATEMENT_BY_COLUMN;
        } else {
            $fetchMode = OCI_FETCHSTATEMENT_BY_ROW;
        }

        $fetchmodquery = $fetchMode + OCI_ASSOC + OCI_RETURN_NULLS;
        $this->numrows = oci_fetch_all($sql_statement, $this->resultset, $this->initialrow, $this->maxrows, $fetchmodquery);

        //Si libera la risorsa che conteneva lo statment sql
        oci_free_statement($sql_statement);
        //oci_close($dbOracle);
    }

    public function executeQuery($sql)
    {
        $this->resultset = array();
        $this->numrows = 0;
        if (!$sql) {
            trigger_error("Non è stata specificata l'istruzione sql da elaborare", E_USER_ERROR);
        }
        $dbOracle = $this->connect($this->orasid, $this->orausr, $this->orapwd);

        $sql_statement = oci_parse($dbOracle, $sql);

        $exitcode = oci_execute($sql_statement);
        $this->numrows = oci_num_rows($sql_statement);
        if (!$exitcode) {
            $ex = oci_error($sql_statement);
            trigger_error('Impossibile eseguire la query: '.$sql.'<br/>'.$ex['message'], E_USER_ERROR);
        }

        //Si libera la risorsa che conteneva lo statment sql
        oci_free_statement($sql_statement);
        //oci_close($dbOracle);
    }

    public function getResultset()
    {
        return $this->resultset;
    }

    public function getNumRows()
    {
        return $this->numrows;
    }
}
