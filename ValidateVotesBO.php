<?php

/**
 * Description of ValidateVotingResultsBO
 * Class that audits all votes of an election.
 * Gets invoked by AuditElectionBO when an election is closed, and will match every voting record against
 * the rules used to encode them. If a single vote is found to be invalid, all votes thereafter will be marked
 * as invalid as well, and the election will be considered to be null and void.
 *
 * @author Théo Carranza
 * theocarranza@gmail.com
 */

class Core_BO_Election_ValidateVotesBO extends Zend_Controller_Action_Helper_Abstract
{
    private $config = $this->getActionController()->getInvokeArg('bootstrap')->getOption('app');
    private $hashAlgorithm = $config['eleicao']['seedHashAlgorithm'];
    private $seedsPath = $config['eleicao']['seedsPath'];

    private $resultsoModel = new Application_Model_Table(array('name' => 'VOTING_RESULTS'));
    private $votesModel = new Application_Model_Table(array('name' => 'VOTES'));

    private $db = Zend_Db_Table::getDefaultAdapter();

    public $resullt = array();


    public function direct($idElection)
    {
        $this->result['success'] = false;
        $this->result['message'] = $this->getActionController()->_getMessage('MSGERAL001');

        try {
            /* Clears the results table records */
            $this->resultsModel->delete("ID_ELECTION = " . $idElection);

            /* Locks the table and sets it up to serialized read */
            $this->db->getConnection()->exec('SET GLOBAL TRANSACTION ISOLATION LEVEL SERIALIZABLE');
            $this->db->beginTransaction();

           /* Open the seed file with read only privileges */

            $fp = @fopen("$seedsPath" . "$idEleicao", "r");

            if (!$fp) {
                $this->result['success'] = false;
                $this->result['message'] = $this->getActionController()->_getMessage(
                    'MVOTACAO001',
                    array('?id_election' => $idElection)
                );
                return $this->getActionController()->getHelper('json')->direct($this->result);
            }

            $seed = trim(fgets($fp, 4096));

            if (!isset($seed)) {
                $this->result['success'] = false;
                $this->resul['message'] = $this->getActionController()->_getMessage('MVOTACAO004');
                return $this->getActionController()->getHelper('json')->direct($this->result);
            }

            fclose($fp);

            $count = 0;
            $previousVote = null;
            $votes = $this->votesModel->select()->where("ID_ELECTION = $idElection")->order('ID')->query()
                ->fetchAll();

            foreach ($votes as $vote) {

                if (! ($r = $this->getActionController()->getHelper('DoValidateVotesBO')
                    ->direct($seed, $hashAlgorithm, $vote, $previousVote))) {
                    $this->result['message'] = $this->getActionController()->_getMessage('MVOTACAO008');
                    return $this->result;
                }
                $cont++;
                $previousVote = $vote;
            }

            $db->commit();

            if ($count == 0) {
                return $this->result;
            }
            else {
                $this->result['success'] = true;
                $this->result['message'] = $this->getActionController()->_getMessage('MSGERAL003');
                return $this->result;
            }

        } catch (Exeption $e) {
            $result['message'] = $e->_getMessage();
            return $this->result;
        }
    }
}


?>
