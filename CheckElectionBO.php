<?php

/**
 * Description of CheckElectionBO 
 * Checks the rules that determine if a particular member can vote in an election.
 * 
 * @author Théo Carranza 
 * theocarranza@gmail.com
 */

class Core_BO_Election_CheckElectionBO extends Zend_Controller_Action_Helper_Abstract
{
    private $userModel = new Application_Model_Table(array('name' => 'USER'));
    private $stSubscriptionModel = new Application_Model_Table(array('name' => 'MEMBER_STATUS_SUBSCRIPTION'));
    private $electionTypeCriteriaModel = new Application_Model_Table(array('name' => 'ELECTION_TYPE_CRITERIA'));

    public $result = array();

    public function direct($election, $member)
    {
        $this->result['success'] = false;
        $this->result['message'] = $this->getActionController()->_getMessage('MSGERAL001');

        /* General rules */

        /* Rule n. 1: An user must be a member to vote */

        if ($member->STATUS == 0) {
            $this->result['message'] = $this->getActionController()->_getMessage('MS001');
            return $this->result;
        }

        /* Rule n. 2: An user must have his status set to 'active' to be able to vote */
        /* User model table */


        /* Obtains the user */

        $user = $this->userModel->select()->where("ID_MEMBER =                                      $member->ID")->query()->fetch();

        if ($user->STATUS == 0) {
            $this->result['message'] = $this->getActionController()->_getMessage('MS003');
            return $this->result;
        }

        /* Rule n . 3 : The user must have updated his subscription informations 
        and changed his password at least one time to be able to vote */

        $stSubscription = $this->stSubscriptionModel->select()->where("ID_MEMBER =                              $member->ID")->query()->fetch();

        if ($stSubscription->ST_SUBSCRIPTION == 0 || $stSubscription->ST_PASSWORD == 0) {
            $this->result['message'] = $this->getActionController->_getMessage('MVOTACAO010');
            return $this->result;
        }

        /* State election specific rule */

        if ($election->ID_ELECTION_TYPE == 2) {

            $electionTypeCriteria = $electionTypeCriteriaModel->select()->where("ID_ELECTION_TYPE = "
                . $election->ID)->query()->fetch();


            if (!isset($electionTypeCriteria->ID)) {
                $this->result['message'] = $this->getActionController()->_getMessage('MELEICAO018');
                return $this->result;
            }

            if ($this->getActionController()->getHelper('getMemberStateBO')->direct($member) !=
                $electionTypeCriteria->ID_ELECTION_TYPE_STATE) {
                $this->result['message'] = $this->getActionController()->_getMessage('MVOTACAO017');
                return $this->result;
            }
        }
        
        /* Particular case: if the election is for a regional office */

        if ($election->ID_election_TIPO == 3) {

            $electionTypeCriteriaTable = new Application_Model_Table(array('name' => 'ELECTION_TYPE_CRITERIA'));
            $electionTypeCriteria = $electionTypeCriteriaTable->select()->where("ID_ELECTION_TYPE = "
                . $election->ID)->query()->fetch();
            $regionalOffice = $this->getActionController()->getHelper('getMemberRegionalOfficeBO')
                ->direct($member);
            $member = $this->getActionController()->getHelper('getMemberStateBO')->direct($member);

            if (!isset($electionTypeCriteria->ID)) {
                $this->result['message'] = $this->getActionController()->_getMessage('MELEICAO018');
                return $this->result;
            }

            /* Rules for retired members */

            if (!$member->IS_RETIRED && !$member->STATE_RESIDES_WORKS) {

                if ($stateOffice->ID_STATEOFFICE == $electionTypeCriteria->ID_ELECTION_TYPE_STATEOFFICE
                    && $stateOffice->ID_STATE == $electionTypeCriteria->ID_ELECTION_TYPE_STATE) {
                    $this->result['success'] = true;
                    $this->result['message'] = $this->getActionController()->_getMessage('MSGERAL003');
                }
                else {
                    $this->result['message'] = $this->getActionController()->_getMessage('MVOTACAO017');

                }
                return $this->result;
            }

            if ($member->IS_RETIRED) {

                if ($state == $electionTypeCriteria->ID_ELECTION_TYPE_STATE) {
                    $this->result['success'] = true;
                    $this->result['message'] = $this->getActionController()->_getMessage('MSGERAL003');
                }
                else {
                    $this->result['message'] = $this->getActionController()->_getMessage('MVOTACAO017');
                }
                return $this->result;
            }

            if (!$member->IS_RETIRED && !$member->STATE_RESIDES_WORKS) {

                if ($ramo->ID_UF == $electionTypeCriterio->ID_election_TIPO_UF) {
                    $this->result['success'] = true;
                    $this->result['message'] = $this->getActionController()->_getMessage('MSGERAL003');
                }
                else {
                    $this->result['message'] = $this->getActionController()->_getMessage('MVOTACAO017');
                }
                return $this->result;
            }
        }

        $this->result['success'] = true;
        $this->result['message'] = $this->getActionController()->_getMessage('MSGERAL003');
        return $this->result;

    }

}

?>
