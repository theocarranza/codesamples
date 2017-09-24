<?php

/**
 * Description of ElectionController
 * Controls all actions used to mantain the election.
 * The checkIdentity() function will interrupt the flow
 * and exit gracefully if the user has no credentials.
 * 
 * @author Théo Carranza 
 * theocarranza@gmail.com
 */

class ElectionController extends Core_Controller_Action

{
    /* Obtains the GET request params for a particular context. */

    private function getParams()
    {
        return $this->getRequest()->getParams();
    }

    /* CRUD */

    /* Creates an election */

    public function createAction()
    {
        $this->checkIdentity();
        $params = $this->getParams();
        return $this->_helper->CreateEleicaoBO($params);
    }


    /* Lists elections according to user's permissions */

    public function listAction()
    {
        $this->checkIdentity();
        return $this->_helper->crud('list');
    }
   
    /* Updates an election */

    public function updateAction()
    {

        $this->checkIdentity();
        $params = $this->getParams();
        return $this->_helper->UpdateElectionoBO($params);
    }
    
    /* Deletes an election */

    public function deleteAction()
    {
        $this->checkIdentity();
        return $this->_helper->crud('delete');
    }

    /**
     * Business logic that is domain specific. All BOs will return an object
     * containing the state of the operation, which will be either true or false,
     * and a message, properly obtained from the messages.ini file.
     */

    /**
     * Elections can have 3 statuses: Active, paused, or closed. This BO will
     * take care of the business logic of changinng these statuses.
     * Evaluates to true if all went well, and false if there was an error or
     * the change in status would result in an inconsistent state.
     **/

    public function changestatusAction()
    {
        $this->checkIdentity();
        $params = $this->getParams();
        return $this->_helper->UpdateStatusBO($params);
    }

    /**
     * Updates the election's type, removes all candidates to prevent
     * inconsistencies (solo canditates running for a slate type election, 
     * for example)
     */

    public function updateType()
    {
        $this->checkIdentity();
        $params = $this->getParams();
        return $this->_helper->UpdateTypeBO($params);
    }

    /**
     * Updates the election's criteria, for elections that are state/city
     * based.
     */

    public function updatecriteriaAction()
    {

        $this->checkIdentity();
        $params = $this->getParams();
        $this->_helper->UpdateCriteriaBO($params);
    }

    /**
     * Audits the rules for a particular election, and evaluates to
     * true if all rules are consistent and properly in place. 
     */

    public function validateEleicao()
    {
        $this->checkIdentity();
        $params = $this->getParams();
        return $this->_helper->ValidateEleicaoBO($params);
    }


    /**
     * Creates a list of all users that can vote for a particular election.
     * This list will be available on the elections tab under the election
     * module.
     **/

    public function voterlistAction()
    {
        $idElection = $this->getParams()['id_election'];
        return $this->_helper->CreateListVoterBO($idElection);
    }

    public function checkElection($election, $voter)
    {
        return $this->_helper->CheckElectionBO($election, $voter);
    }

    // Getter for the voter's state

    public function getVoterState($voter)
    {
        return $this->_helper->GetVoterStateBO($voter);
    }

    // Getter for the voter's local department

    public function getVoterDepartment($voter)
    {
        return $this->_helper->GetVoterDepartmentBO($voter);
    }

    /**
     * Getter for the list of elections a particular voter can see.
     * Takes in account the various rules described in the documentation.
     */

    public function getelectionAction()
    {

        $this->checkIdentity();
        $idVoter = Zend_Auth::getInstance()->getIdentity()->ID_PERSON;
        return $this->_helper->ShowElectionBO($idVoter);
    }

    /**
     * This BO will perform the auditing of the results of an election.
     * It is computationally heavy for elections with a large voters base,
     * so it is important to have time constrains in place to periodically
     * check the state of the operation and make sure it's still active.
     **/

    public function auditAction($idElection = null)
    {
        $this->checkIdentity();
        $idElection = $this->getParams()['id_election'];
        return $this->_helper->AuditElectionBO($idElection);
    }
    /**
     * This BO will perform a few queries to determine if the election being created does not violate any
     * business rules or creates inconsistencies. It will call doValidateVotingBO to perform more 
     * specific computations.
     **/

    public function validatevotingAction($idElection)
    {
        $this->checkIdentity();
        $idElection = $this->getParams()['id_election'];
        return $this->_helper->ValidadeElectionBO($idElection);
    }

    /**
     * @param type $vote
     */

    private function doValidatevotingAction($seed, $hashAlgorithm, $vote, $previosVote = null)
    {
        $this->checkIdentity();
        return $this->_helper->DoValidateVotingBO($seed, $hashAlgorithm, $vote, $previosVote = null);

    }

}

?>
